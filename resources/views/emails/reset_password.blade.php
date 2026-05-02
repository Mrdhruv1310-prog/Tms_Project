<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-900">
    <section class="max-w-2xl px-6 py-8 mx-auto bg-white dark:bg-gray-900">
        <header>
            <a href="#">
                <img class="w-auto h-7 sm:h-8" src="{{ asset('icons/tms.png') }}" alt="Company Logo">
            </a>
        </header>
        <main class="mt-8">
            <h2 class="text-gray-700 dark:text-gray-200">Hi {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }},</h2>
            <br>
            <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
                We have received a request to reset your password. Please click the button below to reset your password:
            </p>
            
            <a href="{{ $resetLink }}" class="inline-block px-6 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Reset Password
            </a>
            
            <p class="mt-8 text-gray-600 dark:text-gray-300">
                If you did not request this, please ignore this email.
            </p>
        </main>
        
        <footer class="mt-8 text-gray-500 dark:text-gray-400">
        </footer>
    </section>
</body>
</html>
