<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/whmcs.php';

// Auth Guard: Require login
if (empty($_SESSION['cf_client_logged_in'])) {
    header("Location: " . $url . "login?redirect=clientarea");
    exit;
}

$clientId    = (int)($_SESSION['cf_client_id'] ?? 0);
$clientEmail = $_SESSION['cf_client_email'] ?? '';
$clientName  = $_SESSION['cf_client_name'] ?? 'Client';

// Initialize data containers
$services = [];
$domains  = [];
$invoices = [];
$tickets  = [];

$statServices = 0;
$statDomains  = 0;
$statInvoicesUnpaid = 0;
$statTicketsOpen = 0;

// Fetch data from local WHMCS database or API fallback
try {
    $mysqli = @new mysqli('localhost', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if (!$mysqli->connect_error && $clientId > 0) {
        // 1. Fetch Services
        $stmt = $mysqli->prepare("SELECT h.id, h.domain, h.domainstatus, h.amount, h.billingcycle, h.nextduedate, p.name as product_name FROM tblhosting h LEFT JOIN tblproducts p ON p.id = h.packageid WHERE h.userid = ? ORDER BY h.id DESC");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 2. Fetch Domains
        $stmt = $mysqli->prepare("SELECT id, domain, status, registrationdate, nextduedate, recurringamount FROM tbldomains WHERE userid = ? ORDER BY id DESC");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $domains = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 3. Fetch Invoices
        $stmt = $mysqli->prepare("SELECT id, invoicenum, date, duedate, total, status FROM tblinvoices WHERE userid = ? ORDER BY id DESC LIMIT 20");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $invoices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // 4. Fetch Support Tickets
        $stmt = $mysqli->prepare("SELECT id, tid, title, date, lastreply, status, urgency FROM tbltickets WHERE userid = ? ORDER BY lastreply DESC LIMIT 20");
        if ($stmt) {
            $stmt->bind_param('i', $clientId);
            $stmt->execute();
            $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        // Calculate counts
        foreach ($services as $s) {
            if (strtolower($s['domainstatus']) === 'active') $statServices++;
        }
        foreach ($domains as $d) {
            if (strtolower($d['status']) === 'active') $statDomains++;
        }
        foreach ($invoices as $inv) {
            if (strtolower($inv['status']) === 'unpaid') $statInvoicesUnpaid++;
        }
        foreach ($tickets as $t) {
            if (in_array(strtolower($t['status']), ['open', 'customer-reply', 'in progress'])) $statTicketsOpen++;
        }
    }
} catch (Exception $e) {
    // Graceful error handling
}

// Format status badge helper
function getStatusBadge($status) {
    $st = strtolower(trim($status));
    if ($st === 'active' || $st === 'paid') {
        return '<span class="cf-badge-active"><i class="las la-check-circle"></i> ' . htmlspecialchars($status) . '</span>';
    } elseif ($st === 'pending' || $st === 'in progress') {
        return '<span class="cf-badge-pending"><i class="las la-clock"></i> ' . htmlspecialchars($status) . '</span>';
    } elseif ($st === 'unpaid' || $st === 'overdue' || $st === 'cancelled') {
        return '<span class="cf-badge-unpaid"><i class="las la-exclamation-circle"></i> ' . htmlspecialchars($status) . '</span>';
    } else {
        return '<span class="cf-badge-suspended"><i class="las la-info-circle"></i> ' . htmlspecialchars($status) . '</span>';
    }
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
    <title>Client Portal Dashboard — CloudFlox Cloud Infrastructure</title>

    <!-- SEO Meta -->
    <meta name="description" content="Manage your CloudFlox hosting services, registered domains, billing invoices, and support tickets in one integrated dashboard.">
    <meta name="robots" content="noindex, follow">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
</head>

<body class="pure-white-theme">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. DASHBOARD HERO BANNER (Integrated Welcome & Action Center)
         ========================================================================= -->
    <section class="cf-dashboard-hero text-white position-relative">
        <div class="container cf-dashboard-container position-relative" style="z-index: 2;">
            <!-- Top sub-navigation & status indicator -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <a href="<?php echo $url; ?>" class="cf-dash-back-link">
                    <i class="las la-arrow-left"></i> Return to Homepage
                </a>
                <div class="d-inline-flex align-items-center gap-2">
                    <span class="cf-hero-status-pill">
                        <span class="cf-pulse-dot-green"></span> High Availability &bull; All Systems Normal
                    </span>
                </div>
            </div>

            <!-- Main Greeting & Action Row -->
            <div class="row align-items-center justify-content-between gy-3">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill cf-hero-session-pill mb-2 border" style="padding: 10px;">
                        <span class="cf-pulse-dot-blue" style="margin-right: 5px; margin-left: 5px;"></span> Active Client Session
                    </div>
                    <h1 class="h2 fw-extrabold text-white mb-2 tracking-tight">
                        Welcome back, <span class="text-gradient-cyan"><?php echo htmlspecialchars($clientName); ?></span>!
                    </h1>
                    <div class="cf-hero-meta d-flex align-items-center flex-wrap gap-2 text-white-50 fs-13">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="las la-envelope text-white-50"></i> <?php echo htmlspecialchars($clientEmail); ?>
                        </span>
                        <span class="text-white-20 d-none d-sm-inline">&bull;</span>
                        <span class="cf-hero-meta-badge">
                            <i class="las la-id-badge" style="color: #67E8F9;"></i> Client ID #<?php echo $clientId; ?>
                        </span>
                        <span class="text-white-20 d-none d-sm-inline">&bull;</span>
                        <span class="cf-hero-meta-badge" style="color: #6EE7B7;">
                            <i class="las la-shield-alt" style="color: #34D399;"></i> Verified Account
                        </span>
                    </div>
                </div>

                <div class="col-lg-5 text-lg-end">
                    <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap mb-2.5">
                        <a href="<?php echo $url; ?>web-hosting" class="btn cf-btn-hero-primary">
                            <i class="las la-plus-circle"></i> Deploy Service
                        </a>
                        <a href="<?php echo $url; ?>domain" class="btn cf-btn-hero-white">
                            <i class="las la-globe text-primary"></i> New Domain
                        </a>
                        <a href="<?php echo $url; ?>login?action=logout" class="btn cf-btn-hero-logout" title="Sign out of your account">
                            <i class="las la-sign-out-alt"></i> Sign Out
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. DASHBOARD STATS & CONTENT AREA (Spacious & Clean Layout)
         ========================================================================= -->
    <main class="cf-dashboard-body-wrap">
        <div class="container cf-dashboard-container">

            <!-- 4 Quick Stats Grid -->
            <div class="row g-3 g-md-3.5 mb-4">
                <!-- Services Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #EFF6FF; color: #2563EB;">
                            <i class="las la-server"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($services); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Cloud Services</div>
                        </div>
                    </div>
                </div>

                <!-- Domains Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #ECFDF5; color: #059669;">
                            <i class="las la-globe"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($domains); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Domains</div>
                        </div>
                    </div>
                </div>

                <!-- Invoices Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: <?php echo $statInvoicesUnpaid > 0 ? '#FEE2E2' : '#F1F5F9'; ?>; color: <?php echo $statInvoicesUnpaid > 0 ? '#DC2626' : '#64748B'; ?>;">
                            <i class="las la-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo $statInvoicesUnpaid; ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Unpaid Invoices</div>
                        </div>
                    </div>
                </div>

                <!-- Tickets Stat -->
                <div class="col-6 col-lg-3">
                    <div class="cf-stat-card">
                        <div class="cf-stat-icon" style="background: #FAF5FF; color: #9333EA;">
                            <i class="las la-headset"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($tickets); ?></div>
                            <div class="fs-12.5 text-secondary fw-semibold">Support Tickets</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Main Card with Tabs -->
            <div class="cf-dashboard-card">
                <!-- Nav Tabs -->
                <ul class="nav cf-dashboard-nav-tabs mb-4" id="clientDashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-pane" type="button" role="tab">
                            <i class="las la-server me-1"></i> My Services (<?php echo count($services); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="domains-tab" data-bs-toggle="tab" data-bs-target="#domains-pane" type="button" role="tab">
                            <i class="las la-globe me-1"></i> My Domains (<?php echo count($domains); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices-pane" type="button" role="tab">
                            <i class="las la-file-invoice-dollar me-1"></i> Invoices &amp; Billing (<?php echo count($invoices); ?>)
                            <?php if ($statInvoicesUnpaid > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?php echo $statInvoicesUnpaid; ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets-pane" type="button" role="tab">
                            <i class="las la-headset me-1"></i> Support Tickets (<?php echo count($tickets); ?>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="clientDashboardContent">

                    <!-- 1. Services Tab -->
                    <div class="tab-pane fade show active" id="services-pane" role="tabpanel">
                        <?php if (empty($services)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-server"></i></div>
                                <h5 class="fw-bold text-slate-800">No Active Cloud Services</h5>
                                <p class="text-secondary fs-13 mb-3">You do not have any cloud hosting services or servers active yet.</p>
                                <a href="<?php echo $url; ?>web-hosting" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-bolt me-1"></i> Deploy High-Speed Hosting
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Product / Service</th>
                                            <th>Assigned Domain</th>
                                            <th>Pricing / Cycle</th>
                                            <th>Next Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $srv): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900"><?php echo htmlspecialchars($srv['product_name'] ?? 'Cloud Hosting'); ?></div>
                                                    <span class="fs-11 text-muted">ID #<?php echo $srv['id']; ?></span>
                                                </td>
                                                <td>
                                                    <a href="http://<?php echo htmlspecialchars($srv['domain']); ?>" target="_blank" rel="noopener" class="fw-semibold text-primary text-decoration-none">
                                                        <?php echo htmlspecialchars($srv['domain']); ?>
                                                        <i class="las la-external-link-alt fs-11 ms-1"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-800">$<?php echo number_format((float)$srv['amount'], 2); ?> USD</div>
                                                    <span class="fs-11 text-muted"><?php echo htmlspecialchars($srv['billingcycle']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo !empty($srv['nextduedate']) && $srv['nextduedate'] !== '0000-00-00' ? htmlspecialchars($srv['nextduedate']) : 'N/A'; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($srv['domainstatus']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Domains Tab -->
                    <div class="tab-pane fade" id="domains-pane" role="tabpanel">
                        <?php if (empty($domains)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-globe"></i></div>
                                <h5 class="fw-bold text-slate-800">No Domains Registered</h5>
                                <p class="text-secondary fs-13 mb-3">You do not have any active registered domains in this account.</p>
                                <a href="<?php echo $url; ?>domain" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-search me-1"></i> Search &amp; Register Domain
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Domain Name</th>
                                            <th>Registration Date</th>
                                            <th>Next Due Date</th>
                                            <th>Renewal Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($domains as $dom): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-slate-900"><?php echo htmlspecialchars($dom['domain']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['registrationdate']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($dom['nextduedate']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-800">$<?php echo number_format((float)$dom['recurringamount'], 2); ?> USD</div>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($dom['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Invoices Tab -->
                    <div class="tab-pane fade" id="invoices-pane" role="tabpanel">
                        <?php if (empty($invoices)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-file-invoice"></i></div>
                                <h5 class="fw-bold text-slate-800">No Invoices Found</h5>
                                <p class="text-secondary fs-13 mb-0">You do not have any invoices generated for your account.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Invoice Date</th>
                                            <th>Due Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $inv): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-slate-900">#<?php echo htmlspecialchars($inv['invoicenum'] ?: $inv['id']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($inv['date']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($inv['duedate']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-slate-900">$<?php echo number_format((float)$inv['total'], 2); ?> USD</div>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($inv['status']); ?>
                                                </td>
                                                <td>
                                                    <?php if (strtolower($inv['status']) === 'unpaid'): ?>
                                                        <a href="<?php echo $url; ?>checkout?invoiceid=<?php echo $inv['id']; ?>" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fs-12 fw-bold">
                                                            <i class="las la-credit-card me-1"></i> Pay Now
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-light btn-sm rounded-pill px-3 py-1 fs-12 fw-semibold text-secondary" disabled>
                                                            Paid
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Support Tickets Tab -->
                    <div class="tab-pane fade" id="tickets-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-slate-900 mb-0">Recent Tickets</h6>
                            <a href="<?php echo $url; ?>contact-us" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                <i class="las la-plus-circle me-1"></i> Open Support Ticket
                            </a>
                        </div>
                        <?php if (empty($tickets)): ?>
                            <div class="text-center py-5">
                                <div class="fs-40 text-muted mb-2"><i class="las la-headset"></i></div>
                                <h5 class="fw-bold text-slate-800">No Support Tickets</h5>
                                <p class="text-secondary fs-13 mb-3">Everything is running smoothly! You don't have any support tickets.</p>
                                <a href="<?php echo $url; ?>contact-us" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                    <i class="las la-comment-alt me-1"></i> Contact CloudFlox Support
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Subject</th>
                                            <th>Last Updated</th>
                                            <th>Urgency</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $tkt): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-slate-900">#<?php echo htmlspecialchars($tkt['tid'] ?: $tkt['id']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-slate-900"><?php echo htmlspecialchars($tkt['title']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="fs-13 text-secondary"><?php echo htmlspecialchars($tkt['lastreply']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($tkt['urgency']); ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($tkt['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Direct Support Help Banner (Spacious & Clean) -->
            <div class="cf-dashboard-support-card d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="cf-stat-icon" style="background: #EFF6FF; color: #2563EB;">
                        <i class="las la-headset fs-28"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-slate-900 mb-1">Need help managing your cloud infrastructure?</h6>
                        <p class="fs-13 text-secondary mb-0">Our 24/7 technical team is available to assist you with server setups, DNS configurations, and billing inquiries.</p>
                    </div>
                </div>
                <a href="<?php echo $url; ?>contact-us" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold flex-shrink-0 text-decoration-none">
                    Contact Support &rarr;
                </a>
            </div>

        </div>
    </main>

        </div>
    </main>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== Javascript Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Javascript Area =========== -->

</body>
</html>
