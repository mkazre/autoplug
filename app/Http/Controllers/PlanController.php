<?php

namespace App\Http\Controllers;

use App\Models\PlanProduct;
use App\Models\PlanSubscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        $products = PlanProduct::where('is_active', true)
            ->with('pricingTiers')
            ->orderBy('type')
            ->get();

        $applications = $request->user()->planApplications()
            ->with('product')
            ->latest()
            ->get();

        $subscriptions = $request->user()->planSubscriptions()
            ->with(['product', 'vehicle', 'installments'])
            ->latest()
            ->get();

        return view('plans.index', compact('products', 'applications', 'subscriptions'));
    }

    public function subscription(Request $request, PlanSubscription $planSubscription): View
    {
        abort_unless((int) $planSubscription->user_id === (int) $request->user()->id, 403);

        $planSubscription->load([
            'product', 'vehicle',
            'installments' => fn ($q) => $q->orderBy('installment_number'),
            'payments' => fn ($q) => $q->latest(),
        ]);

        return view('plans.subscription', ['sub' => $planSubscription]);
    }
}
