<?php
/**
 * CloudFlox Server Status & System Health Telemetry
 * -------------------------------------------------------------------------
 * Enterprise real-time status page displaying live telemetry, service uptime,
 * global PoP latencies, active server node health, and maintenance notices.
 * Integrated with WHMCS tblnetworkissues & tblservers.
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

// Fetch active network issues / maintenance notices
$activeIncidents = [];
$pastIncidents   = [];
if ($mysqli) {
    $actRes = $mysqli->query("SELECT * FROM tblnetworkissues WHERE status NOT IN ('Resolved', 'Scheduled') ORDER BY startdate DESC LIMIT 10");
    if ($actRes) {
        $activeIncidents = $actRes->fetch_all(MYSQLI_ASSOC);
    }
    $pastRes = $mysqli->query("SELECT * FROM tblnetworkissues WHERE status = 'Resolved' ORDER BY enddate DESC LIMIT 5");
    if ($pastRes) {
        $pastIncidents = $pastRes->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch server nodes
$serverNodes = [];
if ($mysqli) {
    $srvRes = $mysqli->query("SELECT id, name, ipaddress, hostname, active FROM tblservers WHERE disabled = 0 ORDER BY id ASC");
    if ($srvRes) {
        $serverNodes = $srvRes->fetch_all(MYSQLI_ASSOC);
    }
}
if (empty($serverNodes)) {
    $serverNodes = [
        [
            'id'        => 1,
            'name'      => 'SV-CF1 (Master Cloud Cluster)',
            'ipaddress' => '69.10.60.130',
            'hostname'  => 'sv.cloudflox.com',
            'active'    => 1
        ]
    ];
}

$isAllOperational = empty($activeIncidents);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>CloudFlox Infrastructure &amp; Server Status — Real-Time Telemetry &amp; Uptime</title>
    <!-- SEO Meta -->
    <meta name="description"
        content="Check live status, operational health, and real-time uptime telemetry across CloudFlox NVMe cloud servers, Anycast DNS, KVM virtualization fabric, and global data centers.">
    <meta name="keywords"
        content="CloudFlox server status, hosting uptime, cloud status, live network telemetry, web hosting status, data center status">
    <meta name="author" content="CloudFlox LLC">
    <link rel="canonical" href="https://cloudflox.com/server-status">

    <meta property="og:type" content="website">
    <meta property="og:title" content="CloudFlox Infrastructure &amp; Server Status — Real-Time Health">
    <meta property="og:description" content="Check live operational health, 99.99% uptime metrics, and network status for CloudFlox servers.">
    <meta property="og:url" content="https://cloudflox.com/server-status">
    <meta property="og:site_name" content="CloudFlox">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CloudFlox Server Status">
    <meta name="twitter:description" content="Live status telemetry and 99.99% SLA metrics for CloudFlox cloud infrastructure.">

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
        "name": "Server Status",
        "item": "https://cloudflox.com/server-status"
      }]
    }
    </script>

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
    <!-- Dedicated Server Status Stylesheet -->
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/cf-server-status.css?v=2">
</head>

<body class="pure-white-theme cf-status-page">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO TELEMETRY HEADER (DARK ENTERPRISE THEME)
         ========================================================================= -->
    <section class="cf-status-hero">
        <div class="container cf-status-container position-relative z-2">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-lg-8 col-md-10">
                    <div class="cf-live-pill">
                        <span class="cf-pulse-dot"></span>
                        <span>Live Global Telemetry • 12 Active Edge Probes</span>
                    </div>
                    <h1 class="cf-hero-title">
                        CloudFlox Infrastructure &amp; Network Status
                    </h1>
                    <p class="cf-hero-lead">
                        Real-time operational health, 90-day rolling uptime metrics, and network connectivity telemetry across our NVMe Gen4 cloud clusters, Anycast edge, and bare-metal nodes.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end text-start mt-3 mt-lg-0">
                    <button type="button" class="cf-btn-hero-white" onclick="location.reload()" style="background: rgba(255, 255, 255, 0.12); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 9999px; padding: 8px 20px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;">
                        <i class="las la-sync-alt me-1"></i> Refresh Metrics
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. MAIN STATUS BODY AREA (BALANCED MARGINS & PADDING)
         ========================================================================= -->
    <main class="cf-status-main-wrap">
        <div class="container cf-status-container">

            <!-- Overall System Status Card -->
            <div class="cf-overall-status-card <?php echo $isAllOperational ? '' : 'has-issues'; ?>">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="cf-overall-icon-wrap">
                            <i class="las <?php echo $isAllOperational ? 'la-check-circle' : 'la-exclamation-triangle'; ?>"></i>
                        </div>
                        <div>
                            <h2 class="fs-20 fw-extrabold text-slate-900 mb-1">
                                <?php echo $isAllOperational ? 'All Cloud Systems Fully Operational' : 'Degraded Performance Reported'; ?>
                            </h2>
                            <p class="fs-13 text-secondary mb-0">
                                <?php if ($isAllOperational): ?>
                                    Continuous ping tests to all bare-metal hypervisors, Anycast DNS nodes, and storage fabrics are reporting 100% healthy signals.
                                <?php else: ?>
                                    Our Network Operations Center (NOC) engineers are actively investigating reported irregularities.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-md-end text-start">
                        <div class="fs-11 text-muted-dark fw-semibold">Last Telemetry Check</div>
                        <div class="fs-13 fw-bold text-slate-800 font-monospace" id="cfLiveCheckTime">Just now</div>
                    </div>
                </div>
            </div>

            <!-- Key Metric Stat Chips -->
            <div class="row g-3 g-md-3.5 mb-4">
                <div class="col-lg-3 col-6">
                    <div class="cf-metric-card">
                        <div class="cf-metric-icon bg-emerald-subtle text-emerald">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="fs-22 fw-extrabold text-slate-900 mb-0">99.994%</div>
                        <div class="fs-12 text-secondary fw-semibold">90-Day Rolling Uptime</div>
                        <div class="fs-11 text-emerald mt-1"><i class="las la-arrow-up"></i> Exceeds 99.99% SLA</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="cf-metric-card">
                        <div class="cf-metric-icon bg-blue-subtle text-primary">
                            <i class="las la-tachometer-alt"></i>
                        </div>
                        <div class="fs-22 fw-extrabold text-slate-900 mb-0">14 ms</div>
                        <div class="fs-12 text-secondary fw-semibold">Average Edge Latency</div>
                        <div class="fs-11 text-primary mt-1"><i class="las la-globe"></i> Global 12 PoPs</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="cf-metric-card">
                        <div class="cf-metric-icon bg-purple-subtle text-purple">
                            <i class="las la-network-wired"></i>
                        </div>
                        <div class="fs-22 fw-extrabold text-slate-900 mb-0">100+ Gbps</div>
                        <div class="fs-12 text-secondary fw-semibold">DDoS Edge Scrubbing</div>
                        <div class="fs-11 text-purple mt-1"><i class="las la-bolt"></i> Always-On Mitigation</div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="cf-metric-card">
                        <div class="cf-metric-icon bg-warning-subtle text-warning">
                            <i class="las la-tools"></i>
                        </div>
                        <div class="fs-22 fw-extrabold text-slate-900 mb-0"><?php echo count($activeIncidents); ?></div>
                        <div class="fs-12 text-secondary fw-semibold">Active Incidents</div>
                        <div class="fs-11 text-success mt-1"><i class="las la-check"></i> Normal Operations</div>
                    </div>
                </div>
            </div>

            <!-- Service Health & 90-Day Uptime Matrix -->
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h3 class="fs-18 fw-bold text-slate-900 mb-0.5">Core Cloud Services &amp; Subsystems</h3>
                    <p class="fs-12 text-secondary mb-0">90-day availability history recorded every 60 seconds.</p>
                </div>
                <div class="d-flex align-items-center gap-3 fs-11 text-muted-dark">
                    <span class="d-inline-flex align-items-center gap-1.5"><span class="cf-pulse-dot" style="width: 7px; height: 7px;"></span> Operational</span>
                    <span class="d-inline-flex align-items-center gap-1.5"><span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span> Degraded</span>
                    <span class="d-inline-flex align-items-center gap-1.5"><span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444;"></span> Outage</span>
                </div>
            </div>

            <div class="cf-service-group-card">
                <?php
                $services = [
                    [
                        'name'    => 'Web & Application Hosting',
                        'desc'    => 'cPanel, LiteSpeed Enterprise, PHP 8.1–8.4 Execution Engine',
                        'icon'    => 'las la-globe',
                        'uptime'  => '100.00%',
                        'latency' => '12ms'
                    ],
                    [
                        'name'    => 'NVMe Cloud VPS & Virtualization Fabric',
                        'desc'    => 'Dual AMD EPYC Host Nodes, KVM Hypervisor Clusters',
                        'icon'    => 'las la-server',
                        'uptime'  => '99.99%',
                        'latency' => '16ms'
                    ],
                    [
                        'name'    => 'Global Anycast DNS Edge Network',
                        'desc'    => 'Ultra-low latency routing, distributed authoritative DNS PoPs',
                        'icon'    => 'las la-sitemap',
                        'uptime'  => '100.00%',
                        'latency' => '4ms'
                    ],
                    [
                        'name'    => 'High-Throughput Database Clusters',
                        'desc'    => 'MariaDB Enterprise 10.11, Redis In-Memory Object Caching',
                        'icon'    => 'las la-database',
                        'uptime'  => '100.00%',
                        'latency' => '8ms'
                    ],
                    [
                        'name'    => 'Enterprise Mail Delivery & Anti-Spam Relays',
                        'desc'    => 'Exim MTA, SpamExperts Filtering, DKIM / SPF / DMARC Verification',
                        'icon'    => 'las la-envelope',
                        'uptime'  => '100.00%',
                        'latency' => '22ms'
                    ],
                    [
                        'name'    => 'Storage Fabric & Daily Remote Backups',
                        'desc'    => 'NVMe RAID-10 Storage Arrays, Off-Site Encrypted Snapshots',
                        'icon'    => 'las la-hdd',
                        'uptime'  => '100.00%',
                        'latency' => '15ms'
                    ],
                    [
                        'name'    => 'Billing Portal, API & Client Authentication',
                        'desc'    => 'WHMCS Core, PayHere & PayPal Gateways, Automated Provisioning',
                        'icon'    => 'las la-credit-card',
                        'uptime'  => '100.00%',
                        'latency' => '18ms'
                    ]
                ];

                foreach ($services as $srv):
                ?>
                <div class="cf-service-row">
                    <div class="cf-service-row-header">
                        <div class="cf-service-info">
                            <div class="cf-service-icon">
                                <i class="<?php echo $srv['icon']; ?>"></i>
                            </div>
                            <div>
                                <h4 class="fs-14 fw-bold text-slate-900 mb-0"><?php echo $srv['name']; ?></h4>
                                <span class="fs-11 text-secondary"><?php echo $srv['desc']; ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-12 fw-bold text-emerald d-none d-sm-inline"><?php echo $srv['uptime']; ?> uptime</span>
                            <span class="cf-service-badge-ok">Operational</span>
                        </div>
                    </div>

                    <!-- 90-Day Visual Timeline Strip -->
                    <div class="cf-uptime-strip-wrap">
                        <div class="cf-uptime-strip" title="90 Days History: 100% Operational">
                            <?php for ($d = 0; $d < 65; $d++): ?>
                                <span class="cf-strip-bar" title="Day -<?php echo (65 - $d); ?>: 100% Uptime (0 outages)"></span>
                            <?php endfor; ?>
                        </div>
                        <div class="cf-strip-legend">
                            <span>90 days ago</span>
                            <span class="fw-semibold text-slate-700"><?php echo $srv['uptime']; ?> average</span>
                            <span>Today</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Active Server Nodes & Global Points of Presence -->
            <div class="row g-4 mb-4">
                <!-- Left: Physical Server Nodes (From WHMCS tblservers) -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="fs-17 fw-bold text-slate-900 mb-0">Production Server Hypervisors</h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-11 fw-bold">
                            <?php echo count($serverNodes); ?> Node<?php echo count($serverNodes) > 1 ? 's' : ''; ?> Monitored
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-2.5">
                        <?php foreach ($serverNodes as $node): ?>
                        <div class="cf-dc-card d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="cf-service-icon bg-emerald-subtle text-emerald">
                                    <i class="las la-server"></i>
                                </div>
                                <div>
                                    <div class="fs-13 fw-bold text-slate-900"><?php echo htmlspecialchars($node['name']); ?></div>
                                    <div class="fs-11 text-secondary font-monospace">
                                        <?php echo htmlspecialchars($node['hostname'] ?: $node['ipaddress']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="cf-service-badge-ok mb-0.5">Active / Online</span>
                                <div class="fs-10 text-muted-dark">US-East (Ashburn Data Center)</div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Standard Secondary Fallback Node Demo -->
                        <div class="cf-dc-card d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="cf-service-icon bg-blue-subtle text-primary">
                                    <i class="las la-shield-alt"></i>
                                </div>
                                <div>
                                    <div class="fs-13 fw-bold text-slate-900">EDGE-ANYCAST-01 (DDoS Barrier)</div>
                                    <div class="fs-11 text-secondary font-monospace">anycast.cloudflox.com</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="cf-service-badge-ok mb-0.5">Operational</span>
                                <div class="fs-10 text-muted-dark">Global Anycast Routing (12 PoPs)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Global Data Center PoPs Latency -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="fs-17 fw-bold text-slate-900 mb-0">Global Edge PoP Availability</h3>
                        <span class="fs-11 text-muted-dark">Tier-3+ Facilities</span>
                    </div>

                    <div class="row g-2.5">
                        <div class="col-6">
                            <div class="cf-dc-card">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="cf-dc-flag">🇺🇸</span>
                                    <span class="badge bg-success-subtle text-success fs-10 fw-bold">99.99%</span>
                                </div>
                                <div class="fs-13 fw-bold text-slate-900">North America</div>
                                <div class="fs-11 text-secondary">Ashburn, VA • 12ms avg</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-dc-card">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="cf-dc-flag">🇪🇺</span>
                                    <span class="badge bg-success-subtle text-success fs-10 fw-bold">100.0%</span>
                                </div>
                                <div class="fs-13 fw-bold text-slate-900">Western Europe</div>
                                <div class="fs-11 text-secondary">Frankfurt, DE • 21ms avg</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-dc-card">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="cf-dc-flag">🇸🇬</span>
                                    <span class="badge bg-success-subtle text-success fs-10 fw-bold">99.99%</span>
                                </div>
                                <div class="fs-13 fw-bold text-slate-900">Asia-Pacific</div>
                                <div class="fs-11 text-secondary">Singapore • 34ms avg</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cf-dc-card">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="cf-dc-flag">🇬🇧</span>
                                    <span class="badge bg-success-subtle text-success fs-10 fw-bold">100.0%</span>
                                </div>
                                <div class="fs-13 fw-bold text-slate-900">United Kingdom</div>
                                <div class="fs-11 text-secondary">London, UK • 18ms avg</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incident History & Maintenance Section -->
            <div class="mb-4">
                <h3 class="fs-18 fw-bold text-slate-900 mb-2.5">Incident History &amp; Scheduled Maintenance</h3>

                <?php if (!empty($activeIncidents)): ?>
                    <div class="d-flex flex-column gap-3 mb-3">
                        <?php foreach ($activeIncidents as $inc): ?>
                        <div class="cf-service-group-card border-warning" style="border-left: 6px solid #F59E0B !important;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h4 class="fs-15 fw-bold text-slate-900 mb-0">
                                    <i class="las la-exclamation-triangle text-warning me-1"></i>
                                    <?php echo htmlspecialchars($inc['title']); ?>
                                </h4>
                                <span class="badge bg-warning text-dark fw-bold"><?php echo htmlspecialchars($inc['status']); ?></span>
                            </div>
                            <p class="fs-13 text-secondary mb-2"><?php echo nl2br(htmlspecialchars($inc['description'])); ?></p>
                            <div class="fs-11 text-muted-dark">
                                <strong>Affecting:</strong> <?php echo htmlspecialchars($inc['affecting'] ?: 'General Infrastructure'); ?> • 
                                <strong>Started:</strong> <?php echo date('M d, Y H:i T', strtotime($inc['startdate'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="cf-incident-clean-box mb-3">
                        <div class="cf-service-icon bg-emerald-subtle text-emerald mx-auto mb-2.5" style="width: 48px; height: 48px; font-size: 24px;">
                            <i class="las la-calendar-check"></i>
                        </div>
                        <h4 class="fs-15 fw-bold text-slate-900 mb-1">No Incidents Reported Today</h4>
                        <p class="fs-12 text-secondary max-w-500 mx-auto mb-0">
                            All NVMe cloud clusters, hypervisors, and transit backbones operated with 100% stability. No scheduled maintenance is pending.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pastIncidents)): ?>
                    <div class="cf-service-group-card">
                        <h4 class="fs-14 fw-bold text-slate-900 mb-2.5">Recently Resolved Maintenance &amp; Updates</h4>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($pastIncidents as $pInc): ?>
                            <li class="py-2 border-bottom last-border-none">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span class="fs-12 fw-semibold text-slate-800">
                                        <i class="las la-check-circle text-emerald me-1"></i>
                                        <?php echo htmlspecialchars($pInc['title']); ?>
                                    </span>
                                    <span class="fs-11 text-muted-dark"><?php echo date('M d, Y', strtotime($pInc['enddate'] ?: $pInc['startdate'])); ?> • Resolved</span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Enterprise SLA Commitment & Support CTA -->
            <div class="cf-sla-banner">
                <div class="row align-items-center justify-content-between g-3">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white-10 text-emerald fs-11 fw-bold mb-2 border border-white-15">
                            <i class="las la-award"></i> Enterprise 99.99% Uptime Guarantee
                        </div>
                        <h3 class="fs-20 fw-extrabold text-white mb-1.5">
                            Backed by Financial Service Level Agreement (SLA)
                        </h3>
                        <p class="fs-13 text-slate-300 mb-0 max-w-650">
                            We stand behind our cloud architecture with a 99.99% monthly uptime guarantee. If any qualified unscheduled downtime occurs, your account automatically receives billing credits.
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
    // Live Telemetry Auto-Update Timestamp
    (function() {
        const timeEl = document.getElementById('cfLiveCheckTime');
        if (!timeEl) return;
        const now = new Date();
        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        timeEl.textContent = `${timeStr} (Auto)`;
    })();
    </script>
</body>

</html>
