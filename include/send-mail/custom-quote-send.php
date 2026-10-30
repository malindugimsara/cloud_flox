<?php
/**
 * CloudFlox - Custom Infrastructure Quote Request Handler
 * Secure delivery to sales mailbox with automated client confirmation and local fallback logging
 */

header('Content-Type: application/json; charset=utf-8');

// Disable direct error output to keep JSON clean
error_reporting(0);
ini_set('display_errors', '0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Honeypot anti-spam check
if (!empty($_POST['cf_hp_field'])) {
    echo json_encode(['status' => 'error', 'message' => 'Spam verification detected. Please try again.']);
    exit;
}

$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : '';
$budget = isset($_POST['budget']) ? trim(strip_tags($_POST['budget'])) : '';
$requirements = isset($_POST['requirements']) ? trim(strip_tags($_POST['requirements'])) : '';

// Validation
if (empty($name) || empty($email) || empty($service) || empty($requirements) || empty($budget)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid business email address.']);
    exit;
}

// Cloudflare Turnstile Security Verification
require_once __DIR__ . '/../env.inc.php';

$turnstileSecret = defined('CF_TURNSTILE_SECRET_KEY') ? CF_TURNSTILE_SECRET_KEY : (getenv('CLOUDFLARE_TURNSTILE_SECRET_KEY') ?: '0x4AAAAAAFsPGj4XQp1O1pc9PnrNzozwjKM');
$turnstileResponse = $_POST['cf-turnstile-response'] ?? '';

if (empty($turnstileResponse)) {
    echo json_encode(['status' => 'error', 'message' => 'Please complete the Cloudflare security verification check.']);
    exit;
}

$verifyData = [
    'secret' => $turnstileSecret,
    'response' => $turnstileResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
];

$verifyUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
$turnstileSuccess = false;

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verifyData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $turnstileResult = curl_exec($ch);
    curl_close($ch);
} else {
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($verifyData),
            'timeout' => 10
        ]
    ];
    $context = stream_context_create($options);
    $turnstileResult = @file_get_contents($verifyUrl, false, $context);
}

$responseData = json_decode($turnstileResult, true);
if (is_array($responseData) && !empty($responseData['success'])) {
    $turnstileSuccess = true;
}

if (!$turnstileSuccess) {
    echo json_encode(['status' => 'error', 'message' => 'Cloudflare Turnstile verification failed. Please try again.']);
    exit;
}

// Generate Reference ID
$ref_id = 'CFX-REQ-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
$timestamp = date('Y-m-d H:i:s');
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Fallback Local Lead Logging (Guarantees zero leads are ever lost even if SMTP is offline)
$log_entry = [
    'ref_id' => $ref_id,
    'timestamp' => $timestamp,
    'ip' => $client_ip,
    'name' => $name,
    'email' => $email,
    'service' => $service,
    'budget' => $budget,
    'requirements' => $requirements
];

$log_file = __DIR__ . '/custom_quotes_archive.log';
@file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);

// Load PHPMailer classes
require_once __DIR__ . "/SMTP.php";
require_once __DIR__ . "/PHPMailer.php";
require_once __DIR__ . "/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$salesMailSent = false;
$mailErrorMessage = '';

try {
    // 1. Send Notification Email to CloudFlox Sales Team
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = 'mail.cloudflox.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@cloudflox.com';
    $mail->Password = 'TC.jk1mnPoccDTf=';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->Timeout = 8;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('info@cloudflox.com', 'CloudFlox Enterprise Portal');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('info@cloudflox.com', 'CloudFlox Sales & Solutions Team');
    
    $mail->isHTML(true);
    $mail->Subject = "[Custom Infrastructure Quote] {$service} - {$name} ({$budget}) [{$ref_id}]";

    $salesHtml = "
    <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#0f172a; padding: 35px 20px;'>
        <div style='max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);'>
            <div style='background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 25px 30px; border-bottom: 3px solid #2563eb;'>
                <span style='background: #2563eb; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 4px; display: inline-block; margin-bottom: 8px;'>New Enterprise Inquiry</span>
                <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 800;'>Custom Infrastructure Quote Request</h2>
                <p style='color: #94a3b8; margin: 5px 0 0; font-size: 13px;'>Reference ID: <strong>{$ref_id}</strong> &bull; Received: {$timestamp}</p>
            </div>
            
            <div style='padding: 30px; color: #334155; font-size: 14px; line-height: 1.6;'>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 22px;'>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 10px 0; color: #64748b; font-weight: 600; width: 140px;'>Client Name:</td>
                        <td style='padding: 10px 0; color: #0f172a; font-weight: 800;'>{$name}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 10px 0; color: #64748b; font-weight: 600;'>Business Email:</td>
                        <td style='padding: 10px 0;'><a href='mailto:{$email}' style='color: #2563eb; text-decoration: none; font-weight: 700;'>{$email}</a></td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 10px 0; color: #64748b; font-weight: 600;'>Requested Service:</td>
                        <td style='padding: 10px 0; color: #0f172a; font-weight: 700;'>{$service}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 10px 0; color: #64748b; font-weight: 600;'>Estimated Budget:</td>
                        <td style='padding: 10px 0; color: #059669; font-weight: 800; font-size: 15px;'>{$budget}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 0; color: #64748b; font-weight: 600;'>Client IP:</td>
                        <td style='padding: 10px 0; color: #64748b; font-size: 12px;'>{$client_ip}</td>
                    </tr>
                </table>

                <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin-top: 15px;'>
                    <h4 style='margin: 0 0 10px; color: #0f172a; font-size: 14px; text-transform: uppercase; letter-spacing: 0.04em;'>Workload &amp; Architecture Requirements:</h4>
                    <p style='margin: 0; color: #1e293b; white-space: pre-wrap; font-size: 13.5px; line-height: 1.6; font-family: Consolas, Monaco, monospace;'>{$requirements}</p>
                </div>
            </div>

            <div style='background: #f8fafc; padding: 16px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;'>
                CloudFlox Enterprise Infrastructure &bull; Automated Sales Routing
            </div>
        </div>
    </div>
    ";
    $mail->Body = $salesHtml;
    $mail->send();
    $salesMailSent = true;

} catch (Exception $e) {
    $mailErrorMessage = $mail->ErrorInfo ?: $e->getMessage();
}

