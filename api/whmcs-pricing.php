<?php
/**
 * CloudFlox Live WHMCS Pricing Bridge
 * -------------------------------------------------------------------------
 * Dynamically fetches live domain pricing and hosting product plans from
 * WHMCS (via direct database connection or WHMCS public feeds) with
 * high-performance file caching.
 */

class WHMCSPricing {

    private static $cacheDir = __DIR__ . '/cache';
    private static $cacheTtl = 3600; // 1 hour cache

    /**
     * Get Database Connection to WHMCS
     */
    private static function getDb() {
        static $db = null;
        if ($db !== null) {
            return $db;
        }

        // Credentials matching cfwhmcs configuration
        $host = '127.0.0.1';
        $user = 'cfwhmcs_whmcs';
        $pass = '+V)Rb%K3wFiK';
        $name = 'cfwhmcs_whmcs3';

        // Check if cfwhmcs/configuration.php has custom settings
        $configFile = __DIR__ . '/../cfwhmcs/configuration.php';
        if (file_exists($configFile)) {
            @include $configFile;
            if (!empty($db_host)) $host = $db_host;
            if (!empty($db_username)) $user = $db_username;
            if (!empty($db_password)) $pass = $db_password;
            if (!empty($db_name)) $name = $db_name;
        }

        try {
            $mysqli = @new mysqli($host, $user, $pass, $name);
            if (!$mysqli->connect_error) {
                $mysqli->set_charset('utf8mb4');
                $db = $mysqli;
                return $db;
            }
        } catch (Exception $e) {}

        $db = false;
        return false;
    }

