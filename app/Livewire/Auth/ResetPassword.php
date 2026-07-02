<?php

namespace App\Livewire\Auth;

use App\Livewire\Traits\HasNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class ResetPassword extends Component
{
    use HasNotification;

    public string $token = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    public bool $showPassword = false;
    public bool $showPasswordConfirmation = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function togglePasswordConfirmation(): void
    {
        $this->showPasswordConfirmation = !$this->showPasswordConfirmation;
    }

    public function resetPassword(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (!$record) {
            $this->addError('email', 'Token reset password tidak valid.');
            return;
        }

        if (!Hash::check($this->token, $record->token)) {
            $this->addError('email', 'Token reset password tidak valid.');
            return;
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            $this->addError('email', 'Token reset password sudah kedaluwarsa.');
            DB::table('password_reset_tokens')->where('email', $this->email)->delete();
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Email tidak ditemukan.');
            return;
        }

        $user->update(['password' => Hash::make($this->password)]);

        DB::table('password_reset_tokens')->where('email', $this->email)->delete();

        $this->notifySuccess('Password berhasil direset. Silakan login dengan password baru.');

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
