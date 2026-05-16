<?php

declare(strict_types=1);

namespace App\Listeners\Central;

use App\Events\Central\TenantCreated;
use App\Notifications\Central\TemplatedEmailNotification;
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
     *
     * Sends two emails:
     * 1. Tenant welcome email to the business email
     * 2. Login credentials email to the admin user
     */
    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;
        $user = $event->user;
        $password = $event->plainPassword;

        // Get the primary domain
        $primaryDomain = $tenant->domains()
            ->where('is_primary', true)
            ->first();

        $domain = $primaryDomain?->domain ?? $tenant->domains->first()?->domain;
        $platformUrl = $domain ? 'https://' . $domain : config('app.url');
        $loginUrl = $domain ? 'https://' . $domain . '/login' : config('app.url') . '/login';

        // 1. Send a tenant welcome email to business email
        Notification::route('mail', $tenant->email)
            ->notify(new TemplatedEmailNotification('tenant_welcome', [
                'tenant_name' => $tenant->name,
                'tenant_domain' => $domain,
                'admin_email' => $user->email,
                'platform_url' => $platformUrl,
                'platform_name' => config('app.name'),
            ]));

        // 2. Send admin login credentials to the admin user
        $user->notify(new TemplatedEmailNotification('tenant_user_created', [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_password' => $password,
            'tenant_name' => $tenant->name,
            'login_url' => $loginUrl,
            'platform_name' => config('app.name'),
        ]));
    }
}
