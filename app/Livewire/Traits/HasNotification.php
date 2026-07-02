<?php

namespace App\Livewire\Traits;

trait HasNotification
{
    public function notifySuccess(string $message, string $title = 'Berhasil'): void
    {
        $this->dispatch('notify', type: 'success', title: $title, message: $message);
    }

    public function notifyError(string $message, string $title = 'Error'): void
    {
        $this->dispatch('notify', type: 'error', title: $title, message: $message);
    }

    public function notifyWarning(string $message, string $title = 'Peringatan'): void
    {
        $this->dispatch('notify', type: 'warning', title: $title, message: $message);
    }

    public function notifyInfo(string $message, string $title = 'Informasi'): void
    {
        $this->dispatch('notify', type: 'info', title: $title, message: $message);
    }

    public function notifyValidationError(\Illuminate\Validation\ValidationException $e): void
    {
        $errors = $e->validator->errors()->all();
        $message = count($errors) > 1 
            ? 'Terdapat ' . count($errors) . ' kesalahan validasi' 
            : $errors[0];
        
        $this->dispatch('notify', type: 'error', title: 'Validasi Gagal', message: $message);
    }
}
