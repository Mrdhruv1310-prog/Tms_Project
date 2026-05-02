<?php

namespace App\Livewire;

use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PasswordResetForm extends Component
{
    public $email;
    public $token;
    public $password;
    public $passwordconfirmation;
    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email'); // Get the email from the query string

        // Validate the token
        $passwordReset = PasswordResetToken::where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (!$passwordReset || $this->tokenExpired($passwordReset)) {
            // Redirect to the forgot password form if token is invalid or expired
            session()->flash('errormessage', 'The reset link is invalid or has expired. Please request a new one.');
            return redirect()->route('forget.password');
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
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one special symbol.',
            
            'passwordconfirmation.required' => 'The password confirmation field is required.',
            'passwordconfirmation.same' => 'The confirm password does not match with above password.',
        ]);
        

        // Verify the token and email
        $passwordReset = PasswordResetToken::where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (!$passwordReset) {
            $this->notify('Invalid or expired token.', 'error');
            return;
        }

        // Update the user's password
        $user = User::where('email', $this->email)->first();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Delete the password reset token
        PasswordResetToken::where('email', $this->email)->delete();

        // Redirect to login page with success message
        session()->flash('successmessage', 'Your password has been reset successfully.');
        return redirect()->route('login');
    }

    protected function tokenExpired($passwordReset)
    {
        $expirationTime = 60; // Token is valid for 60 minutes
    
        // Manually create a Carbon instance from the created_at string
        $createdAt = Carbon::parse($passwordReset->created_at);
        // Check if the token is older than the expiration time
        return $createdAt->addMinutes($expirationTime)->isPast();
    }

    public function render()
    {
        return view('livewire.password-reset-form')->layout('components.layouts.login', [
            'pageTitle' => 'Reset Password | TMS',
        ]);
    }
}
