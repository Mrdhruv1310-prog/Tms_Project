<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ToastNotifier extends Component
{
    protected $listeners = [
        'notify' => 'handleNotify',
    ];

    public function handleNotify($data): void
    {
        $message = is_array($data) ? ($data['message'] ?? '') : $data;
        $type = is_array($data) ? ($data['type'] ?? 'success') : 'success';

        $this->dispatch('show-toast', type: $type, message: $message);
    }

    public function notifySuccess(string $message): void
    {
        $this->dispatch('show-toast', type: 'success', message: $message);
    }

    public function notifyError(string $message): void
    {
        $this->dispatch('show-toast', type: 'danger', message: $message);
    }

    public function notifyWarning(string $message): void
    {
        $this->dispatch('show-toast', type: 'warning', message: $message);
    }

    public function render()
    {
        return view('livewire.toast-notifier');
    }
}
