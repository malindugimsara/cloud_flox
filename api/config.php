<?php
/**
 * CloudFlox On-Site Checkout & WHMCS API Configuration
 * -------------------------------------------------------------------------
 * This configuration bridge connects cloudflox.com to your WHMCS instance
 * (my.cloudflox.com) to enable on-site server and domain checkout without
 * external page redirection.
 */

// WHMCS API Endpoint
define('WHMCS_API_URL', 'https://my.cloudflox.com/includes/api.php');

define('WHMCS_API_IDENTIFIER', 'aYxzdsEu6e4hfsBS0y3SD7QQvSICdyD3'); 
define('WHMCS_API_SECRET', 'L6ZMtif9sRQc4Owd4Rzm65xrvOI8jPIA');     
define('WHMCS_API_ACCESS_KEY', ''); 

// Auto-detect Local Development environment
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocalDev = (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '::1') !== false ||
    preg_match('/^192\.168\./', $host) ||
    preg_match('/^10\./', $host)
);

// Enable local simulation on localhost so WHMCS API IP restrictions don't block development
define('CF_FORCE_LOCAL_DEMO', true);
define('CF_CHECKOUT_DEMO_MODE', empty(WHMCS_API_IDENTIFIER) || empty(WHMCS_API_SECRET) || (CF_FORCE_LOCAL_DEMO && $isLocalDev));

// Active Payment Gateways Configuration (Synced with cfwhmcs)
define('CF_PAYHERE_MERCHANT_ID', '230744');
define('CF_PAYHERE_MERCHANT_SECRET', 'Mjc4NjU2NTM4MDM5OTA5NTg3NDAyNzAyMzk2Mzc1MTM1ODYwMTUy');
define('CF_PAYHERE_CURRENCY', 'LKR');
define('CF_PAYHERE_EXCHANGE_RATE', 335.00); // 1 USD = 335 LKR
define('CF_PAYHERE_SANDBOX', false);
define('CF_PAYPAL_EMAIL', 'info@cloudflox.com');


