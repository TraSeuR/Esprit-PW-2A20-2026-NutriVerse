<?php
/**
 * Mailer.php  (Controller)
 * ──────────────────────────────────────────────────────
 * Simple email-sending functions for NutriVerse.
 * Uses PHPMailer + an external HTML template.
 *
 * HOW TO USE from another controller:
 *   require_once __DIR__ . '/Mailer.php';
 *   mailer_send_verification_code($email, $name, $code);
 */

// ── Load PHPMailer ────────────────────────────────────
// vendor/ is one level above Controller/
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
$manualSrc        = __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} elseif (file_exists($manualSrc)) {
    require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
} else {
    // PHPMailer not found — run: composer require phpmailer/phpmailer
    error_log('PHPMailer not found. Please install it via Composer.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── SMTP settings ─────────────────────────────────────
define('SMTP_HOST',  'smtp.gmail.com');
define('SMTP_PORT',  587);
define('SMTP_USER',  'hallagui0@gmail.com');
define('SMTP_PASS',  'wnob gise eeoi zzln');  // Gmail App Password
define('SMTP_FROM',  'hallagui0@gmail.com');
define('SMTP_NAME',  'NutriVerse');

// ─────────────────────────────────────────────────────
// mailer_send()
// Core function — builds HTML from template and sends email.
//
// $to_email     → recipient email address
// $to_name      → recipient full name
// $subject      → email subject line
// $email_title  → title shown in the green header
// $email_content→ inner HTML body (set before calling this)
// ─────────────────────────────────────────────────────
function mailer_send($to_email, $to_name, $subject, $email_title, $email_content)
{
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer class not found. Email not sent.');
        return false;
    }

    // Build HTML body using the email template view
    // ob_start captures everything echo'd/printed by the template file
    ob_start();
    include __DIR__ . '/../View/FrontOffice/email_template.php';
    $html_body = ob_get_clean();

    // Send via PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Sender and recipient
        $mail->setFrom(SMTP_FROM, SMTP_NAME);
        $mail->addAddress($to_email, $to_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        // Plain-text fallback (strips HTML tags)
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $html_body));

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('Email send failed: ' . $mail->ErrorInfo);
        return false;
    }
}

// ─────────────────────────────────────────────────────
// mailer_send_verification_code()
// Sends the 6-digit OTP to verify a new account.
//
// $to_email → user's email address
// $to_name  → user's full name
// $code     → 6-digit verification code
// ─────────────────────────────────────────────────────
function mailer_send_verification_code($to_email, $to_name, $code)
{
    $email_title = 'Vérifiez votre adresse e-mail';

    $email_content = "
        <p style='font-size:16px;'>
            Bonjour <strong>" . htmlspecialchars($to_name) . "</strong>,
        </p>
        <p>Merci de vous être inscrit sur <strong>NutriVerse</strong> !</p>
        <p>Voici votre code de vérification :</p>

        <div style='margin:30px auto; text-align:center;'>
            <span style='font-size:40px; font-weight:800; letter-spacing:12px;
                         color:#16a34a; background:#f0fdf4;
                         padding:20px 36px; border-radius:12px; display:inline-block;'>
                " . htmlspecialchars($code) . "
            </span>
        </div>

        <p style='color:#6b7280; font-size:14px;'>
            Ce code expire dans <strong>15 minutes</strong>.
            Ne le partagez avec personne.
        </p>
    ";

    $subject = 'NutriVerse – Vérification de votre adresse e-mail';

    return mailer_send($to_email, $to_name, $subject, $email_title, $email_content);
}

// ─────────────────────────────────────────────────────
// mailer_send_password_reset_otp()
// Sends a 6-digit OTP to reset a forgotten password.
//
// $to_email → user's email address
// $to_name  → user's full name
// $code     → 6-digit reset code
// ─────────────────────────────────────────────────────
function mailer_send_password_reset_otp($to_email, $to_name, $code)
{
    $email_title = 'Réinitialisation du mot de passe';

    $email_content = "
        <p style='font-size:16px;'>
            Bonjour <strong>" . htmlspecialchars($to_name) . "</strong>,
        </p>
        <p>Vous avez demandé la réinitialisation de votre mot de passe sur
           <strong>NutriVerse</strong>.</p>
        <p>Voici votre code de sécurité :</p>

        <div style='margin:30px auto; text-align:center;'>
            <span style='font-size:40px; font-weight:800; letter-spacing:12px;
                         color:#16a34a; background:#f0fdf4;
                         padding:20px 36px; border-radius:12px; display:inline-block;'>
                " . htmlspecialchars($code) . "
            </span>
        </div>

        <p style='color:#6b7280; font-size:14px;'>
            Ce code expire dans <strong>15 minutes</strong>.<br>
            Si vous n'avez pas fait cette demande, ignorez cet e-mail.
        </p>
    ";

    $subject = 'NutriVerse – Code de réinitialisation de votre mot de passe';

    return mailer_send($to_email, $to_name, $subject, $email_title, $email_content);
}

// ─────────────────────────────────────────────────────
// mailer_send_password_reset_link()
// Sends a clickable reset link (alternative to OTP flow).
//
// $to_email    → user's email address
// $to_name     → user's full name
// $reset_link  → full URL to the reset page
// ─────────────────────────────────────────────────────
function mailer_send_password_reset_link($to_email, $to_name, $reset_link)
{
    $email_title = 'Réinitialisation du mot de passe';

    $email_content = "
        <p style='font-size:16px;'>
            Bonjour <strong>" . htmlspecialchars($to_name) . "</strong>,
        </p>
        <p>Vous avez demandé la réinitialisation de votre mot de passe sur
           <strong>NutriVerse</strong>.</p>
        <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>

        <div style='margin:30px auto; text-align:center;'>
            <a href='" . htmlspecialchars($reset_link) . "'
               style='background:#16a34a; color:#fff; padding:14px 32px;
                      border-radius:8px; text-decoration:none;
                      font-weight:600; font-size:16px; display:inline-block;'>
                Réinitialiser mon mot de passe
            </a>
        </div>

        <p style='color:#6b7280; font-size:14px;'>
            Ce lien expire dans <strong>60 minutes</strong>.<br>
            Si vous n'avez pas fait cette demande, ignorez cet e-mail.
        </p>
        <p style='color:#9ca3af; font-size:12px; word-break:break-all;'>
            Lien : " . htmlspecialchars($reset_link) . "
        </p>
    ";

    $subject = 'NutriVerse – Réinitialisation de votre mot de passe';

    return mailer_send($to_email, $to_name, $subject, $email_title, $email_content);
}
