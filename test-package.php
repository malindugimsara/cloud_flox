<?php
/**
 * CloudFlox - WHMCS Live Package Inspector (test-package.php)
 * -------------------------------------------------------------------------
 * Dynamically queries WHMCS database (tblproducts, tblproductgroups, tblpricing, tblcurrencies)
 * to inspect, test, and verify active hosting packages, pricing tiers, and direct order links.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';

// Database Connection
$dbHost = '127.0.0.1';
$dbUser = 'cfwhmcs_whmcs';
$dbPass = '+V)Rb%K3wFiK';
$dbName = 'cfwhmcs_whmcs3';

$configFile = __DIR__ . '/cfwhmcs/configuration.php';
if (file_exists($configFile)) {
    @include $configFile;
    if (!empty($db_host)) $dbHost = $db_host;
    if (!empty($db_username)) $dbUser = $db_username;
    if (!empty($db_password)) $dbPass = $db_password;
    if (!empty($db_name)) $dbName = $db_name;
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
    // Fallback to local root without password
    $mysqli = @new mysqli('127.0.0.1', 'root', '', $dbName);
}

$dbConnected = false;
$dbError = null;
if ($mysqli && !$mysqli->connect_error) {
    $mysqli->set_charset('utf8mb4');
    $dbConnected = true;
} else {
    $dbError = $mysqli ? $mysqli->connect_error : 'Failed to instantiate mysqli';
}

// 1. Fetch Currencies
$currencies = [];
$defaultCurrencyId = 1;
if ($dbConnected) {
    $curRes = $mysqli->query("SELECT id, code, prefix, suffix, format, rate, `default` FROM tblcurrencies ORDER BY id ASC");
    if ($curRes) {
        while ($c = $curRes->fetch_assoc()) {
            $currencies[$c['id']] = $c;
            if (!empty($c['default'])) {
                $defaultCurrencyId = (int)$c['id'];
            }
        }
    }
}

// 2. Fetch Product Groups
$groups = [];
if ($dbConnected) {
    $grpRes = $mysqli->query("SELECT id, name, slug, headline, tagline, hidden, `order` FROM tblproductgroups ORDER BY `order` ASC, id ASC");
    if ($grpRes) {
        while ($g = $grpRes->fetch_assoc()) {
            $groups[$g['id']] = $g;
        }
    }
}

// 3. Fetch Products and Pricing
$products = [];
$totalActive = 0;
$totalHidden = 0;
$sharedHostingCount = 0;
$dedicatedCount = 0;

if ($dbConnected) {
    $prodRes = $mysqli->query("SELECT * FROM tblproducts ORDER BY gid ASC, `order` ASC, id ASC");
    if ($prodRes) {
        while ($p = $prodRes->fetch_assoc()) {
            $pid = (int)$p['id'];

            // Fetch pricing for all currencies
            $pricing = [];
            $prRes = $mysqli->query("SELECT * FROM tblpricing WHERE type = 'product' AND relid = $pid");
            if ($prRes) {
                while ($pr = $prRes->fetch_assoc()) {
                    $cid = (int)$pr['currency'];
                    $pricing[$cid] = $pr;
                }
            }
            $p['pricing'] = $pricing;

            $groupName = $groups[$p['gid']]['name'] ?? 'Unassigned';
            $p['group_name'] = $groupName;

            // Counts
            if (empty($p['hidden']) && empty($p['retired'])) {
                $totalActive++;
                if (stripos($groupName, 'shared') !== false || stripos($p['name'], 'hosting') !== false) {
                    $sharedHostingCount++;
                }
                if (stripos($groupName, 'dedicated') !== false || stripos($p['name'], 'server') !== false) {
                    $dedicatedCount++;
                }
            } else {
                $totalHidden++;
            }

            // Parse specifications from description text
            $specs = [];
            if (!empty($p['description'])) {
                $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $p['description']));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    if (strpos($line, ':') !== false) {
                        list($k, $v) = explode(':', $line, 2);
                        $specs[] = ['label' => trim($k), 'value' => trim($v)];
                    } else {
                        $specs[] = ['label' => '', 'value' => $line];
                    }
                }
            }
            $p['parsed_specs'] = $specs;

            $products[$pid] = $p;
        }
    }
}

// If JSON output requested (?format=json)
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => $dbConnected ? 'success' : 'error',
        'database' => [
            'connected' => $dbConnected,
            'name' => $dbName,
            'error' => $dbError
        ],
        'summary' => [
            'total_products' => count($products),
            'active_products' => $totalActive,
            'hidden_products' => $totalHidden,
            'groups_count' => count($groups),
            'currencies_count' => count($currencies)
        ],
        'currencies' => $currencies,
        'groups' => $groups,
        'products' => array_values($products)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>WHMCS Live Package Inspector — CloudFlox Hosting Plans</title>
    <meta name="description" content="Live inspector and testing console for WHMCS hosting packages, pricing tiers, and checkout integration.">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSS Area -->
    <?php include 'include/css.inc.php'; ?>

    <style>
        .cf-test-hero {
            background: linear-gradient(135deg, #0B192C 0%, #1E3E62 50%, #000000 100%);
            padding: 50px 0 40px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .cf-test-hero::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15), transparent 45%),
                        radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15), transparent 45%);
            pointer-events: none;
        }
        .cf-test-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.35);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .cf-stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            transition: all 0.25s ease;
        }
        .cf-stat-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
        }
        .cf-stat-num {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
            font-family: 'Space Grotesk', sans-serif;
        }
        .cf-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94A3B8;
            margin-top: 4px;
        }

        /* Filter Controls */
        .cf-filter-bar {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px 22px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }
        .cf-pill-btn {
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            color: #475569;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 18px;
            border-radius: 999px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .cf-pill-btn:hover {
            background: #EDF2F7;
            color: #0F172A;
            border-color: #94A3B8;
        }
        .cf-pill-btn.active {
            background: #0284C7;
            color: #ffffff;
            border-color: #0284C7;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        /* Package Cards */
        .cf-pkg-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }
        .cf-pkg-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08);
            border-color: #CBD5E1;
        }
        .cf-pkg-header {
            padding: 24px;
            border-bottom: 1px solid #F1F5F9;
            background: #FAFBFD;
            position: relative;
        }
        .cf-pid-badge {
            background: #0F172A;
            color: #38BDF8;
            font-family: 'Space Grotesk', monospace;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }
        .cf-price-block {
            padding: 20px 24px;
            background: #ffffff;
            border-bottom: 1px solid #F1F5F9;
        }
        .cf-price-main {
            font-size: 34px;
            font-weight: 800;
            color: #0F172A;
            font-family: 'Space Grotesk', sans-serif;
            line-height: 1;
        }
        .cf-price-period {
            color: #64748B;
            font-size: 14px;
            font-weight: 500;
        }
        .cf-specs-list {
            padding: 20px 24px;
            flex-grow: 1;
            background: #ffffff;
            list-style: none;
            margin: 0;
        }
        .cf-specs-list li {
            padding: 7px 0;
            font-size: 13.5px;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px dashed #F1F5F9;
        }
        .cf-specs-list li:last-child {
            border-bottom: none;
        }
        .cf-specs-list li i {
            color: #10B981;
            font-size: 16px;
            flex-shrink: 0;
        }
        .cf-pkg-footer {
            padding: 18px 24px 24px;
            background: #FAFBFD;
            border-top: 1px solid #F1F5F9;
        }
        .cf-btn-whmcs-order {
            background: #0284C7;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 16px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .cf-btn-whmcs-order:hover {
            background: #0369A1;
            color: #fff;
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }
        .cf-btn-local-test {
            background: #F1F5F9;
            color: #334155;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 14px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            margin-top: 8px;
            transition: all 0.2s ease;
        }
        .cf-btn-local-test:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
        .cf-pricing-grid-cell {
            padding: 6px 10px;
            background: #F8FAFC;
            border-radius: 8px;
            font-size: 12px;
            text-align: center;
            border: 1px solid #E2E8F0;
        }
        .cf-hidden-card {
            opacity: 0.75;
            border-style: dashed;
        }

        /* Unified Spacious Responsive Container for Package Inspector */
        .cf-inspector-wrapper {
            width: 100%;
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding-left: clamp(20px, 3.5vw, 48px);
            padding-right: clamp(20px, 3.5vw, 48px);
        }
        @media (min-width: 1600px) {
            .cf-inspector-wrapper {
                max-width: 1560px;
                padding-left: 56px;
                padding-right: 56px;
            }
        }
        @media (max-width: 991.98px) {
            .cf-inspector-wrapper {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
        @media (max-width: 575.98px) {
            .cf-inspector-wrapper {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head>

<body class="pure-white-theme">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SECTION & DATABASE TELEMETRY
         ========================================================================= -->
    <section class="cf-test-hero">
        <div class="cf-inspector-wrapper position-relative z-2">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $url; ?>" class="text-white-50 text-decoration-none">
                            <i class="las la-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-white-50">Diagnostics &amp; Testing</li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">WHMCS Live Packages</li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="cf-test-badge mb-3">
                        <i class="las la-database"></i> Live WHMCS Product Bridge
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-2" style="font-family: 'Space Grotesk', sans-serif;">
                        WHMCS Hosting Package Inspector
                    </h1>
                    <p class="lead text-white-50 mb-0" style="font-size: 16px;">
                        Direct real-time query from WHMCS database (<code>cfwhmcs_whmcs3.tblproducts</code> &amp; <code>tblpricing</code>).
                        Verify active plans, multi-currency rates, and test checkout gateways.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="cf-stat-card">
                                <div class="cf-stat-num text-emerald"><?php echo $totalActive; ?></div>
                                <div class="cf-stat-label">Active Plans</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-stat-card">
                                <div class="cf-stat-num text-info"><?php echo count($groups); ?></div>
                                <div class="cf-stat-label">Product Groups</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-stat-card">
                                <div class="cf-stat-num text-warning"><?php echo count($currencies); ?></div>
                                <div class="cf-stat-label">Currencies (USD/LKR)</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-stat-card">
                                <div class="cf-stat-num <?php echo $dbConnected ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $dbConnected ? 'Connected' : 'Offline'; ?>
                                </div>
                                <div class="cf-stat-label">DB: <?php echo htmlspecialchars($dbName); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. INTERACTIVE CONTROLS (Currency Switcher, Cycle, Group Filter)
         ========================================================================= -->
    <section class="cf-inspector-wrapper pb-3">
        <div class="cf-filter-bar">
            <div class="row g-3 align-items-center justify-content-between">
                <!-- Group Filter Pills -->
                <div class="col-12 col-xl-7">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted fw-bold me-1" style="font-size: 13px;">Filter Group:</span>
                        <button type="button" class="cf-pill-btn active" onclick="filterGroup('all', this)">
                            All (<?php echo count($products); ?>)
                        </button>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('budget', this)">
                            <i class="las la-wallet"></i> Budget (3)
                        </button>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('business', this)">
                            <i class="las la-briefcase"></i> Business (4)
                        </button>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('premium', this)">
                            <i class="las la-cloud"></i> Premium Cloud (4)
                        </button>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('shared', this)">
                            <i class="las la-server"></i> Shared Hosting (<?php echo $sharedHostingCount; ?>)
                        </button>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('dedicated', this)">
                            <i class="las la-hdd"></i> Dedicated Servers (<?php echo $dedicatedCount; ?>)
                        </button>
                        <?php if ($totalHidden > 0): ?>
                        <button type="button" class="cf-pill-btn" onclick="filterGroup('hidden', this)">
                            <i class="las la-eye-slash"></i> Hidden / Sync (<?php echo $totalHidden; ?>)
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Currency & Cycle Controls -->
                <div class="col-12 col-xl-5">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-xl-end">
                        <!-- Currency Switcher -->
                        <div class="d-flex align-items-center gap-1 bg-light p-1 rounded-pill border">
                            <button type="button" id="curBtn1" class="btn btn-sm rounded-pill px-3 fw-bold btn-primary" onclick="setCurrency(1, 'USD', '$')">USD ($)</button>
                            <button type="button" id="curBtn2" class="btn btn-sm rounded-pill px-3 fw-bold btn-light" onclick="setCurrency(2, 'LKR', 'LKR ')">LKR (Rs)</button>
                        </div>

                        <!-- Billing Cycle Selector -->
                        <select id="cycleSelector" class="form-select form-select-sm w-auto rounded-pill fw-semibold" onchange="setCycle(this.value)">
                            <option value="monthly" selected>Monthly Billing</option>
                            <option value="annually">Annual Billing</option>
                            <option value="biennially">Biennial (2 Years)</option>
                            <option value="triennially">Triennial (3 Years)</option>
                        </select>

                        <!-- Export JSON Link -->
                        <a href="test-package.php?format=json" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="View Raw JSON API">
                            <i class="las la-code"></i> JSON
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. PRODUCT CARDS GRID
         ========================================================================= -->
    <section class="cf-inspector-wrapper py-4">
        <?php if (!$dbConnected): ?>
            <div class="alert alert-danger rounded-4 p-4 shadow-sm" role="alert">
                <h4 class="alert-heading fw-bold"><i class="las la-exclamation-triangle me-2"></i> WHMCS Database Connection Failed</h4>
                <p class="mb-1">Could not connect to MySQL database <code><?php echo htmlspecialchars($dbName); ?></code> on <code><?php echo htmlspecialchars($dbHost); ?></code>.</p>
                <p class="text-muted small mb-0">Error details: <?php echo htmlspecialchars($dbError); ?></p>
            </div>
        <?php else: ?>

            <div class="row g-4" id="packagesGrid">
                <?php foreach ($products as $pid => $p):
                    $isBudget = (stripos($p['group_name'], 'budget') !== false || stripos($p['name'], 'budget') !== false);
                    $isBusiness = (stripos($p['group_name'], 'business') !== false);
                    $isPremium = (stripos($p['group_name'], 'premium') !== false || stripos($p['name'], 'premium') !== false || stripos($p['group_name'], 'cloud') !== false);
                    $isShared = (stripos($p['group_name'], 'shared') !== false || stripos($p['name'], 'hosting') !== false);
                    $isDedicated = (stripos($p['group_name'], 'dedicated') !== false || stripos($p['name'], 'server') !== false);
                    $isHidden = (!empty($p['hidden']) || !empty($p['retired']));
                    
                    $groupSlug = 'other';
                    if ($isHidden) {
                        $groupSlug = 'hidden';
                    } elseif ($isBudget) {
                        $groupSlug = 'budget';
                    } elseif ($isBusiness) {
                        $groupSlug = 'business';
                    } elseif ($isPremium) {
                        $groupSlug = 'premium';
                    } elseif ($isShared) {
                        $groupSlug = 'shared';
                    } elseif ($isDedicated) {
                        $groupSlug = 'dedicated';
                    }

                    // Prepare pricing JSON payload for client-side currency/cycle reactivity
                    $pricingData = [];
                    foreach ($p['pricing'] as $cid => $pr) {
                        $pricingData[$cid] = [
                            'monthly' => (float)$pr['monthly'],
                            'quarterly' => (float)$pr['quarterly'],
                            'semiannually' => (float)$pr['semiannually'],
                            'annually' => (float)$pr['annually'],
                            'biennially' => (float)$pr['biennially'],
                            'triennially' => (float)$pr['triennially']
                        ];
                    }
                    $jsonPricing = htmlspecialchars(json_encode($pricingData), ENT_QUOTES, 'UTF-8');

                    // Default values (USD monthly)
                    $usdPricing = $pricingData[1] ?? ($pricingData[$defaultCurrencyId] ?? []);
                    $initMonthly = isset($usdPricing['monthly']) && $usdPricing['monthly'] > 0 ? number_format($usdPricing['monthly'], 2) : 'Free';
                ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 pkg-item" data-group="<?php echo $groupSlug; ?>" data-pricing='<?php echo $jsonPricing; ?>' data-pid="<?php echo $pid; ?>">
                    <div class="cf-pkg-card <?php echo $isHidden ? 'cf-hidden-card' : ''; ?>">
                        <!-- Card Header -->
                        <div class="cf-pkg-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="cf-pid-badge">PID #<?php echo $pid; ?></span>
                                <?php if ($isHidden): ?>
                                    <span class="badge bg-secondary">Hidden / Sync</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">Active Plan</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="h5 fw-bold text-dark mb-1" style="font-family: 'Space Grotesk', sans-serif; line-height: 1.25;">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </h3>
                            <div class="text-muted small">
                                <i class="las la-folder me-1"></i><?php echo htmlspecialchars($p['group_name']); ?>
                            </div>
                        </div>

                        <!-- Price Section -->
                        <div class="cf-price-block">
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="price-val cf-price-main">$<?php echo $initMonthly; ?></span>
                                <span class="price-period cf-price-period">/ month</span>
                            </div>
                            <!-- Mini breakdown across cycles -->
                            <div class="d-flex gap-1 mt-2">
                                <div class="cf-pricing-grid-cell flex-fill">
                                    <div class="text-muted" style="font-size: 10px;">MONTHLY</div>
                                    <div class="fw-bold price-cell-monthly">$<?php echo $initMonthly; ?></div>
                                </div>
                                <div class="cf-pricing-grid-cell flex-fill">
                                    <div class="text-muted" style="font-size: 10px;">ANNUAL</div>
                                    <div class="fw-bold price-cell-annually">$<?php echo isset($usdPricing['annually']) && $usdPricing['annually'] > 0 ? number_format($usdPricing['annually'], 2) : '-'; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Specs List -->
                        <ul class="cf-specs-list">
                            <?php if (!empty($p['parsed_specs'])): ?>
                                <?php foreach (array_slice($p['parsed_specs'], 0, 7) as $sp): ?>
                                    <li>
                                        <i class="las la-check-circle"></i>
                                        <span>
                                            <?php if (!empty($sp['label'])): ?>
                                                <strong class="text-dark"><?php echo htmlspecialchars($sp['label']); ?>:</strong>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($sp['value']); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (count($p['parsed_specs']) > 7): ?>
                                    <li class="text-muted small fst-italic">
                                        + <?php echo (count($p['parsed_specs']) - 7); ?> more specifications in WHMCS
                                    </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li class="text-muted small">
                                    <i class="las la-info-circle text-muted"></i>
                                    <span><?php echo !empty($p['description']) ? htmlspecialchars($p['description']) : 'Standard infrastructure package provisioning.'; ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <!-- Card Footer / Actions -->
                        <div class="cf-pkg-footer">
                            <a href="https://my.cloudflox.com/cart.php?a=add&pid=<?php echo $pid; ?>" target="_blank" class="cf-btn-whmcs-order">
                                <i class="las la-shopping-cart"></i> Order on WHMCS Cart
                            </a>
                            <a href="checkout?pid=<?php echo $pid; ?>&cycle=monthly" class="cf-btn-local-test">
                                <i class="las la-external-link-alt"></i> Test Local Checkout
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- =========================================================================
                 4. FULL WHMCS DATABASE TABLE VIEW (Raw Records)
                 ========================================================================= -->
            <div class="card border-0 rounded-4 shadow-sm mt-5 overflow-hidden">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="las la-table"></i> Complete WHMCS Product Records (<code>tblproducts</code>)
                    </h5>
                    <span class="badge bg-secondary"><?php echo count($products); ?> Total Rows</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" style="font-size: 13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">PID</th>
                                <th>Product Name</th>
                                <th>Group (GID)</th>
                                <th>Type</th>
                                <th>Pay Type</th>
                                <th>Monthly (USD)</th>
                                <th>Annual (USD)</th>
                                <th>Monthly (LKR)</th>
                                <th>Annual (LKR)</th>
                                <th>Status</th>
                                <th class="text-end">Direct Links</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $pid => $p):
                                $uPr = $p['pricing'][1] ?? [];
                                $lPr = $p['pricing'][2] ?? [];
                                $isH = (!empty($p['hidden']) || !empty($p['retired']));
                            ?>
                            <tr class="<?php echo $isH ? 'text-muted' : ''; ?>">
                                <td>
                                    <span class="badge bg-dark">#<?php echo $pid; ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <?php if (!empty($p['slug'])): ?>
                                        <div class="text-muted small"><code>/<?php echo htmlspecialchars($p['slug']); ?></code></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['group_name']); ?> (<?php echo $p['gid']; ?>)</span>
                                </td>
                                <td><code><?php echo htmlspecialchars($p['type']); ?></code></td>
                                <td><span class="badge bg-info-subtle text-info border border-info-subtle"><?php echo htmlspecialchars($p['paytype']); ?></span></td>
                                <td class="fw-bold text-emerald">
                                    <?php echo isset($uPr['monthly']) && $uPr['monthly'] > 0 ? '$' . number_format($uPr['monthly'], 2) : '-'; ?>
                                </td>
                                <td>
                                    <?php echo isset($uPr['annually']) && $uPr['annually'] > 0 ? '$' . number_format($uPr['annually'], 2) : '-'; ?>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?php echo isset($lPr['monthly']) && $lPr['monthly'] > 0 ? 'Rs ' . number_format($lPr['monthly'], 2) : '-'; ?>
                                </td>
                                <td>
                                    <?php echo isset($lPr['annually']) && $lPr['annually'] > 0 ? 'Rs ' . number_format($lPr['annually'], 2) : '-'; ?>
                                </td>
                                <td>
                                    <?php if ($isH): ?>
                                        <span class="badge bg-secondary">Hidden</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="https://my.cloudflox.com/cart.php?a=add&pid=<?php echo $pid; ?>" target="_blank" class="btn btn-outline-primary" title="WHMCS Cart Order Link">
                                            <i class="las la-shopping-cart"></i> WHMCS
                                        </a>
                                        <a href="checkout?pid=<?php echo $pid; ?>&cycle=monthly" class="btn btn-outline-secondary" title="Test Local Website Checkout">
                                            <i class="las la-external-link-alt"></i> Checkout
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </section>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <script>
        // Active State
        let currentCurrencyId = 1;
        let currentCurrencyCode = 'USD';
        let currentCurrencyPrefix = '$';
        let currentCycle = 'monthly';

        function setCurrency(id, code, prefix) {
            currentCurrencyId = id;
            currentCurrencyCode = code;
            currentCurrencyPrefix = prefix;

            document.getElementById('curBtn1').className = (id === 1) ? 'btn btn-sm rounded-pill px-3 fw-bold btn-primary' : 'btn btn-sm rounded-pill px-3 fw-bold btn-light';
            document.getElementById('curBtn2').className = (id === 2) ? 'btn btn-sm rounded-pill px-3 fw-bold btn-primary' : 'btn btn-sm rounded-pill px-3 fw-bold btn-light';

            updateAllCards();
        }

        function setCycle(cycle) {
            currentCycle = cycle;
            updateAllCards();
        }

        function filterGroup(group, btnEl) {
            // Update active pill button
            document.querySelectorAll('.cf-pill-btn').forEach(b => b.classList.remove('active'));
            if (btnEl) btnEl.classList.add('active');

            const items = document.querySelectorAll('.pkg-item');
            items.forEach(el => {
                const g = el.getAttribute('data-group');
                if (group === 'all') {
                    el.style.display = '';
                } else if (group === 'hidden') {
                    el.style.display = (g === 'hidden') ? '' : 'none';
                } else if (group === 'budget') {
                    el.style.display = (g === 'budget') ? '' : 'none';
                } else if (group === 'shared') {
                    el.style.display = (g === 'shared') ? '' : 'none';
                } else if (group === 'dedicated') {
                    el.style.display = (g === 'dedicated') ? '' : 'none';
                }
            });
        }

        function updateAllCards() {
            const items = document.querySelectorAll('.pkg-item');
            items.forEach(el => {
                try {
                    const rawData = el.getAttribute('data-pricing');
                    if (!rawData) return;
                    const pricing = JSON.parse(rawData);

                    const curPricing = pricing[currentCurrencyId] || pricing[1] || {};
                    const rate = curPricing[currentCycle] || 0;

                    const priceMainEl = el.querySelector('.price-val');
                    const pricePeriodEl = el.querySelector('.price-period');

                    if (priceMainEl) {
                        if (rate > 0) {
                            priceMainEl.textContent = currentCurrencyPrefix + Number(rate).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        } else {
                            priceMainEl.textContent = 'Free';
                        }
                    }

                    if (pricePeriodEl) {
                        let periodText = '/ month';
                        if (currentCycle === 'annually') periodText = '/ year';
                        else if (currentCycle === 'quarterly') periodText = '/ quarter';
                        else if (currentCycle === 'semiannually') periodText = '/ 6 months';
                        else if (currentCycle === 'biennially') periodText = '/ 2 years';
                        else if (currentCycle === 'triennially') periodText = '/ 3 years';
                        pricePeriodEl.textContent = periodText;
                    }

                    // Update breakdown cells
                    const monthlyEl = el.querySelector('.price-cell-monthly');
                    if (monthlyEl) {
                        const mVal = curPricing['monthly'] || 0;
                        monthlyEl.textContent = mVal > 0 ? (currentCurrencyPrefix + Number(mVal).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })) : '-';
                    }

                    const annualEl = el.querySelector('.price-cell-annually');
                    if (annualEl) {
                        const aVal = curPricing['annually'] || 0;
                        annualEl.textContent = aVal > 0 ? (currentCurrencyPrefix + Number(aVal).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })) : '-';
                    }

                    // Update local checkout link billing cycle
                    const localLink = el.querySelector('.cf-btn-local-test');
                    if (localLink) {
                        const pid = el.getAttribute('data-pid');
                        localLink.href = `checkout.php?action=add&pid=${pid}&billingcycle=${currentCycle}`;
                    }

                } catch (e) {
                    console.error('Pricing card update error:', e);
                }
            });
        }
    </script>
</body>

</html>

