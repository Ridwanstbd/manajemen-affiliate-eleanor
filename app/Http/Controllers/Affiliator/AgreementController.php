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
        $agreements = $this->agreementService->getActiveAgreements();
        $status = $this->agreementService->getAgreementStatus();

        return view('pages.affiliator.agreement.index', compact('agreements', 'status'));
    }
}