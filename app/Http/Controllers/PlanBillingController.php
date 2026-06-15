<?php

namespace App\Http\Controllers;

use App\Models\PlanInstallment;
use App\Models\PlanPayment;
use App\Support\PayFast;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
}
