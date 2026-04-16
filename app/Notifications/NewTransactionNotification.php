<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewTransactionNotification extends Notification
{
    use Queueable;

    protected $amount;
    protected $type;

    // Terima data transaksi saat notifikasi dipanggil
    public function __construct($amount, $type)
    {
        $this->amount = $amount;
        $this->type = $type;
    }

    // Tentukan channel notifikasi (bisa lebih dari satu, misalnya 'database', 'mail', dll)
    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database']; 
    }

    // Format tampilan notifikasi yang muncul di HP/Browser
    public function toWebPush($notifiable, $notification)
    {
        $title = $this->type == 'revenue' ? 'Pemasukan Baru! 💰' : 'Pengeluaran Baru 📉';
        $body = "Transaksi sebesar Rp " . number_format($this->amount, 0, ',', '.') . " berhasil dicatat.";

        return (new WebPushMessage)
            ->title($title)
            ->icon('/icon.png') // Pastikan Anda memiliki gambar icon.png di folder public/
            ->body($body)
            ->action('Lihat Detail', 'view_dashboard'); 
    }

    // Simpan ke database aplikasi (agar bisa dilihat di menu lonceng notifikasi dalam app)
    public function toArray($notifiable)
    {
        return [
            'amount' => $this->amount,
            'type' => $this->type,
            'message' => "Transaksi Rp " . number_format($this->amount, 0, ',', '.') . " ditambahkan."
        ];
    }
}