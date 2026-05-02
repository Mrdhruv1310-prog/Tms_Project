<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ToastNotifier extends Component
{
    public function notifySuccess($message)
    {
        $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => $message]);
    }

    public function notifyError($message)
    {
        $this->dispatchBrowserEvent('show-toast', ['type' => 'danger', 'message' => $message]);
    }

    public function notifyWarning($message)
    {
        $this->dispatchBrowserEvent('show-toast', ['type' => 'warning', 'message' => $message]);
    }

    public function render()
    {
        return view('livewire.toast-notifier');
    }
}