// CloudFlox Products Catalog & Specifications Matrix
$CF_PRODUCTS = [
    // 1. Budget Hosting Series
    '101' => [
        'name'        => 'Mini Plan',
        'category'    => 'Budget Cloud Hosting',
        'monthly'     => 3.00,
        'yearly'      => 28.80, // $2.40/mo * 12
        'specs'       => '5 GB Pure NVMe • 1 Website • 250 GB Bandwidth',
        'icon'        => 'las la-leaf',
        'tag'         => 'Starter Pick',
        'popular'     => false
    ],
    '102' => [
        'name'        => 'Basic Plan',
        'category'    => 'Budget Cloud Hosting',
        'monthly'     => 4.50,
        'yearly'      => 43.20, // $3.60/mo * 12
        'specs'       => '10 GB Pure NVMe • 2 Websites • 500 GB Bandwidth',
        'icon'        => 'las la-bolt',
        'tag'         => 'Most Popular',
        'popular'     => true
    ],
    '103' => [
        'name'        => 'Plus Plan',
        'category'    => 'Budget Cloud Hosting',
        'monthly'     => 6.00,
        'yearly'      => 57.60, // $4.80/mo * 12
        'specs'       => '20 GB Pure NVMe • 5 Websites • Unmetered Bandwidth',
        'icon'        => 'las la-rocket',
        'tag'         => 'High Value',
        'popular'     => false
    ],

    // 2. Business Web Hosting Series
    '201' => [
        'name'        => 'Business Starter',
        'category'    => 'Business NVMe Web Hosting',
        'monthly'     => 7.50,
        'yearly'      => 72.00, // $6.00/mo * 12
        'specs'       => '20 GB Pure NVMe • 1 vCPU • 2 GB RAM • 1 Website',
        'icon'        => 'las la-building',
        'tag'         => 'Startup Pick',
        'popular'     => false
    ],
    '202' => [
        'name'        => 'Business Growth',
        'category'    => 'Business NVMe Web Hosting',
        'monthly'     => 15.00,
        'yearly'      => 144.00, // $12.00/mo * 12
        'specs'       => '40 GB Pure NVMe • 2 vCPU • 4 GB RAM • 5 Websites',
        'icon'        => 'las la-chart-line',
        'tag'         => 'Featured Business',
        'popular'     => true
    ],
    '203' => [
        'name'        => 'Business Professional',
        'category'    => 'Business NVMe Web Hosting',
        'monthly'     => 27.00,
        'yearly'      => 259.20, // $21.60/mo * 12
        'specs'       => '80 GB Pure NVMe • 4 vCPU • 8 GB RAM • 15 Websites',
        'icon'        => 'las la-gem',
        'tag'         => 'Corporate Tier',
        'popular'     => false
    ],
    '204' => [
        'name'        => 'Business Enterprise',
        'category'    => 'Business NVMe Web Hosting',
        'monthly'     => 48.00,
        'yearly'      => 460.80, // $38.40/mo * 12
        'specs'       => '150 GB Pure NVMe • 6 vCPU • 12 GB RAM • Unlimited Websites',
        'icon'        => 'las la-shield-alt',
        'tag'         => 'VIP Performance',
        'popular'     => false
    ],

    // 3. Premium Managed Cloud Series
    '301' => [
        'name'        => 'Cloud-01 Starter',
        'category'    => 'Premium Managed Cloud',
        'monthly'     => 75.00,
        'yearly'      => 720.00, // $60.00/mo * 12
        'specs'       => '100 GB NVMe • 4 vCPU Cores • 8 GB RAM • 10 Gbps Uplink',
        'icon'        => 'las la-cloud',
        'tag'         => 'Cloud Core',
        'popular'     => false
    ],
    '302' => [
        'name'        => 'Cloud-02 Business',
        'category'    => 'Premium Managed Cloud',
        'monthly'     => 120.00,
        'yearly'      => 1152.00, // $96.00/mo * 12
        'specs'       => '250 GB NVMe • 8 vCPU Cores • 16 GB RAM • Priority Support',
        'icon'        => 'las la-server',
        'tag'         => 'High Traffic',
        'popular'     => true
    ],
    '303' => [
        'name'        => 'Cloud-03 Pro',
        'category'    => 'Premium Managed Cloud',
        'monthly'     => 210.00,
        'yearly'      => 2016.00, // $168.00/mo * 12
        'specs'       => '500 GB NVMe • 16 vCPU Cores • 32 GB RAM • Enterprise SLA',
        'icon'        => 'las la-microchip',
        'tag'         => 'Powerhouse',
        'popular'     => false
    ],
    '304' => [
        'name'        => 'Cloud-04 Enterprise',
        'category'    => 'Premium Managed Cloud',
        'monthly'     => 350.00,
        'yearly'      => 3360.00, // $280.00/mo * 12
        'specs'       => '1,000 GB NVMe • 32 vCPU Cores • 64 GB RAM • VIP Dedicated Team',
        'icon'        => 'las la-crown',
        'tag'         => 'Ultimate Tier',
        'popular'     => false
    ],

    // 4. KVM VPS Series
    '401' => [
        'name'        => 'KVM VPS Starter (1 Core)',
        'category'    => 'KVM Cloud VPS',
        'monthly'     => 7.99,
        'yearly'      => 76.70,
        'specs'       => '1 vCPU • 2 GB RAM • 40 GB NVMe • 1 TB Traffic',
        'icon'        => 'las la-terminal',
        'tag'         => 'Dev Pick',
        'popular'     => false
    ],
    '402' => [
        'name'        => 'KVM VPS Business (2 Cores)',
        'category'    => 'KVM Cloud VPS',
        'monthly'     => 14.99,
        'yearly'      => 143.90,
        'specs'       => '2 vCPU • 4 GB RAM • 80 GB NVMe • 2 TB Traffic',
        'icon'        => 'las la-server',
        'tag'         => 'Top Seller',
        'popular'     => true
    ],
    '403' => [
        'name'        => 'KVM VPS Pro (4 Cores)',
        'category'    => 'KVM Cloud VPS',
        'monthly'     => 24.99,
        'yearly'      => 239.90,
        'specs'       => '4 vCPU • 8 GB RAM • 160 GB NVMe • 4 TB Traffic',
        'icon'        => 'las la-database',
        'tag'         => 'Production Ready',
        'popular'     => false
    ],
    '404' => [
        'name'        => 'KVM VPS Enterprise (8 Cores)',
        'category'    => 'KVM Cloud VPS',
        'monthly'     => 39.99,
        'yearly'      => 383.90,
        'specs'       => '8 vCPU • 16 GB RAM • 320 GB NVMe • 8 TB Traffic',
        'icon'        => 'las la-network-wired',
        'tag'         => 'Enterprise VPS',
        'popular'     => false
    ]
];

// Top TLD Pricing for On-Site Registration
$CF_TLDS = [
    'com'    => ['price' => 13.57, 'badge' => 'Popular', 'tag' => 'Commercial'],
    'net'    => ['price' => 15.49, 'badge' => 'Tech', 'tag' => 'Networking'],
    'org'    => ['price' => 17.49, 'badge' => 'Org', 'tag' => 'Organizations'],
    'cloud'  => ['price' => 9.99,  'badge' => 'Hot Deal', 'tag' => 'Cloud & SaaS'],
    'io'     => ['price' => 39.99, 'badge' => 'Tech Fav', 'tag' => 'Startups & APIs'],
    'co'     => ['price' => 24.99, 'badge' => 'Global', 'tag' => 'Company'],
    'store'  => ['price' => 4.99,  'badge' => 'E-Com', 'tag' => 'Online Store'],
    'online' => ['price' => 3.99,  'badge' => 'Sale', 'tag' => 'Web Presence']
];
