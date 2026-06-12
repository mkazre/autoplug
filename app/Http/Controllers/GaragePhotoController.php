<?php

namespace App\Http\Controllers;

use App\Models\GaragePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GaragePhotoController extends Controller
{
    public function index(Request $request): View
    {
        $garage = $request->user()->garage;
        $photos = $garage->photos()->with('branch')->latest()->get();
        $branches = $garage->branches()->orderBy('name')->get();

        return view('garage.photos.index', compact('garage', 'photos', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $garage = $request->user()->garage;

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:4096'],
            'type' => ['required', 'in:workshop,product,affiliation'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        if ($data['type'] === 'affiliation' && $garage->photos()->where('type', 'affiliation')->count() >= 5) {
            return back()->with('error', 'You can upload up to 5 affiliation logos.');
        }

        $branchId = null;
        if (! empty($data['branch_id'])) {
            $branch = $garage->branches()->find($data['branch_id']);
            abort_unless($branch !== null, 403);
            $branchId = $branch->id;
        }

        $path = $request->file('photo')->store('garage-photos', 'public');

        $garage->photos()->create([
            'branch_id' => $branchId,
            'photo_url' => $path,
            'type' => $data['type'],
        ]);

        return back()->with('status', 'Photo uploaded.');
    }

    public function destroy(Request $request, GaragePhoto $photo): RedirectResponse
    {
        abort_unless((int) $photo->garage_id === (int) $request->user()->garage?->id, 403);

        Storage::disk('public')->delete($photo->photo_url);
        $photo->delete();

        return back()->with('status', 'Photo deleted.');
    }
}
