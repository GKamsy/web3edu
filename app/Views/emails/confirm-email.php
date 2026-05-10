<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="padding:40px 0;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                
                <!-- Header -->
                <tr>
                    <td style="background:#1e40af; color:#ffffff; padding:20px; text-align:center;">
                        <h1 style="margin:0; font-size:22px;">
                            <?= htmlspecialchars($school['name']) ?>
                        </h1>
                        <p style="margin:5px 0 0; font-size:14px;">
                            <?= htmlspecialchars($school['tagline']) ?>
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px; color:#333;">
                        <h2 style="margin-top:0;">Confirm Your Email Address</h2>

                        <p>Hello <strong><?= htmlspecialchars($firstname) ?></strong>,</p>

                        <p>
                            Thank you for registering with us.  
                            Please confirm that this email address belongs to you by clicking the link below:
                        </p>

                        <p style="text-align:center; margin:30px 0;">
                            <a href="<?= htmlspecialchars($verifyLink) ?>"
                               style="
                                   background:#1e40af;
                                   color:#ffffff;
                                   text-decoration:none;
                                   padding:12px 24px;
                                   border-radius:6px;
                                   font-weight:bold;
                                   display:inline-block;
                               ">
                                Confirm Email
                            </a> 
                        </p>

                        <p style="font-size:14px; color:#666;">
                            This link will expire in 24 hours.
                        </p>

                        <p style="font-size:13px; color:#999;">
                            Please, don't reply to this email, it is automated. 
                            If you did not create this account, you can <strong>contact us</strong> or safely ignore this email.
                        </p>

                        <p style="text-align:center; margin:30px 0;">
                            <a href="http://127.0.0.1/contact"
                               style="
                                   background:#1e40af;
                                   color:#ffffff;
                                   text-decoration:none;
                                   padding:12px 24px;
                                   border-radius:6px;
                                   font-weight:bold;
                                   display:inline-block;
                               ">
                                Contact us
                            </a>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#555;">
                        © <?= date('Y') ?> <?= htmlspecialchars($school['name']) ?>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