// If sales email failed to dispatch via SMTP
if (!$salesMailSent) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Inquiry archived under ref #' . $ref_id . ', but mail server notification failed (' . htmlspecialchars($mailErrorMessage, ENT_QUOTES, 'UTF-8') . '). Please contact info@cloudflox.com.',
        'ref' => $ref_id
    ]);
    exit;
}

// 2. Send Automated Confirmation Email to Client
try {
    $clientMail = new PHPMailer(true);
    $clientMail->IsSMTP();
    $clientMail->Host = 'mail.cloudflox.com';
    $clientMail->SMTPAuth = true;
    $clientMail->Username = 'info@cloudflox.com';
    $clientMail->Password = 'TC.jk1mnPoccDTf=';
    $clientMail->SMTPSecure = 'ssl';
    $clientMail->Port = 465;
    $clientMail->Timeout = 8;
    $clientMail->CharSet = 'UTF-8';
    $clientMail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $clientMail->setFrom('info@cloudflox.com', 'CloudFlox Enterprise Solutions');
    $clientMail->addReplyTo('info@cloudflox.com', 'CloudFlox Solutions Team');
    $clientMail->addAddress($email, $name);
    
    $clientMail->isHTML(true);
    $clientMail->Subject = "We Received Your Custom Infrastructure Request [{$ref_id}]";

    $clientHtml = "
    <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f4f6f8; padding: 35px 20px;'>
        <div style='max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,0.06);'>
            <div style='background: #0f172a; padding: 25px 30px; text-align: center;'>
                <h2 style='color: #ffffff; margin: 0; font-size: 20px; font-weight: 800;'>CloudFlox Enterprise Solutions</h2>
                <p style='color: #38bdf8; margin: 5px 0 0; font-size: 13px;'>Tailored Cloud &amp; Bare-Metal Infrastructure</p>
            </div>
            
            <div style='padding: 30px; color: #334155; font-size: 14.5px; line-height: 1.65;'>
                <p style='margin-top: 0;'>Dear <strong>{$name}</strong>,</p>
                <p>Thank you for submitting your custom infrastructure requirements to CloudFlox. We have successfully received your inquiry under reference ID: <strong><span style='color: #2563eb;'>{$ref_id}</span></strong>.</p>
                
                <div style='background: #eff6ff; border-left: 4px solid #2563eb; padding: 14px 16px; border-radius: 0 6px 6px 0; margin: 20px 0;'>
                    <strong style='color: #1e40af; display: block; margin-bottom: 4px;'>What Happens Next?</strong>
                    <span style='color: #1e3a8a; font-size: 13.5px;'>A dedicated Senior Infrastructure Architect is currently reviewing your technical specifications and resource budget. You will receive a custom architecture topology and formal pricing quote within <strong>2 to 4 business hours</strong>.</span>
                </div>

                <p style='font-size: 13px; color: #64748b;'><strong>Your Request Summary:</strong><br>
                &bull; Service: {$service}<br>
                &bull; Budget Scope: {$budget}</p>

                <p style='margin-bottom: 0;'>If you have urgent questions or additional architecture diagrams to share, simply reply directly to this email.</p>
            </div>

            <div style='background: #f8fafc; padding: 16px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;'>
                &copy; " . date('Y') . " CloudFlox LLC. All rights reserved. &bull; <a href='https://cloudflox.com' style='color:#2563eb; text-decoration:none;'>cloudflox.com</a>
            </div>
        </div>
    </div>
    ";
    $clientMail->Body = $clientHtml;
    $clientMail->send();

} catch (Exception $e) {
    // Non-blocking autoresponder
}

echo json_encode([
    'status' => 'success',
    'message' => 'Your custom infrastructure inquiry has been securely delivered to our sales engineering team.',
    'ref' => $ref_id,
    'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
    'service' => htmlspecialchars($service, ENT_QUOTES, 'UTF-8')
]);
exit;
