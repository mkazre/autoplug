<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GarageService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchServiceController extends Controller
{
    public function index(Request $request, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $branch->load('services.service');
        $available = Service::whereNotIn('id', $branch->services->pluck('service_id'))
            ->orderBy('name')
            ->get();

        return view('garage.branches.services', compact('branch', 'available'));
    }

    public function store(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $branch->services()->firstOrCreate(
            ['service_id' => $data['service_id']],
            ['price' => $data['price'] ?? null, 'notes' => $data['notes'] ?? null],
        );

        return back()->with('status', 'Service added.');
    }

    public function update(Request $request, Branch $branch, GarageService $garageService): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);
        abort_unless((int) $garageService->branch_id === (int) $branch->id, 403);

        $garageService->update($request->validate([
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Price updated.');
    }

    public function destroy(Request $request, Branch $branch, GarageService $garageService): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);
        abort_unless((int) $garageService->branch_id === (int) $branch->id, 403);

        $garageService->delete();

        return back()->with('status', 'Service removed.');
    }

    private function authorizeBranch(Request $request, Branch $branch): void
    {
        abort_unless((int) $branch->garage_id === (int) $request->user()->garage?->id, 403);
    }
}
