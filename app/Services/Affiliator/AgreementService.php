<?php

namespace App\Services\Affiliator;

use App\Models\Agreement;
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

    public function getAgreementStatus()
    {
        $user = Auth::user();
        
        $blacklist = $user->blacklists()->latest('blacklist_date')->first();

        if ($blacklist || $user->account_status === 'BANNED') {
            return [
                'is_agreed'      => false,
                'status_text'    => 'Tidak Disetujui',
                'desc'           => $blacklist ? 'Alasan: ' . $blacklist->violation_reason : 'Kemitraan Anda telah dibatalkan/ditangguhkan karena pelanggaran.',
                'date_label'     => 'Tanggal Penangguhan:',
                'date_value'     => $blacklist ? $blacklist->blacklist_date : $user->updated_at,
                'is_blacklisted' => true
            ];
        }
        
        return [
            'is_agreed'      => true,
            'status_text'    => 'Telah Disetujui',
            'desc'           => 'Berlaku untuk seluruh pengajuan sampel aktif.',
            'date_label'     => 'Terakhir disetujui:',
            'date_value'     => $user->created_at ?? now(),
            'is_blacklisted' => false
        ];
    }
}