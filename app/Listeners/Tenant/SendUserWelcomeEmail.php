<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\Tenant\UserCreated;
use App\Listeners\Tenant\Concerns\BuildsStoreMailTemplateData;
use App\Notifications\Tenant\TemplatedEmailNotification;

/**
 * Sends welcome / credentials when staff is created via the user module (not auth self-register).
 */
class SendUserWelcomeEmail
{
    use BuildsStoreMailTemplateData;

    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        if ($event->plainPassword !== null && $event->plainPassword !== '') {
            $user->notify(new TemplatedEmailNotification('store_user_created', [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_password' => $event->plainPassword,
                'login_url' => $this->storeLoginUrl(),
                'store_name' => $this->storeName(),
            ]));

            return;
        }

        $user->notify(new TemplatedEmailNotification('store_user_welcome', [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'login_url' => $this->storeLoginUrl(),
            'store_name' => $this->storeName(),
        ]));
    }
}
