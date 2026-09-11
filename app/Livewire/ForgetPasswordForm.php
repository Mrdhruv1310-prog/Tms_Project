<?php

namespace App\Livewire;

use App\Mail\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgetPasswordForm extends Component
{
    public string $email = '';
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Please enter the email address.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }

    /**
     * Helper to dispatch toast notifications safely.
     */
    private function notifyUser(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', ['message' => $message, 'type' => $type]);
    }

    public function forgotPassword()
    {
        $this->submitted = true;

        $this->validate();

        $user = User::where('email', $this->email)
            ->where('status', 1)
            ->first();

        if (! $user) {
            $this->addError('email', 'No account associated with this email address was found in our system.');
            return;
        }

        // Clean up old tokens for this email
        PasswordResetToken::where('email', $user->email)->delete();

        $token = Str::random(60);

        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new SendResetPasswordEmail($user, $token));

            $this->notifyUser('Password reset link sent successfully.', 'success');
        } catch (\Exception $e) {
            Log::error('Forgot Password Mail Error: ' . $e->getMessage());

            $this->notifyUser('Error in sending password reset email.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.forget-password-form')->layout('components.layouts.login', [
            'pageTitle' => 'Forgot Password | TMS',
        ]);
    }
}
