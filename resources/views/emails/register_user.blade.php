<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register User</title>
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
            Your account has been registered successfully.
        </p>

        <p>
            Below are your login details:
        </p>

        <div
            style="
            background:#f8f9fa;
            border:1px solid #ddd;
            padding:15px;
            border-radius:8px;
            margin-top:20px;
        ">

            <p style="margin:0 0 10px 0;">
                <strong>Email:</strong> {{ $user->email }}
            </p>

            <p style="margin:0;">
                <strong>Password:</strong> {{ $plainPassword }}
            </p>

        </div>

        <p style="margin-top:30px;">
            Please keep your login details safe and do not share them with anyone.
        </p>

        <a href="{{ url('/') }}"
            style="
                background:#007bff;
                color:white;
                padding:12px 20px;
                text-decoration:none;
                border-radius:5px;
                display:inline-block;
                margin-top:10px;
           ">
            Login Now
        </a>

    </div>

</body>

</html>
