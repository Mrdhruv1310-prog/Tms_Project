<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body
    style="margin:0; padding:0; background:linear-gradient(135deg, rgb(230,242,234) 0%, rgb(240,245,248) 50%, rgb(225,238,247) 100%); font-family:system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0"
        style="background:linear-gradient(135deg, rgb(230,242,234) 0%, rgb(240,245,248) 50%, rgb(225,238,247) 100%); padding:40px 16px; min-height:100vh;">

        <tr>
            <td align="center">

                <!-- Main Card Container -->
                <table width="480" cellpadding="0" cellspacing="0"
                    style="width:100%; max-width:480px; background:rgba(255,255,255,0.9); backdrop-filter:blur(20px); border-radius:24px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.08); border:1px solid rgba(255,255,255,0.8);">

                    <!-- Header -->
                    <tr>
                        <td style="padding:40px 40px 24px; text-align:center;">

                            <!-- Logo (Direct image without background container, matching login page) -->
                            <table cellpadding="0" cellspacing="0" style="margin:0 auto 16px;">
                                <tr>
                                    <td align="center">
                                        <img src="{{ url('icons/tms.png') }}" alt="Company Logo"
                                            style="display:block; max-height:44px; width:auto; object-fit:contain; border:0;">
                                    </td>
                                </tr>
                            </table>

                            <h1
                                style="margin:16px 0 0; color:#111827; font-size:24px; line-height:32px; font-weight:700; letter-spacing:-0.025em;">
                                Reset Password
                            </h1>

                            <p style="margin:8px 0 0; color:#6b7280; font-size:14px; line-height:20px;">
                                We received a request to reset your password.
                            </p>

                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:0 40px 40px; color:#374151; font-size:14px; line-height:1.6;">

                            <p style="margin:0 0 16px;">
                                Hi <strong style="color:#111827;">{{ ucfirst($user->first_name) }}
                                    {{ ucfirst($user->last_name) }}</strong>,
                            </p>

                            <p style="margin:0 0 24px; color:#4b5563;">
                                Please click the button below to securely create a new password for your account.
                            </p>

                            <!-- Reset Password Button with Gradient -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetLink }}"
                                            style="
                                            display:block;
                                            background:linear-gradient(to right, rgb(7,139,221), rgb(7,139,221));
                                            color:rgb(255,255,255);
                                            text-decoration:none;
                                            padding:14px 24px;
                                            border-radius:12px;
                                            font-size:14px;
                                            line-height:20px;
                                            font-weight:600;
                                            text-align:center;
                                            box-shadow:0 10px 25px rgba(7,139,221,0.25);
                                        ">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Message Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
                                <tr>
                                    <td
                                        style="
                                    background:rgb(249,250,251);
                                    padding:16px;
                                    border-radius:12px;
                                    border:1px solid rgb(243,244,246);
                                    color:#4b5563;
                                    font-size:13px;
                                    line-height:20px;
                                ">
                                        If you did not request this password reset, you can safely ignore this email.
                                    </td>
                                </tr>
                            </table>

                            <!-- Regards -->
                            <p style="margin:24px 0 0; color:#4b5563;">
                                Best regards,<br>
                                <strong style="color:#111827;">Task Management Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="
                        background:rgb(249,250,251);
                        padding:20px 40px;
                        text-align:center;
                        border-top:1px solid rgb(243,244,246);
                    ">
                            <p
                                style="
                            margin:0;
                            color:#9ca3af;
                            font-size:12px;
                            line-height:18px;
                        ">
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
