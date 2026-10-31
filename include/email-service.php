<?php
/**
 * CloudFlox Automated Email Service
 * -------------------------------------------------------------------------
 * Handles transactional, automated emails for:
 * 1. Hosting Account Provisioning & cPanel Credentials
 * 2. Domain Registration & WHOIS Activation
 * 3. Invoice Payment Receipts & Settled Orders
 */

require_once __DIR__ . '/send-mail/SMTP.php';
require_once __DIR__ . '/send-mail/PHPMailer.php';
require_once __DIR__ . '/send-mail/Exception.php';
require_once __DIR__ . '/url.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CloudFloxEmailService {

    /**
     * Build and configure an authenticated PHPMailer instance
     */
    public static function getMailer() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'mail.cloudflox.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@cloudflox.com';
        $mail->Password   = 'TC.jk1mnPoccDTf=';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->Timeout    = 10;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];
        $mail->setFrom('info@cloudflox.com', 'CloudFlox LLC Hosting');
        $mail->addReplyTo('support@cloudflox.com', 'CloudFlox Support');
        return $mail;
    }

    /**
     * Settle an invoice, activate all associated services/domains, and dispatch welcome credentials email
     */
    public static function settleAndActivateInvoice($invoiceId, $mysqli = null) {
        $invoiceId = intval($invoiceId);
        if ($invoiceId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid invoice ID.'];
        }

        $closeDb = false;
        if (!$mysqli) {
            $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
            if ($mysqli->connect_error) {
                return ['status' => 'error', 'message' => 'Database connection failed: ' . $mysqli->connect_error];
            }
            $mysqli->set_charset('utf8mb4');
            $closeDb = true;
        }

        $now   = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // 1. Mark Invoice as Paid
        $upd = $mysqli->prepare("UPDATE tblinvoices SET status = 'Paid', datepaid = ?, updated_at = ? WHERE id = ?");
        if ($upd) {
            $upd->bind_param('ssi', $now, $now, $invoiceId);
            $upd->execute();
            $upd->close();
        }

        // 2. Mark Associated Order as Active
        $ordUpd = $mysqli->prepare("UPDATE tblorders SET status = 'Active' WHERE invoiceid = ?");
        if ($ordUpd) {
            $ordUpd->bind_param('i', $invoiceId);
            $ordUpd->execute();
            $ordUpd->close();
        }

        // 3. Mark Hosting Service as Active
        $hostUpd = $mysqli->prepare("UPDATE tblhosting SET domainstatus = 'Active', updated_at = ? WHERE orderid IN (SELECT id FROM tblorders WHERE invoiceid = ?) OR (userid IN (SELECT userid FROM tblinvoices WHERE id = ?) AND regdate = ? AND domainstatus = 'Pending')");
        if ($hostUpd) {
            $hostUpd->bind_param('siis', $now, $invoiceId, $invoiceId, $today);
            $hostUpd->execute();
            $hostUpd->close();
        }

        // 4. Mark Domain Service as Active
        $domUpd = $mysqli->prepare("UPDATE tbldomains SET status = 'Active', updated_at = ? WHERE orderid IN (SELECT id FROM tblorders WHERE invoiceid = ?) OR (userid IN (SELECT userid FROM tblinvoices WHERE id = ?) AND registrationdate = ? AND status = 'Pending')");
        if ($domUpd) {
            $domUpd->bind_param('siis', $now, $invoiceId, $invoiceId, $today);
            $domUpd->execute();
            $domUpd->close();
        }

        // 5. Dispatch Welcome & Credentials Email
        $mailResult = self::sendOrderWelcomeAndCredentials($invoiceId, $mysqli);

        if ($closeDb) {
            $mysqli->close();
        }

        return [
            'status'      => 'success',
            'invoice_id'  => $invoiceId,
            'mail_result' => $mailResult
        ];
    }

    /**
     * Dispatch the official CloudFlox Welcome & cPanel Credentials Email
     */
    public static function sendOrderWelcomeAndCredentials($invoiceId, $mysqli = null) {
        $invoiceId = intval($invoiceId);
        if ($invoiceId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid invoice ID.'];
        }

        $closeDb = false;
        if (!$mysqli) {
            $mysqli = @new mysqli('127.0.0.1', 'cfwhmcs_whmcs', '+V)Rb%K3wFiK', 'cfwhmcs_whmcs3');
            if ($mysqli->connect_error) {
                return ['status' => 'error', 'message' => 'Database connection failed'];
            }
            $mysqli->set_charset('utf8mb4');
            $closeDb = true;
        }

        // 1. Fetch Invoice
        $invStmt = $mysqli->prepare("SELECT * FROM tblinvoices WHERE id = ? LIMIT 1");
        $invoice = null;
        if ($invStmt) {
            $invStmt->bind_param('i', $invoiceId);
            $invStmt->execute();
            $invoice = $invStmt->get_result()->fetch_assoc();
            $invStmt->close();
        }

        if (!$invoice) {
            if ($closeDb) $mysqli->close();
            return ['status' => 'error', 'message' => 'Invoice not found'];
        }

        // Check for duplicate dispatch
        if (!empty($invoice['notes']) && strpos($invoice['notes'], '[EMAIL_DISPATCHED]') !== false) {
            if ($closeDb) $mysqli->close();
            return ['status' => 'skipped', 'message' => 'Email has already been dispatched for this invoice.'];
        }

        $userId = intval($invoice['userid']);

        // 2. Fetch Client Details
        $cliStmt = $mysqli->prepare("SELECT * FROM tblclients WHERE id = ? LIMIT 1");
        $client = null;
        if ($cliStmt) {
            $cliStmt->bind_param('i', $userId);
            $cliStmt->execute();
            $client = $cliStmt->get_result()->fetch_assoc();
            $cliStmt->close();
        }

        if (!$client || empty($client['email'])) {
            if ($closeDb) $mysqli->close();
            return ['status' => 'error', 'message' => 'Client email not found.'];
        }

        $clientEmail = trim($client['email']);
        $firstName   = trim($client['firstname'] ?? 'Valued');
        $lastName    = trim($client['lastname'] ?? 'Customer');
        $clientName  = trim($firstName . ' ' . $lastName);

        // 3. Fetch Associated Order
        $order = null;
        $ordStmt = $mysqli->prepare("SELECT * FROM tblorders WHERE invoiceid = ? ORDER BY id DESC LIMIT 1");
        if ($ordStmt) {
            $ordStmt->bind_param('i', $invoiceId);
            $ordStmt->execute();
            $order = $ordStmt->get_result()->fetch_assoc();
            $ordStmt->close();
        }
        $orderId = $order ? intval($order['id']) : 0;

        // 4. Fetch Hosting Accounts
        $hostingAccounts = [];
        $hostSql = "SELECT h.*, p.name as package_name, p.description as package_desc, s.ipaddress as server_ip, s.hostname as server_hostname 
                    FROM tblhosting h 
                    LEFT JOIN tblproducts p ON p.id = h.packageid 
                    LEFT JOIN tblservers s ON s.id = h.server 
                    WHERE (h.orderid = ? AND h.orderid > 0) OR (h.userid = ? AND h.regdate = ?)";
        $hostStmt = $mysqli->prepare($hostSql);
        if ($hostStmt) {
            $invDate = !empty($invoice['date']) ? $invoice['date'] : date('Y-m-d');
            $hostStmt->bind_param('iis', $orderId, $userId, $invDate);
            $hostStmt->execute();
            $hostingAccounts = $hostStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $hostStmt->close();
        }

        // 5. Fetch Domains
        $domains = [];
        $domSql = "SELECT * FROM tbldomains WHERE (orderid = ? AND orderid > 0) OR (userid = ? AND registrationdate = ?)";
        $domStmt = $mysqli->prepare($domSql);
        if ($domStmt) {
            $invDate = !empty($invoice['date']) ? $invoice['date'] : date('Y-m-d');
            $domStmt->bind_param('iis', $orderId, $userId, $invDate);
            $domStmt->execute();
            $domains = $domStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $domStmt->close();
        }

        // Build URLs
        global $url;
        $siteUrl = !empty($url) ? $url : 'https://cloudflox.com/';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https' : 'http';
        $hostHeader = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        if (substr($siteUrl, 0, 4) === 'http') {
            $fullSiteUrl = rtrim($siteUrl, '/') . '/';
        } else {
            $fullSiteUrl = $proto . '://' . $hostHeader . '/' . ltrim($siteUrl, '/');
            $fullSiteUrl = rtrim($fullSiteUrl, '/') . '/';
        }

        $invoiceUrl    = $fullSiteUrl . 'invoice?id=' . $invoiceId;
        $clientAreaUrl = $fullSiteUrl . 'clientarea';
        $hasHosting    = !empty($hostingAccounts);
        $hasDomain     = !empty($domains);

        // Subject line
        if ($hasHosting) {
            $primaryDom = $hostingAccounts[0]['domain'] ?? 'your domain';
            $subject = "🚀 Your CloudFlox Hosting Server is Ready! (cPanel Access Credentials - {$primaryDom})";
        } elseif ($hasDomain) {
            $primaryDom = $domains[0]['domain'] ?? 'your domain';
            $subject = "🌐 Domain Registration Confirmed - {$primaryDom} (CloudFlox LLC)";
        } else {
            $subject = "🧾 Invoice Payment Received & Settled - Invoice #{$invoiceId} (CloudFlox LLC)";
        }

        // Build HTML Body
        $html = self::renderEmailHtml([
            'clientName'      => $clientName,
            'firstName'       => $firstName,
            'invoice'         => $invoice,
            'hostingAccounts' => $hostingAccounts,
            'domains'         => $domains,
            'fullSiteUrl'     => $fullSiteUrl,
            'invoiceUrl'      => $invoiceUrl,
            'clientAreaUrl'   => $clientAreaUrl
        ]);

        try {
            $mail = self::getMailer();
            $mail->addAddress($clientEmail, $clientName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = "Hello {$firstName},\n\nYour CloudFlox order has been processed and invoice #{$invoiceId} is settled.\nPlease visit {$clientAreaUrl} to view your server details and cPanel credentials.\n\nThank you,\nCloudFlox Support";

            $sent = $mail->send();

            // Mark invoice as dispatched
            $updNotes = $mysqli->prepare("UPDATE tblinvoices SET notes = CONCAT(COALESCE(notes, ''), ' [EMAIL_DISPATCHED]') WHERE id = ?");
            if ($updNotes) {
                $updNotes->bind_param('i', $invoiceId);
                $updNotes->execute();
                $updNotes->close();
            }

            if ($closeDb) $mysqli->close();

            return [
                'status'  => 'success',
                'message' => 'Credentials email successfully dispatched to ' . $clientEmail
            ];
        } catch (Exception $e) {
            if ($closeDb) $mysqli->close();
            return [
                'status'  => 'error',
                'message' => 'Mailer error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Render the high-converting, responsive HTML email template
     */
    private static function renderEmailHtml(array $data) {
        $clientName      = htmlspecialchars($data['clientName']);
        $firstName       = htmlspecialchars($data['firstName']);
        $invoice         = $data['invoice'];
        $hostingAccounts = $data['hostingAccounts'];
        $domains         = $data['domains'];
        $invoiceUrl      = htmlspecialchars($data['invoiceUrl']);
        $clientAreaUrl   = htmlspecialchars($data['clientAreaUrl']);

        $invoiceId       = htmlspecialchars($invoice['id']);
        $totalPaid       = '$' . number_format((float)($invoice['total'] ?? 0.00), 2);
        $datePaid        = !empty($invoice['datepaid']) && $invoice['datepaid'] !== '0000-00-00 00:00:00' 
                            ? date('M d, Y', strtotime($invoice['datepaid'])) 
                            : date('M d, Y');
        $paymentMethod   = ucfirst($invoice['paymentmethod'] ?? 'Online');

        // Hosting Section HTML
        $hostingHtml = '';
        if (!empty($hostingAccounts)) {
            foreach ($hostingAccounts as $h) {
                $domName   = htmlspecialchars($h['domain']);
                $pkgName   = htmlspecialchars($h['package_name'] ?: 'Cloud NVMe Hosting');
                $username  = htmlspecialchars($h['username'] ?: 'cf' . rand(100, 999));
                $serverIp  = htmlspecialchars($h['server_ip'] ?: '69.10.60.130');
                $hostname  = htmlspecialchars($h['server_hostname'] ?: 'sv.cloudflox.com');

                // Extract temporary password from notes if saved
                $tempPassword = 'Available in Client Area';
                if (!empty($h['notes']) && preg_match('/InitialPass:\s*([^\s\r\n]+)/', $h['notes'], $m)) {
                    $tempPassword = htmlspecialchars(trim($m[1]));
                }

                $hostingHtml .= "
                <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; border-radius: 8px; padding: 20px; margin-bottom: 24px;'>
                    <div style='display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;'>
                        <h3 style='margin: 0; color: #0f172a; font-size: 16px; font-weight: 800;'>
                            🚀 Hosting Service: <span style='color: #2563eb;'>{$domName}</span>
                        </h3>
                        <span style='background: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 999px; text-transform: uppercase;'>Active</span>
                    </div>
                    <table style='width: 100%; border-collapse: collapse; font-size: 13px; color: #334155;'>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600; width: 140px;'>Package Tier:</td>
                            <td style='padding: 8px 0; color: #0f172a; font-weight: 700;'>{$pkgName}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>cPanel Direct URL:</td>
                            <td style='padding: 8px 0;'>
                                <a href='https://sv.cloudflox.com:2083' target='_blank' style='color: #2563eb; font-weight: 800; text-decoration: none;'>https://sv.cloudflox.com:2083 &rarr;</a>
                            </td>
                        </tr>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>cPanel Username:</td>
                            <td style='padding: 8px 0;'>
                                <code style='background: #eef2ff; color: #1e40af; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 13px; font-weight: bold;'>{$username}</code>
                            </td>
                        </tr>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>Initial Password:</td>
                            <td style='padding: 8px 0;'>
                                <code style='background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 13px; font-weight: bold;'>{$tempPassword}</code>
                                <span style='color: #64748b; font-size: 11px; margin-left: 6px;'>(Reset anytime in Client Area)</span>
                            </td>
                        </tr>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>Server Hostname:</td>
                            <td style='padding: 8px 0; color: #0f172a; font-weight: 600;'>{$hostname} ({$serverIp})</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>Webmail Access:</td>
                            <td style='padding: 8px 0;'>
                                <a href='https://sv.cloudflox.com:2096' target='_blank' style='color: #2563eb; font-weight: 600; text-decoration: none;'>https://sv.cloudflox.com:2096</a>
                            </td>
                        </tr>
                    </table>
                    <div style='margin-top: 16px;'>
                        <a href='https://sv.cloudflox.com:2083' target='_blank' style='display: inline-block; background: #2563eb; color: #ffffff; padding: 10px 20px; font-size: 13px; font-weight: 700; border-radius: 6px; text-decoration: none;'>
                            Log In to cPanel Directly &rarr;
                        </a>
                    </div>
                </div>";
            }
        }

        // Domain Section HTML
        $domainHtml = '';
        if (!empty($domains)) {
            foreach ($domains as $d) {
                $dName   = htmlspecialchars($d['domain']);
                $period  = htmlspecialchars($d['registrationperiod'] ?: '1') . ' Year(s)';
                $expiry  = !empty($d['expirydate']) ? date('M d, Y', strtotime($d['expirydate'])) : '1 Year from today';
                $dStatus = htmlspecialchars($d['status'] ?: 'Active');

                $domainHtml .= "
                <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 8px; padding: 20px; margin-bottom: 24px;'>
                    <div style='display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;'>
                        <h3 style='margin: 0; color: #0f172a; font-size: 16px; font-weight: 800;'>
                            🌐 Domain: <span style='color: #059669;'>{$dName}</span>
                        </h3>
                        <span style='background: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 999px; text-transform: uppercase;'>{$dStatus}</span>
                    </div>
                    <table style='width: 100%; border-collapse: collapse; font-size: 13px; color: #334155;'>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600; width: 140px;'>Registration Term:</td>
                            <td style='padding: 8px 0; color: #0f172a; font-weight: 700;'>{$period}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #edf2f7;'>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>Next Expiry Date:</td>
                            <td style='padding: 8px 0; color: #0f172a; font-weight: 700;'>{$expiry}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #64748b; font-weight: 600;'>Security Features:</td>
                            <td style='padding: 8px 0; color: #059669; font-weight: 700;'>✔ Free WHOIS Privacy &bull; Global Anycast DNS</td>
                        </tr>
                    </table>
                </div>";
            }
        }

        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Your CloudFlox Services Are Ready</title>
</head>
<body style='margin: 0; padding: 0; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;'>
    <div style='max-width: 620px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 30px 32px; border-bottom: 3px solid #2563eb;'>
            <div style='display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;'>
                <span style='color: #ffffff; font-size: 20px; font-weight: 900; letter-spacing: -0.5px;'>
                    Cloud<span style='color: #3b82f6;'>Flox</span>
                </span>
                <span style='background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 999px; border: 1px solid rgba(16, 185, 129, 0.3);'>
                    Payment Confirmed
                </span>
            </div>
            <h1 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; line-height: 1.3;'>
                Your Cloud Infrastructure is Provisioned &amp; Ready!
            </h1>
            <p style='color: #94a3b8; margin: 6px 0 0; font-size: 13px;'>
                Invoice Settled &bull; Service Activated &bull; 99.99% NVMe Uptime SLA
            </p>
        </div>

        <!-- Body Content -->
        <div style='padding: 32px; color: #334155; font-size: 14px; line-height: 1.6;'>
            
            <p style='margin-top: 0; font-size: 15px;'>
                Hello <strong>{$firstName}</strong>,
            </p>
            <p style='color: #475569;'>
                Thank you for your order with CloudFlox! Your transaction has been recorded, your invoice has been settled in full, and your services are now completely provisioned and operational.
            </p>

            <!-- Hosting Services -->
            {$hostingHtml}

            <!-- Nameservers Notice -->
            <div style='background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px;'>
                <strong style='color: #1e40af; font-size: 13px; display: block; margin-bottom: 6px;'>
                    📡 DNS Nameservers (Point your domain to CloudFlox):
                </strong>
                <div style='font-family: monospace; font-size: 13px; color: #1e3a8a;'>
                    &bull; Primary NS:&nbsp; <strong>ns1.cloudflox.com</strong><br>
                    &bull; Secondary NS: <strong>ns2.cloudflox.com</strong>
                </div>
            </div>

            <!-- Domain Services -->
            {$domainHtml}

            <!-- Invoice Receipt Table -->
            <div style='border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 26px;'>
                <div style='background: #f8fafc; padding: 12px 18px; border-bottom: 1px solid #e2e8f0;'>
                    <strong style='color: #0f172a; font-size: 13px;'>🧾 Invoice Payment Receipt Summary</strong>
                </div>
                <div style='padding: 16px 18px;'>
                    <table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                        <tr style='border-bottom: 1px solid #f1f5f9;'>
                            <td style='padding: 6px 0; color: #64748b;'>Invoice Number:</td>
                            <td style='padding: 6px 0; text-align: right; font-weight: 700; color: #0f172a;'>#{$invoiceId}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #f1f5f9;'>
                            <td style='padding: 6px 0; color: #64748b;'>Amount Settled:</td>
                            <td style='padding: 6px 0; text-align: right; font-weight: 800; color: #2563eb; font-size: 15px;'>{$totalPaid} USD</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #f1f5f9;'>
                            <td style='padding: 6px 0; color: #64748b;'>Payment Method:</td>
                            <td style='padding: 6px 0; text-align: right; color: #0f172a;'>{$paymentMethod}</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #64748b;'>Settlement Date:</td>
                            <td style='padding: 6px 0; text-align: right; color: #0f172a;'>{$datePaid}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Call to Action Buttons -->
            <div style='text-align: center; margin: 30px 0 10px;'>
                <a href='{$clientAreaUrl}' target='_blank' style='display: inline-block; background: #2563eb; color: #ffffff; padding: 13px 28px; font-size: 14px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 14px rgba(37,99,235,0.3);'>
                    Access Your Client Area Dashboard &rarr;
                </a>
                <div style='margin-top: 12px;'>
                    <a href='{$invoiceUrl}' target='_blank' style='color: #64748b; font-size: 12px; text-decoration: underline;'>
                        Download Official PDF Invoice Receipt
                    </a>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div style='background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6;'>
            <p style='margin: 0 0 4px;'>
                <strong>CloudFlox LLC</strong> &bull; Enterprise Cloud &amp; High-Speed NVMe Web Hosting
            </p>
            <p style='margin: 0;'>
                Need assistance? Our support engineers are available 24/7/365 at <a href='mailto:support@cloudflox.com' style='color: #2563eb; text-decoration: none;'>support@cloudflox.com</a> or via <a href='{$clientAreaUrl}?tab=tickets' style='color: #2563eb; text-decoration: none;'>Support Tickets</a>.
            </p>
        </div>

    </div>
</body>
</html>";
    }

    /**
     * Compute a fully qualified, absolute URL (with scheme, domain/port and base directory)
     * suitable for email hyperlinks both in localhost and production.
     */
    public static function getAbsoluteUrl($path = '') {
        global $url;
        $siteUrl = !empty($url) ? $url : '/';
        if (substr($siteUrl, 0, 4) === 'http') {
            $base = rtrim($siteUrl, '/') . '/';
        } else {
            $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https' : 'http';
            $hostHeader = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
            if (empty($hostHeader)) {
                $hostHeader = 'localhost:8080';
            } elseif (strpos($hostHeader, 'localhost') !== false || strpos($hostHeader, '127.0.0.1') !== false) {
                if (strpos($hostHeader, ':') === false && !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
                    $hostHeader .= ':' . $_SERVER['SERVER_PORT'];
                }
            }
            $base = $proto . '://' . $hostHeader . '/' . ltrim($siteUrl, '/');
            $base = rtrim($base, '/') . '/';
        }
        return $base . ltrim($path, '/');
    }

    /**
     * Dispatch branded Password Reset email via authenticated SMTP
     */
    public static function sendPasswordResetEmail($recipientEmail, $resetUrl, $clientName = '') {
        $recipientEmail = trim($recipientEmail);
        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid recipient email address.'];
        }

        // Ensure resetUrl is an absolute URL with scheme and host
        if (substr($resetUrl, 0, 4) !== 'http') {
            $resetUrl = self::getAbsoluteUrl($resetUrl);
        }

        $greetingName = !empty($clientName) ? htmlspecialchars($clientName) : 'Valued Customer';
        $safeUrl      = htmlspecialchars($resetUrl);
        $encodedEmail = htmlspecialchars($recipientEmail);

        try {
            $mail = self::getMailer();
            $mail->addAddress($recipientEmail, $clientName ?: 'CloudFlox Customer');
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your CloudFlox Account Password';
            $mail->Body    = self::buildPasswordResetHtml($greetingName, $safeUrl, $encodedEmail);
            $mail->AltBody = "Hello {$greetingName},\n\nWe received a request to reset your CloudFlox Client Portal account password.\n\nPlease visit the following link to reset your password:\n{$resetUrl}\n\nThis link will expire in 1 hour.\nIf you did not request this, please ignore this email.\n\nCloudFlox LLC Support Team\nsupport@cloudflox.com";

            $mail->send();
            return ['status' => 'success', 'message' => 'Password reset email dispatched successfully.'];
        } catch (Exception $e) {
            error_log('CloudFloxEmailService::sendPasswordResetEmail Exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Mail delivery failed: ' . $e->getMessage()];
        }
    }

    /**
     * Build branded HTML template for Password Reset
     */
    private static function buildPasswordResetHtml($greetingName, $safeUrl, $encodedEmail) {
        return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Your CloudFlox Password</title>
</head>
<body style='margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;'>
    <div style='max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 36px; text-align: center; color: #ffffff;'>
            <div style='font-size: 24px; font-weight: 900; letter-spacing: -0.5px;'>
                <span style='color: #ffffff;'>Cloud</span><span style='color: #3b82f6;'>Flox</span>
            </div>
            <p style='margin: 6px 0 0; color: #94a3b8; font-size: 13px; font-weight: 500;'>
                Account Security &amp; Access Recovery
            </p>
        </div>

        <!-- Body -->
        <div style='padding: 36px 36px 28px;'>
            <h2 style='margin: 0 0 16px; font-size: 20px; font-weight: 800; color: #0f172a;'>
                Reset Your Password
            </h2>
            <p style='margin: 0 0 16px; font-size: 14.5px; color: #334155; line-height: 1.6;'>
                Hello <strong>{$greetingName}</strong>,
            </p>
            <p style='margin: 0 0 24px; font-size: 14px; color: #475569; line-height: 1.6;'>
                We received a request to reset the password for your CloudFlox Client Portal account associated with <strong style='color: #0f172a;'>{$encodedEmail}</strong>.
            </p>

            <!-- Reset Button Box -->
            <div style='text-align: center; margin: 28px 0; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;'>
                <p style='margin: 0 0 16px; font-size: 13px; color: #64748b;'>
                    Click the button below to choose a new password:
                </p>
                <a href='{$safeUrl}' target='_blank' style='display: inline-block; background: #2563eb; color: #ffffff; padding: 13px 32px; font-size: 14.5px; font-weight: 700; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 14px rgba(37,99,235,0.3);'>
                    Set New Password &rarr;
                </a>
            </div>

            <!-- Expiry Warning -->
            <div style='background: #eff6ff; border-left: 4px solid #3b82f6; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px;'>
                <p style='margin: 0; font-size: 13px; color: #1e40af; line-height: 1.5;'>
                    <strong>Security Notice:</strong> This link is single-use and will automatically expire in <strong>60 minutes</strong>. If you did not initiate this request, no changes have been made and you can safely ignore this email.
                </p>
            </div>

            <!-- Plain Link Fallback -->
            <p style='margin: 0 0 6px; font-size: 12px; color: #64748b;'>
                If the button above does not work, copy and paste this link into your browser:
            </p>
            <p style='margin: 0; font-size: 11.5px; word-break: break-all; color: #2563eb; background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;'>
                <a href='{$safeUrl}' style='color: #2563eb; text-decoration: none;'>{$safeUrl}</a>
            </p>
        </div>

        <!-- Footer -->
        <div style='background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6;'>
            <p style='margin: 0 0 4px;'>
                <strong>CloudFlox LLC</strong> &bull; Enterprise Cloud &amp; High-Speed NVMe Web Hosting
            </p>
            <p style='margin: 0;'>
                For assistance or security concerns, reach out to our 24/7 team at <a href='mailto:support@cloudflox.com' style='color: #2563eb; text-decoration: none;'>support@cloudflox.com</a>.
            </p>
        </div>

    </div>
</body>
</html>";
    }

    /**
     * Dispatch branded Server Quotation Request confirmation to Customer
     */
    public static function sendQuoteRequestClientConfirmation($recipientEmail, $clientName, $quoteId, $planName, array $specsList, $portalUrl = '') {
        $recipientEmail = trim($recipientEmail);
        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid customer email address.'];
        }

        $greetingName = !empty($clientName) ? htmlspecialchars($clientName) : 'Valued Customer';
        $safePlanName = htmlspecialchars($planName);
        $portalUrl    = !empty($portalUrl) ? self::getAbsoluteUrl($portalUrl) : self::getAbsoluteUrl('clientarea');

        // Build Specs HTML Table Rows
        $specsHtml = '';
        foreach ($specsList as $k => $v) {
            if (empty($v)) continue;
            $specsHtml .= "<tr>
                <td style='padding: 8px 12px; font-weight: 700; color: #475569; width: 35%; border-bottom: 1px solid #f1f5f9; font-size: 12.5px;'>" . htmlspecialchars($k) . "</td>
                <td style='padding: 8px 12px; color: #0f172a; font-weight: 600; border-bottom: 1px solid #f1f5f9; font-size: 12.5px;'>" . htmlspecialchars($v) . "</td>
            </tr>";
        }

        $body = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Server Quotation Request Received - CloudFlox</title>
</head>
<body style='margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <div style='max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;'>
        <div style='background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 36px; text-align: center; color: #ffffff;'>
            <div style='font-size: 24px; font-weight: 900; letter-spacing: -0.5px;'>
                <span style='color: #ffffff;'>Cloud</span><span style='color: #3b82f6;'>Flox</span>
            </div>
            <p style='margin: 8px 0 0; color: #94a3b8; font-size: 13px; font-weight: 500;'>Enterprise Cloud &amp; Bare Metal Infrastructure</p>
        </div>

        <div style='padding: 36px 36px 28px;'>
            <div style='display: inline-block; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 11.5px; font-weight: 700; padding: 4px 12px; border-radius: 999px; text-transform: uppercase; margin-bottom: 16px;'>
                Quotation Request Received • RFQ #{$quoteId}
            </div>
            
            <h2 style='margin: 0 0 14px; font-size: 20px; font-weight: 800; color: #0f172a;'>
                We have received your server requirements!
            </h2>
            
            <p style='margin: 0 0 18px; font-size: 14px; color: #475569; line-height: 1.6;'>
                Hello <strong>{$greetingName}</strong>,<br>
                Thank you for considering CloudFlox infrastructure for your business workloads. Our Enterprise Architecture team has received your custom specification request for <strong>{$safePlanName}</strong>.
            </p>

            <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 24px;'>
                <h4 style='margin: 0 0 12px; font-size: 13.5px; font-weight: 800; color: #1e293b;'>Your Requested Server Specifications:</h4>
                <table style='width: 100%; border-collapse: collapse;'>
                    {$specsHtml}
                </table>
            </div>

            <div style='background: #eff6ff; border-left: 4px solid #2563eb; border-radius: 6px; padding: 14px 16px; margin-bottom: 26px;'>
                <h5 style='margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #1e40af;'>Next Steps in our Review Process:</h5>
                <p style='margin: 0; font-size: 12.5px; color: #1e40af; line-height: 1.5;'>
                    Our cloud engineers are verifying physical node capacity, datacenter routing, and software licensing. You will receive an official formal quotation with tailored pricing within <strong>1–2 business hours</strong> directly to this email and inside your client portal.
                </p>
            </div>

            <div style='text-align: center; margin: 30px 0 15px;'>
                <a href='{$portalUrl}' style='display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 13px 32px; border-radius: 8px;'>
                    View Request in Client Portal &rarr;
                </a>
            </div>
        </div>

        <div style='background: #f8fafc; padding: 22px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6;'>
            <p style='margin: 0 0 4px;'><strong>CloudFlox LLC</strong> &bull; Custom Cloud &amp; Dedicated Bare Metal Solutions</p>
            <p style='margin: 0;'>Questions? Reply to this email or reach out to <a href='mailto:support@cloudflox.com' style='color: #2563eb; text-decoration: none;'>support@cloudflox.com</a>.</p>
        </div>
    </div>
</body>
</html>";

        try {
            $mail = self::getMailer();
            $mail->addAddress($recipientEmail, $clientName ?: 'CloudFlox Customer');
            $mail->isHTML(true);
            $mail->Subject = "We Received Your Server Quotation Request [#RFQ-{$quoteId}] — CloudFlox";
            $mail->Body    = $body;
            $mail->AltBody = "Hello {$greetingName},\n\nWe received your custom server quotation request (#RFQ-{$quoteId}) for {$safePlanName}.\nOur engineering team is reviewing your hardware requirements and will dispatch an official quotation shortly.\n\nYou can track this request at:\n{$portalUrl}\n\nCloudFlox LLC Support Team\nsupport@cloudflox.com";
            $mail->send();
            return ['status' => 'success', 'message' => 'Quotation confirmation email sent.'];
        } catch (Exception $e) {
            error_log('CloudFloxEmailService::sendQuoteRequestClientConfirmation Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Dispatch alert notification to CloudFlox Admin Mailbox
     */
    public static function sendQuoteRequestAdminAlert($quoteId, $clientName, $companyName, $clientEmail, $clientPhone, $country, $planName, array $specsList, $customNotes = '', $budget = '') {
        $adminMail = 'info@cloudflox.com';
        $safeClientName = htmlspecialchars($clientName);
        $safeEmail      = htmlspecialchars($clientEmail);
        $safePhone      = htmlspecialchars($clientPhone);
        $safeCompany    = htmlspecialchars($companyName ?: 'Individual');
        $safePlan       = htmlspecialchars($planName);
        $safeBudget     = htmlspecialchars($budget ?: 'Not specified');
        $safeNotes      = nl2br(htmlspecialchars($customNotes ?: 'None provided.'));

        $specsHtml = '';
        foreach ($specsList as $k => $v) {
            if (empty($v)) continue;
            $specsHtml .= "<tr>
                <td style='padding: 6px 10px; font-weight: bold; color: #334155; width: 35%; border-bottom: 1px solid #e2e8f0; font-size: 12px;'>" . htmlspecialchars($k) . "</td>
                <td style='padding: 6px 10px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-size: 12px;'>" . htmlspecialchars($v) . "</td>
            </tr>";
        }

        $body = "<!DOCTYPE html>
<html>
<body style='font-family: Arial, sans-serif; background: #f8fafc; padding: 20px;'>
    <div style='max-width: 650px; margin: 0 auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 24px;'>
        <div style='background: #1e293b; color: #ffffff; padding: 14px 18px; border-radius: 6px; margin-bottom: 18px;'>
            <h3 style='margin: 0;'>🔥 New Server Quotation Request [#RFQ-{$quoteId}]</h3>
            <p style='margin: 4px 0 0; font-size: 13px; color: #94a3b8;'>Submitted via CloudFlox Server Solution Builder</p>
        </div>

        <h4 style='color: #0f172a; margin: 0 0 10px;'>1. Customer Details:</h4>
        <ul style='font-size: 13px; color: #334155; line-height: 1.6; margin-top: 0;'>
            <li><strong>Client Name:</strong> {$safeClientName}</li>
            <li><strong>Company / Organization:</strong> {$safeCompany}</li>
            <li><strong>Email:</strong> <a href='mailto:{$safeEmail}'>{$safeEmail}</a></li>
            <li><strong>Phone:</strong> {$safePhone}</li>
            <li><strong>Country:</strong> " . htmlspecialchars($country) . "</li>
        </ul>

        <h4 style='color: #0f172a; margin: 18px 0 10px;'>2. Requested Server Architecture:</h4>
        <table style='width: 100%; border-collapse: collapse; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;'>
            {$specsHtml}
        </table>

        <h4 style='color: #0f172a; margin: 18px 0 10px;'>3. Workload, Budget &amp; Custom Requirements:</h4>
        <div style='background: #f1f5f9; padding: 12px 14px; border-radius: 6px; font-size: 13px; color: #1e293b;'>
            <p style='margin: 0 0 6px;'><strong>Estimated Budget:</strong> {$safeBudget}</p>
            <p style='margin: 0;'><strong>Project Notes:</strong><br>{$safeNotes}</p>
        </div>

        <div style='margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;'>
            <p><strong>Admin Action Required:</strong> Log in to WHMCS Admin &rarr; Billing &rarr; Quotes to review Quote #{$quoteId}, tailor line items and deliver quotation to customer.</p>
        </div>
    </div>
</body>
</html>";

        try {
            $mail = self::getMailer();
            $mail->addAddress($adminMail, 'CloudFlox Sales & Quotes');
            $mail->addReplyTo($clientEmail, $clientName ?: 'Client');
            $mail->isHTML(true);
            $mail->Subject = "🔥 [NEW RFQ] Server Quotation #{$quoteId} - {$clientName} ({$safePlan})";
            $mail->Body    = $body;
            $mail->send();
            return ['status' => 'success'];
        } catch (Exception $e) {
            error_log('CloudFloxEmailService::sendQuoteRequestAdminAlert Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    /**
     * Send formal Quotation Delivered email to customer with pricing and acceptance link
     */
    public static function sendQuoteDeliveredCustomerEmail($recipientEmail, $clientName, $quoteId, $planName, $price, $validUntil = '', $portalUrl = '') {
        $recipientEmail = trim($recipientEmail);
        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid customer email address.'];
        }

        $safeClientName = htmlspecialchars($clientName);
        $safePlan       = htmlspecialchars($planName);
        $formattedPrice = '$' . number_format((float)$price, 2) . ' USD';
        $validUntilText = !empty($validUntil) ? date('F d, Y', strtotime($validUntil)) : date('F d, Y', strtotime('+30 days'));
        $reviewLink     = !empty($portalUrl) ? $portalUrl : 'clientarea?tab=quotes';

        $body = "<!DOCTYPE html>
<html>
<body style='font-family: Arial, sans-serif; background: #f8fafc; padding: 20px;'>
    <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 24px;'>
        <div style='background: #2563eb; color: #ffffff; padding: 18px; border-radius: 6px; margin-bottom: 20px; text-align: center;'>
            <h2 style='margin: 0; font-size: 20px;'>Your Custom Server Quotation is Ready!</h2>
            <p style='margin: 6px 0 0; font-size: 14px; opacity: 0.9;'>Quotation #RFQ-{$quoteId}</p>
        </div>

        <p style='font-size: 14px; color: #334155; line-height: 1.6;'>Hello <strong>{$safeClientName}</strong>,</p>
        <p style='font-size: 14px; color: #334155; line-height: 1.6;'>
            Our Cloud Infrastructure Architecture team has reviewed your server requirements for <strong>{$safePlan}</strong> and tailored a binding official quotation for your deployment.
        </p>

        <div style='background: #f1f5f9; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 18px; margin: 20px 0; text-align: center;'>
            <span style='font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;'>Quoted Pricing</span>
            <div style='font-size: 28px; font-weight: 800; color: #0f172a; margin: 6px 0;'>{$formattedPrice}</div>
            <span style='font-size: 12px; color: #059669; font-weight: 600;'>Valid Until: {$validUntilText}</span>
        </div>

        <p style='font-size: 13.5px; color: #475569; line-height: 1.6;'>
            You can review the full hardware specification breakdown, network routing, and terms directly in your CloudFlox Client Portal. You can accept the quotation with one click to generate your invoice and provision your server.
        </p>

        <div style='text-align: center; margin: 28px 0;'>
            <a href='{$reviewLink}' style='background: #2563eb; color: #ffffff; font-weight: bold; font-size: 15px; padding: 14px 28px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                Review &amp; Accept Quotation &rarr;
            </a>
        </div>

        <div style='margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;'>
            <p style='margin: 0;'>CloudFlox Cloud Infrastructure &bull; High-Performance Enterprise Solutions</p>
        </div>
    </div>
</body>
</html>";

        try {
            $mail = self::getMailer();
            $mail->addAddress($recipientEmail, $clientName ?: 'Valued Client');
            $mail->isHTML(true);
            $mail->Subject = "CloudFlox Quotation #RFQ-{$quoteId} Ready for Review ({$safePlan})";
            $mail->Body    = $body;
            $mail->send();
            return ['status' => 'success'];
        } catch (Exception $e) {
            error_log('CloudFloxEmailService::sendQuoteDeliveredCustomerEmail Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
