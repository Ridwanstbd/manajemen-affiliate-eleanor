<?php

namespace App\Services\Affiliator;

use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function getProfileData(): array
    {
        $user = Auth::user();
        $statusDisplay = 'Tidak Diketahui';
        
        switch ($user->account_status) {
            case 'ACTIVE':
                $statusDisplay = 'Akun Aktif';
                break;
            case 'PENDING':
                $statusDisplay = 'Menunggu Persetujuan';
                break;
            case 'BANNED':
                $statusDisplay = 'Akun Diblokir';
                break;
            default:
                $statusDisplay = ucfirst(strtolower($user->account_status));
                break;
        }

        return [
            'username' => str_starts_with($user->username, '@') ? $user->username : '@' . $user->username,
            'email'    => $user->email ?? 'Belum mengatur email',
            'phone'    => $user->phone_number ?? 'Belum mengatur nomor HP',
            'status'   => $statusDisplay,
        ];
    }

    public function getMenuActions(): array
    {
        return [
            [
                'title'    => 'Pengajuan Sampel Saya',
                'subtitle' => 'Lacak pengiriman & konfirmasi sampel diterima.',
                'icon'     => 'invoices',
                'route'    => '#',
            ],
            [
                'title'    => 'Integritas & Status Akun',
                'subtitle' => 'Rapor kepatuhan tugas dan peringatan (warning).',
                'icon'     => 'check-circle', 
                'route'    => '#',
            ],
            [
                'title'    => 'Pengaturan Keamanan',
                'subtitle' => 'Ubah kata sandi dan pemulihan keamanan.',
                'icon'     => 'gear',
                'route'    => '#',
            ],
            [
                'title'    => 'Persetujuan Kerjasama',
                'subtitle' => 'Lihat kembali Syarat & Ketentuan (Agreement).',
                'icon'     => 'journal',
                'route'    => '#',
            ],
        ];
    }
}