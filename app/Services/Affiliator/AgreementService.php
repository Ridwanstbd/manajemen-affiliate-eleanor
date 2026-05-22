<?php

namespace App\Services\Affiliator;

use App\Models\Agreement;
use App\Models\KOLContract;
use Illuminate\Support\Facades\Auth;

class AgreementService
{
    public function getActiveAgreements()
    {
        $user = Auth::user();

        return Agreement::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                     ->orWhere(function ($q) {
                          $q->whereNull('user_id')->where('is_kol', false);
                      });

                if ($user->is_kol) {
                    $query->orWhere(function ($q) {
                        $q->whereNull('user_id')->where('is_kol', true);
                    });
                }
            })
            ->orderBy('is_kol', 'asc')
            ->latest()
            ->get();
    }

    public function getAgreementData(): array
    {
        $user        = Auth::user();
        $allActive   = $this->getActiveAgreements();

        $personal    = $allActive->whereNotNull('user_id')->where('user_id', $user->id)->values();
        $kolGeneral  = $allActive->whereNull('user_id')->where('is_kol', true)->values();
        $general     = $allActive->whereNull('user_id')->where('is_kol', false)->values();

        $activeContract = null;
        if ($user->is_kol) {
            $activeContract = KOLContract::where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->with('products')
                ->latest()
                ->first();
        }

        return compact('personal', 'kolGeneral', 'general', 'activeContract');
    }

    public function getAgreementStatus(): array
    {
        $user     = Auth::user();
        $blacklist = $user->blacklists()->latest('blacklist_date')->first();

        if ($blacklist || $user->account_status === 'BANNED') {
            return [
                'is_agreed'      => false,
                'status_text'    => 'Tidak Disetujui',
                'desc'           => $blacklist ? 'Alasan: ' . $blacklist->violation_reason : 'Kemitraan Anda telah dibatalkan/ditangguhkan karena pelanggaran.',
                'date_label'     => 'Tanggal Penangguhan:',
                'date_value'     => $blacklist ? $blacklist->blacklist_date : $user->updated_at,
                'is_blacklisted' => true,
            ];
        }

        return [
            'is_agreed'      => true,
            'status_text'    => 'Telah Disetujui',
            'desc'           => 'Berlaku untuk seluruh pengajuan sampel aktif.',
            'date_label'     => 'Terakhir disetujui:',
            'date_value'     => $user->created_at ?? now(),
            'is_blacklisted' => false,
        ];
    }
}