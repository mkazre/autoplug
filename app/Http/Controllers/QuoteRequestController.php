<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestGarage;
use App\Notifications\NewQuoteRequest;
use App\Notifications\QuoteLost;
use App\Notifications\QuoteWon;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = $request->user()->quoteRequests()
            ->withCount('requestGarages')
            ->with(['service', 'requestGarages.quote'])
            ->latest()
            ->get();

        return view('quotes.index', compact('requests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_ids' => ['required', 'array', 'min:1'],
            'branch_ids.*' => ['integer', 'exists:branches,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'service_id' => ['nullable', 'exists:services,id'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'radius' => ['nullable', 'integer'],
            'vehicle_id' => ['nullable', 'string', 'max:20'],
            'vehicle_make' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'vehicle_reg' => ['nullable', 'string', 'max:30'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $user = $request->user();

        $vehicleId = null;
        if ($request->filled('vehicle_id') && $request->input('vehicle_id') !== 'new') {
            $vehicleId = $user->vehicles()->whereKey($request->input('vehicle_id'))->value('id');
        }
        if (! $vehicleId && ! empty($data['vehicle_make']) && ! empty($data['vehicle_model'])) {
            $vehicleId = $user->vehicles()->create([
                'make' => $data['vehicle_make'],
                'model' => $data['vehicle_model'],
                'year' => $data['vehicle_year'] ?? null,
                'registration' => $data['vehicle_reg'] ?? null,
            ])->id;
        }

        $imagePaths = [];
        foreach ((array) $request->file('images', []) as $img) {
            if ($img) {
                $imagePaths[] = $img->store('quote-requests', 'public');
            }
        }

        $quoteRequest = $user->quoteRequests()->create([
            'vehicle_id' => $vehicleId,
            'service_id' => $data['service_id'] ?? null,
            'description' => $data['description'] ?? null,
            'images' => $imagePaths ?: null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'radius_km' => $data['radius'] ?? 15,
            'status' => 'open',
        ]);

        $branches = Branch::whereIn('id', $data['branch_ids'])
            ->where('is_active', true)
            ->whereHas('garage', fn ($q) => $q->where('status', 'approved'))
            ->with('garage.user')
            ->get();

        $garageExpiresAt = now()->addMinutes(Settings::int('garage_response_window_minutes', 2880));

        foreach ($branches as $branch) {
            $quoteRequest->requestGarages()->create([
                'branch_id' => $branch->id,
                'status' => 'pending',
                'expires_at' => $garageExpiresAt,
            ]);
            $branch->garage->user?->notify(new NewQuoteRequest($quoteRequest, $branch));
        }

        return redirect()->route('quotes.show', $quoteRequest)->with('status', 'Your quote request has been sent to '.$branches->count().' garage(s).');
    }

    public function show(Request $request, QuoteRequest $quoteRequest): View
    {
        abort_unless((int) $quoteRequest->user_id === (int) $request->user()->id, 403);

        $quoteRequest->load(['service', 'vehicle', 'requestGarages.branch.garage', 'requestGarages.quote']);

        $discount = \App\Support\PlanDiscount::for($request->user());

        return view('quotes.show', compact('quoteRequest', 'discount'));
    }

    public function accept(Request $request, QuoteRequest $quoteRequest, Quote $quote): RedirectResponse
    {
        abort_unless((int) $quoteRequest->user_id === (int) $request->user()->id, 403);

        $qrg = $quote->quoteRequestGarage;
        abort_unless($qrg && (int) $qrg->quote_request_id === (int) $quoteRequest->id, 403);

        if ($quote->isExpired()) {
            return back()->with('error', 'This quote has expired and can no longer be accepted. You can request a fresh quote from the garage.');
        }

        $quote->update(['status' => 'accepted']);
        $qrg->update(['status' => 'accepted']);

        Quote::whereHas('quoteRequestGarage', fn ($q) => $q->where('quote_request_id', $quoteRequest->id))
            ->where('id', '!=', $quote->id)
            ->update(['status' => 'rejected']);

        QuoteRequestGarage::where('quote_request_id', $quoteRequest->id)
            ->where('id', '!=', $qrg->id)
            ->where('status', 'quoted')
            ->update(['status' => 'declined']);

        $quoteRequest->update(['status' => 'closed']);

        // Notify the winning garage and the losing bidders (bell + chime + email/SMS).
        $qrg->loadMissing('branch.garage.user', 'quoteRequest.service', 'quote');
        $qrg->branch?->garage?->user?->notify(new QuoteWon($qrg));

        $losers = QuoteRequestGarage::where('quote_request_id', $quoteRequest->id)
            ->where('id', '!=', $qrg->id)
            ->where('status', 'declined')
            ->with(['branch.garage.user', 'quoteRequest.service'])
            ->get();
        foreach ($losers as $loser) {
            $loser->branch?->garage?->user?->notify(new QuoteLost($loser));
        }

        return back()->with('status', 'Quote accepted. Online booking & payment arrive in the next phase.');
    }
}
