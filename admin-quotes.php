<?php
/**
 * CloudFlox Server RFQ Admin Management Console
 * -------------------------------------------------------------------------
 * Allows administrators to:
 * 1. View incoming server custom quotation requests
 * 2. Review hardware specifications, customer budget & notes
 * 3. Set tailored pricing and formally deliver quotations to customers
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

include 'include/url.inc.php';

// Database connection
$mysqli = null;
try {
    $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset('utf8');
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// Fetch all quotes
$quotes = [];
$res = $mysqli->query("SELECT id, subject, stage, validuntil, userid, firstname, lastname, companyname, email, phonenumber, country, subtotal, total, proposal, customernotes, adminnotes, datecreated FROM tblquotes ORDER BY id DESC LIMIT 50");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $quotes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Quotations Admin Console — CloudFlox</title>
    <meta name="robots" content="noindex, nofollow">

    <?php include 'include/css.inc.php'; ?>

    <style>
        /* Admin Page Canvas & Container Spacing */
        .cf-admin-wrap {
            background: #F8FAFC !important;
            min-height: 100vh;
            padding: 42px 0 80px !important;
        }
        .cf-admin-container {
            max-width: 1320px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        /* Top Admin Meta Badge (Zero Orange Leakage) */
        .cf-admin-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px !important;
            border-radius: 999px;
            background: #EFF6FF !important;
            color: #1D4ED8 !important;
            border: 1.5px solid #BFDBFE !important;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .cf-admin-meta-badge i {
            color: #2563EB !important;
            font-size: 14px;
        }

        /* Top Action Buttons */
        .cf-btn-admin-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FFFFFF !important;
            color: #334155 !important;
            border: 1.5px solid #CBD5E1 !important;
            border-radius: 999px;
            padding: 9px 20px !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }
        .cf-btn-admin-outline:hover {
            background: #F1F5F9 !important;
            border-color: #94A3B8 !important;
            color: #0F172A !important;
            transform: translateY(-1px);
        }
        .cf-btn-admin-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%) !important;
            color: #FFFFFF !important;
            border: none;
            border-radius: 999px;
            padding: 9px 22px !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        }
        .cf-btn-admin-primary:hover {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%) !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.38);
        }

        /* 3 Stat Metric Cards */
        .cf-stat-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 18px;
            padding: 24px 28px !important;
            box-shadow: 0 4px 20px -6px rgba(15, 23, 42, 0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .cf-stat-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
            transform: translateY(-2px);
        }
        .cf-stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .cf-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .cf-stat-icon-amber {
            background: #FEF3C7;
            color: #D97706;
            border: 1px solid #FDE68A;
        }
        .cf-stat-icon-cyan {
            background: #E0F2FE;
            color: #0284C7;
            border: 1px solid #BAE6FD;
        }
        .cf-stat-icon-emerald {
            background: #DCFCE7;
            color: #059669;
            border: 1px solid #BBF7D0;
        }
        .cf-stat-number {
            font-size: 34px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .cf-stat-unit {
            font-size: 14px;
            font-weight: 600;
            color: #64748B;
        }
        .cf-stat-footer {
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #F1F5F9;
        }
        .cf-stat-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .cf-stat-dot.bg-amber { background: #F59E0B; }
        .cf-stat-dot.bg-cyan { background: #06B6D4; }
        .cf-stat-dot.bg-emerald { background: #10B981; }

        /* Main Table Card */
        .cf-admin-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 20px;
            box-shadow: 0 4px 24px -6px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
        .cf-admin-card-header {
            padding: 22px 28px !important;
            background: #FFFFFF;
            border-bottom: 1.5px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .cf-admin-table {
            table-layout: fixed;
            width: 100%;
            margin-bottom: 0 !important;
            border-collapse: separate;
            border-spacing: 0;
        }
        .cf-admin-table th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748B;
            background: #F8FAFC;
            border-bottom: 1.5px solid #E2E8F0;
            padding: 14px 20px !important;
            vertical-align: middle;
        }
        .cf-admin-table td {
            font-size: 13.5px;
            padding: 18px 20px !important;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            color: #334155;
            transition: background 0.15s ease;
        }
        .cf-admin-table tbody tr:hover td {
            background: #F8FAFC !important;
        }

        /* RFQ Reference Pill (Clean Monospace Blue) */
        .cf-rfq-ref-pill {
            display: inline-block;
            background: #EFF6FF !important;
            color: #1D4ED8 !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
            font-weight: 800;
            padding: 4px 10px !important;
            border-radius: 8px;
            border: 1px solid #BFDBFE !important;
        }

        /* Status Pills */
        .cf-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .cf-status-draft {
            background: #FEF3C7 !important;
            color: #B45309 !important;
            border: 1px solid #FDE68A !important;
        }
        .cf-status-delivered {
            background: #E0F2FE !important;
            color: #0369A1 !important;
            border: 1px solid #BAE6FD !important;
        }
        .cf-status-accepted {
            background: #DCFCE7 !important;
            color: #15803D !important;
            border: 1px solid #BBF7D0 !important;
        }

        /* Table Action Buttons */
        .cf-btn-table-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 16px !important;
            border-radius: 999px;
            font-size: 12px !important;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            cursor: pointer;
        }
        .cf-btn-action-draft {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%) !important;
            color: #FFFFFF !important;
            border: none;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }
        .cf-btn-action-draft:hover {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%) !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .cf-btn-action-delivered {
            background: #FFFFFF !important;
            color: #0284C7 !important;
            border: 1.5px solid #BAE6FD !important;
        }
        .cf-btn-action-delivered:hover {
            background: #E0F2FE !important;
            border-color: #7DD3FC !important;
            color: #0369A1 !important;
        }
        .cf-btn-action-accepted {
            background: #FFFFFF !important;
            color: #059669 !important;
            border: 1.5px solid #BBF7D0 !important;
        }
        .cf-btn-action-accepted:hover {
            background: #DCFCE7 !important;
            border-color: #86EFAC !important;
            color: #15803D !important;
        }

        /* Modal Polish */
        .cf-modal-box {
            border-radius: 22px !important;
            overflow: hidden;
            border: none !important;
            box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.25) !important;
        }
        .cf-modal-header {
            padding: 22px 28px !important;
            background: #FFFFFF;
            border-bottom: 1.5px solid #F1F5F9;
        }
        .cf-modal-body {
            padding: 28px !important;
            background: #FFFFFF;
        }
        .cf-modal-footer {
            padding: 18px 28px !important;
            background: #F8FAFC;
            border-top: 1.5px solid #E2E8F0;
        }
    </style>
</head>

<body class="pure-white-theme">

    <?php include 'include/header.inc.php'; ?>

    <div class="cf-admin-wrap">
        <div class="cf-admin-container">

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb custom-breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $url; ?>" class="text-decoration-none text-muted">
                            <i class="las la-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">NOC Administration</span>
                    </li>
                    <li class="breadcrumb-item active text-cyan fw-semibold" aria-current="page">Server Quotations</li>
                </ol>
            </nav>

            <!-- Admin Title Bar (Clean Layout & Clear Alignment) -->
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-2">
                <div>
                    <div class="cf-admin-meta-badge mb-2">
                        <i class="las la-shield-alt"></i>
                        <span>CLOUDFLOX NOC ADMINISTRATION CONSOLE</span>
                    </div>
                    <h1 class="fw-800 text-slate-900 tracking-tight mb-1" style="font-size: 28px; line-height: 1.25;">
                        Server Quotations &amp; RFQ Manager
                    </h1>
                    <p class="text-secondary fs-14 mb-0" style="max-width: 720px;">
                        Review custom compute, storage, and networking requirements submitted by clients, configure tailored pricing, and dispatch official binding quotations.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2.5 flex-wrap">
                    <a href="<?php echo $url; ?>cfwhmcs/admin/quotes.php" class="cf-btn-admin-outline">
                        <i class="las la-external-link-alt text-primary"></i>
                        <span>WHMCS Admin Quotes</span>
                    </a>
                    <a href="<?php echo $url; ?>request-quote" class="cf-btn-admin-primary">
                        <i class="las la-plus"></i>
                        <span>New RFQ Form</span>
                    </a>
                </div>
            </div>

            <!-- Stats Overview Metric Cards -->
            <?php
            $countDraft = 0;
            $countDelivered = 0;
            $countAccepted = 0;
            foreach ($quotes as $q) {
                $st = strtolower($q['stage']);
                if ($st === 'draft') $countDraft++;
                elseif ($st === 'delivered') $countDelivered++;
                elseif ($st === 'accepted') $countAccepted++;
            }
            ?>
            <div class="row g-3 g-xl-4 mb-4 pb-1">
                <!-- Card 1: Pending Review (Drafts) -->
                <div class="col-12 col-md-4">
                    <div class="cf-stat-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="cf-stat-label">Pending Review (Drafts)</span>
                                <div class="cf-stat-icon cf-stat-icon-amber">
                                    <i class="las la-hourglass-half"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h3 class="cf-stat-number"><?php echo $countDraft; ?></h3>
                                <span class="cf-stat-unit">Requests Pending</span>
                            </div>
                        </div>
                        <div class="cf-stat-footer text-warning">
                            <span class="cf-stat-dot bg-amber"></span>
                            <span>Awaiting engineer review &amp; pricing</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Delivered to Clients -->
                <div class="col-12 col-md-4">
                    <div class="cf-stat-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="cf-stat-label">Delivered to Clients</span>
                                <div class="cf-stat-icon cf-stat-icon-cyan">
                                    <i class="las la-paper-plane"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h3 class="cf-stat-number"><?php echo $countDelivered; ?></h3>
                                <span class="cf-stat-unit">Quotations Sent</span>
                            </div>
                        </div>
                        <div class="cf-stat-footer text-cyan">
                            <span class="cf-stat-dot bg-cyan"></span>
                            <span>Sent to client &bull; Awaiting acceptance</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Accepted & Invoiced -->
                <div class="col-12 col-md-4">
                    <div class="cf-stat-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="cf-stat-label">Accepted &amp; Invoiced</span>
                                <div class="cf-stat-icon cf-stat-icon-emerald">
                                    <i class="las la-check-circle"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h3 class="cf-stat-number"><?php echo $countAccepted; ?></h3>
                                <span class="cf-stat-unit">Orders Converted</span>
                            </div>
                        </div>
                        <div class="cf-stat-footer text-success">
                            <span class="cf-stat-dot bg-emerald"></span>
                            <span>Converted to invoice &bull; Ready to deploy</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotes Table Card -->
            <div class="cf-admin-card">
                <div class="cf-admin-card-header">
                    <div>
                        <h5 class="fw-800 text-slate-900 mb-0 fs-17">Incoming Server Quotation Requests</h5>
                        <p class="text-secondary fs-12 mb-0 mt-0.5">Click "Set Price &amp; Send" to specify client billing and dispatch formal quotation.</p>
                    </div>
                    <span class="badge bg-slate-100 text-slate-700 border px-3 py-1.5 rounded-pill fs-12 fw-bold">
                        <?php echo count($quotes); ?> Total Records
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="cf-admin-table">
                        <colgroup>
                            <col style="width: 10%;">
                            <col style="width: 24%;">
                            <col style="width: 28%;">
                            <col style="width: 12%;">
                            <col style="width: 12%;">
                            <col style="width: 14%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>RFQ #</th>
                                <th>Customer Details</th>
                                <th>Requested Infrastructure</th>
                                <th>Requested On</th>
                                <th>Status</th>
                                <th class="text-end">Price &amp; Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($quotes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="las la-inbox fs-40 d-block mb-2 text-slate-400"></i>
                                        No server quote requests received yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($quotes as $q): 
                                    $st = strtolower($q['stage']);
                                    $statusPillClass = 'cf-status-draft';
                                    $btnClass = 'cf-btn-action-draft';
                                    $btnText = '<i class="las la-tag me-1"></i> Review &amp; Price';
                                    $dotClass = 'bg-amber';

                                    if ($st === 'delivered') {
                                        $statusPillClass = 'cf-status-delivered';
                                        $btnClass = 'cf-btn-action-delivered';
                                        $btnText = '<i class="las la-edit me-1"></i> View / Re-Price';
                                        $dotClass = 'bg-cyan';
                                    } elseif ($st === 'accepted') {
                                        $statusPillClass = 'cf-status-accepted';
                                        $btnClass = 'cf-btn-action-accepted';
                                        $btnText = '<i class="las la-check-circle me-1"></i> View Accepted';
                                        $dotClass = 'bg-emerald';
                                    }
                                ?>
                                    <tr id="row-quote-<?php echo $q['id']; ?>">
                                        <!-- RFQ Ref -->
                                        <td>
                                            <span class="cf-rfq-ref-pill">#<?php echo $q['id']; ?></span>
                                        </td>

                                        <!-- Customer Details -->
                                        <td>
                                            <div class="fw-800 text-slate-900 fs-14 mb-0.5">
                                                <?php echo htmlspecialchars(trim($q['firstname'] . ' ' . $q['lastname']) ?: 'Client #' . $q['userid']); ?>
                                            </div>
                                            <div class="fs-12 text-slate-600 d-flex align-items-center gap-1">
                                                <i class="las la-envelope text-slate-400 fs-13"></i>
                                                <span><?php echo htmlspecialchars($q['email']); ?></span>
                                            </div>
                                            <?php if (!empty($q['phonenumber'])): ?>
                                                <div class="fs-11 text-slate-500 d-flex align-items-center gap-1 mt-0.5">
                                                    <i class="las la-phone text-slate-400 fs-12"></i>
                                                    <span><?php echo htmlspecialchars($q['phonenumber']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Requested Architecture -->
                                        <td>
                                            <div class="fw-700 text-slate-800 fs-13.5 mb-1 text-truncate" title="<?php echo htmlspecialchars($q['subject']); ?>">
                                                <?php echo htmlspecialchars($q['subject'] ?: 'Custom Hardware Infrastructure'); ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 fs-11 text-slate-500">
                                                <span class="badge bg-slate-100 text-slate-600 border px-2 py-0.5 rounded">Client ID: #<?php echo $q['userid']; ?></span>
                                                <span><i class="las la-globe me-0.5 text-slate-400"></i> <?php echo htmlspecialchars($q['country'] ?: 'LK'); ?></span>
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td>
                                            <span class="fs-13 fw-semibold text-slate-700">
                                                <?php echo date('M d, Y', strtotime($q['datecreated'])); ?>
                                            </span>
                                        </td>

                                        <!-- Stage Status -->
                                        <td>
                                            <span class="cf-status-pill <?php echo $statusPillClass; ?>">
                                                <span class="cf-stat-dot <?php echo $dotClass; ?>"></span>
                                                <span><?php echo htmlspecialchars(ucfirst($q['stage'] ?: 'Draft')); ?></span>
                                            </span>
                                        </td>

                                        <!-- Price & Actions -->
                                        <td class="text-end">
                                            <div class="d-flex flex-column align-items-end gap-1.5">
                                                <?php if ((float)$q['total'] > 0): ?>
                                                    <div class="d-flex align-items-baseline gap-1">
                                                        <strong class="fs-15 fw-800 text-slate-900">$<?php echo number_format((float)$q['total'], 2); ?></strong>
                                                        <span class="fs-11 text-secondary fw-semibold">USD</span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="fs-12 text-muted fst-italic">Pending Price</span>
                                                <?php endif; ?>

                                                <button type="button" class="cf-btn-table-action <?php echo $btnClass; ?>" onclick="openAdminQuoteModal(<?php echo $q['id']; ?>)">
                                                    <?php echo $btnText; ?>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- ADMIN MODAL: REVIEW SPECS & DELIVER QUOTE -->
    <div class="modal fade" id="adminQuoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content cf-modal-box">
                <div class="cf-modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                            <i class="las la-server"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 text-slate-900 mb-0" id="aqModalTitle">Review Quote #RFQ</h5>
                            <span class="fs-12 text-secondary" id="aqModalSubtitle">Client Specifications &amp; Pricing Setup</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="cf-modal-body">
                    <!-- Loading Spinner -->
                    <div id="aqModalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="fs-13 text-secondary mt-2">Loading proposal details...</p>
                    </div>

                    <!-- Content -->
                    <div id="aqModalContent" style="display: none;">
                        <!-- Client Contact Row -->
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="row g-2 fs-13">
                                <div class="col-sm-6">
                                    <strong class="text-secondary">Client:</strong> <span id="aqClientName" class="text-slate-900 fw-bold"></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong class="text-secondary">Email:</strong> <span id="aqClientEmail" class="text-slate-900"></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong class="text-secondary">Phone:</strong> <span id="aqClientPhone" class="text-slate-900"></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong class="text-secondary">Status:</strong> <span id="aqClientStage" class="badge bg-secondary"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Table -->
                        <h6 class="fw-bold text-slate-900 mb-2">Requested Hardware Architecture:</h6>
                        <div class="border rounded-3 p-3 bg-white mb-3" style="max-height: 220px; overflow-y: auto;">
                            <pre id="aqProposalText" class="mb-0 fs-13 text-slate-800" style="white-space: pre-wrap; font-family: inherit;"></pre>
                        </div>

                        <!-- Pricing & Delivery Form -->
                        <div class="p-3.5 border rounded-3 bg-light">
                            <h6 class="fw-bold text-slate-900 mb-3"><i class="las la-tag text-primary me-1"></i> Admin Pricing &amp; Formal Delivery:</h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fs-13 fw-bold text-slate-800">Quoted Price ($ USD / month) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold bg-white text-slate-700">$</span>
                                        <input type="number" step="0.01" min="1" class="form-control fw-bold fs-16 text-slate-900" id="aqInputPrice" placeholder="e.g. 49.00" required>
                                        <span class="input-group-text bg-white text-slate-700">USD</span>
                                    </div>
                                    <small class="text-muted fs-11">The total amount the client will be invoiced upon acceptance.</small>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fs-13 fw-bold text-slate-800">Quotation Valid Until</label>
                                    <input type="date" class="form-control" id="aqInputValidUntil">
                                    <small class="text-muted fs-11">Default is 30 days from today.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fs-13 fw-bold text-slate-800">Admin Remarks / Notes (Optional)</label>
                                    <input type="text" class="form-control" id="aqInputNotes" placeholder="e.g. Applied 15% annual enterprise discount. Port speed upgraded to 1 Gbps free.">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cf-modal-footer d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fs-13 fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="cf-btn-admin-primary px-4 py-2" id="aqBtnDeliver" onclick="submitAdminDeliverQuote()">
                        <i class="las la-paper-plane me-1"></i> Deliver Quotation to Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/footer.inc.php'; ?>
    <?php include 'include/js.inc.php'; ?>

    <script>
        let currentAdminQuoteId = 0;

        function openAdminQuoteModal(quoteId) {
            currentAdminQuoteId = quoteId;
            const modalEl = document.getElementById('adminQuoteModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            document.getElementById('aqModalLoading').style.display = 'block';
            document.getElementById('aqModalContent').style.display = 'none';
            document.getElementById('aqModalTitle').innerText = 'Review Quote #RFQ-' + quoteId;

            fetch('api/quote-manage.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'admin_get_quote', quote_id: quoteId })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('aqModalLoading').style.display = 'none';
                if (data.status === 'success' && data.quote) {
                    const q = data.quote;
                    document.getElementById('aqModalContent').style.display = 'block';
                    document.getElementById('aqClientName').innerText = q.client_name;
                    document.getElementById('aqClientEmail').innerText = q.email;
                    document.getElementById('aqClientPhone').innerText = q.phone || 'N/A';
                    document.getElementById('aqClientStage').innerText = q.stage;
                    document.getElementById('aqProposalText').innerText = q.proposal;

                    // Set default price or existing
                    const existingTotal = parseFloat(q.total.replace(/,/g, ''));
                    document.getElementById('aqInputPrice').value = existingTotal > 0 ? existingTotal.toFixed(2) : '';
                    document.getElementById('aqInputValidUntil').value = q.validuntil;
                } else {
                    alert(data.message || 'Unable to load quote details.');
                }
            })
            .catch(err => {
                document.getElementById('aqModalLoading').style.display = 'none';
                alert('Connection error loading quote.');
            });
        }

        function submitAdminDeliverQuote() {
            const price = parseFloat(document.getElementById('aqInputPrice').value);
            const validUntil = document.getElementById('aqInputValidUntil').value;
            const notes = document.getElementById('aqInputNotes').value;

            if (isNaN(price) || price <= 0) {
                alert('Please enter a valid quotation price greater than $0.00.');
                document.getElementById('aqInputPrice').focus();
                return;
            }

            const btn = document.getElementById('aqBtnDeliver');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Delivering...';

            fetch('api/quote-manage.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'admin_deliver_quote',
                    quote_id: currentAdminQuoteId,
                    price: price,
                    valid_until: validUntil,
                    admin_notes: notes
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Error delivering quotation.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert('Server connection error.');
            });
        }
    </script>
</body>
</html>
