<?php

namespace App\Console\Commands;

use App\Models\Garage;
use App\Models\GaragePayout;
use App\Models\GarageReferral;
use App\Notifications\GaragePayoutRun;
use App\Support\Settings;
use Illuminate\Console\Command;

class PlanPayouts extends Command
{
    protected $signature = 'app:plan-payouts {--force}';

    protected $description = 'Settle pending garage payouts (redemptions, discount subsidies, referral rewards) per the configured cycle.';

    public function handle(): int
    {
        $cycle = Settings::get('plan_payout_cycle', 'monthly');
        $shouldRun = $this->option('force') || ($cycle === 'weekly' ? now()->isMonday() : now()->day === 1);

        if (! $shouldRun) {
            $this->info("Not a payout day for the {$cycle} cycle.");

            return self::SUCCESS;
        }

        $label = $cycle.'-'.now()->format('Y-m-d');
        $pending = GaragePayout::where('status', 'pending')->get();

        if ($pending->isEmpty()) {
            $this->info('No pending payouts.');

            return self::SUCCESS;
        }

        $settled = 0;
        foreach ($pending->groupBy('garage_id') as $garageId => $rows) {
            GaragePayout::whereIn('id', $rows->pluck('id'))->update(['status' => 'paid', 'cycle' => $label, 'paid_at' => now()]);

            $referralIds = $rows->where('type', 'referral')->pluck('source_id')->filter();
            if ($referralIds->isNotEmpty()) {
                GarageReferral::whereIn('id', $referralIds)->update(['reward_status' => 'paid', 'paid_at' => now()]);
            }

            $garage = Garage::with('user')->find($garageId);
            $garage?->user?->notify(new GaragePayoutRun((float) $rows->sum('amount'), $rows->count(), $label));
            $settled += $rows->count();
        }

        $this->info("Settled {$settled} payout(s) in cycle {$label}.");

        return self::SUCCESS;
    }
}
