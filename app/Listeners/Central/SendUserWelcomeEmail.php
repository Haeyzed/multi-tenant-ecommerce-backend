<?php

declare(strict_types=1);

namespace App\Listeners\Central;

use App\Events\Central\UserCreated;
use App\Listeners\Central\Concerns\BuildsCentralMailTemplateData;
use App\Notifications\Central\TemplatedEmailNotification;

/**
 * Sends welcome / credentials email when an admin creates a central user (user module).
 */
class SendUserWelcomeEmail
{
    use BuildsCentralMailTemplateData;

    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        if ($event->plainPassword !== null && $event->plainPassword !== '') {
            $user->notify(new TemplatedEmailNotification('central_user_created', [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_password' => $event->plainPassword,
                'login_url' => $this->centralLoginUrl(),
                'platform_name' => $this->platformName(),
            ]));

            return;
        }

        $user->notify(new TemplatedEmailNotification('central_user_welcome', [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'login_url' => $this->centralLoginUrl(),
            'platform_name' => $this->platformName(),
        ]));
    }
}
