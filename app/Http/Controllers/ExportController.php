<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Garage;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\User;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function users(): StreamedResponse
    {
        $rows = User::with('fleet')->latest()->get()->map(fn ($u) => [
            $u->id, $u->name, $u->email, $u->phone, $u->role, $u->fleet?->name, $u->created_at?->format('Y-m-d H:i'),
        ]);

        return $this->stream('users', ['ID', 'Name', 'Email', 'Phone', 'Role', 'Fleet', 'Registered'], $rows);
    }

    public function quoteRequests(): StreamedResponse
    {
        $rows = QuoteRequest::with(['user', 'vehicle', 'service'])->withCount('requestGarages')->latest()->get()
            ->map(fn ($q) => [
                $q->id, $q->user?->name, $q->user?->email, $q->user?->phone,
                $q->service?->name ?? 'General',
                trim(($q->vehicle?->make.' '.$q->vehicle?->model)),
                $q->vehicle?->registration, $q->description,
                $q->request_garages_count, $q->status, $q->created_at?->format('Y-m-d H:i'),
            ]);

        return $this->stream('quote-requests', ['ID', 'Customer', 'Email', 'Phone', 'Service', 'Vehicle', 'Reg', 'Description', 'Garages', 'Status', 'Created'], $rows);
    }

    public function quotes(): StreamedResponse
    {
        $rows = Quote::with(['quoteRequestGarage.branch.garage', 'quoteRequestGarage.quoteRequest.user'])->latest()->get()
            ->map(fn ($q) => [
                $q->id,
                $q->quoteRequestGarage?->branch?->garage?->name,
                $q->quoteRequestGarage?->quoteRequest?->user?->name,
                $q->total_price, $q->valid_until?->format('Y-m-d'), $q->status, $q->created_at?->format('Y-m-d H:i'),
            ]);

        return $this->stream('quotes', ['ID', 'Garage', 'Customer', 'Total', 'Valid until', 'Status', 'Created'], $rows);
    }

    public function bookings(): StreamedResponse
    {
        $rows = Booking::with(['user', 'branch.garage', 'quote.quoteRequestGarage.quoteRequest.service', 'payment'])->latest()->get()
            ->map(fn ($b) => [
                $b->id, $b->user?->name, $b->branch?->garage?->name, $b->branch?->name,
                $b->quote?->quoteRequestGarage?->quoteRequest?->service?->name ?? 'General',
                $b->scheduled_at?->format('Y-m-d H:i'), $b->quote?->total_price, $b->status, $b->payment?->status,
                $b->created_at?->format('Y-m-d H:i'),
            ]);

        return $this->stream('bookings', ['ID', 'Customer', 'Garage', 'Branch', 'Service', 'Scheduled', 'Amount', 'Status', 'Payment', 'Booked'], $rows);
    }

    public function garages(): StreamedResponse
    {
        $rows = Garage::with('user')->withCount('branches')->latest()->get()
            ->map(fn ($g) => [
                $g->id, $g->name, $g->user?->name, $g->user?->email, $g->status, $g->branches_count, $g->created_at?->format('Y-m-d H:i'),
            ]);

        return $this->stream('garages', ['ID', 'Garage', 'Owner', 'Owner email', 'Status', 'Branches', 'Created'], $rows);
    }

    public function payments(): StreamedResponse
    {
        $rows = Payment::with('booking.user')->latest()->get()
            ->map(fn ($p) => [
                $p->id, $p->reference, $p->booking_id, $p->booking?->user?->name, $p->amount, $p->gateway, $p->status,
                $p->paid_at?->format('Y-m-d H:i'), $p->created_at?->format('Y-m-d H:i'),
            ]);

        return $this->stream('payments', ['ID', 'Reference', 'Booking', 'Customer', 'Amount', 'Gateway', 'Status', 'Paid at', 'Created'], $rows);
    }

    private function stream(string $name, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $writer = new Writer();
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues($headers));
            foreach ($rows as $row) {
                $writer->addRow(Row::fromValues(array_map(fn ($v) => $v ?? '', $row)));
            }
            $writer->close();
        }, $name.'-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
