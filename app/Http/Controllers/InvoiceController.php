<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function download(Request $request, Booking $booking): Response
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        abort_unless($booking->payment && $booking->payment->status === 'paid', 422, 'An invoice is available once payment is complete.');

        $booking->load(['branch.garage', 'quote', 'payment', 'user']);

        $html = view('invoices.booking', compact('booking'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-'.$booking->id.'.pdf"',
        ]);
    }
}
