<?php

use App\Livewire\TeamPerformance;
use App\Livewire\CategoryManager;
use App\Livewire\CategoryReport;
use App\Livewire\Dashboard;
use App\Livewire\ExportTaskModal;
use App\Livewire\ForgetPasswordForm;
use App\Livewire\Forms\LoginForm;
use App\Livewire\Forms\RegisterForm;
use App\Livewire\GroupPerformance;
use App\Livewire\PasswordResetForm;
use App\Livewire\TaskTable;
use App\Livewire\UserGroupCard;
use App\Livewire\Users;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/password/reset/{token}', PasswordResetForm::class)->name('password.reset');
Route::get('/forget-password', ForgetPasswordForm::class)->name('forget.password');
Route::group(['middleware' => ['auth', 'prevent-back-history']], function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/tasks', TaskTable::class)->name('tasks');
    Route::get('/categoryReport', CategoryReport::class)->name('categoryReport');
    Route::get('/teamPerformance', TeamPerformance::class)->name('teamPerformance');
    Route::get('/export', ExportTaskModal::class)->name('export');

    Route::group(['middleware' => 'role:admin'], function () {
        Route::get('/categories', CategoryManager::class)->name('categories');
        Route::get('/users', Users::class)->name('users');
        Route::get('/manageusergroup', UserGroupCard::class)->name('manageusergroup');
        Route::get('/group/{id}', GroupPerformance::class)->name('group.details');
    });
});


Route::middleware(['guest'])->group(function () {
    Route::get('/register', RegisterForm::class)->name('register');
    Route::get('/login', LoginForm::class)->name('login');
    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

Route::get('clear', function () {
    // Clear compiled files and caches
    Artisan::call('optimize:clear');

    // Clear application cache
    Artisan::call('cache:clear');

    // Clear route cache
    Artisan::call('route:clear');

    // Clear configuration cache
    Artisan::call('config:clear');

    // Clear view cache
    Artisan::call('view:clear');

    // Rebuild caches
    // Cache the configuration
    Artisan::call('config:cache');

    // Cache the routes
    Artisan::call('route:cache');

    // Cache the views
    Artisan::call('view:cache');

    return 'Caches cleared and rebuilt successfully.';
});
