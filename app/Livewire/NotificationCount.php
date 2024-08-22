<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationCount extends Component
{
    public $notificationsCount;

    public function mount()
    {
        $this->updateNotificationsCount();
    }

    public function updateNotificationsCount()
    {
        $this->notificationsCount = Auth::user()->unreadNotifications->count();
    }

    protected $listeners = [
        'notificationUpdated' => 'updateNotificationsCount'
    ];

    public function render()
    {
        return view('livewire.notification-count');
    }
}
