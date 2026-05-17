<?php

declare(strict_types=1);

namespace App\Support\Notifications;

use App\Models\Tenant\NotificationTemplate;
use App\Models\Tenant\Setting;
use Illuminate\Support\Facades\Schema;

/**
 * Default tenant store notification email templates (event + channel unique).
 */
class TenantNotificationTemplateCatalog
{
    /**
     * @return array<string, mixed>
     */
    public static function brandingDefaults(): array
    {
        $colors = Setting::brandColors();

        return [
            'logo_url' => Setting::get('store_logo_url'),
            'logo_alt' => Setting::storeName(),
            'header_bg_color' => $colors['primary'] ?? '#1e2b2e',
            'accent_color' => $colors['accent'] ?? '#73bc1c',
            'is_active' => true,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function templates(): array
    {
        $defaults = self::brandingDefaults();

        return [
            [
                'event' => 'email_verification_otp',
                'channel' => 'email',
                'subject' => 'Verify your email - {store_name}',
                'body' => "Thank you for joining <strong>{store_name}</strong>.\n\nUse the code below to verify your email. It expires in <strong>{expires_in}</strong>.\n\n<div style=\"text-align: center; margin: 24px 0;\"><span style=\"font-size: 28px; font-weight: bold; letter-spacing: 6px; background: #f4f4f4; padding: 12px 24px; border-radius: 6px;\">{otp}</span></div>\n\nIf you did not create an account, you can ignore this email.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Best regards,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
            [
                'event' => 'password_reset_otp',
                'channel' => 'email',
                'subject' => 'Password reset code - {store_name}',
                'body' => "We received a request to reset your password for <strong>{store_name}</strong>.\n\nEnter this code to continue. It expires in <strong>{expires_in}</strong>.\n\n<div style=\"text-align: center; margin: 24px 0;\"><span style=\"font-size: 28px; font-weight: bold; letter-spacing: 6px; background: #f4f4f4; padding: 12px 24px; border-radius: 6px;\">{otp}</span></div>\n\nIf you did not request this, please ignore this email.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Stay secure,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
            [
                'event' => 'store_user_welcome',
                'channel' => 'email',
                'subject' => 'Welcome to {store_name}',
                'body' => "Your staff account at <strong>{store_name}</strong> has been created.\n\n<strong>Email:</strong> {user_email}\n\nSign in with the password provided by your store administrator. Change your password after your first login.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #73bc1c; text-decoration: none;\">{login_url}</a>",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome to the team,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
            [
                'event' => 'store_user_created',
                'channel' => 'email',
                'subject' => 'Your {store_name} account - login details',
                'body' => "An account has been created for you at <strong>{store_name}</strong>.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #73bc1c; text-decoration: none;\">{login_url}</a>\n<strong>Email:</strong> {user_email}\n<strong>Password:</strong> <code style=\"background: #f4f4f4; padding: 2px 6px; border-radius: 3px;\">{user_password}</code>\n\nPlease change your password after your first login. Do not share these credentials.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome aboard,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
        ];
    }

    public static function syncMissing(): int
    {
        if (! Schema::hasTable('notification_templates')) {
            return 0;
        }

        $created = 0;

        foreach (self::templates() as $template) {
            $record = NotificationTemplate::query()->updateOrCreate(
                [
                    'event' => $template['event'],
                    'channel' => $template['channel'],
                ],
                $template
            );

            if ($record->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    public static function syncIfEmpty(): void
    {
        if (! Schema::hasTable('notification_templates')) {
            return;
        }

        if (NotificationTemplate::query()->count() === 0) {
            self::syncMissing();
        }
    }
}
