<?php

namespace App\Livewire;

use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.login')]
#[Title('Reset Password | TMS')]
class PasswordResetForm extends Component
{
    public string $email = '';
    public string $token = '';
    public string $password = '';
    public string $passwordconfirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        $emailQuery = request()->query('email', '');
        $this->email = is_string($emailQuery) ? $emailQuery : '';

        /** @var PasswordResetToken|null $passwordReset */
        $passwordReset = PasswordResetToken::query()
            ->where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (! $passwordReset || $this->tokenExpired($passwordReset)) {
            session()->flash('errormessage', 'The reset link is invalid or has expired. Please request a new one.');
            $this->redirect(route('forget.password'), navigate: true);
        }
    }

    public function resetPassword(): mixed
    {
        $this->validate([
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[!@#$%^&*()_+\-=\[\]{}|\\:;,.<>\/?~]/',
            ],
            'passwordconfirmation' => 'required|same:password',
        ], [
            'password.required' => 'Please enter the password.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, and one special character.',

            'passwordconfirmation.required' => 'Please confirm the password.',
            'passwordconfirmation.same' => 'The password confirmation does not match the password.',
        ]);

        /** @var PasswordResetToken|null $passwordReset */
        $passwordReset = PasswordResetToken::query()
            ->where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (! $passwordReset) {
            $this->dispatch('notify', ['message' => 'Invalid or expired token.', 'type' => 'error']);
            return null;
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $this->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        PasswordResetToken::query()->where('email', $this->email)->delete();

        session()->flash('successmessage', 'Your password has been reset successfully.');
        return $this->redirect(route('login'), navigate: true);
    }

    protected function tokenExpired(PasswordResetToken $passwordReset): bool
    {
        $expirationTime = 60;

        $createdAt = Carbon::parse($passwordReset->created_at);
        return $createdAt->addMinutes($expirationTime)->isPast();
    }

    public function render(): View
    {
        return view('livewire.password-reset-form');
    }
}
