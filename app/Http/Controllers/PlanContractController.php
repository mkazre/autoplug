<?php

namespace App\Http\Controllers;

use App\Models\PlanSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanContractController extends Controller
{
    public function download(Request $request, PlanSubscription $planSubscription)
    {
        $user = $request->user();
        abort_unless((int) $planSubscription->user_id === (int) $user->id || $user->hasRole('admin'), 403);
        abort_unless($planSubscription->contract_path && Storage::disk('local')->exists($planSubscription->contract_path), 404);

        return Storage::disk('local')->download($planSubscription->contract_path, 'plan-contract-'.$planSubscription->id.'.pdf');
    }
}
