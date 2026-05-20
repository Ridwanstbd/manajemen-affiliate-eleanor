<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AccessRequestNotification extends Notification
{
    use Queueable;

    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray($notifiable)
    {
        if ($this->status === 'APPROVED') {
            return [
                'title' => 'Permintaan Akses Disetujui! 🎉',
                'desc'  => 'Akun affiliator Anda aktif. Password bawaan: password123',
                'route' => route('login'),
            ];
        }

        return [
            'title' => 'Permintaan Akses Ditandai ❌',
            'desc'  => 'Mohon maaf, permintaan akses Anda belum dapat kami setujui.',
            'route' => '#',
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $data = $this->toArray($notifiable);

        return (new WebPushMessage)
            ->title($data['title'])
            ->body($data['desc'])
            ->icon('/img/logo.png')
            ->data(['url' => $data['route']]);
    }
}