<?php

declare(strict_types=1);

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * Central platform settings (singleton row).
 */
class Setting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    private const string CACHE_KEY = 'central_settings_instance';

    private const int CACHE_TTL = 3600;

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'trial_days' => 'integer',
            'default_plan_id' => 'integer',
        ];
    }

    public function defaultPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'default_plan_id');
    }

    /**
     * Get the singleton settings record (cached by ID).
     *
     * Only the row ID is cached — not the model — because
     * config/cache.php sets serializable_classes to false.
     */
    public static function instance(): self
    {
        $id = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): int {
            return (int) self::firstOrCreate([], [
                'site_name' => config('app.name', 'Multi-Tenant E-Commerce'),
                'support_email' => config('mail.from.address', 'support@example.com'),
                'currency' => 'USD',
                'maintenance_mode' => false,
                'trial_days' => 14,
                'email_notifications' => true,
                'sms_notifications' => false,
            ])->id;
        });

        $instance = self::find($id);

        if ($instance instanceof self) {
            return $instance;
        }

        self::clearCache();

        return self::firstOrCreate([], [
            'site_name' => config('app.name', 'Multi-Tenant E-Commerce'),
            'support_email' => config('mail.from.address', 'support@example.com'),
            'currency' => 'USD',
            'maintenance_mode' => false,
            'trial_days' => 14,
            'email_notifications' => true,
            'sms_notifications' => false,
        ]);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return data_get(self::instance(), $key, $default);
    }

    public static function updateSettings(array $data): self
    {
        $instance = self::instance();
        $instance->update($data);

        self::clearCache();

        return $instance->fresh(['defaultPlan']) ?? $instance;
    }

    public static function siteName(): string
    {
        return (string) self::get('site_name', config('app.name', 'Platform'));
    }

    /** Alias used in notification templates ({platform_name}). */
    public static function platformName(): string
    {
        return self::siteName();
    }

    /**
     * @return array<string, string|null>
     */
    public static function brandColors(): array
    {
        return [
            'primary' => self::get('primary_color', '#1e2b2e'),
            'accent' => self::get('accent_color', '#73bc1c'),
            'secondary' => self::get('secondary_color'),
        ];
    }

    /**
     * Default branding fields for notification_templates rows.
     *
     * @return array<string, mixed>
     */
    public static function templateBrandingDefaults(): array
    {
        $colors = self::brandColors();

        return [
            'logo_url' => self::resolvePublicLogoUrl(self::get('site_logo_url')),
            'logo_alt' => self::siteName(),
            'header_bg_color' => $colors['primary'] ?? '#1e2b2e',
            'accent_color' => $colors['accent'] ?? '#73bc1c',
            'is_active' => true,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mailFrom(): array
    {
        return [
            'address' => (string) self::get('support_email', config('mail.from.address')),
            'name' => self::siteName(),
        ];
    }

    public static function isMaintenanceMode(): bool
    {
        return (bool) self::get('maintenance_mode', false);
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Email clients require an absolute URL for images.
     */
    public static function resolvePublicLogoUrl(mixed $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = (string) $url;

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }
}