    /**
     * Get cached data if valid
     */
    private static function getCache($key) {
        $file = self::$cacheDir . '/' . preg_replace('/[^a-z0-9_\-]/i', '', $key) . '.json';
        if (file_exists($file)) {
            $mtime = filemtime($file);
            if ((time() - $mtime) < self::$cacheTtl) {
                $content = @file_get_contents($file);
                if (!empty($content)) {
                    $json = json_decode($content, true);
                    if (is_array($json) && !empty($json)) {
                        return $json;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Save data to file cache
     */
    private static function setCache($key, array $data) {
        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0755, true);
        }
        $file = self::$cacheDir . '/' . preg_replace('/[^a-z0-9_\-]/i', '', $key) . '.json';
        @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Fetch Live Domain Pricing from WHMCS
     * 
     * @return array Keyed by TLD (e.g. 'com' => ['extension' => '.com', 'price' => '$14.93', ...])
     */
    public static function getDomainPricing() {
        $cached = self::getCache('whmcs_domains');
        if ($cached) {
            return $cached;
        }

        $results = [];
        $db = self::getDb();

        if ($db) {
            // 1. Fetch from WHMCS Database
            $sql = "SELECT dp.id, dp.extension, 
                           pr_reg.msetupfee AS reg_1yr, pr_reg.monthly AS reg_2yr,
                           pr_ren.msetupfee AS ren_1yr,
                           pr_tra.msetupfee AS tra_1yr
                    FROM tbldomainpricing dp
                    LEFT JOIN tblpricing pr_reg ON pr_reg.relid = dp.id AND pr_reg.type = 'domainregister' AND pr_reg.currency = 1
                    LEFT JOIN tblpricing pr_ren ON pr_ren.relid = dp.id AND pr_ren.type = 'domainrenew' AND pr_ren.currency = 1
                    LEFT JOIN tblpricing pr_tra ON pr_tra.relid = dp.id AND pr_tra.type = 'domaintransfer' AND pr_tra.currency = 1
                    ORDER BY dp.order ASC, dp.id ASC";

            $res = $db->query($sql);
            if ($res && $res->num_rows > 0) {
                while ($r = $res->fetch_assoc()) {
                    $rawExt = strtolower(trim($r['extension']));
                    $cleanExt = ltrim($rawExt, '.');
                    $reg = (float)$r['reg_1yr'];
                    $tra = (float)$r['tra_1yr'];
                    $ren = (float)$r['ren_1yr'];

                    // Skip disabled/unpriced extensions (-1.00)
                    if ($reg <= 0 && $reg != 0) continue;
                    if (isset($results[$cleanExt])) continue; // prevent duplicates

                    // Badge and category metadata
                    $badge = '';
                    $category = 'Global Domain';
                    if (in_array($cleanExt, ['com', 'net', 'org'])) {
                        $badge = 'Popular';
                        $category = ($cleanExt === 'com') ? 'Commercial / Global' : (($cleanExt === 'net') ? 'Network & Tech' : 'Organizations');
                    } elseif (in_array($cleanExt, ['cloud', 'io', 'tech', 'dev'])) {
                        $badge = ($cleanExt === 'io') ? 'Tech Favorite' : 'Cloud / Tech';
                        $category = 'Technology & APIs';
                    } elseif (in_array($cleanExt, ['site', 'online', 'xyz', 'store', 'shop'])) {
                        $badge = ($cleanExt === 'site' || $cleanExt === 'store') ? 'Special Offer' : 'Sale';
                        $category = 'E-Commerce & Portfolios';
                    } elseif (in_array($cleanExt, ['biz', 'co', 'info', 'pro'])) {
                        $badge = 'Business';
                        $category = 'Business & Innovation';
                    }

                    $results[$cleanExt] = [
                        'tld'       => $cleanExt,
                        'extension' => '.' . $cleanExt,
                        'register'  => $reg,
                        'transfer'  => ($tra > 0) ? $tra : 0.00,
                        'renew'     => ($ren > 0) ? $ren : $reg,
                        'price'     => '$' . number_format($reg, 2),
                        'badge'     => $badge,
                        'category'  => $category
                    ];
                }
            }
        }

        // 2. Fallback to public WHMCS feed if database returned empty
        if (empty($results)) {
            $feedUrl = 'https://my.cloudflox.com/feeds/domainpricing.php';
            $ch = curl_init($feedUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $html = curl_exec($ch);
            curl_close($ch);

            if (!empty($html)) {
                $html = preg_replace("/^document\.write\('/i", '', trim($html));
                $html = preg_replace("/'\);$/i", '', $html);

                preg_match_all('#<tr>\s*<td>([a-z0-9\.\-]+)</td>\s*<td>(\d+)</td>\s*<td>([^<]+)</td>\s*<td>([^<]+)</td>\s*<td>([^<]+)</td>\s*</tr>#i', $html, $matches, PREG_SET_ORDER);
                foreach ($matches as $m) {
                    $ext = strtolower(trim($m[1]));
                    $cleanExt = ltrim($ext, '.');
                    $reg = (float)trim(str_replace(['USD', '$', ' '], '', $m[3]));
                    $tra = (float)trim(str_replace(['USD', '$', 'FREE!', ' '], ['','','0.00',''], $m[4]));
                    $ren = (float)trim(str_replace(['USD', '$', ' '], '', $m[5]));

                    if ($reg <= 0) continue;

                    $badge = in_array($cleanExt, ['com', 'net', 'org']) ? 'Popular' : (in_array($cleanExt, ['cloud', 'io']) ? 'Hot Deal' : '');

                    $results[$cleanExt] = [
                        'tld'       => $cleanExt,
                        'extension' => '.' . $cleanExt,
                        'register'  => $reg,
                        'transfer'  => $tra,
                        'renew'     => $ren,
                        'price'     => '$' . number_format($reg, 2),
                        'badge'     => $badge,
                        'category'  => 'Standard Domain'
                    ];
                }
            }
        }

        // 3. Fallback defaults if offline
        if (empty($results)) {
            $results = [
                'com'    => ['tld' => 'com', 'extension' => '.com', 'register' => 14.93, 'transfer' => 12.73, 'renew' => 13.81, 'price' => '$14.93', 'badge' => 'Popular', 'category' => 'Commercial / Global'],
                'net'    => ['tld' => 'net', 'extension' => '.net', 'register' => 18.47, 'transfer' => 16.81, 'renew' => 16.81, 'price' => '$18.47', 'badge' => 'Popular', 'category' => 'Network & Tech'],
                'org'    => ['tld' => 'org', 'extension' => '.org', 'register' => 9.89,  'transfer' => 16.81, 'renew' => 16.81, 'price' => '$9.89',  'badge' => 'Popular', 'category' => 'Organizations'],
                'info'   => ['tld' => 'info', 'extension' => '.info', 'register' => 5.27, 'transfer' => 28.81, 'renew' => 28.81, 'price' => '$5.27', 'badge' => 'Global', 'category' => 'Information'],
                'biz'    => ['tld' => 'biz', 'extension' => '.biz', 'register' => 29.05, 'transfer' => 26.41, 'renew' => 26.41, 'price' => '$29.05', 'badge' => 'Business', 'category' => 'Business'],
                'blog'   => ['tld' => 'blog', 'extension' => '.blog', 'register' => 6.59, 'transfer' => 31.22, 'renew' => 31.22, 'price' => '$6.59', 'badge' => 'Hot Deal', 'category' => 'Blogs & Media'],
                'io'     => ['tld' => 'io', 'extension' => '.io', 'register' => 59.39, 'transfer' => 69.59, 'renew' => 69.59, 'price' => '$59.39', 'badge' => 'Tech Favorite', 'category' => 'Tech Startups'],
                'online' => ['tld' => 'online', 'extension' => '.online', 'register' => 2.63, 'transfer' => 41.24, 'renew' => 41.24, 'price' => '$2.63', 'badge' => 'Sale', 'category' => 'Online Presence'],
                'store'  => ['tld' => 'store', 'extension' => '.store', 'register' => 2.63, 'transfer' => 62.99, 'renew' => 62.99, 'price' => '$2.63', 'badge' => 'Special Offer', 'category' => 'E-Commerce'],
                'site'   => ['tld' => 'site', 'extension' => '.site', 'register' => 1.31, 'transfer' => 41.24, 'renew' => 41.24, 'price' => '$1.31', 'badge' => 'Super Deal', 'category' => 'Personal & Showcase'],
                'xyz'    => ['tld' => 'xyz', 'extension' => '.xyz', 'register' => 2.63, 'transfer' => 17.99, 'renew' => 17.99, 'price' => '$2.63', 'badge' => 'Sale', 'category' => 'Next-Gen Web']
            ];
        }

        self::setCache('whmcs_domains', $results);
        return $results;
    }

    /**
     * Fetch Live Hosting Products & Pricing from WHMCS
     * 
     * @return array Keyed by product ID
     */
    public static function getHostingProducts() {
        $cached = self::getCache('whmcs_hosting');
        if ($cached) {
            return $cached;
        }

        $specsMap = [
            '1' => [
                'alias'    => '101',
                'name'     => 'Mini Plan',
                'category' => 'Budget Cloud Hosting',
                'specs'    => '5 GB Pure NVMe • 1 Website Included • Free SSL & Business Email',
                'icon'     => 'las la-leaf',
                'tag'      => 'Personal & Blogs',
                'popular'  => false
            ],
            '2' => [
                'alias'    => '201',
                'name'     => 'Business Starter',
                'category' => 'Business NVMe Web Hosting',
                'specs'    => '20 GB Pure NVMe • 1 vCPU & 2 GB Dedicated RAM • Free Daily Backups',
                'icon'     => 'las la-building',
                'tag'      => 'Small Business & E-Com',
                'popular'  => true
            ],
            '3' => [
                'alias'    => '202',
                'name'     => 'Business Growth',
                'category' => 'Business NVMe Web Hosting',
                'specs'    => '40 GB Pure NVMe • 2 vCPU & 4 GB Dedicated RAM • 5 Websites Included',
                'icon'     => 'las la-chart-line',
                'tag'      => 'Multi-Site & Agencies',
                'popular'  => false
            ],
            '4' => [
                'alias'    => '203',
                'name'     => 'Enterprise NVMe Hosting',
                'category' => 'Enterprise Cloud Hosting',
                'specs'    => '150 GB Pure NVMe • 4 vCPU & 8 GB RAM • Priority 24/7/365 Tech Support',
                'icon'     => 'las la-gem',
                'tag'      => 'VIP Performance',
                'popular'  => false
            ],
            '5' => [
                'alias'    => '301',
                'name'     => 'Entry Dedicated Server',
                'category' => 'Dedicated Infrastructure',
                'specs'    => 'Xeon E3 (4C/8T) • 16 GB DDR3 • 2x 240 GB SSD • 1 Gbps Unmetered',
                'icon'     => 'las la-server',
                'tag'      => 'Dedicated Power',
                'popular'  => false
            ],
            '13' => [
                'alias'    => '13',
                'name'     => 'Mini Plan',
                'category' => 'Budget Web Hosting',
                'specs'    => '5 GB Pure NVMe • 1 vCPU • 1 GB RAM • 1 Site • Free SSL',
                'icon'     => 'las la-wallet',
                'tag'      => 'Starter Pick',
                'popular'  => false
            ],
            '14' => [
                'alias'    => '14',
                'name'     => 'Basic Plan',
                'category' => 'Budget Web Hosting',
                'specs'    => '15 GB Pure NVMe • 1.5 vCPU • 2 GB RAM • 3 Sites • Daily Backups',
                'icon'     => 'las la-layer-group',
                'tag'      => 'Recommended',
                'popular'  => true
            ],
            '15' => [
                'alias'    => '15',
                'name'     => 'Plus Plan',
                'category' => 'Budget Web Hosting',
                'specs'    => '30 GB Pure NVMe • 2.0 vCPU • 3 GB RAM • 10 Sites • Free SSL',
                'icon'     => 'las la-rocket',
                'tag'      => 'Maximum Power',
                'popular'  => false
            ],
            // Business Web Hosting (Live WHMCS PIDs 16, 17, 18, 19)
            '16' => [
                'alias'    => '16',
                'name'     => 'Starter Plan',
                'category' => 'Business Web Hosting',
                'specs'    => '20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Site • LiteSpeed',
                'icon'     => 'las la-bolt',
                'tag'      => 'Starter Business',
                'popular'  => false
            ],
            '17' => [
                'alias'    => '17',
                'name'     => 'Business Plan',
                'category' => 'Business Web Hosting',
                'specs'    => '50 GB Pure NVMe • 2 vCPU • 4 GB RAM • 5 Sites • LiteSpeed',
                'icon'     => 'las la-fire',
                'tag'      => 'Most Popular',
                'popular'  => true
            ],
            '18' => [
                'alias'    => '18',
                'name'     => 'Professional Plan',
                'category' => 'Business Web Hosting',
                'specs'    => '100 GB Pure NVMe • 4 vCPU • 8 GB RAM • 15 Sites • Free Dedicated IP',
                'icon'     => 'las la-crown',
                'tag'      => 'Pro Scale',
                'popular'  => false
            ],
            '19' => [
                'alias'    => '19',
                'name'     => 'Enterprise Plan',
                'category' => 'Business Web Hosting',
                'specs'    => '200 GB Pure NVMe • 6 vCPU • 12 GB RAM • Unlimited Sites • 2x Daily Backup',
                'icon'     => 'las la-gem',
                'tag'      => 'Maximum Scale',
                'popular'  => false
            ],
            // Premium Cloud Hosting (Live WHMCS PIDs 22, 21, 23, 24)
            '22' => [
                'alias'    => '22',
                'name'     => 'Starter Plan',
                'category' => 'Premium Cloud Hosting',
                'specs'    => '4 vCPU Cores • 16 GB ECC RAM • 100 GB Gen4 NVMe • 1 Free Dedicated IP',
                'icon'     => 'las la-server',
                'tag'      => 'Dedicated Managed',
                'popular'  => false
            ],
            '21' => [
                'alias'    => '21',
                'name'     => 'Business Plan',
                'category' => 'Premium Cloud Hosting',
                'specs'    => '8 vCPU Cores • 32 GB ECC RAM • 250 GB Gen4 NVMe • 1 Free Dedicated IP',
                'icon'     => 'las la-gem',
                'tag'      => 'Recommended',
                'popular'  => true
            ],
            '23' => [
                'alias'    => '23',
                'name'     => 'Pro Plan',
                'category' => 'Premium Cloud Hosting',
                'specs'    => '12 vCPU Cores • 64 GB ECC RAM • 500 GB Gen4 NVMe • 2 Free Dedicated IPs',
                'icon'     => 'las la-tachometer-alt',
                'tag'      => 'VIP Architecture',
                'popular'  => false
            ],
            '24' => [
                'alias'    => '24',
                'name'     => 'Enterprise Plan',
                'category' => 'Premium Cloud Hosting',
                'specs'    => '16 vCPU Cores • 128 GB ECC RAM • 1 TB Gen4 NVMe • 4 Free Dedicated IPs',
                'icon'     => 'las la-crown',
                'tag'      => 'Enterprise Cluster',
                'popular'  => false
            ]
        ];

        $results = [];
        $db = self::getDb();

        if ($db) {
            $sql = "SELECT p.id, p.gid, p.name AS whmcs_name, p.type, pr.monthly, pr.quarterly, pr.semiannually, pr.annually, pr.biennially
                    FROM tblproducts p
                    LEFT JOIN tblpricing pr ON pr.relid = p.id AND pr.type = 'product' AND pr.currency = 1
                    WHERE (p.retired = 0 OR p.retired IS NULL)
                    ORDER BY p.gid ASC, p.order ASC, p.id ASC";

            $res = $db->query($sql);
            if ($res && $res->num_rows > 0) {
                while ($r = $res->fetch_assoc()) {
                    $pid = (string)$r['id'];
                    $monthly = (float)$r['monthly'];
                    $annually = (float)$r['annually'];

                    if ($monthly <= 0 && $annually <= 0) continue;

                    $meta = $specsMap[$pid] ?? [
                        'alias'    => $pid,
                        'name'     => $r['whmcs_name'],
                        'category' => 'Cloud Hosting',
                        'specs'    => 'High-performance cloud hosting with 99.99% SLA',
                        'icon'     => 'las la-server',
                        'tag'      => 'Cloud Tier',
                        'popular'  => false
                    ];

                    $item = [
                        'id'         => (int)$pid,
                        'pid'        => $pid,
                        'whmcs_name' => $r['whmcs_name'],
                        'name'       => $meta['name'],
                        'category'   => $meta['category'],
                        'monthly'    => $monthly,
                        'yearly'     => $annually,
                        'monthly_formatted' => '$' . number_format($monthly, 2),
                        'yearly_formatted'  => '$' . number_format($annually, 2),
                        'specs'      => $meta['specs'],
                        'icon'       => $meta['icon'],
                        'tag'        => $meta['tag'],
                        'popular'    => $meta['popular']
                    ];

                    $results[$pid] = $item;

                    // Also map by legacy alias (e.g. '101' => 1, '201' => 2, '202' => 3)
                    if (!empty($meta['alias']) && $meta['alias'] !== $pid) {
                        $results[$meta['alias']] = $item;
                    }
                }
            }
        }

        // Fallback defaults if offline
        if (empty($results)) {
            $defaultData = [
                '1' => ['monthly' => 2.99, 'yearly' => 30.50],
                '2' => ['monthly' => 5.99, 'yearly' => 61.10],
                '3' => ['monthly' => 9.99, 'yearly' => 101.90],
                '4' => ['monthly' => 19.99, 'yearly' => 203.90],
                '5' => ['monthly' => 80.00, 'yearly' => 800.00]
            ];
            foreach ($defaultData as $pid => $prices) {
                $meta = $specsMap[$pid];
                $item = [
                    'id'         => (int)$pid,
                    'pid'        => $pid,
                    'whmcs_name' => $meta['name'],
                    'name'       => $meta['name'],
                    'category'   => $meta['category'],
                    'monthly'    => $prices['monthly'],
                    'yearly'     => $prices['yearly'],
                    'monthly_formatted' => '$' . number_format($prices['monthly'], 2),
                    'yearly_formatted'  => '$' . number_format($prices['yearly'], 2),
                    'specs'      => $meta['specs'],
                    'icon'       => $meta['icon'],
                    'tag'        => $meta['tag'],
                    'popular'    => $meta['popular']
                ];
                $results[$pid] = $item;
                if (!empty($meta['alias']) && $meta['alias'] !== $pid) {
                    $results[$meta['alias']] = $item;
                }
            }
        }

        self::setCache('whmcs_hosting', $results);
        return $results;
    }

    /**
     * Clear Cache (useful when admin updates pricing in WHMCS)
     */
    public static function purgeCache() {
        @unlink(self::$cacheDir . '/whmcs_domains.json');
        @unlink(self::$cacheDir . '/whmcs_hosting.json');
    }
}

// Support direct JSON API requests (e.g. api/whmcs-pricing.php?action=domains)
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'whmcs-pricing.php') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: public, max-age=300');

    $action = $_GET['action'] ?? 'all';
    if ($action === 'purge') {
        WHMCSPricing::purgeCache();
        echo json_encode(['status' => 'success', 'message' => 'WHMCS pricing cache cleared.']);
        exit;
    }

    if ($action === 'domains') {
        echo json_encode(['status' => 'success', 'domains' => WHMCSPricing::getDomainPricing()]);
    } elseif ($action === 'hosting') {
        echo json_encode(['status' => 'success', 'hosting' => WHMCSPricing::getHostingProducts()]);
    } else {
        echo json_encode([
            'status'  => 'success',
            'domains' => WHMCSPricing::getDomainPricing(),
            'hosting' => WHMCSPricing::getHostingProducts()
        ]);
    }
    exit;
}

