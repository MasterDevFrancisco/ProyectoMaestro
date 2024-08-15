<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ZipCreatedNotification extends Notification
{
    protected $zipFilePath;

    public function __construct($zipFilePath)
    {
        $this->zipFilePath = $zipFilePath;
    }

    public function via($notifiable)
    {
        return ['database']; // O usa otro canal según tu necesidad
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Archivo ZIP creado exitosamente.',
            'file_path' => $this->zipFilePath,
            'created_at' => now()->format('Y-m-d H:i:s'), // Formatea la fecha y hora según sea necesario
        ];
    }
}
