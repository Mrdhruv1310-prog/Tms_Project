<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Reminder</title>
</head>

<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb; padding:30px 12px;">
        <tr>
            <td align="center">

                <table width="620" cellpadding="0" cellspacing="0"
                    style="width:100%; max-width:620px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,0.10);">

                    <tr>
                        <td style="background:#0f172a; padding:28px 32px; text-align:center;">

                            <img src="{{ asset('icons/tms.png') }}"
                                alt="Company Logo"
                                style="max-width:110px; height:auto; margin-bottom:16px;">

                            <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                                Task Reminder
                            </h1>

                            <p style="margin:8px 0 0; color:#cbd5e1; font-size:14px;">
                                You have a pending task that requires your attention.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px; color:#334155; font-size:15px; line-height:1.6;">

                            <p style="margin:0 0 18px;">
                                Dear
                                <strong style="color:#0f172a;">
                                    {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                                </strong>,
                            </p>

                            <p style="margin:0 0 22px;">
                                {{ $remark ?? 'This is a reminder regarding your assigned task.' }}
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">

                                <tr>
                                    <td colspan="2"
                                        style="background:#f8fafc; padding:14px 18px; color:#0f172a; font-size:16px; font-weight:700; border-bottom:1px solid #e2e8f0;">
                                        Task Details
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width:35%; padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:600;">
                                        Task ID
                                    </td>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#0f172a;">
                                        #{{ $task->id ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width:35%; padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:600;">
                                        Task Title
                                    </td>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#0f172a;">
                                        {{ $task->title ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:600;">
                                        Description
                                    </td>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#0f172a;">
                                        {{ $description ?? ($task->description ?? 'N/A') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:600;">
                                        Reminder Message
                                    </td>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#0f172a;">
                                        {{ $remark ?? 'No reminder message available.' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:600;">
                                        Due Date
                                    </td>
                                    <td style="padding:14px 18px; border-bottom:1px solid #e2e8f0; color:#0f172a;">
                                        @if(!empty($due_date))
                                            {{ \Carbon\Carbon::parse($due_date)->format('d M Y h:i A') }}
                                        @elseif(!empty($task->due_date))
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y h:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:14px 18px; color:#64748b; font-weight:600;">
                                        Priority
                                    </td>
                                    <td style="padding:14px 18px;">
                                        @php
                                            $priorityText = ucfirst($priority ?? ($task->priority ?? 'Low'));
                                        @endphp

                                        <span style="display:inline-block; padding:6px 12px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:13px; font-weight:700;">
                                            {{ $priorityText }}
                                        </span>
                                    </td>
                                </tr>

                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $task_url ?? url('/tasks?task_view=tasks&task_id=' . ($task->id ?? '')) }}"
                                            style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:10px; font-size:15px; font-weight:700;">
                                            View Pending Task
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:28px 0 0; color:#475569;">
                                Best regards,<br>
                                <strong style="color:#0f172a;">
                                    Task Management Team
                                </strong>
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc; padding:18px 28px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; color:#64748b; font-size:12px; line-height:1.5;">
                                This is an automated reminder email. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
