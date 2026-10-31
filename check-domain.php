<?php
/**
 * CloudFlox Instant Domain Availability Checker API (Single & Bulk)
 * High-speed parallel verification via authoritative ICANN RDAP & Anycast DoH.
 * Supports:
 * 1. Single domain search with instant suggestions
 * 2. Multi-domain bulk check (comma or newline separated list)
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

error_reporting(0);
ini_set('display_errors', '0');

require_once __DIR__ . '/include/url.inc.php';
require_once __DIR__ . '/api/whmcs-pricing.php';
$tldPricing = WHMCSPricing::getDomainPricing();
$hostingPlans = WHMCSPricing::getHostingProducts();

function getDomainLookupConfig($domain) {
    $parts = explode('.', $domain);
    $ext = strtolower(end($parts));
    if ($ext === 'com') {
        return ['type' => 'rdap', 'url' => "https://rdap.verisign.com/com/v1/domain/" . urlencode($domain)];
    } elseif ($ext === 'net') {
        return ['type' => 'rdap', 'url' => "https://rdap.verisign.com/net/v1/domain/" . urlencode($domain)];
    } elseif ($ext === 'org') {
        return ['type' => 'rdap', 'url' => "https://rdap.publicinterestregistry.org/rdap/domain/" . urlencode($domain)];
    } else {
        return ['type' => 'rdap', 'url' => "https://rdap.org/domain/" . urlencode($domain)];
    }
}

$rawQuery = trim($_GET['domain'] ?? $_GET['query'] ?? $_POST['domain'] ?? $_POST['query'] ?? $_REQUEST['domain'] ?? $_REQUEST['query'] ?? '');
$isBulkParam = !empty($_REQUEST['bulk']) || strpos($rawQuery, ',') !== false || strpos($rawQuery, "\n") !== false;

if (empty($rawQuery)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter domain name(s) to search.']);
    exit;
}

// =============================================================================
// MODE A: BULK DOMAIN SEARCH
// =============================================================================
if ($isBulkParam) {
    $rawList = preg_split('/[\r\n,]+/', $rawQuery);
    $bulkDomains = [];

    foreach ($rawList as $item) {
        $c = preg_replace('#^https?://#i', '', trim($item));
        $c = preg_replace('#^www\.#i', '', $c);
        $c = preg_replace('#/.*$#', '', $c);
        $c = strtolower(trim($c));
        $c = preg_replace('/[^a-z0-9\.\-]/', '', $c);

        if (empty($c)) continue;
        if (strpos($c, '.') === false) {
            $c .= '.com'; // default to .com
        }
        if (substr($c, -3) === '.lk') continue; // exclude .lk

        if (!in_array($c, $bulkDomains, true) && count($bulkDomains) < 25) {
            $bulkDomains[] = $c;
        }
    }

    if (empty($bulkDomains)) {
        echo json_encode(['status' => 'error', 'message' => 'No valid domain names found to search.']);
        exit;
    }

    // Parallel RDAP query via curl_multi
    $mh = curl_multi_init();
    $handles = [];
    foreach ($bulkDomains as $d) {
        $cfg = getDomainLookupConfig($d);
        $ch = curl_init($cfg['url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 4,
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'CloudFlox/1.0'
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$d] = ['ch' => $ch, 'cfg' => $cfg];
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh, 0.2);
    } while ($running > 0);

    $bulkResults = [];
    foreach ($handles as $d => $item) {
        $ch = $item['ch'];
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // RDAP: 404 is available
        $isAvailable = ($httpCode === 404);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        $parts = explode('.', $d, 2);
        $bSld = $parts[0];
        $bTld = $parts[1] ?? 'com';

        $bulkResults[] = [
            'domain'       => $d,
            'sld'          => $bSld,
            'tld'          => '.' . $bTld,
            'is_available' => $isAvailable,
            'price'        => $tldPricing[$bTld]['price'] ?? '$14.93',
            'raw_price'    => (float)($tldPricing[$bTld]['register'] ?? 14.93),
            'badge'        => $tldPricing[$bTld]['badge'] ?? '',
            'tag'          => $tldPricing[$bTld]['category'] ?? ($tldPricing[$bTld]['tag'] ?? '')
        ];
    }
    curl_multi_close($mh);

    echo json_encode([
        'status'  => 'success',
        'is_bulk' => true,
        'count'   => count($bulkResults),
        'results' => $bulkResults
    ]);
    exit;
}

// =============================================================================
// MODE B: SINGLE DOMAIN SEARCH + ALTERNATIVES
// =============================================================================
$cleaned = preg_replace('#^https?://#i', '', $rawQuery);
$cleaned = preg_replace('#^www\.#i', '', $cleaned);
$cleaned = preg_replace('#/.*$#', '', $cleaned);
$cleaned = strtolower(trim($cleaned));
$cleaned = preg_replace('/[^a-z0-9\.\-]/', '', $cleaned);

if (empty($cleaned)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid domain name with alphanumeric characters.']);
    exit;
}

if (strpos($cleaned, '.') !== false) {
    $parts = explode('.', $cleaned, 2);
    $sld = $parts[0];
    $tld = $parts[1];
} else {
    $sld = $cleaned;
    $tld = 'com';
}

if ($tld === 'lk') {
    echo json_encode(['status' => 'error', 'message' => '.lk domain registrations are not supported here. Please choose another extension (.com, .net, .cloud, etc.).']);
    exit;
}

$fullDomain = $sld . '.' . $tld;

$domainsToQuery = [$fullDomain];
$suggestTlds = ['com', 'net', 'cloud', 'org', 'store', 'io', 'online', 'biz', 'info'];

foreach ($suggestTlds as $altTld) {
    if ($altTld === $tld) continue;
    $domainsToQuery[] = $sld . '.' . $altTld;
}

$mh = curl_multi_init();
$handles = [];

foreach ($domainsToQuery as $d) {
    $cfg = getDomainLookupConfig($d);
    $ch = curl_init($cfg['url']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 4,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'CloudFlox/1.0'
    ]);
    curl_multi_add_handle($mh, $ch);
    $handles[$d] = ['ch' => $ch, 'cfg' => $cfg];
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh, 0.2);
} while ($running > 0);

$availabilityMap = [];
foreach ($handles as $d => $item) {
    $ch = $item['ch'];
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $availabilityMap[$d] = ($httpCode === 404);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

$isPrimaryAvailable = $availabilityMap[$fullDomain] ?? false;
$primaryPrice = $tldPricing[$tld]['price'] ?? '$14.93';
$primaryRawPrice = (float)($tldPricing[$tld]['register'] ?? 14.93);
$primaryBadge = $tldPricing[$tld]['badge'] ?? '';
$primaryTag   = $tldPricing[$tld]['category'] ?? ($tldPricing[$tld]['tag'] ?? '');

$suggestions = [];
foreach ($suggestTlds as $altTld) {
    if ($altTld === $tld) continue;
    $altDomain = $sld . '.' . $altTld;
    $altAvailable = $availabilityMap[$altDomain] ?? false;

    $suggestions[] = [
        'domain'       => $altDomain,
        'sld'          => $sld,
        'tld'          => '.' . $altTld,
        'is_available' => $altAvailable,
        'price'        => $tldPricing[$altTld]['price'] ?? '$14.99',
        'raw_price'    => (float)($tldPricing[$altTld]['register'] ?? 14.99),
        'badge'        => $tldPricing[$altTld]['badge'] ?? '',
        'tag'          => $tldPricing[$altTld]['category'] ?? ($tldPricing[$altTld]['tag'] ?? ''),
        'register_url' => $url . 'checkout?domain=' . urlencode($altDomain) . '&domainType=register',
        'transfer_url' => $url . 'checkout?domain=' . urlencode($altDomain) . '&domainType=transfer'
    ];
}

echo json_encode([
    'status'       => 'success',
    'is_bulk'      => false,
    'query'        => $fullDomain,
    'sld'          => $sld,
    'tld'          => '.' . $tld,
    'primary'      => [
        'domain'       => $fullDomain,
        'is_available' => $isPrimaryAvailable,
        'price'        => $primaryPrice,
        'raw_price'    => $primaryRawPrice,
        'badge'        => $primaryBadge,
        'tag'          => $primaryTag,
        'register_url' => $url . 'checkout?domain=' . urlencode($fullDomain) . '&domainType=register',
        'transfer_url' => $url . 'checkout?domain=' . urlencode($fullDomain) . '&domainType=transfer'
    ],
    'suggestions'  => $suggestions
]);
exit;
