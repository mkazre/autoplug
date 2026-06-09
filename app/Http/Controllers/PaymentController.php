<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function pay(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($booking->status, ['pending', 'confirmed']), 422, 'This booking cannot be paid.');

        $booking->load('quote');
        $amount = (float) ($booking->quote?->total_price ?? 0);
        abort_if($amount <= 0, 422, 'Nothing to pay.');

        if ($booking->payment && $booking->payment->status === 'paid') {
            return redirect()->route('bookings.show', $booking)->with('status', 'This booking is already paid.');
        }

        $reference = 'AP-'.$booking->id.'-'.now()->timestamp;

        $booking->payment()->updateOrCreate([], [
            'gateway' => 'payfast',
            'amount' => $amount,
            'status' => 'pending',
            'reference' => $reference,
        ]);

        $data = [
            'merchant_id' => config('payfast.merchant_id'),
            'merchant_key' => config('payfast.merchant_key'),
            'return_url' => route('payments.return'),
            'cancel_url' => route('payments.cancel'),
            'notify_url' => route('payfast.notify'),
            'name_first' => $request->user()->name,
            'email_address' => $request->user()->email,
            'm_payment_id' => $reference,
            'amount' => number_format($amount, 2, '.', ''),
            'item_name' => 'Autoplug booking #'.$booking->id,
        ];
        $data['signature'] = $this->signature($data, config('payfast.passphrase'));

        $process = config('payfast.sandbox')
            ? 'https://sandbox.payfast.co.za/eng/process'
            : 'https://www.payfast.co.za/eng/process';

        return view('payments.redirect', ['action' => $process, 'fields' => $data]);
    }

    public function return(): View
    {
        return view('payments.return');
    }

    public function cancel(): View
    {
        return view('payments.cancel');
    }

    public function notify(Request $request): Response
    {
        $data = $request->all();

        // 1. Signature check
        $expected = $this->signature($data, config('payfast.passphrase'));
        if (($data['signature'] ?? '') !== $expected) {
            Log::warning('PayFast ITN: signature mismatch', ['ref' => $data['m_payment_id'] ?? null]);
            return response('invalid signature', 400);
        }

        // 2. Server confirmation (post the data back to PayFast)
        if (! $this->serverConfirm($data)) {
            Log::warning('PayFast ITN: server validation failed', ['ref' => $data['m_payment_id'] ?? null]);
            return response('not validated', 400);
        }

        // 3. Locate our payment
        $payment = Payment::where('reference', $data['m_payment_id'] ?? '')->first();
        if (! $payment) {
            return response('payment not found', 200);
        }

        // 4. Amount must match
        if (abs((float) ($data['amount_gross'] ?? 0) - (float) $payment->amount) > 0.01) {
            Log::warning('PayFast ITN: amount mismatch', ['ref' => $payment->reference]);
            return response('amount mismatch', 400);
        }

        // 5. Apply status
        if (($data['payment_status'] ?? '') === 'COMPLETE') {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            $payment->loadMissing('booking');
            if ($payment->booking && $payment->booking->status === 'pending') {
                $payment->booking->update(['status' => 'confirmed']);
            }
        } else {
            $payment->update(['status' => 'failed']);
        }

        return response('OK', 200);
    }

    private function signature(array $data, ?string $passphrase = ''): string
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($key === 'signature') {
                continue;
            }
            if ($val === null || $val === '') {
                continue;
            }
            $pfOutput .= $key.'='.urlencode(trim((string) $val)).'&';
        }
        $getString = rtrim($pfOutput, '&');
        if (! empty($passphrase)) {
            $getString .= '&passphrase='.urlencode(trim($passphrase));
        }

        return md5($getString);
    }

    private function serverConfirm(array $data): bool
    {
        unset($data['signature']);
        $host = config('payfast.sandbox') ? 'https://sandbox.payfast.co.za' : 'https://www.payfast.co.za';

        try {
            $resp = Http::asForm()->post($host.'/eng/query/validate', $data);

            return trim($resp->body()) === 'VALID';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
