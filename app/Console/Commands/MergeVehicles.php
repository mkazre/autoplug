<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeVehicles extends Command
{
    protected $signature = 'app:merge-vehicles {user} {keep}';

    protected $description = 'Merge all of a user\'s vehicles into one (repoint quote requests/plans, then delete the rest).';

    public function handle(): int
    {
        $userId = (int) $this->argument('user');
        $keepId = (int) $this->argument('keep');

        $keep = Vehicle::where('user_id', $userId)->find($keepId);
        if (! $keep) {
            $this->error("Vehicle #{$keepId} not found for user #{$userId}.");

            return self::FAILURE;
        }

        $others = Vehicle::where('user_id', $userId)->where('id', '!=', $keepId)->pluck('id');
        if ($others->isEmpty()) {
            $this->info('Nothing to merge.');

            return self::SUCCESS;
        }

        DB::table('quote_requests')->whereIn('vehicle_id', $others)->update(['vehicle_id' => $keepId]);
        DB::table('plan_applications')->whereIn('vehicle_id', $others)->update(['vehicle_id' => $keepId]);
        DB::table('plan_subscriptions')->whereIn('vehicle_id', $others)->update(['vehicle_id' => $keepId]);
        $deleted = Vehicle::whereIn('id', $others)->delete();

        $this->info("Merged {$deleted} vehicle(s) into #{$keepId} ({$keep->make} {$keep->model}).");

        return self::SUCCESS;
    }
}
