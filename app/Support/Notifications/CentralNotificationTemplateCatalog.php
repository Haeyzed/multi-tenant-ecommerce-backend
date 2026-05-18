<?php

declare(strict_types=1);

namespace App\Support\Notifications;

use App\Models\Central\NotificationTemplate;
use App\Models\Central\Setting;
use Illuminate\Support\Facades\Schema;

/**
 * Default central notification email templates (event + channel unique).
 */
class CentralNotificationTemplateCatalog
{
    /**
     * @return array<string, mixed>
     */
    public static function brandingDefaults(): array
    {
        return Setting::templateBrandingDefaults();
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
                'subject' => 'Verify your email - {platform_name}',
                'body' => "Thank you for registering with <strong>{platform_name}</strong>.\n\nUse the verification code below to confirm your email address. This code expires in <strong>{expires_in}</strong>.\n\n<div style=\"text-align: center; margin: 24px 0;\"><span style=\"font-size: 28px; font-weight: bold; letter-spacing: 6px; background: #f4f4f4; padding: 12px 24px; border-radius: 6px;\">{otp}</span></div>\n\nIf you did not create an account, you can safely ignore this email.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Best regards,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
            [
                'event' => 'password_reset_otp',
                'channel' => 'email',
                'subject' => 'Password reset code - {platform_name}',
                'body' => "We received a request to reset your password.\n\nEnter this code to continue. It expires in <strong>{expires_in}</strong>.\n\n<div style=\"text-align: center; margin: 24px 0;\"><span style=\"font-size: 28px; font-weight: bold; letter-spacing: 6px; background: #f4f4f4; padding: 12px 24px; border-radius: 6px;\">{otp}</span></div>\n\nIf you did not request a password reset, please ignore this email or contact support.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Stay secure,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
            [
                'event' => 'central_user_welcome',
                'channel' => 'email',
                'subject' => 'Welcome to {platform_name}',
                'body' => "Your central platform account has been created.\n\n<strong>Email:</strong> {user_email}\n\nYou can sign in using the password provided by your administrator. For security, change your password after your first login.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #ff641a; text-decoration: none;\">{login_url}</a>",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome aboard,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
            [
                'event' => 'central_user_created',
                'channel' => 'email',
                'subject' => 'Your {platform_name} account - login details',
                'body' => "Your central platform account has been created.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #ff641a; text-decoration: none;\">{login_url}</a>\n<strong>Email:</strong> {user_email}\n<strong>Password:</strong> <code style=\"background: #f4f4f4; padding: 2px 6px; border-radius: 3px;\">{user_password}</code>\n\nPlease change your password after your first login. Do not share these credentials.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome aboard,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
            [
                'event' => 'tenant_welcome',
                'channel' => 'email',
                'subject' => 'Welcome to {platform_name} - Your Tenant is Ready!',
                'body' => "We are thrilled to welcome <strong>{tenant_name}</strong> to our platform!\n\nYour tenant has been successfully created and is now ready for use. Here are your details:\n\n<strong>Tenant Name:</strong> {tenant_name}\n<strong>Domain:</strong> {tenant_domain}\n<strong>Admin Email:</strong> {admin_email}\n<strong>Platform URL:</strong> <a href=\"{platform_url}\" style=\"color: #ff641a; text-decoration: none;\">{platform_url}</a>\n\nYou can log in to your admin dashboard using the credentials that have been sent to your admin email address.",
                'greeting' => 'Hello {tenant_name} Team,',
                'closing' => 'Best regards,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
            [
                'event' => 'tenant_user_created',
                'channel' => 'email',
                'subject' => 'Your {tenant_name} Admin Account - Login Details',
                'body' => "Your admin account has been created for <strong>{tenant_name}</strong>.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #ff641a; text-decoration: none;\">{login_url}</a>\n<strong>Email:</strong> {user_email}\n<strong>Password:</strong> <code style=\"background: #f4f4f4; padding: 2px 6px; border-radius: 3px;\">{user_password}</code>\n\nPlease change your password after your first login.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome to the team,',
                'sign_off' => '{platform_name}',
                ...$defaults,
            ],
        ];
    }

    /**
     * Insert any missing templates (does not truncate existing rows).
     */
    public static function syncMissing(): int
    {
        if (! Schema::hasTable('notification_templates')) {
            return 0;
        }

        $created = 0;

        $branding = Setting::templateBrandingDefaults();

        foreach (self::templates() as $template) {
            $record = NotificationTemplate::query()->updateOrCreate(
                [
                    'event' => $template['event'],
                    'channel' => $template['channel'],
                ],
                array_merge($template, $branding)
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
