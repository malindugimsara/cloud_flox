<?php
/**
 * CloudFlox Instant Domain Availability Checker API
 * High-speed parallel verification via authoritative ICANN RDAP & Anycast DoH.
 * Eliminates external page redirection, returning instant inline availability and alternatives in < 1 second.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Disable error display in JSON response
error_reporting(0);
ini_set('display_errors', '0');

// Dynamic Live WHMCS Pricing Integration
require_once __DIR__ . '/api/whmcs-pricing.php';
$tldPricing = WHMCSPricing::getDomainPricing();
$hostingPlans = WHMCSPricing::getHostingProducts();

// Read input query
$rawQuery = trim($_GET['domain'] ?? $_GET['query'] ?? $_POST['domain'] ?? $_POST['query'] ?? $_REQUEST['domain'] ?? $_REQUEST['query'] ?? '');
if (empty($rawQuery)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please enter a domain name to search.'
    ]);
    exit;
}

// Normalize & clean domain string
$cleaned = preg_replace('#^https?://#i', '', $rawQuery);
$cleaned = preg_replace('#^www\.#i', '', $cleaned);
$cleaned = preg_replace('#/.*$#', '', $cleaned);
$cleaned = strtolower(trim($cleaned));
$cleaned = preg_replace('/[^a-z0-9\.\-]/', '', $cleaned);

if (empty($cleaned)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please enter a valid domain name with alphanumeric characters.'
    ]);
    exit;
}

// Split into SLD and TLD
if (strpos($cleaned, '.') !== false) {
    $parts = explode('.', $cleaned, 2);
    $sld = $parts[0];
    $tld = $parts[1];
} else {
    $sld = $cleaned;
    $tld = 'com';
}

// Explicitly disallow .lk domains from being searched
if ($tld === 'lk') {
    echo json_encode([
        'status'  => 'error',
        'message' => '.lk domain registrations are not supported here. Please choose another extension (.com, .net, .cloud, etc.).'
    ]);
    exit;
}

$fullDomain = $sld . '.' . $tld;

// Map domain to authoritative query endpoint
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

// Build list of domains to query concurrently (primary + top alternatives)
$domainsToQuery = [$fullDomain];
$suggestTlds = ['com', 'net', 'cloud', 'org', 'store', 'io'];

foreach ($suggestTlds as $altTld) {
    if ($altTld === $tld) continue;
    $domainsToQuery[] = $sld . '.' . $altTld;
}

// Execute parallel HTTP requests via curl_multi
$mh = curl_multi_init();
$handles = [];

foreach ($domainsToQuery as $d) {
    $cfg = getDomainLookupConfig($d);
    $ch = curl_init($cfg['url']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
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
    $cfg = $item['cfg'];
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content = curl_multi_getcontent($ch);
    
    $isAvailable = false;
    if ($cfg['type'] === 'doh') {
        $json = json_decode($content, true);
        $hasAnswer = !empty($json['Answer']);
        $isAvailable = !$hasAnswer;
    } else {
        // RDAP: 404 is available, 200 is taken
        $isAvailable = ($httpCode === 404);
    }

    $availabilityMap[$d] = $isAvailable;
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

// Primary result
$isPrimaryAvailable = $availabilityMap[$fullDomain] ?? false;
$primaryPrice = $tldPricing[$tld]['price'] ?? '$14.93';
$primaryBadge = $tldPricing[$tld]['badge'] ?? '';
$primaryTag   = $tldPricing[$tld]['category'] ?? ($tldPricing[$tld]['tag'] ?? '');

// Build suggestions
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
        'badge'        => $tldPricing[$altTld]['badge'] ?? '',
        'tag'          => $tldPricing[$altTld]['category'] ?? ($tldPricing[$altTld]['tag'] ?? ''),
        'register_url' => 'https://my.cloudflox.com/cart.php?a=add&domain=register&query=' . urlencode($altDomain)
    ];
}

echo json_encode([
    'status'       => 'success',
    'query'        => $fullDomain,
    'sld'          => $sld,
    'tld'          => '.' . $tld,
    'primary'      => [
        'domain'       => $fullDomain,
        'is_available' => $isPrimaryAvailable,
        'price'        => $primaryPrice,
        'badge'        => $primaryBadge,
        'tag'          => $primaryTag,
        'register_url' => 'https://my.cloudflox.com/cart.php?a=add&domain=register&query=' . urlencode($fullDomain),
        'transfer_url' => 'https://my.cloudflox.com/cart.php?a=add&domain=transfer&query=' . urlencode($fullDomain)
    ],
    'suggestions'  => $suggestions,
    'hosting_plans' => [
        '1' => $hostingPlans['1'] ?? null,
        '2' => $hostingPlans['2'] ?? null,
        '3' => $hostingPlans['3'] ?? null
    ]
]);
