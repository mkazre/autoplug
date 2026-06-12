<?php

namespace App\Console\Commands;

use App\Models\Quote;
use App\Models\QuoteRequestGarage;
use Illuminate\Console\Command;

class ExpireStale extends Command
{
    protected $signature = 'app:expire-stale';

    protected $description = 'Mark quotes and quote-requests whose acceptance/response timers have elapsed as expired.';

    public function handle(): int
    {
        $now = now();

        $garages = QuoteRequestGarage::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->update(['status' => 'expired']);

        $quotes = Quote::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->update(['status' => 'expired']);

        $this->info("Expired {$garages} request-garage(s) and {$quotes} quote(s).");

        return self::SUCCESS;
    }
}
