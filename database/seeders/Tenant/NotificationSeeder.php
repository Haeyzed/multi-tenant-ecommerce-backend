<?php

declare(strict_types=1);

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds notification channels and email templates for each tenant database.
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedChannels();
        $this->seedTemplates();
        $this->seedPreferences();
    }

    private function seedChannels(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('notification_channels')->truncate();
        Schema::enableForeignKeyConstraints();

        $channels = [
            [
                'key' => 'email',
                'label' => 'Email',
                'is_active' => true,
                'config' => json_encode([
                    'driver' => 'smtp',
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms',
                'label' => 'SMS',
                'is_active' => true,
                'config' => json_encode([
                    'provider' => 'twilio',
                    'from_number' => null,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notification_channels')->insert($channels);

        $this->command?->info('Seeded '.count($channels).' tenant notification channels.');
    }

    private function seedTemplates(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('notification_templates')->truncate();
        Schema::enableForeignKeyConstraints();

        $defaults = Setting::templateBrandingDefaults();
        $storeName = Setting::storeName();

        $templates = [
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
                'body' => "Your staff account at <strong>{store_name}</strong> has been created.\n\n<strong>Email:</strong> {user_email}\n\nSign in with the password provided by your store administrator. Change your password after your first login.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: {$defaults['accent_color']}; text-decoration: none;\">{login_url}</a>",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome to the team,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
            [
                'event' => 'store_user_created',
                'channel' => 'email',
                'subject' => 'Your {store_name} account - login details',
                'body' => "An account has been created for you at <strong>{store_name}</strong>.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: {$defaults['accent_color']}; text-decoration: none;\">{login_url}</a>\n<strong>Email:</strong> {user_email}\n<strong>Password:</strong> <code style=\"background: #f4f4f4; padding: 2px 6px; border-radius: 3px;\">{user_password}</code>\n\nPlease change your password after your first login. Do not share these credentials.",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome aboard,',
                'sign_off' => '{store_name}',
                ...$defaults,
            ],
        ];

        $templates = array_map(function (array $template) {
            $template['created_at'] = now();
            $template['updated_at'] = now();

            return $template;
        }, $templates);

        DB::table('notification_templates')->insert($templates);

        $this->command?->info('Seeded '.count($templates)." tenant notification templates for {$storeName}.");
    }

    private function seedPreferences(): void
    {
        if (DB::table('users')->count() === 0) {
            $this->command?->warn('No tenant users found. Skipping notification preferences.');

            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('notification_preferences')->truncate();
        Schema::enableForeignKeyConstraints();

        $events = [
            'email_verification_otp',
            'password_reset_otp',
            'store_user_welcome',
            'store_user_created',
        ];

        $preferences = [];

        foreach (DB::table('users')->limit(10)->get() as $user) {
            foreach ($events as $event) {
                foreach (['email', 'sms'] as $channel) {
                    $preferences[] = [
                        'notifiable_type' => 'App\\Models\\Tenant\\User',
                        'notifiable_id' => $user->id,
                        'event' => $event,
                        'channel' => $channel,
                        'enabled' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if ($preferences !== []) {
            DB::table('notification_preferences')->insert($preferences);
        }

        $this->command?->info('Seeded '.count($preferences).' tenant notification preferences.');
    }

}
