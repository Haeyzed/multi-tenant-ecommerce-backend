<?php

declare(strict_types=1);

namespace App\Listeners\Central;

use App\Events\Central\TenantCreated;
use App\Listeners\Central\Concerns\BuildsCentralMailTemplateData;
use App\Notifications\Central\TemplatedEmailNotification;
use Illuminate\Support\Facades\Notification;

class SendTenantWelcomeEmail
{
    use BuildsCentralMailTemplateData;

    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;
        $user = $event->user;

        $primaryDomain = $tenant->domains()
            ->where('is_primary', true)
            ->first();

        $domain = $primaryDomain?->domain ?? $tenant->domains->first()?->domain;
        $platformUrl = $domain ? 'https://'.$domain : config('app.url');
        $loginUrl = $domain ? 'https://'.$domain.'/login' : $this->centralLoginUrl();
        $platformName = $this->platformName();

        Notification::route('mail', $tenant->email)
            ->notify(new TemplatedEmailNotification('tenant_welcome', [
                'tenant_name' => $tenant->name,
                'tenant_domain' => $domain,
                'admin_email' => $user->email,
                'platform_url' => $platformUrl,
                'platform_name' => $platformName,
            ]));

        $user->notify(new TemplatedEmailNotification('tenant_user_created', [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_password' => $event->plainPassword,
            'tenant_name' => $tenant->name,
            'login_url' => $loginUrl,
            'platform_name' => $platformName,
        ]));
    }
}
