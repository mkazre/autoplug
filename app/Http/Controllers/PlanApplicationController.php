<?php

namespace App\Http\Controllers;

use App\Models\PlanApplication;
use App\Models\PlanApplicationDocument;
use App\Models\PlanProduct;
use App\Models\User;
use App\Notifications\PlanApplicationSubmitted;
use App\Support\PlanAudit;
use App\Support\PlanEligibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlanApplicationController extends Controller
{
    public function create(Request $request, PlanProduct $planProduct): View
    {
        abort_unless($planProduct->is_active, 404);
        $planProduct->load('pricingTiers', 'benefitItems');
        $vehicles = $request->user()->vehicles()->get();

        return view('plans.apply', compact('planProduct', 'vehicles'));
    }

    public function store(Request $request, PlanProduct $planProduct): RedirectResponse
    {
        abort_unless($planProduct->is_active, 404);
        $user = $request->user();
        $isMaintenance = $planProduct->type === 'maintenance';

        $rules = [
            'vehicle_id' => ['required', 'string'],
            'odometer_km' => ['required', 'integer', 'min:0', 'max:2000000'],
            'first_registered_on' => ['required', 'date', 'before_or_equal:today'],
            'pricing_tier_id' => ['required', 'integer', 'exists:plan_pricing_tiers,id'],
            'payment_method' => ['required', 'in:upfront,installments'],
            'agree_terms' => ['accepted'],
            'signatory_name' => ['required', 'string', 'max:120'],
            'signature' => ['required', 'string'],
            'registration_papers' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
            'proof_of_address' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
            'service_history' => [$isMaintenance ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
        ];
        if ($request->input('vehicle_id') === 'new') {
            $rules['vehicle_make'] = ['required', 'string', 'max:100'];
            $rules['vehicle_model'] = ['required', 'string', 'max:100'];
            $rules['vehicle_year'] = ['nullable', 'integer', 'min:1900', 'max:2100'];
            $rules['vehicle_reg'] = ['nullable', 'string', 'max:30'];
        }
        $data = $request->validate($rules);

        $vehicle = $data['vehicle_id'] === 'new'
            ? $user->vehicles()->create([
                'make' => $data['vehicle_make'],
                'model' => $data['vehicle_model'],
                'year' => $data['vehicle_year'] ?? null,
                'registration' => $data['vehicle_reg'] ?? null,
            ])
            : $user->vehicles()->findOrFail((int) $data['vehicle_id']);

        $vehicle->update([
            'odometer_km' => $data['odometer_km'],
            'first_registered_on' => $data['first_registered_on'],
        ]);

        $tier = $planProduct->pricingTiers()->findOrFail((int) $data['pricing_tier_id']);

        $eligibility = PlanEligibility::check($vehicle->fresh(), $planProduct);
        if (! $eligibility['eligible']) {
            return back()->withInput()->withErrors(['vehicle_id' => 'Not eligible: '.implode(' ', $eligibility['reasons'])]);
        }

        $application = $user->planApplications()->create([
            'vehicle_id' => $vehicle->id,
            'plan_product_id' => $planProduct->id,
            'pricing_tier_id' => $tier->id,
            'status' => 'submitted',
            'terms_version' => $planProduct->terms_version,
            'payment_method' => $data['payment_method'],
            'referral_code' => $request->cookie('ref_code'),
            'submitted_at' => now(),
        ]);

        foreach (['registration_papers', 'id_document', 'proof_of_address', 'service_history'] as $type) {
            if ($request->hasFile($type)) {
                $path = $request->file($type)->store('plan-documents/'.$application->id, 'local');
                $application->documents()->create([
                    'document_type' => $type,
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }

        $application->signature()->create([
            'signatory_name' => $data['signatory_name'],
            'signature_path' => $this->storeSignature($data['signature'], $application->id),
            'signed_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => substr((string) $request->userAgent(), 0, 500),
            'terms_version' => $planProduct->terms_version,
        ]);

        PlanAudit::log($application, 'submitted', ['status' => 'submitted']);

        User::role('admin')->get()->each(fn ($admin) => $admin->notify(new PlanApplicationSubmitted($application)));

        return redirect()->route('plans.applications.show', $application)
            ->with('status', 'Application submitted — we will review it and be in touch.');
    }

    public function show(Request $request, PlanApplication $planApplication): View
    {
        abort_unless((int) $planApplication->user_id === (int) $request->user()->id, 403);
        $planApplication->load('product', 'vehicle', 'tier', 'documents', 'signature', 'subscription');

        return view('plans.show', ['application' => $planApplication]);
    }

    public function document(Request $request, PlanApplicationDocument $planApplicationDocument)
    {
        $application = $planApplicationDocument->application;
        $user = $request->user();
        abort_unless($application && ((int) $application->user_id === (int) $user->id || $user->hasRole('admin')), 403);
        abort_unless(Storage::disk('local')->exists($planApplicationDocument->file_path), 404);

        if ($request->boolean('download')) {
            return Storage::disk('local')->download($planApplicationDocument->file_path);
        }

        return Storage::disk('local')->response($planApplicationDocument->file_path);
    }

    private function storeSignature(string $dataUrl, int $applicationId): string
    {
        $parts = explode(',', $dataUrl, 2);
        $binary = base64_decode(end($parts) ?: '', true) ?: '';
        $path = 'plan-signatures/'.$applicationId.'-'.uniqid().'.png';
        Storage::disk('local')->put($path, $binary);

        return $path;
    }
}
