<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ToastNotifier extends Component
{
    /** @var array<string, string> */
    protected $listeners = [
        'notify' => 'handleNotify',
    ];

    /**
     * @param array{message?: string, type?: string}|string $data
     */
    public function handleNotify(mixed $data): void
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

    public function render(): mixed
    {
        return view('livewire.toast-notifier');
    }
}
