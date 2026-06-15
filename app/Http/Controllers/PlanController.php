<?php

namespace App\Http\Controllers;

use App\Models\PlanProduct;
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

        return view('plans.index', compact('products', 'applications'));
    }
}
