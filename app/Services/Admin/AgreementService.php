<?php

namespace App\Services\Admin;

use App\Models\Agreement;
use Illuminate\Support\Facades\DB;

class AgreementService
{

    public function createAgreement(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Agreement::create($data);
        });
    }

    public function updateAgreement(Agreement $agreement, array $data)
    {
        return DB::transaction(function () use ($agreement, $data) {
            
            return $agreement->update($data);
        });
    }

    public function deleteAgreement(Agreement $agreement)
    {
        return $agreement->delete();
    }
}