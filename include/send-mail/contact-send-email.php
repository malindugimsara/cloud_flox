<?php

require "SMTP.php";
require "PHPMailer.php";
require "Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

$name = $_POST["name"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$subject = $_POST["subject"];
$message = $_POST["message"];

// Check if required fields are filled
if ($name == "" || $email == "" || $subject == "" || $message == "") {
    echo "Fill in the all fields!";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address!";
} else if (!empty($phone) && !preg_match("/^[0-9\+\-\s\(\)\.]{7,25}$/", $phone)) {
    echo "Invalid phone number! Please enter a valid telephone number.";
} else {

    $recaptchaSecret = "6Le_PGssAAAAAJi3rY9BgtOBDe8VZ4NmR0loBkNX";
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verifyUrl . "?secret=" . $recaptchaSecret . "&response=" . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        echo "reCAPTCHA verification failed!";
        exit;
    }

    $mail = new PHPMailer;
    $mail->IsSMTP();
    $mail->Host = 'mail.cloudflox.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@cloudflox.com';
    $mail->Password = '7q6M(Ice^1a9';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('info@cloudflox.com', 'CloudFlox LLC Hosting');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('info@cloudflox.com');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $bodyContent = "
                 <div style='font-family: Arial, sans-serif; padding:20px; background:#f6f6f6;'>
            <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:6px;'>

                <div style='padding:15px; border-bottom:1px solid #dddddd;'>
                    <h2 style='margin:0; color:#333333;'>New Contact Enquiry</h2>
                </div>

                <div style='padding:20px; color:#444444; font-size:14px;'>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Phone:</strong> {$phone}</p>
                    <p><strong>Message:</strong></p>
                    <p style='border-left:3px solid #dddddd; padding-left:10px;'>{$message}</p>
                </div>

                <div style='padding:10px; text-align:center; font-size:11px; color:#999999; border-top:1px solid #eeeeee;'>
                    Powered by CloudFlox Hosting
                </div>

            </div>
        </div>";

    $mail->Body  = $bodyContent;

    if (!$mail->send()) {
        // echo $mail->ErrorInfo;
    }
    $mail = new PHPMailer;
    $mail->IsSMTP();
    $mail->Host = 'mail.cloudflox.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@cloudflox.com';
    $mail->Password = '7q6M(Ice^1a9';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('info@cloudflox.com', 'CloudFlox LLC Hosting');
    $mail->addReplyTo('info@cloudflox.com', 'CloudFlox LLC Hosting');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Thank you for contacting CloudFlox Hosting';
    $bodyContent = " <div style='font-family: Arial, sans-serif; padding:20px; background:#f6f6f6;'>
            <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:6px;'>

                <div style='padding:15px; border-bottom:1px solid #dddddd;'>
                    <h2 style='margin:0; color:#333333;'>Thank You for Contacting Us</h2>
                </div>

                <div style='padding:20px; color:#444444; font-size:14px;'>
                    <p>Dear {$name},</p>

                    <p>
                        Thank you for reaching out to <strong>CloudFlox Hosting</strong>.
                        We have received your enquiry and our team will get back to you shortly.
                    </p>

                    <p><strong>Your Message:</strong></p>
                    <p style='border-left:3px solid #dddddd; padding-left:10px;'>{$message}</p>

                    <p>
                        Kind regards,<br>
                        <strong>CloudFlox Hosting</strong>
                    </p>
                </div>

                <div style='padding:10px; text-align:center; font-size:11px; color:#999999; border-top:1px solid #eeeeee;'>
                    Powered by CloudFlox Hosting
                </div>

            </div>
        </div>";

    $mail->Body  = $bodyContent;
    if (!$mail->send()) {
    } else {
        echo "success";
    }
}
