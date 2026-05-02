<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style type="text/css">
    body {
      background-color: #fdf3ec;
      font-family: Arial, sans-serif;
      color: #4a4a4a;
      margin: 0;
      padding: 0;
    }
    .logo-container {
      text-align: center;
      padding: 20px 0;
    }
    .logo {
      width: 21vh;
      height: auto;
    }
    .email-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .header {
      text-align: center;
      font-size: 18px;
      color: #333333;
      margin-top: 20px;
    }
    .content {
      font-size: 16px;
      line-height: 1.5;
      color: #555555;
      margin: 20px 0;
    }
    .dynamic-field {
      color: #2b7a78;
      font-weight: bold;
    }
    .footer {
      margin-top: 30px;
      font-size: 14px;
      color: #777777;
      text-align: center;
    }
  </style>
</head>
<body>

<!-- Logo Section (Outside the White Box) -->
<div class="logo-container">
  <img src="{{ asset('icons/tms.png') }}" alt="Logo" class="logo">
</div>

<div class="email-container">
  <!-- Header Section -->
  <div class="header">
    Task Reminder
  </div>

  <!-- Content Section -->
  <div class="content">
    Hi <span class="dynamic-field">
      {{ Str::ucfirst($recipient->first_name) . ' ' . Str::ucfirst($recipient->last_name) }}
    </span>,
    <br><br>
    This is a quick reminder that your task "<span class="dynamic-field">{{ $task['title'] }}</span>" is scheduled for completion by <span class="dynamic-field">{{ \Carbon\Carbon::parse($task['due_date'])->format('d-m-Y H:i') }}</span>.
    <br><br>
    <strong>Task Details:</strong>
    <ul>
      <li><strong>Priority:</strong> <span class="dynamic-field">{{ ucfirst($task['priority']) }}</span></li>
      <li><strong>Assigned By:</strong>
        <span class="dynamic-field">
          {{ Str::ucfirst($assignedByUser->first_name) . ' ' . Str::ucfirst($assignedByUser->last_name) }}
        </span></li>
      <li><strong>Description:</strong> <span class="dynamic-field">{{ $task['description'] }}</span></li>
    </ul>
    Please make sure to complete it on time. For more information, log in to your task dashboard.
    <br>
  </div>

  <!-- Footer Section -->
  <div class="footer">
    *This is an automated reminder. Please do not reply to this email.*
  </div>
</div>

</body>
</html>
