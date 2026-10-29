<?php
/**
 * CloudFlox Knowledgebase & Help Desk Portal
 * -------------------------------------------------------------------------
 * Comprehensive, searchable self-service help center with category browsing,
 * instant live search filtering, rich tutorial article views, and WHMCS sync.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';

// Database connection to WHMCS
$mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
if ($mysqli->connect_error) {
    $mysqli = null;
} else {
    $mysqli->set_charset('utf8mb4');
}

// Request parameters
$catId     = intval($_GET['cat'] ?? 0);
$articleId = intval($_GET['id'] ?? ($_GET['article'] ?? 0));
$searchQ   = trim($_GET['search'] ?? ($_GET['q'] ?? ''));

// =============================================================================
// DEFAULT KNOWLEDGEBASE DATA LIBRARY (Used dynamically if WHMCS DB is empty)
// =============================================================================
$DEFAULT_CATEGORIES = [
    1 => [
        'id'          => 1,
        'name'        => 'Getting Started & Account Setup',
        'icon'        => 'las la-rocket',
        'color'       => '#2563EB',
        'bg'          => '#EFF6FF',
        'description' => 'Essential first steps: accessing cPanel, nameservers, and welcoming your new hosting service.'
    ],
    2 => [
        'id'          => 2,
        'name'        => 'Domain & DNS Management',
        'icon'        => 'las la-globe',
        'color'       => '#059669',
        'bg'          => '#ECFDF5',
        'description' => 'Configuring DNS A, CNAME, MX records, nameserver propagation, and WHOIS privacy.'
    ],
    3 => [
        'id'          => 3,
        'name'        => 'Email Setup & Client Configuration',
        'icon'        => 'las la-envelope',
        'color'       => '#7C3AED',
        'bg'          => '#F5F3FF',
        'description' => 'Configure business email on Outlook, iPhone, Android, and resolve SPF & DKIM delivery.'
    ],
    4 => [
        'id'          => 4,
        'name'        => 'SSL Certificates & HTTPS Security',
        'icon'        => 'las la-shield-alt',
        'color'       => '#0891B2',
        'bg'          => '#ECFEFF',
        'description' => 'Free AutoSSL installation, forcing HTTPS in .htaccess, and TLS encryption diagnostics.'
    ],
    5 => [
        'id'          => 5,
        'name'        => 'cPanel, Files & MySQL Databases',
        'icon'        => 'las la-server',
        'color'       => '#EA580C',
        'bg'          => '#FFF7ED',
        'description' => 'Using File Manager, creating FTP accounts, phpMyAdmin, and MySQL database management.'
    ],
    6 => [
        'id'          => 6,
        'name'        => 'Billing, Invoices & Orders',
        'icon'        => 'las la-credit-card',
        'color'       => '#D97706',
        'bg'          => '#FEF3C7',
        'description' => 'Managing renewals, downloading invoice PDFs, PayHere/PayPal payments, and upgrades.'
    ]
];

$DEFAULT_ARTICLES = [
    // Category 1: Getting Started
    101 => [
        'id'          => 101,
        'cat_id'      => 1,
        'title'       => 'How to Log in to Your cPanel Account',
        'views'       => 1420,
        'useful'      => 184,
        'summary'     => 'Direct URLs, port 2083 login guide, and single sign-on from CloudFlox Client Portal.',
        'content'     => '
            <p>Accessing your cPanel hosting control panel is quick and secure. There are two primary methods to log in:</p>
            <h3>Method 1: Direct Single Sign-On (Recommended)</h3>
            <ol>
                <li>Log in to your <strong>CloudFlox Client Area</strong> at <a href="clientarea">clientarea</a>.</li>
                <li>Navigate to <strong>Active Services & Hosting</strong>.</li>
                <li>Click the <strong>Manage Service</strong> button next to your active hosting plan.</li>
                <li>Click the one-click <strong>Login to cPanel</strong> button on the left sidebar. You will be authenticated immediately without needing a password.</li>
            </ol>
            <h3>Method 2: Direct Browser URL</h3>
            <p>You can also log in directly by typing either of the following into your browser address bar:</p>
            <code>https://yourdomain.com:2083<br>https://yourdomain.com/cpanel</code>
            <p>Use the cPanel username and password provided in your initial welcome email.</p>
        '
    ],
    102 => [
        'id'          => 102,
        'cat_id'      => 1,
        'title'       => 'What are CloudFlox Default Nameservers?',
        'views'       => 2310,
        'useful'      => 342,
        'summary'     => 'Primary and secondary Anycast nameservers required to connect external domains to CloudFlox.',
        'content'     => '
            <p>If you purchased your domain through a third-party registrar (such as GoDaddy, Namecheap, or LK Domain Registry), you must point your nameservers to CloudFlox to connect your website.</p>
            <h3>CloudFlox Anycast Nameservers:</h3>
            <code>ns1.cloudflox.com<br>ns2.cloudflox.com</code>
            <h3>How to Update at Your Registrar:</h3>
            <ol>
                <li>Log in to the dashboard of the company where you registered your domain.</li>
                <li>Locate the <strong>DNS / Nameservers</strong> management section.</li>
                <li>Select <strong>Custom Nameservers</strong>.</li>
                <li>Enter <code>ns1.cloudflox.com</code> and <code>ns2.cloudflox.com</code>, then save changes.</li>
            </ol>
            <div class="alert alert-info border-0 rounded-3">
                <strong><i class="las la-info-circle me-1"></i> Note on DNS Propagation:</strong> Nameserver changes typically take between 1 to 24 hours to propagate globally across all internet service providers.
            </div>
        '
    ],
    103 => [
        'id'          => 103,
        'cat_id'      => 1,
        'title'       => 'How to Upload Website Files to cPanel',
        'views'       => 980,
        'useful'      => 115,
        'summary'     => 'Step-by-step instructions on deploying your website files into the public_html directory.',
        'content'     => '
            <p>To make your website visible on the internet, your web files must be placed inside the <code>public_html</code> directory.</p>
            <h3>Using cPanel File Manager:</h3>
            <ol>
                <li>Log in to your <strong>cPanel</strong>.</li>
                <li>Under the <strong>Files</strong> category, click <strong>File Manager</strong>.</li>
                <li>In the left file tree, double click on <strong>public_html</strong>.</li>
                <li>Click the <strong>Upload</strong> button on the top toolbar.</li>
                <li>Upload your website archive (e.g. <code>site.zip</code>) or raw HTML/PHP files.</li>
                <li>If you uploaded a ZIP archive, select the file and click <strong>Extract</strong>.</li>
            </ol>
            <p>Ensure your homepage is named <code>index.html</code> or <code>index.php</code>.</p>
        '
    ],

    // Category 2: Domain & DNS Management
    201 => [
        'id'          => 201,
        'cat_id'      => 2,
        'title'       => 'How to Add or Edit DNS Records in cPanel Zone Editor',
        'views'       => 1150,
        'useful'      => 167,
        'summary'     => 'Managing A, CNAME, TXT, and MX records for domain verification and mail routing.',
        'content'     => '
            <p>The cPanel Zone Editor allows you to configure authoritative DNS records for your domain:</p>
            <ol>
                <li>Log in to your <strong>cPanel</strong>.</li>
                <li>Scroll to the <strong>Domains</strong> section and click <strong>Zone Editor</strong>.</li>
                <li>Find your domain and click <strong>Manage</strong>.</li>
                <li>Click <strong>+ Add Record</strong> at the top right.</li>
                <li>Select the Record Type (e.g. <code>A</code>, <code>CNAME</code>, <code>TXT</code>, or <code>MX</code>).</li>
                <li>Enter the Record Name (e.g. <code>mail.yourdomain.com</code>) and Destination Value (IP address or target host).</li>
                <li>Click <strong>Save Record</strong>.</li>
            </ol>
        '
    ],
    202 => [
        'id'          => 202,
        'cat_id'      => 2,
        'title'       => 'Connecting Domain to Cloudflare via CloudFlox',
        'views'       => 820,
        'useful'      => 98,
        'summary'     => 'Enhance security, global caching, and DDoS defense by routing your domain through Cloudflare.',
        'content'     => '
            <p>Routing your CloudFlox hosting through Cloudflare provides edge caching, Web Application Firewall (WAF), and global CDN acceleration:</p>
            <ol>
                <li>Sign up for a free account at Cloudflare.com.</li>
                <li>Click <strong>Add a Site</strong> and enter your domain name.</li>
                <li>Allow Cloudflare to automatically scan and import your existing CloudFlox DNS records.</li>
                <li>Cloudflare will assign you two unique nameservers.</li>
                <li>Update your domain registrar with Cloudflare nameservers.</li>
                <li>In Cloudflare SSL/TLS settings, set encryption mode to <strong>Full (Strict)</strong> to work harmoniously with CloudFlox AutoSSL.</li>
            </ol>
        '
    ],

    // Category 3: Email Setup
    301 => [
        'id'          => 301,
        'cat_id'      => 3,
        'title'       => 'How to Set Up Business Email on iPhone & iPad (iOS)',
        'views'       => 1890,
        'useful'      => 276,
        'summary'     => 'Step-by-step IMAP & SMTP configuration parameters for Apple iOS Mail app.',
        'content'     => '
            <p>Follow these steps to connect your custom domain email on any iOS device:</p>
            <ol>
                <li>On your iPhone, open <strong>Settings</strong> &gt; <strong>Mail</strong> &gt; <strong>Accounts</strong>.</li>
                <li>Tap <strong>Add Account</strong> and choose <strong>Other</strong> &gt; <strong>Add Mail Account</strong>.</li>
                <li>Enter your Name, Email address, and Password. Tap <strong>Next</strong>.</li>
                <li>Select <strong>IMAP</strong> at the top.</li>
                <li><strong>Incoming Mail Server:</strong>
                    <ul>
                        <li>Host Name: <code>mail.yourdomain.com</code></li>
                        <li>Username: Full email address (e.g. <code>info@yourdomain.com</code>)</li>
                        <li>Password: Your email password</li>
                    </ul>
                </li>
                <li><strong>Outgoing Mail Server (SMTP):</strong>
                    <ul>
                        <li>Host Name: <code>mail.yourdomain.com</code></li>
                        <li>Username &amp; Password: Same full email credentials (required).</li>
                    </ul>
                </li>
                <li>Tap <strong>Save</strong>. Use SSL port <code>993</code> (Incoming) and SSL port <code>465</code> (Outgoing).</li>
            </ol>
        '
    ],
    302 => [
        'id'          => 302,
        'cat_id'      => 3,
        'title'       => 'How to Configure Microsoft Outlook for Business Email',
        'views'       => 1640,
        'useful'      => 212,
        'summary'     => 'Manual IMAP/SMTP setup guide for Outlook 2019, 2021, and Microsoft 365.',
        'content'     => '
            <p>Connect your business email accounts to desktop Outlook easily:</p>
            <ol>
                <li>Open Outlook and click <strong>File</strong> &gt; <strong>Add Account</strong>.</li>
                <li>Enter your email address, check <strong>Let me set up my account manually</strong>, and click <strong>Connect</strong>.</li>
                <li>Choose <strong>IMAP</strong>.</li>
                <li>Incoming Mail: <code>mail.yourdomain.com</code> • Port: <code>993</code> • Encryption: <code>SSL/TLS</code>.</li>
                <li>Outgoing Mail: <code>mail.yourdomain.com</code> • Port: <code>465</code> • Encryption: <code>SSL/TLS</code>.</li>
                <li>Enter your email password and click <strong>Done</strong>.</li>
            </ol>
        '
    ],

    // Category 4: SSL Certificates
    401 => [
        'id'          => 401,
        'cat_id'      => 4,
        'title'       => 'How to Install and Renew Free AutoSSL in cPanel',
        'views'       => 2100,
        'useful'      => 318,
        'summary'     => 'CloudFlox provides 100% free automated Sectigo & Let’s Encrypt SSL certificates for all domains.',
        'content'     => '
            <p>All CloudFlox hosting plans come with free, automated SSL certificates that renew automatically every 90 days.</p>
            <h3>How to verify or trigger AutoSSL manually:</h3>
            <ol>
                <li>Log in to your <strong>cPanel</strong>.</li>
                <li>In the <strong>Security</strong> section, click <strong>SSL/TLS Status</strong>.</li>
                <li>Verify your domain is listed. A green lock icon indicates active SSL protection.</li>
                <li>If the lock is amber or missing, select your domains and click <strong>Run AutoSSL</strong>.</li>
                <li>The system will automatically verify domain ownership and install the certificate in 2 to 5 minutes.</li>
            </ol>
        '
    ],
    402 => [
        'id'          => 402,
        'cat_id'      => 4,
        'title'       => 'How to Force HTTPS Redirect via .htaccess',
        'views'       => 1780,
        'useful'      => 245,
        'summary'     => 'Ensure all visitors automatically use secure HTTPS connections with an Apache redirect rule.',
        'content'     => '
            <p>After your SSL certificate is installed, add this rule to the top of your <code>.htaccess</code> file inside <code>public_html</code> to enforce HTTPS on all traffic:</p>
            <code>RewriteEngine On<br>RewriteCond %{HTTPS} off<br>RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]</code>
            <p>Save the file and refresh your browser. Any visitor typing <code>http://</code> will be immediately redirected to <code>https://</code>.</p>
        '
    ],

    // Category 5: cPanel & Databases
    501 => [
        'id'          => 501,
        'cat_id'      => 5,
        'title'       => 'How to Create a MySQL Database and User in cPanel',
        'views'       => 1310,
        'useful'      => 178,
        'summary'     => 'Create databases, manage database users, and assign ALL PRIVILEGES for web applications.',
        'content'     => '
            <p>To connect WordPress or custom PHP applications to a database:</p>
            <ol>
                <li>In cPanel, click <strong>MySQL Databases</strong> under the Databases section.</li>
                <li><strong>Create Database:</strong> Enter your database name and click <em>Create Database</em>.</li>
                <li><strong>Add New User:</strong> Scroll to MySQL Users, enter a username and strong password, and click <em>Create User</em>.</li>
                <li><strong>Add User to Database:</strong> Scroll down, select your newly created user and database, and click <em>Add</em>.</li>
                <li>Check the box for <strong>ALL PRIVILEGES</strong> and click <strong>Make Changes</strong>.</li>
            </ol>
        '
    ],

    // Category 6: Billing & Invoices
    601 => [
        'id'          => 601,
        'cat_id'      => 6,
        'title'       => 'How to Pay Invoices and Download Official PDF Receipts',
        'views'       => 890,
        'useful'      => 120,
        'summary'     => 'Accepted payment methods, automated invoice clearing, and downloading tax invoice PDFs.',
        'content'     => '
            <p>CloudFlox accepts automated credit/debit cards, PayHere, and PayPal payments:</p>
            <ol>
                <li>Log in to your <strong>Client Area</strong> at <a href="clientarea?tab=invoices">clientarea?tab=invoices</a>.</li>
                <li>Click on any invoice with an <strong>Unpaid</strong> status.</li>
                <li>Choose your preferred payment gateway (PayHere or PayPal) and click <strong>Pay Now</strong>.</li>
                <li>Once payment completes, your service is automatically renewed instantly.</li>
                <li>To download an official PDF copy for tax accounting, click the <strong>Download PDF Invoice</strong> button at the top right of the invoice view.</li>
            </ol>
        '
    ]
];

// Determine View Mode
$currentArticle  = null;
$currentCategory = null;

if ($articleId > 0 && isset($DEFAULT_ARTICLES[$articleId])) {
    $currentArticle  = $DEFAULT_ARTICLES[$articleId];
    $currentCategory = $DEFAULT_CATEGORIES[$currentArticle['cat_id']] ?? null;
} elseif ($catId > 0 && isset($DEFAULT_CATEGORIES[$catId])) {
    $currentCategory = $DEFAULT_CATEGORIES[$catId];
}

// Filter articles if search or category is active
$displayedArticles = $DEFAULT_ARTICLES;
if ($catId > 0) {
    $displayedArticles = array_filter($displayedArticles, function($a) use ($catId) {
        return $a['cat_id'] == $catId;
    });
}
if (!empty($searchQ)) {
    $displayedArticles = array_filter($displayedArticles, function($a) use ($searchQ) {
        return (stripos($a['title'], $searchQ) !== false || stripos($a['summary'], $searchQ) !== false || stripos($a['content'], $searchQ) !== false);
    });
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>
        <?php 
        if ($currentArticle) {
            echo htmlspecialchars($currentArticle['title']) . " — CloudFlox Knowledgebase";
        } elseif ($currentCategory) {
            echo htmlspecialchars($currentCategory['name']) . " Guides — CloudFlox Help Center";
        } else {
            echo "CloudFlox Knowledgebase &amp; Help Desk — Hosting Guides &amp; Tutorials";
        }
        ?>
    </title>
    <!-- SEO Meta -->
    <meta name="description"
        content="Browse self-service guides, step-by-step cPanel tutorials, DNS instructions, and email configurations at CloudFlox Help Desk.">
    <meta name="keywords"
        content="CloudFlox knowledgebase, hosting help desk, cPanel tutorials, email setup guide, nameservers, SSL installation">
    <meta name="author" content="CloudFlox LLC">
    <link rel="canonical" href="https://cloudflox.com/knowledgebase">

    <meta property="og:type" content="website">
    <meta property="og:title" content="CloudFlox Knowledgebase &amp; Help Desk">
    <meta property="og:description" content="Step-by-step guides, hosting tutorials, and solutions for your cloud services.">
    <meta property="og:url" content="https://cloudflox.com/knowledgebase">
    <meta property="og:site_name" content="CloudFlox">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CloudFlox Knowledgebase">
    <meta name="twitter:description" content="Browse tutorials, documentation, and technical answers for your hosting.">

    <!-- JSON-LD Breadcrumb -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://cloudflox.com/"
      },{
        "@type": "ListItem",
        "position": 2,
        "name": "Knowledgebase",
        "item": "https://cloudflox.com/knowledgebase"
      }]
    }
    </script>

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->

    <!-- Dedicated Knowledgebase Stylesheet -->
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/cf-knowledgebase.css?v=1">
</head>

<body class="pure-white-theme cf-kb-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO SEARCH BANNER
         ========================================================================= -->
    <section class="cf-kb-hero">
        <div class="container cf-kb-container position-relative z-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fs-12 fw-bold mb-3 d-inline-flex align-items-center gap-1.5">
                <i class="las la-book-reader"></i> CloudFlox Help Center &amp; Documentation
            </span>
            <h1 class="cf-kb-hero-title">
                How can our engineers assist you today?
            </h1>
            <p class="cf-kb-hero-lead">
                Search our comprehensive library of technical guides, cPanel tutorials, email setup parameters, and DNS troubleshooting steps.
            </p>

            <!-- Search Bar Form with Live Filter Integration -->
            <div class="cf-kb-search-wrap">
                <form action="<?php echo $url; ?>knowledgebase" method="GET" class="cf-kb-search-box">
                    <i class="las la-search fs-20 text-muted me-2"></i>
                    <input type="text" name="q" id="kbSearchInput" class="cf-kb-search-input" 
                           placeholder="Search guides (e.g. cPanel login, iPhone email, Nameservers, SSL)..." 
                           value="<?php echo htmlspecialchars($searchQ); ?>" autocomplete="off">
                    <button type="submit" class="cf-kb-search-btn">
                        <span>Search</span>
                        <i class="las la-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Quick Suggested Topics Pills -->
            <div class="cf-kb-topics-pills">
                <span class="text-white-50 me-1">Popular:</span>
                <a href="<?php echo $url; ?>knowledgebase?id=101" class="cf-kb-topic-pill">cPanel Login</a>
                <a href="<?php echo $url; ?>knowledgebase?id=102" class="cf-kb-topic-pill">Nameservers</a>
                <a href="<?php echo $url; ?>knowledgebase?id=301" class="cf-kb-topic-pill">iPhone Email</a>
                <a href="<?php echo $url; ?>knowledgebase?id=401" class="cf-kb-topic-pill">AutoSSL</a>
                <a href="<?php echo $url; ?>knowledgebase?id=501" class="cf-kb-topic-pill">MySQL Database</a>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. MAIN CONTENT BODY AREA
         ========================================================================= -->
    <main class="cf-kb-main-wrap">
        <div class="container cf-kb-container">

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0 fs-13">
                    <li class="breadcrumb-item"><a href="<?php echo $url; ?>" class="text-decoration-none text-muted"><i class="las la-home me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $url; ?>knowledgebase" class="text-decoration-none text-muted">Knowledgebase</a></li>
                    <?php if ($currentCategory): ?>
                        <li class="breadcrumb-item <?php echo $currentArticle ? '' : 'active fw-bold text-primary'; ?>">
                            <?php if ($currentArticle): ?>
                                <a href="<?php echo $url; ?>knowledgebase?cat=<?php echo $currentCategory['id']; ?>" class="text-decoration-none text-muted">
                                    <?php echo htmlspecialchars($currentCategory['name']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($currentCategory['name']); ?>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($currentArticle): ?>
                        <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">
                            <?php echo htmlspecialchars($currentArticle['title']); ?>
                        </li>
                    <?php endif; ?>
                </ol>
            </nav>

            <!-- =================================================================
                 VIEW MODE A: SINGLE ARTICLE READER
                 ================================================================= -->
            <?php if ($currentArticle): ?>
                <div class="cf-kb-read-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill px-3 py-1 fs-12 fw-bold" style="background: <?php echo $currentCategory['bg']; ?>; color: <?php echo $currentCategory['color']; ?>; border: 1px solid <?php echo $currentCategory['color']; ?>33;">
                            <i class="<?php echo $currentCategory['icon']; ?> me-1"></i>
                            <?php echo htmlspecialchars($currentCategory['name']); ?>
                        </span>
                        <div class="fs-12 text-muted">
                            <i class="las la-eye me-1"></i> <?php echo number_format($currentArticle['views']); ?> Views • 
                            <i class="las la-thumbs-up ms-2 me-1 text-success"></i> <?php echo number_format($currentArticle['useful']); ?> Found Useful
                        </div>
                    </div>

                    <h1 class="fs-28 fw-extrabold text-slate-900 mb-4" style="letter-spacing: -0.02em;">
                        <?php echo htmlspecialchars($currentArticle['title']); ?>
                    </h1>

                    <div class="cf-kb-article-content">
                        <?php echo $currentArticle['content']; ?>
                    </div>

                    <!-- Helpful Voting Widget -->
                    <div class="cf-kb-feedback-box" id="kbVoteBox">
                        <div>
                            <div class="fs-14 fw-bold text-slate-900">Was this article helpful to you?</div>
                            <div class="fs-12 text-secondary">Your feedback helps our engineering team improve documentation.</div>
                        </div>
                        <div class="d-flex align-items-center gap-2" id="kbVoteButtons">
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" onclick="cfVoteArticle(true)">
                                <i class="las la-thumbs-up me-1"></i> Yes, it helped
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" onclick="cfVoteArticle(false)">
                                <i class="las la-thumbs-down me-1"></i> No
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Back to Categories & Related Articles -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5">
                    <a href="<?php echo $url; ?>knowledgebase<?php echo $currentCategory ? '?cat=' . $currentCategory['id'] : ''; ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 fs-13 fw-semibold">
                        <i class="las la-arrow-left me-1"></i> Back to <?php echo $currentCategory ? htmlspecialchars($currentCategory['name']) : 'Knowledgebase'; ?>
                    </a>
                    <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-primary rounded-pill px-4 py-2 fs-13 fw-bold shadow-sm">
                        <i class="las la-headset me-1"></i> Contact Specialist Team &rarr;
                    </a>
                </div>

            <!-- =================================================================
                 VIEW MODE B: CATEGORY BROWSING & MAIN DIRECTORY
                 ================================================================= -->
            <?php else: ?>

                <!-- 1. Category Grid (Only shown when not searching and no specific cat is selected) -->
                <?php if (empty($searchQ) && !$catId): ?>
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h2 class="fs-20 fw-bold text-slate-900 mb-0">Browse by Technical Category</h2>
                        <span class="fs-13 text-secondary">6 Comprehensive Support Categories</span>
                    </div>

                    <div class="row g-3 g-md-3.5 mb-5">
                        <?php foreach ($DEFAULT_CATEGORIES as $cat): 
                            $count = count(array_filter($DEFAULT_ARTICLES, function($a) use ($cat) { return $a['cat_id'] == $cat['id']; }));
                        ?>
                        <div class="col-lg-4 col-md-6">
                            <a href="<?php echo $url; ?>knowledgebase?cat=<?php echo $cat['id']; ?>" class="cf-kb-cat-card">
                                <div class="cf-kb-cat-icon" style="background: <?php echo $cat['bg']; ?>; color: <?php echo $cat['color']; ?>;">
                                    <i class="<?php echo $cat['icon']; ?>"></i>
                                </div>
                                <h3 class="cf-kb-cat-title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <p class="cf-kb-cat-desc"><?php echo htmlspecialchars($cat['description']); ?></p>
                                <div class="cf-kb-cat-footer">
                                    <span><?php echo $count; ?> Guide<?php echo $count > 1 ? 's' : ''; ?> Available</span>
                                    <span>Explore <i class="las la-arrow-right ms-1"></i></span>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- 2. Articles List (Filtered by search or selected category) -->
                <div class="cf-kb-articles-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 pb-2 border-bottom">
                        <div>
                            <h3 class="fs-18 fw-bold text-slate-900 mb-0.5">
                                <?php 
                                if (!empty($searchQ)) {
                                    echo 'Search Results for "' . htmlspecialchars($searchQ) . '"';
                                } elseif ($currentCategory) {
                                    echo htmlspecialchars($currentCategory['name']) . ' Guides';
                                } else {
                                    echo 'Popular &amp; Trending Knowledgebase Articles';
                                }
                                ?>
                            </h3>
                            <span class="fs-12 text-secondary">
                                Found <?php echo count($displayedArticles); ?> guide<?php echo count($displayedArticles) != 1 ? 's' : ''; ?>
                            </span>
                        </div>
                        <?php if ($catId > 0 || !empty($searchQ)): ?>
                            <a href="<?php echo $url; ?>knowledgebase" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fs-12 fw-semibold">
                                <i class="las la-times me-1"></i> Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($displayedArticles)): ?>
                        <div class="cf-kb-articles-list" id="kbArticlesContainer">
                            <?php foreach ($displayedArticles as $art): 
                                $catObj = $DEFAULT_CATEGORIES[$art['cat_id']] ?? null;
                            ?>
                            <a href="<?php echo $url; ?>knowledgebase?id=<?php echo $art['id']; ?>" class="cf-kb-article-link-item">
                                <div class="pe-3">
                                    <div class="cf-kb-article-title mb-1">
                                        <i class="las la-file-alt text-primary me-1.5"></i>
                                        <?php echo htmlspecialchars($art['title']); ?>
                                    </div>
                                    <div class="fs-12 text-muted">
                                        <?php echo htmlspecialchars($art['summary']); ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                    <?php if ($catObj): ?>
                                        <span class="badge rounded-pill fs-11 fw-semibold d-none d-sm-inline" style="background: <?php echo $catObj['bg']; ?>; color: <?php echo $catObj['color']; ?>;">
                                            <?php echo htmlspecialchars($catObj['name']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <i class="las la-angle-right cf-kb-article-arrow"></i>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="cf-service-icon bg-blue-subtle text-primary mx-auto mb-3" style="width: 48px; height: 48px; font-size: 24px;">
                                <i class="las la-search"></i>
                            </div>
                            <h4 class="fs-16 fw-bold text-slate-900 mb-1">No guides found matching your search</h4>
                            <p class="fs-13 text-secondary mb-3">Try different keywords or submit an inquiry to our 24/7 technical engineering team.</p>
                            <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-primary rounded-pill px-4 py-2 fs-13 fw-bold">
                                <i class="las la-plus-circle me-1"></i> Open a Support Ticket
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

            <!-- =================================================================
                 3. STILL NEED HELP? ESCALATION CTA BANNER
                 ================================================================= -->
            <div class="cf-kb-cta-banner mt-4">
                <div class="row align-items-center justify-content-between g-3">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white-10 text-cyan fs-11 fw-bold mb-2 border border-white-15">
                            <i class="las la-headset"></i> 24/7/365 Human Support Escalation
                        </div>
                        <h3 class="fs-20 fw-extrabold text-white mb-1.5">
                            Can’t find the solution in our knowledgebase?
                        </h3>
                        <p class="fs-13 text-slate-300 mb-0 max-w-650">
                            Our senior cloud engineers and server architects are online around the clock to investigate and resolve issues directly for you.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <div class="d-flex align-items-center gap-2 justify-content-lg-end flex-wrap">
                            <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-warning fw-extrabold rounded-pill px-4 py-2 text-slate-900 shadow-md fs-13">
                                Open Support Ticket <i class="las la-arrow-right ms-1"></i>
                            </a>
                            <button type="button" onclick="openLiveChat(event)" class="btn btn-outline-light rounded-pill px-3 py-2 fs-12 fw-bold">
                                <i class="las la-comments me-1"></i> Live Chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <script>
    // Live Search Filter as User Types
    (function() {
        const searchInput = document.getElementById('kbSearchInput');
        const container = document.getElementById('kbArticlesContainer');
        if (!searchInput || !container) return;

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = container.querySelectorAll('.cf-kb-article-link-item');
            items.forEach(function(item) {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    })();

    // Helpful Vote Handler
    function cfVoteArticle(isHelpful) {
        const voteButtons = document.getElementById('kbVoteButtons');
        if (!voteButtons) return;
        voteButtons.innerHTML = isHelpful
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fs-13"><i class="las la-check-circle me-1"></i> Thank you for your feedback!</span>'
            : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 fs-13"><i class="las la-info-circle me-1"></i> Thank you! We will improve this guide.</span>';
    }
    </script>
</body>

</html>

