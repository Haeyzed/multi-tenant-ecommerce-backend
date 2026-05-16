<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationSeeder extends Seeder
{
    /**
     * Run the notification database seeds.
     *
     * Seeds default notification channels, templates, and sample preferences.
     */
    public function run(): void
    {
        $this->seedChannels();
        $this->seedTemplates();
        $this->seedPreferences();
    }

    /**
     * Seed the notification_channels table.
     */
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

        $this->command->info('Seeded ' . count($channels) . ' notification channels.');
    }

    /**
     * Seed the notification_templates table.
     */
    private function seedTemplates(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('notification_templates')->truncate();
        Schema::enableForeignKeyConstraints();

        $templates = [
            // === TENANT WELCOME EMAIL ===
            [
                'event' => 'tenant_welcome',
                'channel' => 'email',
                'subject' => 'Welcome to {platform_name} - Your Tenant is Ready!',
                'body' => "We are thrilled to welcome <strong>{tenant_name}</strong> to our platform!\n\nYour tenant has been successfully created and is now ready for use. Here are your details:\n\n<strong>Tenant Name:</strong> {tenant_name}\n<strong>Domain:</strong> {tenant_domain}\n<strong>Admin Email:</strong> {admin_email}\n<strong>Platform URL:</strong> <a href=\"{platform_url}\" style=\"color: #ff641a; text-decoration: none;\">{platform_url}</a>\n\nYou can log in to your admin dashboard using the credentials that have been sent to your admin email address.\n\nIf you have any questions or need assistance getting started, our support team is here to help.\n\nThank you for choosing us!",
                'greeting' => 'Hello {tenant_name} Team,',
                'closing' => 'Best regards,',
                'sign_off' => '{platform_name}',
                'logo_url' => 'https://lawma.softalliance.com/assets/LAWMA-white2Asset-CGHAptko.png',
                'logo_alt' => 'Platform Logo',
                'header_bg_color' => '#1e2b2e',
                'accent_color' => '#73bc1c',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // === TENANT USER CREATED (LOGIN CREDENTIALS) ===
            [
                'event' => 'tenant_user_created',
                'channel' => 'email',
                'subject' => 'Your {tenant_name} Admin Account - Login Details',
                'body' => "Your admin account has been created for <strong>{tenant_name}</strong>.\n\nPlease find your login credentials below. For security reasons, we recommend changing your password after your first login.\n\n<strong>Login URL:</strong> <a href=\"{login_url}\" style=\"color: #ff641a; text-decoration: none;\">{login_url}</a>\n<strong>Email:</strong> {user_email}\n<strong>Password:</strong> <code style=\"background: #f4f4f4; padding: 2px 6px; border-radius: 3px;\">{user_password}</code>\n\n<strong>Important Security Notes:</strong>\n• Do not share your password with anyone\n• Change your password after your first login\n• Use a strong, unique password\n\nIf you did not request this account, please contact our support team immediately.\n\nWelcome aboard!",
                'greeting' => 'Hello {user_name},',
                'closing' => 'Welcome to the team,',
                'sign_off' => '{platform_name}',
                'logo_url' => 'https://lawma.softalliance.com/assets/LAWMA-white2Asset-CGHAptko.png',
                'logo_alt' => 'Platform Logo',
                'header_bg_color' => '#1e2b2e',
                'accent_color' => '#73bc1c',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notification_templates')->insert($templates);

        $this->command->info('Seeded ' . count($templates) . ' notification templates.');
    }

    /**
     * Seed sample notification_preferences (optional/demo data).
     */
    private function seedPreferences(): void
    {
        // Only seed if users exist in the database
        $userCount = DB::table('users')->count();

        if ($userCount === 0) {
            $this->command->warn('No users found. Skipping notification preferences seed.');

            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('notification_preferences')->truncate();
        Schema::enableForeignKeyConstraints();

        $events = [
            'facility_onboarded',
            'facility_updated',
            'invoice_generated',
            'payment_received',
            'payment_overdue',
            'user_registered',
            'password_reset',
            'system_maintenance',
            'report_generated',
        ];

        $channels = ['email', 'sms', 'in_app'];
        $preferences = [];
        $users = DB::table('users')->limit(5)->get();

        foreach ($users as $user) {
            foreach ($events as $event) {
                foreach ($channels as $channel) {
                    $preferences[] = [
                        'notifiable_type' => 'App\\Models\\User',
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

        DB::table('notification_preferences')->insert($preferences);

        $this->command->info('Seeded ' . count($preferences) . ' notification preferences.');
    }
}
