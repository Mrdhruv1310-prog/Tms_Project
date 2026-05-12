<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body style="font-family: Arial; background:#f5f5f5; padding:30px;">

    <div
        style="
        max-width:600px;
        margin:auto;
        background:white;
        padding:30px;
        border-radius:10px;
    ">

        <div style="text-align:center;">

            <img src="{{ url('icons/tms.png') }}" width="120" alt="Logo">

        </div>

        <h2>
            Hi {{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }},
        </h2>

        <p>
            We received a request to reset your password.
        </p>

        <p>
            Click the button below:
        </p>

        <a href="{{ $resetLink }}"
            style="
                background:#007bff;
                color:white;
                padding:12px 20px;
                text-decoration:none;
                border-radius:5px;
                display:inline-block;
           ">
            Reset Password
        </a>

        <p style="margin-top:30px;">
            If you did not request this,
            please ignore this email.
        </p>

    </div>

</body>

</html>
