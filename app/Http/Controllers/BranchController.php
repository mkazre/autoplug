<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $branches = $request->user()->garage->branches()->latest()->get();

        return view('garage.branches.index', compact('branches'));
    }

    public function create(): View
    {
        return view('garage.branches.form', ['branch' => new Branch()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->garage->branches()->create($this->validated($request));

        return redirect()->route('garage.branches.index')->with('status', 'Branch created.');
    }

    public function edit(Request $request, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        return view('garage.branches.form', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $branch->update($this->validated($request));

        return redirect()->route('garage.branches.index')->with('status', 'Branch updated.');
    }

    public function destroy(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $branch->delete();

        return redirect()->route('garage.branches.index')->with('status', 'Branch deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function authorizeBranch(Request $request, Branch $branch): void
    {
        abort_unless((int) $branch->garage_id === (int) $request->user()->garage?->id, 403);
    }
}
