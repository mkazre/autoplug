<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Notifications\NewQuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = $request->user()->quoteRequests()
            ->withCount('requestGarages')
            ->with('service')
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
            'vehicle_make' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'vehicle_reg' => ['nullable', 'string', 'max:30'],
        ]);

        $user = $request->user();

        $vehicleId = null;
        if (! empty($data['vehicle_make']) && ! empty($data['vehicle_model'])) {
            $vehicleId = $user->vehicles()->create([
                'make' => $data['vehicle_make'],
                'model' => $data['vehicle_model'],
                'year' => $data['vehicle_year'] ?? null,
                'registration' => $data['vehicle_reg'] ?? null,
            ])->id;
        }

        $quoteRequest = $user->quoteRequests()->create([
            'vehicle_id' => $vehicleId,
            'service_id' => $data['service_id'] ?? null,
            'description' => $data['description'] ?? null,
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

        foreach ($branches as $branch) {
            $quoteRequest->requestGarages()->create([
                'branch_id' => $branch->id,
                'status' => 'pending',
            ]);
            $branch->garage->user?->notify(new NewQuoteRequest($quoteRequest, $branch));
        }

        return redirect()->route('quotes.show', $quoteRequest)->with('status', 'Your quote request has been sent to '.$branches->count().' garage(s).');
    }

    public function show(Request $request, QuoteRequest $quoteRequest): View
    {
        abort_unless((int) $quoteRequest->user_id === (int) $request->user()->id, 403);

        $quoteRequest->load(['service', 'vehicle', 'requestGarages.branch.garage', 'requestGarages.quote']);

        return view('quotes.show', compact('quoteRequest'));
    }

    public function accept(Request $request, QuoteRequest $quoteRequest, Quote $quote): RedirectResponse
    {
        abort_unless((int) $quoteRequest->user_id === (int) $request->user()->id, 403);

        $qrg = $quote->quoteRequestGarage;
        abort_unless($qrg && (int) $qrg->quote_request_id === (int) $quoteRequest->id, 403);

        $quote->update(['status' => 'accepted']);
        $qrg->update(['status' => 'accepted']);

        Quote::whereHas('quoteRequestGarage', fn ($q) => $q->where('quote_request_id', $quoteRequest->id))
            ->where('id', '!=', $quote->id)
            ->update(['status' => 'rejected']);

        $quoteRequest->update(['status' => 'closed']);

        return back()->with('status', 'Quote accepted. Online booking & payment arrive in the next phase.');
    }
}
