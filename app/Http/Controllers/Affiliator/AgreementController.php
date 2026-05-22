<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Services\Affiliator\AgreementService;

class AgreementController extends Controller
{
    public function __construct(
        protected AgreementService $agreementService
    ) {}

    public function index()
    {
        $status        = $this->agreementService->getAgreementStatus();
        $agreementData = $this->agreementService->getAgreementData();

        return view('pages.affiliator.agreement.index', array_merge(
            compact('status'),
            $agreementData
        ));
    }
}