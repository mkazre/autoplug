<?php

namespace App\Support;

use App\Models\PlanSubscription;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class PlanContract
{
    public static function generate(PlanSubscription $subscription): string
    {
        $subscription->loadMissing('application.signature', 'user', 'product', 'tier', 'vehicle', 'installments');

        $signature = $subscription->application?->signature;
        $sigDataUri = null;
        if ($signature && Storage::disk('local')->exists($signature->signature_path)) {
            $sigDataUri = 'data:image/png;base64,'.base64_encode(Storage::disk('local')->get($signature->signature_path));
        }

        $html = view('plans.contract', ['subscription' => $subscription, 'sigDataUri' => $sigDataUri])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $path = 'plan-contracts/'.$subscription->id.'-'.uniqid().'.pdf';
        Storage::disk('local')->put($path, $dompdf->output());

        return $path;
    }
}
