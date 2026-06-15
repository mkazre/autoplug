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
        $subscription->loadMissing('application.signature', 'application.approver', 'user', 'product', 'tier', 'vehicle', 'installments');
        $application = $subscription->application;

        $logo = Settings::get('logo');

        $html = view('plans.contract', [
            'subscription' => $subscription,
            'sigDataUri' => self::imageUri(Storage::disk('local'), $application?->signature?->signature_path),
            'adminSigUri' => self::imageUri(Storage::disk('local'), $application?->admin_signature_path),
            'logoUri' => $logo ? self::imageUri(Storage::disk('public'), $logo) : null,
        ])->render();

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

    private static function imageUri($disk, ?string $path): ?string
    {
        if (! $path || ! $disk->exists($path)) {
            return null;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'svg') {
            return null; // dompdf SVG support is unreliable; skip
        }
        $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($disk->get($path));
    }
}
