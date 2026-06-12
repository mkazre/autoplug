<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequestGarage;
use App\Notifications\QuoteReady;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GarageQuoteController extends Controller
{
    public function index(Request $request): View
    {
        $branchIds = $request->user()->garage->branches()->pluck('id');

        $requests = QuoteRequestGarage::whereIn('branch_id', $branchIds)
            ->with(['quoteRequest.service', 'branch', 'quote'])
            ->latest()
            ->get();

        return view('garage.requests.index', compact('requests'));
    }

    public function show(Request $request, QuoteRequestGarage $quoteRequestGarage): View
    {
        $this->authorizeQrg($request, $quoteRequestGarage);

        $quoteRequestGarage->load(['quoteRequest.service', 'quoteRequest.vehicle', 'quoteRequest.user', 'branch', 'quote']);

        return view('garage.requests.show', ['qrg' => $quoteRequestGarage]);
    }

    public function storeQuote(Request $request, QuoteRequestGarage $quoteRequestGarage): RedirectResponse
    {
        $this->authorizeQrg($request, $quoteRequestGarage);

        $quoteRequestGarage->loadMissing('quoteRequest');

        // Optionally lock quoting once the customer has accepted a quote for this request.
        if (Settings::bool('lock_quote_after_accept', true)
            && optional($quoteRequestGarage->quoteRequest)->status === 'closed') {
            return back()->with('error', 'This request has already been awarded, so quotes are locked.');
        }

        // Once the response window closes you can no longer submit a first quote.
        if ($quoteRequestGarage->status !== 'quoted' && $quoteRequestGarage->isExpired()) {
            return back()->with('error', 'The response window for this request has closed.');
        }

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $total = collect($data['items'])->sum(fn ($i) => (float) $i['price']);

        // The customer's acceptance clock starts (or restarts) the moment a quote is sent.
        $acceptExpiresAt = now()->addMinutes(Settings::int('quote_accept_window_minutes', 2880));

        $quoteRequestGarage->quote()->updateOrCreate([], [
            'items_json' => $data['items'],
            'total_price' => $total,
            'valid_until' => $data['valid_until'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
            'expires_at' => $acceptExpiresAt,
        ]);

        $quoteRequestGarage->update(['status' => 'quoted']);

        $quoteRequestGarage->load('branch.garage', 'quote');
        $quoteRequestGarage->quoteRequest->user?->notify(new QuoteReady($quoteRequestGarage));

        return redirect()->route('garage.requests.index')->with('status', 'Quote sent.');
    }

    private function authorizeQrg(Request $request, QuoteRequestGarage $qrg): void
    {
        $garageId = $request->user()->garage?->id;
        abort_unless($qrg->branch && (int) $qrg->branch->garage_id === (int) $garageId, 403);
    }
}
