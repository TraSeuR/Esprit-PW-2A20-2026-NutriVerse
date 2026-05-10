<?php
/**
 * email_template.php
 * ──────────────────────────────────────────────────────
 * HTML wrapper for ALL NutriVerse emails.
 *
 * This file is loaded by Mailer.php using output buffering.
 * Before including this file, you MUST set these variables:
 *
 *   $email_title   → shown in the green header  (e.g. "Vérifiez votre e-mail")
 *   $email_content → the inner HTML body content (HTML string)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($email_title) ?></title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:'Segoe UI', Arial, sans-serif;">

    <!-- ── Outer wrapper ─────────────────────────────── -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:40px 0;">
        <tr>
            <td align="center">

                <!-- ── Email card ──────────────────────── -->
                <table width="560" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:16px; overflow:hidden;
                              box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    <!-- ── Green header ──────────────────── -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#16a34a,#15803d);
                                   padding:32px 40px; text-align:center;">
                            <h1 style="margin:0; color:#fff; font-size:26px;
                                       font-weight:800; letter-spacing:-0.5px;">
                                🌿 NutriVerse
                            </h1>
                            <p style="margin:8px 0 0; color:#bbf7d0; font-size:14px;">
                                <?= htmlspecialchars($email_title) ?>
                            </p>
                        </td>
                    </tr>

                    <!-- ── Email body ────────────────────── -->
                    <tr>
                        <td style="padding:40px;">

                            <?= $email_content ?>

                            <!-- Divider -->
                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:32px 0;">

                            <!-- Footer note -->
                            <p style="color:#9ca3af; font-size:12px; text-align:center; margin:0;">
                                © 2026 NutriVerse &nbsp;·&nbsp; Nutrition intelligente pour une vie plus saine.
                            </p>

                        </td>
                    </tr>

                </table>
                <!-- /.email card -->

            </td>
        </tr>
    </table>
    <!-- /.outer wrapper -->

</body>
</html>
