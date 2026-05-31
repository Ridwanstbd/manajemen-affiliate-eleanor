<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Kata Sandi – Manajemen Affiliate')
            ->greeting('Halo, ' . ($notifiable->username ?? 'Pengguna') . '!')
            ->line('Kami menerima permintaan untuk mereset kata sandi akun Manajemen Affiliate Anda.')
            ->line('Klik tombol di bawah ini untuk membuat kata sandi baru. Tautan ini hanya berlaku selama **60 menit**.')
            ->action('Reset Kata Sandi', $url)
            ->line('Jika Anda tidak merasa membuat permintaan ini, abaikan email ini. Kata sandi Anda tidak akan berubah.')
            ->salutation('Salam, Tim Manajemen Affiliate');
    }
}