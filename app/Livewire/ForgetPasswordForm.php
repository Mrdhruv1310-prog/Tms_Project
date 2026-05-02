<?php

namespace App\Livewire;

use App\Mail\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Str;

class ForgetPasswordForm extends Component
{
    public $email;

    public function forgotPassword()
    {
        $this->validate([
            'email' => 'required|email',
        ]);
       
            // Clear any existing reset tokens for the same email
            $user = User::where('email', $this->email)->where('status', 1)->first();
            if (!$user) {
                // Handle case when user is not found
                $this->notify('No account associated with this email address was found in our system', 'error');
                return;
            }
            PasswordResetToken::where('email', $user->email)->delete();
            // Create a password reset token
            $token = Str::random(60);
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);
            try {
                //code...
                Mail::to($user->email)->send(new SendResetPasswordEmail($user, $token));
                $message = 'Password reset link sent successfully. Please check your email.';
                $type='success';
            } catch (\Throwable $th) {
                //throw $th;
                $message = 'Error in sending password reset email. Please try again later.';
                $type='error';
            }
            // Send password reset email directly
            $this->notify($message, $type);
            return;
    }

    public function render()
    {
        return view('livewire.forget-password-form')->layout('components.layouts.login', [
            'pageTitle' => 'Forgot Password | TMS',
        ]);
    }
}
