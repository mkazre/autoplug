<?php

namespace App\Http\Controllers;

use App\Models\PlanInstallment;
use App\Models\PlanPayment;
use App\Models\User;
use App\Notifications\ManualPaymentSubmitted;
use App\Support\PayFast;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanBillingController extends Controller
{
    public function pay(Request $request, PlanInstallment $planInstallment): View|RedirectResponse
    {
        $subscription = $planInstallment->subscription;
        abort_unless($subscription && (int) $subscription->user_id === (int) $request->user()->id, 403);
        abort_if($planInstallment->status === 'paid', 422, 'This installment is already paid.');

        $amount = (float) $planInstallment->amount_due;
        abort_if($amount <= 0, 422, 'Nothing to pay.');

        $reference = 'PLAN-'.$planInstallment->id.'-'.now()->timestamp;

        PlanPayment::create([
            'plan_subscription_id' => $subscription->id,
            'plan_installment_id' => $planInstallment->id,
            'gateway' => 'payfast',
            'method' => 'card',
            'amount' => $amount,
            'status' => 'pending',
            'reference' => $reference,
        ]);

        $fields = PayFast::fields([
            'return_url' => route('payments.return'),
            'cancel_url' => route('payments.cancel'),
            'notify_url' => route('payfast.notify'),
            'name_first' => $request->user()->name,
            'email_address' => $request->user()->email,
            'm_payment_id' => $reference,
            'amount' => number_format($amount, 2, '.', ''),
            'item_name' => 'Autoplug plan installment #'.$planInstallment->installment_number,
        ]);

        return view('payments.redirect', ['action' => PayFast::processUrl(), 'fields' => $fields]);
    }

    public function manualPay(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'installment_id' => ['required', 'integer', 'exists:plan_installments,id'],
            'method' => ['required', 'in:eft,deposit'],
            'amount' => ['required', 'numeric', 'min:0'],
            'txn_reference' => ['required', 'string', 'max:120'],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
        ]);

        $installment = PlanInstallment::findOrFail($data['installment_id']);
        $subscription = $installment->subscription;
        abort_unless($subscription && (int) $subscription->user_id === (int) $request->user()->id, 403);
        abort_if($installment->status === 'paid', 422, 'This installment is already paid.');

        $proofPath = $request->file('proof')->store('plan-payment-proofs/'.$subscription->id, 'local');

        $payment = PlanPayment::create([
            'plan_subscription_id' => $subscription->id,
            'plan_installment_id' => $installment->id,
            'gateway' => 'manual',
            'method' => $data['method'],
            'amount' => $data['amount'],
            'reference' => 'PLANM-'.$installment->id.'-'.now()->timestamp,
            'txn_reference' => $data['txn_reference'],
            'paid_on' => $data['paid_on'],
            'proof_path' => $proofPath,
            'status' => 'pending',
        ]);

        User::role('admin')->get()->each(fn ($a) => $a->notify(new ManualPaymentSubmitted($payment)));

        return back()->with('status', 'Proof of payment submitted — we will verify it and activate your cover shortly.');
    }

    public function proof(Request $request, PlanPayment $planPayment)
    {
        $user = $request->user();
        $subscription = $planPayment->subscription;
        abort_unless($subscription && ((int) $subscription->user_id === (int) $user->id || $user->hasRole('admin')), 403);
        abort_unless($planPayment->proof_path && Storage::disk('local')->exists($planPayment->proof_path), 404);

        return Storage::disk('local')->response($planPayment->proof_path);
    }
}
