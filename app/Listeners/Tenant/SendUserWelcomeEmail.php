<?php

namespace App\Listeners\Tenant;

use App\Events\Tenant\UserCreated;
use App\Notifications\Tenant\UserWelcomeNotification;

class SendUserWelcomeEmail
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
    public function handle(UserCreated $event): void
    {
        $event->user->notify(new UserWelcomeNotification());
    }
}
