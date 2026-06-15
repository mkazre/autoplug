<?php

namespace App\Http\Controllers;

use App\Models\PlanApplication;
use App\Support\PlanLifecycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanReviewController extends Controller
{
    public function sign(Request $request, PlanApplication $planApplication): View
    {
        abort_unless(in_array($planApplication->status, ['submitted', 'under_review']), 404);
        $planApplication->load('user', 'vehicle', 'product', 'tier');

        return view('admin.plan-sign', ['app' => $planApplication]);
    }

    public function approve(Request $request, PlanApplication $planApplication): RedirectResponse
    {
        abort_unless(in_array($planApplication->status, ['submitted', 'under_review']), 422);

        $data = $request->validate([
            'admin_signature' => ['required', 'string'],
            'confirm' => ['accepted'],
        ]);

        PlanLifecycle::approve(
            $planApplication,
            $request->user()->id,
            $data['admin_signature'],
            $request->user()->name,
            $request->ip(),
        );

        return redirect('/admin/plan-applications');
    }
}
