<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Status Updated</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fdf3ec;
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        @media only screen and (max-width: 600px) {

            .main {
                width: 100% !important;
            }

            .content {
                padding: 15px !important;
            }

            .button {
                width: 100% !important;
                display: block !important;
                text-align: center !important;
            }
        }
    </style>

</head>

<body>

    <table class="main"
        align="center"
        width="600"
        style="margin:auto; background:#ffffff;">

        <!-- Logo -->
        <tr>
            <td align="center" style="padding:20px;">
             <img src="{{ asset('icons/tms.png') }}">
            </td>
        </tr>

        <!-- Heading -->
        <tr>
            <td align="center"
                style="padding:10px 20px 0 20px;">

                <h1 style="margin:0; font-size:26px; color:#333;">
                    Task Status Updated
                </h1>

            </td>
        </tr>

        <!-- Header Bar -->
        <tr>
            <td align="center"
                style="
                    background:#007bff;
                    color:#ffffff;
                    padding:12px;
                    font-size:18px;
                    font-weight:bold;
                ">

                Task Update Notification

            </td>
        </tr>

        <!-- Content -->
        <tr>

            <td class="content"
                style="
                    padding:25px;
                    color:#555;
                    font-size:16px;
                    line-height:24px;
                ">

                Dear
                <strong style="color:#2b7a78;">
                    {{ $user->first_name ?? '' }}
                    {{ $user->last_name ?? '' }}
                </strong>,

                <br><br>

                Your assigned task has been updated successfully.

                <br><br>

                <strong>Updated Task Details:</strong>

                <table width="100%"
                    style="
                        margin-top:15px;
                        border-collapse:collapse;
                    ">

                    <tr>
                        <td style="padding:10px; border:1px solid #eee;">
                            <strong>Task Title</strong>
                        </td>

                        <td style="padding:10px; border:1px solid #eee;">
                            {{ $task->title ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px; border:1px solid #eee;">
                            <strong>Description</strong>
                        </td>

                        <td style="padding:10px; border:1px solid #eee;">
                            {{ $task->description ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px; border:1px solid #eee;">
                            <strong>Remark</strong>
                        </td>

                        <td style="padding:10px; border:1px solid #eee;">
                            {{ $remark ?? 'No remark added' }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px; border:1px solid #eee;">
                            <strong>Due Date</strong>
                        </td>

                        <td style="padding:10px; border:1px solid #eee;">

                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y h:i A') }}

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px; border:1px solid #eee;">
                            <strong>Priority</strong>
                        </td>

                        <td style="padding:10px; border:1px solid #eee;">
                            {{ ucfirst($task->priority ?? 'Low') }}
                        </td>
                    </tr>

                </table>

                <br><br>

                Please review the updated task details.

                <br><br>

                <!-- Button -->
                <table width="100%">
                    <tr>
                        <td align="center">

                            <a href="{{ url('/tasks?task_view=tasks') }}"
                                class="button"
                                style="
                                    background-color:#007bff;
                                    color:#ffffff;
                                    padding:12px 25px;
                                    text-decoration:none;
                                    border-radius:5px;
                                    display:inline-block;
                                    font-weight:bold;
                                ">
                                View Task
                            </a>
                        </td>
                    </tr>
                </table>
                <br><br>
                Best regards,
                <br>

                <strong>
                    Task Management Team
                </strong>
            </td>
        </tr>

        <tr>
            <td align="center"
                style="
                    background:#f5f5f5;
                    padding:15px;
                    font-size:12px;
                    color:#777;
                ">

                This is an automated notification email.
                Please do not reply.
            </td>
        </tr>
    </table>

</body>

</html>
