<?php

namespace App\Listeners\Central;

use App\Events\Central\TenantCreated;
use App\Notifications\Central\TenantWelcomeNotification;
use Illuminate\Support\Facades\Notification;

class SendTenantWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenantCreated $event): void
    {
        Notification::route('mail', $event->tenant->email)
            ->notify(new TenantWelcomeNotification($event->tenant));
    }
}
