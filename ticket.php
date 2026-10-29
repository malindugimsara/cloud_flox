<?php
/**
 * CloudFlox Support Ticket Portal
 * -------------------------------------------------------------------------
 * Comprehensive, enterprise support ticket system integrated with WHMCS.
 * Supports:
 * - Viewing all client tickets with real-time status filtering
 * - Opening new support tickets across departments with priority & service linking
 * - Multi-turn conversation thread viewing with staff & client reply timeline
 * - Direct client reply posting and ticket closure
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
include 'include/url.inc.php';
require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/whmcs.php';

// Auth Guard: Require client login
if (empty($_SESSION['cf_client_logged_in'])) {
    $redirectUrl = urlencode($_SERVER['REQUEST_URI'] ?? 'ticket');
    header("Location: " . $url . "login?redirect=" . $redirectUrl);
    exit;
}

$clientId    = (int)($_SESSION['cf_client_id'] ?? 0);
$clientEmail = $_SESSION['cf_client_email'] ?? '';
$clientName  = $_SESSION['cf_client_name'] ?? 'Client';

// Database connection
$mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
if ($mysqli->connect_error) {
    $mysqli = null;
} else {
    $mysqli->set_charset('utf8mb4');
}

$action = strtolower(trim($_GET['action'] ?? ''));
$ticketId = intval($_GET['id'] ?? 0);
$ticketTid = trim($_GET['tid'] ?? '');

$flashSuccess = $_SESSION['ticket_flash_success'] ?? '';
$flashError   = $_SESSION['ticket_flash_error'] ?? '';
unset($_SESSION['ticket_flash_success'], $_SESSION['ticket_flash_error']);

// Helper: Format status badge
if (!function_exists('getTicketStatusBadge')) {
    function getTicketStatusBadge($status) {
        $st = strtolower(trim($status));
        if ($st === 'open') {
            return '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-dot-circle text-success me-1"></i>Open</span>';
        } elseif ($st === 'customer-reply') {
            return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-clock text-warning me-1"></i>Customer Reply</span>';
        } elseif ($st === 'in progress') {
            return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-spinner text-primary me-1"></i>In Progress</span>';
        } elseif ($st === 'answered') {
            return '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-reply text-info me-1"></i>Staff Answered</span>';
        } elseif ($st === 'closed') {
            return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fs-12 fw-bold"><i class="las la-check text-secondary me-1"></i>Closed</span>';
        } else {
            return '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fs-12 fw-bold">' . htmlspecialchars($status) . '</span>';
        }
    }
}

// Helper: Format urgency/priority badge
if (!function_exists('getTicketUrgencyBadge')) {
    function getTicketUrgencyBadge($urgency) {
        $urg = strtolower(trim($urgency));
        if ($urg === 'critical' || $urg === 'urgent' || $urg === 'high') {
            return '<span class="badge bg-danger text-white rounded-pill px-2 py-0.5 fs-11 fw-bold"><i class="las la-fire me-1"></i>' . ucfirst($urgency) . '</span>';
        } elseif ($urg === 'medium') {
            return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-11 fw-semibold">' . ucfirst($urgency) . '</span>';
        } else {
            return '<span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-11 fw-semibold">' . ucfirst($urgency ?: 'Low') . '</span>';
        }
    }
}

// =============================================================================
// POST HANDLER 1: OPEN NEW TICKET
// =============================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_ticket') {
    $deptId   = intval($_POST['department'] ?? 1);
    $urgency  = trim($_POST['urgency'] ?? 'Medium');
    $service  = trim($_POST['service'] ?? '');
    $subject  = trim($_POST['subject'] ?? '');
    $message  = trim($_POST['message'] ?? '');

    if (empty($subject)) {
        $_SESSION['ticket_flash_error'] = 'Please enter a ticket subject describing your inquiry.';
        header("Location: " . $url . "ticket?action=new");
        exit;
    }
    if (empty($message)) {
        $_SESSION['ticket_flash_error'] = 'Please provide details or explanation for your support request.';
        header("Location: " . $url . "ticket?action=new");
        exit;
    }

    // Generate unique WHMCS-style TID (e.g. ABC-123456)
    $letters = strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 3));
    $numbers = rand(100000, 999999);
    $generatedTid = $letters . '-' . $numbers;
    $accessKey = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

    // Handle optional file attachment
    $attachmentName = '';
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = __DIR__ . '/cfwhmcs/attachments/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['attachment']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'log', 'zip', 'tar', 'gz'];
        if (in_array($ext, $allowedExts) && $_FILES['attachment']['size'] <= 15 * 1024 * 1024) {
            $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $origName);
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $safeFileName)) {
                $attachmentName = $safeFileName;
            }
        }
    }

    $newTicketId = 0;
    if ($mysqli) {
        $stmt = $mysqli->prepare("INSERT INTO tbltickets (tid, did, userid, date, title, message, status, urgency, lastreply, service, c, ipaddress, attachment, editor) VALUES (?, ?, ?, NOW(), ?, ?, 'Open', ?, NOW(), ?, ?, ?, ?, 'markdown')");
        if ($stmt) {
            $stmt->bind_param('siisssssss', $generatedTid, $deptId, $clientId, $subject, $message, $urgency, $service, $accessKey, $clientIp, $attachmentName);
            $stmt->execute();
            $newTicketId = $stmt->insert_id;
            $stmt->close();
        }
    }

    // Also attempt WHMCS API OpenTicket sync
    try {
        WHMCSClient::call('OpenTicket', [
            'clientid' => $clientId,
            'deptid'   => $deptId,
            'subject'  => $subject,
            'message'  => $message,
            'priority' => $urgency,
            'serviceid'=> $service
        ]);
    } catch (Exception $e) {}

    $_SESSION['ticket_flash_success'] = "Support ticket #{$generatedTid} has been opened successfully! Our technical engineers will review and respond shortly.";
    if ($newTicketId > 0) {
        header("Location: " . $url . "ticket?id=" . $newTicketId);
    } else {
        header("Location: " . $url . "ticket");
    }
    exit;
}

// =============================================================================
// POST HANDLER 2: POST REPLY TO TICKET
// =============================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply_ticket') {
    $tId     = intval($_POST['ticket_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($tId <= 0 || empty($message)) {
        $_SESSION['ticket_flash_error'] = 'Reply message cannot be empty.';
        header("Location: " . $url . "ticket?id=" . $tId);
        exit;
    }

    // Verify ticket ownership
    $validTicket = false;
    $currentStatus = '';
    if ($mysqli) {
        $vStmt = $mysqli->prepare("SELECT id, status FROM tbltickets WHERE id = ? AND userid = ? LIMIT 1");
        if ($vStmt) {
            $vStmt->bind_param('ii', $tId, $clientId);
            $vStmt->execute();
            $vRes = $vStmt->get_result();
            if ($row = $vRes->fetch_assoc()) {
                $validTicket = true;
                $currentStatus = $row['status'];
            }
            $vStmt->close();
        }
    }

    if (!$validTicket) {
        $_SESSION['ticket_flash_error'] = 'Invalid ticket or permission denied.';
        header("Location: " . $url . "ticket");
        exit;
    }

    // Handle optional file attachment
    $attachmentName = '';
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = __DIR__ . '/cfwhmcs/attachments/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['attachment']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'log', 'zip', 'tar', 'gz'];
        if (in_array($ext, $allowedExts) && $_FILES['attachment']['size'] <= 15 * 1024 * 1024) {
            $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $origName);
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $safeFileName)) {
                $attachmentName = $safeFileName;
            }
        }
    }

    // Insert reply
    if ($mysqli) {
        $rStmt = $mysqli->prepare("INSERT INTO tblticketreplies (tid, userid, name, email, date, message, attachment, editor) VALUES (?, ?, ?, ?, NOW(), ?, ?, 'markdown')");
        if ($rStmt) {
            $rStmt->bind_param('iissss', $tId, $clientId, $clientName, $clientEmail, $message, $attachmentName);
            $rStmt->execute();
            $rStmt->close();
        }

        // Update ticket status to Customer-Reply and set lastreply timestamp
        $upStmt = $mysqli->prepare("UPDATE tbltickets SET status = 'Customer-Reply', lastreply = NOW(), adminunread = '1', updated_at = NOW() WHERE id = ?");
        if ($upStmt) {
            $upStmt->bind_param('i', $tId);
            $upStmt->execute();
            $upStmt->close();
        }
    }

    // Sync reply to WHMCS API
    try {
        WHMCSClient::call('AddTicketReply', [
            'ticketid' => $tId,
            'clientid' => $clientId,
            'message'  => $message
        ]);
    } catch (Exception $e) {}

    $_SESSION['ticket_flash_success'] = 'Your reply has been posted successfully.';
    header("Location: " . $url . "ticket?id=" . $tId);
    exit;
}

// =============================================================================
// POST HANDLER 3: CLOSE TICKET
// =============================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'close_ticket') {
    $tId = intval($_POST['ticket_id'] ?? 0);
    if ($tId > 0 && $mysqli) {
        $cStmt = $mysqli->prepare("UPDATE tbltickets SET status = 'Closed', updated_at = NOW() WHERE id = ? AND userid = ?");
        if ($cStmt) {
            $cStmt->bind_param('ii', $tId, $clientId);
            $cStmt->execute();
            $cStmt->close();
        }
        $_SESSION['ticket_flash_success'] = 'Ticket has been marked as closed.';
    }
    header("Location: " . $url . "ticket?id=" . $tId);
    exit;
}

// =============================================================================
// FETCH DATA FOR THE VIEWS
// =============================================================================

// 1. Fetch departments
$departments = [];
if ($mysqli) {
    $depRes = $mysqli->query("SELECT id, name, description FROM tblticketdepartments WHERE hidden != 'on' OR hidden IS NULL ORDER BY `order` ASC, id ASC");
    if ($depRes) {
        $departments = $depRes->fetch_all(MYSQLI_ASSOC);
    }
}
if (empty($departments)) {
    $departments = [
        ['id' => 1, 'name' => 'General Enquiries', 'description' => 'General questions and support'],
        ['id' => 2, 'name' => 'Technical Support', 'description' => 'Hosting, servers, DNS and SSL help'],
        ['id' => 3, 'name' => 'Billing & Accounts', 'description' => 'Invoices, payments and accounts']
    ];
}

// 2. Fetch Client Services (for linking when opening a ticket)
$clientServices = [];
if ($mysqli && $clientId > 0) {
    $srvStmt = $mysqli->prepare("SELECT h.id, h.domain, p.name as product_name FROM tblhosting h LEFT JOIN tblproducts p ON p.id = h.packageid WHERE h.userid = ? ORDER BY h.id DESC");
    if ($srvStmt) {
        $srvStmt->bind_param('i', $clientId);
        $srvStmt->execute();
        $clientServices = $srvStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $srvStmt->close();
    }
    $domStmt = $mysqli->prepare("SELECT id, domain FROM tbldomains WHERE userid = ? ORDER BY id DESC");
    if ($domStmt) {
        $domStmt->bind_param('i', $clientId);
        $domStmt->execute();
        $domainsList = $domStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $domStmt->close();
        foreach ($domainsList as $dl) {
            $clientServices[] = [
                'id'           => 'D' . $dl['id'],
                'domain'       => $dl['domain'],
                'product_name' => 'Domain Registration'
            ];
        }
    }
}

// 3. Determine View Mode
$currentTicket = null;
$ticketReplies = [];

if ($ticketId > 0 || !empty($ticketTid)) {
    // Single Ticket View
    if ($mysqli) {
        if ($ticketId > 0) {
            $tStmt = $mysqli->prepare("SELECT t.*, d.name as dept_name FROM tbltickets t LEFT JOIN tblticketdepartments d ON d.id = t.did WHERE t.id = ? AND t.userid = ? LIMIT 1");
            if ($tStmt) {
                $tStmt->bind_param('ii', $ticketId, $clientId);
                $tStmt->execute();
                $currentTicket = $tStmt->get_result()->fetch_assoc();
                $tStmt->close();
            }
        } elseif (!empty($ticketTid)) {
            $tStmt = $mysqli->prepare("SELECT t.*, d.name as dept_name FROM tbltickets t LEFT JOIN tblticketdepartments d ON d.id = t.did WHERE t.tid = ? AND t.userid = ? LIMIT 1");
            if ($tStmt) {
                $tStmt->bind_param('si', $ticketTid, $clientId);
                $tStmt->execute();
                $currentTicket = $tStmt->get_result()->fetch_assoc();
                if ($currentTicket) $ticketId = (int)$currentTicket['id'];
                $tStmt->close();
            }
        }

        // Fetch ticket replies
        if ($currentTicket && $ticketId > 0) {
            $rStmt = $mysqli->prepare("SELECT * FROM tblticketreplies WHERE tid = ? ORDER BY date ASC, id ASC");
            if ($rStmt) {
                $rStmt->bind_param('i', $ticketId);
                $rStmt->execute();
                $ticketReplies = $rStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $rStmt->close();
            }
        }
    }
}

// 4. If list mode, fetch tickets list and stats
$allTickets = [];
$statOpen = 0;
$statAnswered = 0;
$statClosed = 0;

if (!$currentTicket && $action !== 'new') {
    if ($mysqli && $clientId > 0) {
        $listStmt = $mysqli->prepare("SELECT t.*, d.name as dept_name FROM tbltickets t LEFT JOIN tblticketdepartments d ON d.id = t.did WHERE t.userid = ? ORDER BY t.lastreply DESC");
        if ($listStmt) {
            $listStmt->bind_param('i', $clientId);
            $listStmt->execute();
            $allTickets = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $listStmt->close();
        }
    }

    foreach ($allTickets as $tk) {
        $st = strtolower($tk['status']);
        if ($st === 'open' || $st === 'customer-reply' || $st === 'in progress') {
            $statOpen++;
        } elseif ($st === 'answered') {
            $statAnswered++;
        } elseif ($st === 'closed') {
            $statClosed++;
        }
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
    <title>
        <?php 
        if ($currentTicket) {
            echo "Ticket #" . htmlspecialchars($currentTicket['tid']) . " — CloudFlox Support";
        } elseif ($action === 'new') {
            echo "Open New Support Ticket — CloudFlox Help Desk";
        } else {
            echo "Support Tickets & Help Desk — CloudFlox Client Portal";
        }
        ?>
    </title>

    <!-- SEO Meta -->
    <meta name="description" content="CloudFlox 24/7 technical support desk. Submit questions, track server setup issues, and converse directly with enterprise cloud engineers.">
    <meta name="robots" content="noindex, follow">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->

    <style>
    /* CloudFlox Support Tickets Styling & Spacing Refinements */
    .cf-ticket-thread-box {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 24px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .cf-msg-card {
        padding: 26px 30px;
        border-bottom: 1.5px solid #F1F5F9;
        transition: background 0.15s ease;
    }
    .cf-msg-card:last-child {
        border-bottom: none;
    }
    .cf-msg-card.staff-reply {
        background: #F8FAFD;
        border-left: 4px solid #2563EB;
    }
    .cf-msg-card.client-msg {
        background: #FFFFFF;
    }
    @media (max-width: 767.98px) {
        .cf-msg-card {
            padding: 18px 16px;
        }
    }
    .cf-avatar-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .cf-avatar-client {
        background: #EFF6FF;
        color: #2563EB;
        border: 1.5px solid #DBEAFE;
    }
    .cf-avatar-staff {
        background: #0F172A;
        color: #38BDF8;
        border: 1.5px solid #1E293B;
    }
    .cf-msg-body {
        font-size: 14.5px;
        line-height: 1.7;
        color: #1E293B;
        white-space: pre-line;
    }
    .cf-sidebar-item {
        padding: 12px 0;
        border-bottom: 1px solid #F1F5F9;
    }
    .cf-sidebar-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .cf-sidebar-item:first-child {
        padding-top: 0;
    }
    .cf-ticket-input-label {
        font-size: 13px;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 7px;
    }
    .cf-ticket-form-group {
        margin-bottom: 22px;
    }
    </style>
</head>

<body class="pure-white-theme">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- =========================================================================
         1. HERO HEADER AREA
         ========================================================================= -->
    <section class="cf-dashboard-hero text-white position-relative">
        <div class="cf-hero-gradient-orb cf-hero-orb-1" aria-hidden="true"></div>
        <div class="cf-hero-gradient-orb cf-hero-orb-2" aria-hidden="true"></div>

        <div class="container cf-dashboard-container position-relative z-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <nav aria-label="breadcrumb">
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo $url; ?>clientarea" class="cf-dash-back-link">
                            <i class="las la-angle-left"></i> Client Dashboard
                        </a>
                        <span class="text-white-50 fs-12">/</span>
                        <?php if ($currentTicket): ?>
                            <a href="<?php echo $url; ?>ticket" class="cf-dash-back-link">Tickets</a>
                            <span class="text-white-50 fs-12">/</span>
                            <span class="text-white fs-12 fw-semibold">#<?php echo htmlspecialchars($currentTicket['tid']); ?></span>
                        <?php elseif ($action === 'new'): ?>
                            <a href="<?php echo $url; ?>ticket" class="cf-dash-back-link">Tickets</a>
                            <span class="text-white-50 fs-12">/</span>
                            <span class="text-white fs-12 fw-semibold">Open New</span>
                        <?php else: ?>
                            <span class="text-white fs-12 fw-semibold">Support Desk</span>
                        <?php endif; ?>
                    </div>
                </nav>
                <div class="d-inline-flex align-items-center gap-2">
                    <span class="cf-hero-status-pill">
                        <span class="cf-pulse-dot-green"></span> 24/7 SLA Priority Active
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <?php if ($currentTicket): ?>
                        <h1 class="h3 fw-bold text-white mb-2">
                            <?php echo htmlspecialchars($currentTicket['title']); ?>
                        </h1>
                        <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 fs-13">
                            <span class="cf-hero-meta-badge"><i class="las la-hashtag text-info"></i> Ticket #<?php echo htmlspecialchars($currentTicket['tid']); ?></span>
                            <span class="cf-hero-meta-badge"><i class="las la-building text-info"></i> <?php echo htmlspecialchars($currentTicket['dept_name'] ?? 'Support'); ?></span>
                            <span class="cf-hero-meta-badge"><i class="las la-calendar text-info"></i> Created <?php echo date('M d, Y H:i', strtotime($currentTicket['date'])); ?></span>
                        </div>
                    <?php elseif ($action === 'new'): ?>
                        <h1 class="h3 fw-bold text-white mb-1">
                            Open a Support Ticket
                        </h1>
                        <p class="text-white-50 fs-13 mb-0">Describe your inquiry and our engineering team will inspect and respond directly.</p>
                    <?php else: ?>
                        <h1 class="h3 fw-bold text-white mb-1">
                            Support Tickets &amp; Help Desk
                        </h1>
                        <p class="text-white-50 fs-13 mb-0">Track active inquiries, submit technical requests, and review response logs.</p>
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php if ($currentTicket || $action === 'new'): ?>
                        <a href="<?php echo $url; ?>ticket" class="cf-btn-hero-white">
                            <i class="las la-arrow-left"></i> All Tickets
                        </a>
                    <?php endif; ?>
                    <?php if ($action !== 'new'): ?>
                        <a href="<?php echo $url; ?>ticket?action=new" class="cf-btn-hero-primary">
                            <i class="las la-plus-circle"></i> Open New Ticket
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $url; ?>clientarea" class="cf-btn-hero-white">
                        <i class="las la-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo $url; ?>clientarea?tab=profile" class="cf-btn-hero-white" title="My Profile &amp; Account Settings">
                        <i class="las la-user-cog"></i> Profile Details
                    </a>
                    <a href="<?php echo $url; ?>login?action=logout" class="cf-btn-hero-logout" title="Sign out of your account">
                        <i class="las la-sign-out-alt"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. MAIN BODY CONTENT
         ========================================================================= -->
    <main class="cf-dashboard-body-wrap">
        <div class="container cf-dashboard-container">

            <!-- Flash Alerts -->
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4 border-0 rounded-3 shadow-xs" role="alert">
                    <i class="las la-check-circle fs-20 text-success"></i>
                    <div><?php echo htmlspecialchars($flashSuccess); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 border-0 rounded-3 shadow-xs" role="alert">
                    <i class="las la-exclamation-circle fs-20 text-danger"></i>
                    <div><?php echo htmlspecialchars($flashError); ?></div>
                </div>
            <?php endif; ?>


            <!-- =================================================================
                 VIEW 1: SINGLE TICKET THREAD & CONVERSATION
                 ================================================================= -->
            <?php if ($currentTicket): ?>

                <div class="row g-4">
                    <!-- Left: Conversation Timeline -->
                    <div class="col-lg-8">
                        <div class="cf-ticket-thread-box mb-4">
                            <!-- Thread Card Header -->
                            <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="cf-stat-icon" style="width: 36px; height: 36px; background: #EFF6FF; color: #2563EB; font-size: 18px; border-radius: 10px;">
                                        <i class="las la-comments"></i>
                                    </div>
                                    <h5 class="fw-bold text-slate-900 mb-0 fs-16">Ticket Conversation Log</h5>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <?php echo getTicketStatusBadge($currentTicket['status']); ?>
                                    <?php echo getTicketUrgencyBadge($currentTicket['urgency']); ?>
                                </div>
                            </div>

                            <!-- 1. Original Client Message -->
                            <div class="cf-msg-card client-msg">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cf-avatar-badge cf-avatar-client">
                                            <i class="las la-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-900 fs-14"><?php echo htmlspecialchars($clientName); ?></div>
                                            <span class="badge bg-light text-muted border fs-11">Ticket Creator</span>
                                        </div>
                                    </div>
                                    <span class="text-muted fs-12">
                                        <i class="las la-clock me-1"></i> <?php echo date('M d, Y \a\t H:i', strtotime($currentTicket['date'])); ?>
                                    </span>
                                </div>
                                <div class="cf-msg-body ps-md-5 ms-md-2">
                                    <?php echo nl2br(htmlspecialchars($currentTicket['message'])); ?>
                                </div>
                                <?php if (!empty($currentTicket['attachment'])): ?>
                                    <div class="mt-3.5 ps-md-5 ms-md-2 pt-3 border-top">
                                        <span class="fs-12 text-muted fw-bold d-block mb-1.5"><i class="las la-paperclip me-1"></i> Attachment:</span>
                                        <a href="<?php echo $url; ?>cfwhmcs/attachments/<?php echo urlencode($currentTicket['attachment']); ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fs-12 fw-semibold">
                                            <i class="las la-download me-1 text-primary"></i> <?php echo htmlspecialchars($currentTicket['attachment']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- 2. Replies in chronological order -->
                            <?php if (!empty($ticketReplies)): ?>
                                <?php foreach ($ticketReplies as $rep): 
                                    $isStaff = !empty($rep['admin']);
                                    $authorName = $isStaff ? htmlspecialchars($rep['admin']) : htmlspecialchars($rep['name'] ?: $clientName);
                                ?>
                                    <div class="cf-msg-card <?php echo $isStaff ? 'staff-reply' : 'client-msg'; ?>">
                                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="cf-avatar-badge <?php echo $isStaff ? 'cf-avatar-staff' : 'cf-avatar-client'; ?>">
                                                    <i class="<?php echo $isStaff ? 'las la-shield-alt' : 'las la-user'; ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong class="text-slate-900 fs-14"><?php echo $authorName; ?></strong>
                                                        <?php if ($isStaff): ?>
                                                            <span class="badge bg-primary text-white fs-10 px-2 py-0.5 rounded-pill">
                                                                <i class="las la-check-circle me-0.5"></i> CloudFlox Staff
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="text-muted fs-11"><?php echo $isStaff ? 'Support Engineer' : 'Client'; ?></span>
                                                </div>
                                            </div>
                                            <span class="text-muted fs-12">
                                                <i class="las la-clock me-1"></i> <?php echo date('M d, Y \a\t H:i', strtotime($rep['date'])); ?>
                                            </span>
                                        </div>
                                        <div class="cf-msg-body ps-md-5 ms-md-2">
                                            <?php echo nl2br(htmlspecialchars($rep['message'])); ?>
                                        </div>
                                        <?php if (!empty($rep['attachment'])): ?>
                                            <div class="mt-3.5 ps-md-5 ms-md-2 pt-3 border-top">
                                                <span class="fs-12 text-muted fw-bold d-block mb-1.5"><i class="las la-paperclip me-1"></i> Attachment:</span>
                                                <a href="<?php echo $url; ?>cfwhmcs/attachments/<?php echo urlencode($rep['attachment']); ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fs-12 fw-semibold">
                                                    <i class="las la-download me-1 text-primary"></i> <?php echo htmlspecialchars($rep['attachment']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- 3. Reply Form Card -->
                        <div class="cf-dashboard-card p-4 p-md-4 mb-4">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                <h6 class="fw-bold text-slate-900 mb-0 fs-16">
                                    <i class="las la-reply text-primary me-1.5"></i> Post a Reply
                                </h6>
                                <?php if (strtolower($currentTicket['status']) === 'closed'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill fs-11 px-2.5 py-1">
                                        Submitting a reply will re-open this ticket
                                    </span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="<?php echo $url; ?>ticket" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="reply_ticket">
                                <input type="hidden" name="ticket_id" value="<?php echo $currentTicket['id']; ?>">

                                <div class="cf-ticket-form-group">
                                    <label class="cf-ticket-input-label">Your Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="message" rows="5" placeholder="Type your response or additional information here..." required style="border-radius: 12px;"></textarea>
                                </div>

                                <div class="row align-items-center g-3">
                                    <div class="col-sm-7">
                                        <label class="cf-ticket-input-label mb-1">
                                            <i class="las la-paperclip me-1"></i> Add Attachment <span class="text-muted fw-normal fs-11">(Optional, max 15MB)</span>
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt,.log,.zip" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-sm-5 text-sm-end pt-sm-3">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                                            <i class="las la-paper-plane me-1"></i> Send Reply
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Ticket Information Sidebar -->
                    <div class="col-lg-4">
                        <div class="cf-dashboard-card p-4 mb-4">
                            <h6 class="fw-bold text-slate-900 mb-3 pb-2.5 border-bottom fs-15">
                                <i class="las la-info-circle text-primary me-1.5"></i> Ticket Details
                            </h6>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Ticket Reference</span>
                                <strong class="fs-16 text-slate-900">#<?php echo htmlspecialchars($currentTicket['tid']); ?></strong>
                            </div>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Current Status</span>
                                <div><?php echo getTicketStatusBadge($currentTicket['status']); ?></div>
                            </div>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Department</span>
                                <div class="fw-semibold text-slate-800 fs-13">
                                    <i class="las la-building text-secondary me-1"></i> <?php echo htmlspecialchars($currentTicket['dept_name'] ?? 'Support'); ?>
                                </div>
                            </div>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Urgency / Priority</span>
                                <div><?php echo getTicketUrgencyBadge($currentTicket['urgency']); ?></div>
                            </div>

                            <?php if (!empty($currentTicket['service'])): ?>
                                <div class="cf-sidebar-item">
                                    <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Related Service</span>
                                    <div class="fw-semibold text-primary fs-13">
                                        <i class="las la-server me-1"></i> <?php echo htmlspecialchars($currentTicket['service']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Submitted On</span>
                                <span class="text-secondary fs-13"><?php echo date('M d, Y H:i', strtotime($currentTicket['date'])); ?></span>
                            </div>

                            <div class="cf-sidebar-item">
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Last Updated</span>
                                <span class="text-secondary fs-13"><?php echo date('M d, Y H:i', strtotime($currentTicket['lastreply'])); ?></span>
                            </div>

                            <?php if (strtolower($currentTicket['status']) !== 'closed'): ?>
                                <div class="pt-3 mt-3 border-top text-center">
                                    <form method="POST" action="<?php echo $url; ?>ticket" onsubmit="return confirm('Are you sure you want to close this support ticket?');">
                                        <input type="hidden" name="action" value="close_ticket">
                                        <input type="hidden" name="ticket_id" value="<?php echo $currentTicket['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3.5 py-1.5 fw-semibold w-100">
                                            <i class="las la-times-circle me-1"></i> Close This Ticket
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 24/7 Support Banner -->
                        <div class="cf-dashboard-card p-4">
                            <div class="d-flex align-items-center gap-2.5 mb-2">
                                <div class="cf-stat-icon" style="width: 36px; height: 36px; background: #ECFDF5; color: #059669; font-size: 18px; border-radius: 10px;">
                                    <i class="las la-headset"></i>
                                </div>
                                <h6 class="fw-bold text-slate-900 mb-0 fs-14">Need Urgent Assistance?</h6>
                            </div>
                            <p class="fs-12.5 text-secondary mb-3">Our engineers monitor ticket queues around the clock. For critical emergency server downtime, mark priority as High.</p>
                            <a href="<?php echo $url; ?>contact-us" class="btn btn-light border btn-sm rounded-pill w-100 fw-semibold text-secondary py-2">
                                <i class="las la-envelope me-1"></i> Contact Channels
                            </a>
                        </div>
                    </div>
                </div>


            <!-- =================================================================
                 VIEW 2: OPEN NEW SUPPORT TICKET (action === 'new')
                 ================================================================= -->
            <?php elseif ($action === 'new'): ?>

                <div class="row g-4 justify-content-center">
                    <div class="col-lg-8">
                        <div class="cf-dashboard-card p-4 p-md-5 mb-4">
                            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                                <div class="cf-stat-icon" style="width: 44px; height: 44px; background: #EFF6FF; color: #2563EB; font-size: 20px; border-radius: 12px;">
                                    <i class="las la-edit"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-slate-900 mb-0.5 fs-18">Submit a Support Request</h5>
                                    <p class="text-secondary fs-12 mb-0">Fill in the details below to open a ticket directly in our WHMCS help desk.</p>
                                </div>
                            </div>
                            <form method="POST" action="<?php echo $url; ?>ticket" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="create_ticket">

                                <div class="row g-3 cf-ticket-form-group">
                                    <!-- Department Selection -->
                                    <div class="col-md-6">
                                        <label class="cf-ticket-input-label">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" name="department" required style="border-radius: 10px;">
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['id']; ?>" <?php echo ($dept['id'] == 2) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted fs-11">Choose the team best suited to handle your question.</small>
                                    </div>

                                    <!-- Priority / Urgency -->
                                    <div class="col-md-6">
                                        <label class="cf-ticket-input-label">Priority / Urgency <span class="text-danger">*</span></label>
                                        <select class="form-select" name="urgency" required style="border-radius: 10px;">
                                            <option value="Low">Low — General questions or minor items</option>
                                            <option value="Medium" selected>Medium — Standard technical support</option>
                                            <option value="High">High — Production website error</option>
                                            <option value="Critical">Critical — Complete server outage</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Related Service (Optional) -->
                                <div class="cf-ticket-form-group">
                                    <label class="cf-ticket-input-label">Related Service / Domain <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                    <select class="form-select" name="service" style="border-radius: 10px;">
                                        <option value="">None / General Inquiry</option>
                                        <?php foreach ($clientServices as $cs): ?>
                                            <option value="<?php echo htmlspecialchars($cs['domain']); ?>">
                                                <?php echo htmlspecialchars($cs['product_name']); ?> — <?php echo htmlspecialchars($cs['domain']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Subject -->
                                <div class="cf-ticket-form-group">
                                    <label class="cf-ticket-input-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="subject" placeholder="Brief summary of your inquiry (e.g. SSL installation issue)" required style="border-radius: 10px;">
                                </div>

                                <!-- Detailed Message -->
                                <div class="cf-ticket-form-group">
                                    <label class="cf-ticket-input-label">Message Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="message" rows="7" placeholder="Please provide detailed information, error messages, domain names, or steps to reproduce the issue..." required style="border-radius: 12px;"></textarea>
                                </div>

                                <!-- Attachments -->
                                <div class="cf-ticket-form-group mb-4">
                                    <label class="cf-ticket-input-label mb-1">
                                        <i class="las la-paperclip me-1"></i> Attach Screenshots or Log Files <span class="text-muted fw-normal fs-11">(Optional, max 15MB)</span>
                                    </label>
                                    <input type="file" class="form-control form-control-sm" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt,.log,.zip" style="border-radius: 10px;">
                                    <small class="text-muted fs-11 d-block mt-1">Allowed formats: PNG, JPG, PDF, TXT, LOG, ZIP.</small>
                                </div>

                                <!-- Submit Button Row -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-3 border-top">
                                    <a href="<?php echo $url; ?>ticket" class="btn btn-light border rounded-pill px-4 fs-13 fw-semibold">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                                        <i class="las la-paper-plane me-1.5"></i> Submit Support Ticket
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right Tips Column -->
                    <div class="col-lg-4">
                        <div class="cf-dashboard-card p-4 mb-4">
                            <h6 class="fw-bold text-slate-900 mb-3 pb-2.5 border-bottom fs-14">
                                <i class="las la-lightbulb text-warning me-1.5"></i> Tips for Faster Resolution
                            </h6>
                            <div class="fs-13 text-secondary">
                                <ul class="ps-3 mb-0 d-flex flex-column gap-2.5">
                                    <li>Provide the exact <strong>domain name</strong> or server IP involved.</li>
                                    <li>Include full <strong>error messages</strong> or screenshots of the issue.</li>
                                    <li>Mention any recent changes made (DNS records, plugins, code).</li>
                                    <li>For email delivery issues, provide sample sender and recipient addresses.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="cf-dashboard-card p-4">
                            <div class="d-flex align-items-center gap-2 mb-2.5">
                                <i class="las la-shield-alt text-primary fs-22"></i>
                                <h6 class="fw-bold text-slate-900 mb-0 fs-14">Average Response Times</h6>
                            </div>
                            <div class="fs-12.5 text-secondary">
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span>Critical Server Outages:</span>
                                    <strong class="text-danger">&lt; 15 Minutes</strong>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span>High Priority:</span>
                                    <strong class="text-slate-800">&lt; 45 Minutes</strong>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span>General &amp; Technical:</span>
                                    <strong class="text-slate-800">&lt; 2 Hours</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <!-- =================================================================
                 VIEW 3: TICKETS LIST (DEFAULT)
                 ================================================================= -->
            <?php else: ?>

                <!-- Stats Counters Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="cf-stat-card">
                            <div class="cf-stat-icon" style="background: #EFF6FF; color: #2563EB;">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo count($allTickets); ?></div>
                                <div class="fs-12.5 text-secondary fw-semibold">Total Tickets</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="cf-stat-card">
                            <div class="cf-stat-icon" style="background: #ECFDF5; color: #059669;">
                                <i class="las la-clock"></i>
                            </div>
                            <div>
                                <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo $statOpen; ?></div>
                                <div class="fs-12.5 text-secondary fw-semibold">Active / Open</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="cf-stat-card">
                            <div class="cf-stat-icon" style="background: #FAF5FF; color: #9333EA;">
                                <i class="las la-reply"></i>
                            </div>
                            <div>
                                <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo $statAnswered; ?></div>
                                <div class="fs-12.5 text-secondary fw-semibold">Staff Answered</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="cf-stat-card">
                            <div class="cf-stat-icon" style="background: #F1F5F9; color: #64748B;">
                                <i class="las la-check-circle"></i>
                            </div>
                            <div>
                                <div class="fs-24 fw-800 text-slate-900 lh-1 mb-1"><?php echo $statClosed; ?></div>
                                <div class="fs-12.5 text-secondary fw-semibold">Resolved / Closed</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card: Tickets List Table -->
                <div class="cf-dashboard-card p-0 overflow-hidden">
                    <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="cf-stat-icon" style="width: 36px; height: 36px; background: #EFF6FF; color: #2563EB; font-size: 18px; border-radius: 10px;">
                                <i class="las la-list"></i>
                            </div>
                            <h5 class="fw-bold text-slate-900 mb-0 fs-16">
                                Support Inquiries (<?php echo count($allTickets); ?>)
                            </h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="ticketSearchInput" class="form-control form-control-sm rounded-pill px-3 py-1.5" placeholder="Search tickets..." style="max-width: 220px;" onkeyup="filterTicketRows()">
                            <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-bold shadow-sm">
                                <i class="las la-plus me-1"></i> New Ticket
                            </a>
                        </div>
                    </div>

                    <div>
                        <?php if (empty($allTickets)): ?>
                            <div class="text-center py-5 px-3">
                                <div class="fs-48 text-muted mb-2"><i class="las la-headset"></i></div>
                                <h5 class="fw-bold text-slate-800">No Support Tickets Yet</h5>
                                <p class="text-secondary fs-13 mb-3">All your cloud systems are functioning optimally! Have a question or need assistance?</p>
                                <a href="<?php echo $url; ?>ticket?action=new" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="las la-plus-circle me-1"></i> Open Your First Ticket
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cf-dash-table mb-0" id="ticketsTable">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Subject / Summary</th>
                                            <th>Department</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allTickets as $t): ?>
                                            <tr class="ticket-row" data-search="<?php echo htmlspecialchars(strtolower($t['tid'] . ' ' . $t['title'] . ' ' . ($t['dept_name'] ?? '') . ' ' . $t['status'])); ?>">
                                                <td>
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $t['id']; ?>" class="fw-bold text-primary text-decoration-none">
                                                        #<?php echo htmlspecialchars($t['tid'] ?: $t['id']); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $t['id']; ?>" class="fw-bold text-slate-900 text-decoration-none hover-primary d-block">
                                                        <?php echo htmlspecialchars($t['title']); ?>
                                                    </a>
                                                    <?php if (!empty($t['service'])): ?>
                                                        <small class="text-muted fs-11"><i class="las la-server me-0.5"></i> <?php echo htmlspecialchars($t['service']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="text-secondary"><?php echo htmlspecialchars($t['dept_name'] ?? 'General'); ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getTicketUrgencyBadge($t['urgency']); ?>
                                                </td>
                                                <td>
                                                    <?php echo getTicketStatusBadge($t['status']); ?>
                                                </td>
                                                <td>
                                                    <span class="text-secondary fs-12"><?php echo date('M d, Y H:i', strtotime($t['lastreply'])); ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?php echo $url; ?>ticket?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-12 fw-semibold">
                                                        <i class="las la-eye me-1"></i> View &amp; Reply
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== Javascript Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== Javascript Area =========== -->

    <script>
    function filterTicketRows() {
        const query = (document.getElementById('ticketSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.ticket-row');
        rows.forEach(r => {
            const data = r.getAttribute('data-search') || '';
            r.style.display = (!query || data.includes(query)) ? '' : 'none';
        });
    }
    </script>

</body>
</html>
