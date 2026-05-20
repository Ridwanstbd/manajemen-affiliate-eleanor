<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\SampleRequest;

class SampleStatusNotification extends Notification
{
    use Queueable;

    protected $sampleRequest;
    protected $status;

    public function __construct(SampleRequest $sampleRequest, $status)
    {
        $this->sampleRequest = $sampleRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray($notifiable)
    {
        $messages = [
            'APPROVED' => 'Admin sedang memproses logistik pengiriman paket Anda.',
            'SHIPPED'  => 'Dikirim via ' . ($this->sampleRequest->courier ?? 'Kurir') . '. Resi: ' . ($this->sampleRequest->tracking_number ?? '-'),
            'REJECTED' => 'Alasan: ' . ($this->sampleRequest->reject_reason ?? 'Tidak ada keterangan'),
        ];

        $titles = [
            'APPROVED' => 'Pengajuan Sampel Disetujui ✅',
            'SHIPPED'  => 'Paket Sampel Dikirim 🚚',
            'REJECTED' => 'Pengajuan Sampel Dibatalkan ❌',
        ];

        return [
            'title' => $titles[$this->status] ?? 'Pembaruan Sampel',
            'desc'  => $messages[$this->status] ?? '',
            'route' => route('affiliator.sample-request.show', $this->sampleRequest->id),
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