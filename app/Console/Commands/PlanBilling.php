<?php

namespace App\Console\Commands;

use App\Models\PlanInstallment;
use App\Models\PlanSubscription;
use App\Notifications\InstallmentOverdue;
use App\Notifications\InstallmentReminder;
use App\Notifications\PlanSuspended;
use App\Support\PlanAudit;
use App\Support\Settings;
use Illuminate\Console\Command;

class PlanBilling extends Command
{
    protected $signature = 'app:plan-billing';

    protected $description = 'Send installment reminders, flag overdue, auto-suspend, and complete plans at term end.';

    public function handle(): int
    {
        $leadDays = Settings::int('plan_reminder_lead_days', 3);
        $graceDays = Settings::int('plan_grace_days', 7);
        $suspendAfter = max(1, Settings::int('plan_suspend_after_missed', 2));

        $reminded = 0;
        PlanInstallment::where('status', 'pending')
            ->whereDate('due_date', today()->addDays($leadDays)->toDateString())
            ->whereHas('subscription', fn ($q) => $q->where('status', 'active'))
            ->with('subscription.user')
            ->get()
            ->each(function ($inst) use (&$reminded) {
                $inst->subscription?->user?->notify(new InstallmentReminder($inst));
                $reminded++;
            });

        $overdue = 0;
        PlanInstallment::where('status', 'pending')
            ->whereDate('due_date', '<', today()->subDays($graceDays)->toDateString())
            ->with('subscription.user')
            ->get()
            ->each(function ($inst) use (&$overdue) {
                $inst->update(['status' => 'overdue']);
                $inst->subscription?->user?->notify(new InstallmentOverdue($inst));
                $overdue++;
            });

        $suspended = 0;
        PlanSubscription::where('status', 'active')
            ->withCount(['installments as overdue_count' => fn ($q) => $q->where('status', 'overdue')])
            ->get()
            ->each(function ($sub) use ($suspendAfter, &$suspended) {
                if ($sub->overdue_count >= $suspendAfter) {
                    $sub->update(['status' => 'suspended', 'missed_count' => $sub->overdue_count]);
                    PlanAudit::log($sub, 'suspended', ['overdue' => $sub->overdue_count]);
                    $sub->user?->notify(new PlanSuspended($sub));
                    $suspended++;
                }
            });

        $completed = PlanSubscription::whereIn('status', ['active', 'suspended'])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today()->toDateString())
            ->update(['status' => 'completed']);

        $this->info("Reminders: {$reminded}, overdue: {$overdue}, suspended: {$suspended}, completed: {$completed}.");

        return self::SUCCESS;
    }
}
