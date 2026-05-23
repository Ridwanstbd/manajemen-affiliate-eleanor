<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ImportFinishedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Sinkronisasi Produk Selesai',
            'desc'  => 'Proses background telah selesai. Data produk Anda sudah diperbarui dari file Excel.',
            'type'  => 'product_updated',
            'route' => '#',
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Sinkronisasi Produk Selesai ✅')
            ->body('Proses antrean background telah selesai. Data produk telah diperbarui.')
            ->data(['url' => route('admin-dashboard.dashboard')]);
    }
}