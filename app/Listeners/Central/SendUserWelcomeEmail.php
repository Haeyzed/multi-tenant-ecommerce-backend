<?php

namespace App\Listeners\Central;

use App\Events\Central\UserCreated;
use App\Notifications\Central\UserWelcomeNotification;

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
