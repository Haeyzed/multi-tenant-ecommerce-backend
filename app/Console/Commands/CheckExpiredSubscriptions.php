<?php

namespace App\Console\Commands;

use App\Events\Central\SubscriptionExpired;
use App\Models\Central\Subscription;
use App\Enums\Central\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update their status';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredSubscriptions = Subscription::where('status', SubscriptionStatus::ACTIVE->value)
            ->where('ends_at', '<', Carbon::now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'status' => SubscriptionStatus::EXPIRED->value
            ]);

            event(new SubscriptionExpired($subscription));

            $this->info("Subscription ID {$subscription->id} for Tenant ID {$subscription->tenant_id} has expired.");
        }

        $this->info('Finished checking for expired subscriptions.');
    }
}
