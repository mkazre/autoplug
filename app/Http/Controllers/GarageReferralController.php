<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GarageReferralController extends Controller
{
    public function index(Request $request): View
    {
        $garage = $request->user()->garage;
        abort_unless($garage, 403);

        if (! $garage->referral_code) {
            do {
                $code = strtoupper(Str::random(6));
            } while (Garage::where('referral_code', $code)->exists());
            $garage->update(['referral_code' => $code]);
        }

        $referrals = $garage->referrals()->with('referredUser', 'subscription.product')->latest()->get();
        $payouts = $garage->payouts()->latest()->get();

        return view('garage.referrals.index', compact('garage', 'referrals', 'payouts'));
    }
}
