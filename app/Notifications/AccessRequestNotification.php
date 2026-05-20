<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->status === 'APPROVED') {
            return (new MailMessage)
                ->subject('Permintaan Akses Disetujui! 🎉')
                ->greeting('Selamat Datang!')
                ->line('Akun affiliator Anda kini telah aktif.')
                ->line('Silakan login menggunakan password bawaan sistem: password123')
                ->action('Login Sekarang', route('login'));
        }

        return (new MailMessage)
            ->subject('Permintaan Akses Ditangguhkan ❌')
            ->greeting('Halo,')
            ->line('Mohon maaf, permintaan akses Anda sebagai Affiliator belum dapat kami setujui saat ini.');
    }
}