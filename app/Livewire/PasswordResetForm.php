<?php

namespace App\Livewire;

use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PasswordResetForm extends Component
{
    public string $email = '';
    public string $token = '';
    public string $password = '';
    public string $passwordconfirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');

        // Validate the token and expiration
        $passwordReset = PasswordResetToken::where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (! $passwordReset || $this->tokenExpired($passwordReset)) {
            session()->flash('errormessage', 'The reset link is invalid or has expired. Please request a new one.');
            $this->redirect(route('forget.password'), navigate: true);
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',           // At least one uppercase letter
                'regex:/[a-z]/',           // At least one lowercase letter
                'regex:/[!@#$%^&*()_+\-=\[\]{}|\\:;,.<>\/?~]/',  // At least one special symbol
            ],
            'passwordconfirmation' => 'required|same:password',
        ], [
            'password.required' => 'Please enter the password.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, and one special character.',

            'passwordconfirmation.required' => 'Please confirm the password.',
            'passwordconfirmation.same' => 'The password confirmation does not match the password.',
        ]);

        // Verify the token and email again
        $passwordReset = PasswordResetToken::where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (! $passwordReset) {
            $this->dispatch('notify', ['message' => 'Invalid or expired token.', 'type' => 'error']);
            return;
        }

        // Update the user's password
        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        // Delete the used password reset token
        PasswordResetToken::where('email', $this->email)->delete();

        // Redirect to login page with success message
        session()->flash('successmessage', 'Your password has been reset successfully.');
        return $this->redirect(route('login'), navigate: true);
    }

    protected function tokenExpired(PasswordResetToken $passwordReset): bool
    {
        $expirationTime = 60; // Token is valid for 60 minutes

        $createdAt = Carbon::parse($passwordReset->created_at);
        return $createdAt->addMinutes($expirationTime)->isPast();
    }

    public function render()
    {
        return view('livewire.password-reset-form')->layout('components.layouts.login', [
            'pageTitle' => 'Reset Password | TMS',
        ]);
    }
}
