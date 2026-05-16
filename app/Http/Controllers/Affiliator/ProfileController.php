<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Services\Affiliator\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $user  = $this->profileService->getProfileData();
        $menus = $this->profileService->getMenuActions();

        return view('pages.affiliator.profile.index', compact('user', 'menus'));
    }
}