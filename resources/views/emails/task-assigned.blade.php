{{-- <!DOCTYPE html>
<html>
<head>
    <title>Task Assigned</title>
</head>
<body>
    <h1>Task Assigned</h1>
    <p>Dear {{ $user->name }},</p>
    <p>You have been assigned a new task:</p>
    <ul>
        <li><strong>Title:</strong> {{ $task->title }}</li>
        <li><strong>Description:</strong> {{ $task->description }}</li>
        <li><strong>Due Date:</strong> {{ $task->due_date }}</li>
        <li><strong>Priority:</strong> {{ ucfirst($task->priority) }}</li>
    </ul>
    <p>Please complete this task by the due date.</p>
    <p>Best regards,<br>Your Task Management Team</p>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assigned</title>

    <style>
        @media only screen and (max-width: 600px) {
            .main {
                width: 320px !important;
            }

            .content {
                padding: 15px !important;
            }

            .btn a {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>

</head>

<body style="margin:0; padding:0; background-color:#fdf3ec; font-family:Arial, sans-serif;">

    <table class="main" align="center" width="600" style="margin:auto; background:#ffffff; border-collapse:collapse;">

        <!-- Logo -->
        <tr>
            <td align="center" style="padding:20px;">
                <img src="{{ asset('icons/tms.png') }}" width="120" alt="Logo">
            </td>
        </tr>

        <!-- Main Heading -->
        <tr>
            <td align="center" style="padding:10px 20px 0 20px;">
                <h1 style="margin:0; font-size:26px; color:#333;">Task Assigned</h1>
            </td>
        </tr>

        <!-- Sub Header -->
        <tr>
            <td
                style="background:#28a745; color:#ffffff; text-align:center; padding:12px; font-size:18px; font-weight:bold;">
                New Task Notification
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td class="content" style="padding:25px; color:#555; font-size:16px; line-height:24px;">
                ```
                Dear <strong style="color:#2b7a78;">{{ $user->name }}</strong>,
                <br><br>

                You have been assigned a new task. Please find the details below:

                <br><br>

                <strong>Task Details:</strong>

                <table width="100%" style="margin-top:10px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px; border:1px solid #eee;"><strong>Title</strong></td>
                        <td style="padding:8px; border:1px solid #eee;">
                            {{ $task->title }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px; border:1px solid #eee;"><strong>Description</strong></td>
                        <td style="padding:8px; border:1px solid #eee;">
                            {{ $task->description }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px; border:1px solid #eee;"><strong>Due Date</strong></td>
                        <td style="padding:8px; border:1px solid #eee;">
                            {{ $task->due_date }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px; border:1px solid #eee;"><strong>Priority</strong></td>
                        <td style="padding:8px; border:1px solid #eee;">
                            {{ ucfirst($task->priority) }}
                        </td>
                    </tr>
                </table>

                <br>

                Please make sure to complete this task by the due date.

                <br><br>

                <!-- Button -->
                <div class="btn" style="text-align:center;">
                    <a href="#"
                        style="background-color:#ff8300; color:#ffffff;padding:12px 25px;text-decoration:none;
                    border-radius:5px;display:inline-block;font-weight:bold;">
                        View Task
                    </a>
                </div>

                <br><br>

                Best regards,<br>
                <strong>Your Task Management Team</strong>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#f5f5f5; text-align:center; padding:15px; font-size:12px; color:#777;">
                *This is an automated notification. Please do not reply to this email.*
            </td>
        </tr>

    </table>

</body>

</html>
