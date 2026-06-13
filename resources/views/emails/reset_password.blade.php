<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb; padding:30px 12px;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0"
                style="width:100%; max-width:620px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,0.10);">

                <!-- Header -->
                <tr>
                    <td style="background:#0f172a; padding:28px 32px; text-align:center;">
                        <img src="{{ url('icons/tms.png') }}"
                            alt="Company Logo"
                            style="max-width:110px; height:auto; margin-bottom:16px;">

                        <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                            Reset Password
                        </h1>

                        <p style="margin:8px 0 0; color:#cbd5e1; font-size:14px;">
                            We received a request to reset your password.
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:32px; color:#334155; font-size:15px; line-height:1.6;">

                        <p style="margin:0 0 18px;">
                            Hi
                            <strong style="color:#0f172a;">
                                {{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}
                            </strong>,
                        </p>

                        <p style="margin:0 0 22px;">
                            We received a request to reset your password. Click the button below to create a new password.
                        </p>

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $resetLink }}"
                                        style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:10px; font-size:15px; font-weight:700;">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:28px 0 0; color:#475569;">
                            If you did not request this, please ignore this email.
                        </p>

                        <p style="margin:28px 0 0; color:#475569;">
                            Best regards,<br>
                            <strong style="color:#0f172a;">Task Management Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f8fafc; padding:18px 28px; text-align:center; border-top:1px solid #e2e8f0;">
                        <p style="margin:0; color:#64748b; font-size:12px; line-height:1.5;">
                            This is an automated email. Please do not reply.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
