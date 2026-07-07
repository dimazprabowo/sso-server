<?php

namespace App\Livewire\Auth;

use App\Livewire\Traits\HasNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    use HasNotification;

    #[Validate('required|email')]
    public string $email = '';

    public bool $emailSent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Email tidak ditemukan dalam sistem.');
            return;
        }

        if (!$user->isActive()) {
            $this->addError('email', 'Akun tidak aktif. Hubungi administrator.');
            return;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            $user->notify(new \App\Notifications\ResetPasswordNotification($token));
            $this->emailSent = true;
            $this->notifySuccess('Link reset password telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            $this->notifyError('Gagal mengirim email. Silakan coba lagi nanti.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
