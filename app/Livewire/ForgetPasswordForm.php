<?php

namespace App\Livewire;

use App\Mail\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Str;

class ForgetPasswordForm extends Component
{
    public $email = '';
    public $submitted = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'Please enter the email address.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }

    public function forgotPassword()
    {
        $this->submitted = true;

        $this->validate();

        $user = User::where('email', $this->email)
            ->where('status', 1)
            ->first();

        if (!$user) {
            $this->addError('email', 'No account associated with this email address was found in our system.');
            return;
        }

        PasswordResetToken::where('email', $user->email)->delete();

        $token = Str::random(60);

        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        try {
            Mail::to($user->email)
                ->send(new SendResetPasswordEmail($user, $token));

            $message = 'Password reset link sent successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            Log::error('Forgot Password Mail Error: ' . $e->getMessage());

            $message = 'Error in sending password reset email.';
            $type = 'error';
        }

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
