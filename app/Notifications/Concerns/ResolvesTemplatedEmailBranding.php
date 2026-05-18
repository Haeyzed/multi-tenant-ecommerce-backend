<?php

declare(strict_types=1);

namespace App\Notifications\Concerns;

/**
 * Resolves logo and colors for emails.notification view.
 *
 * Live platform/store settings take precedence over stale notification_templates rows.
 */
trait ResolvesTemplatedEmailBranding
{
    /**
     * @param  array<string, mixed>  $defaults  From Setting::templateBrandingDefaults()
     * @return array{logoUrl: ?string, logoAlt: string, headerBgColor: string, accentColor: string}
     */
    protected function resolveMailBranding(
        object $template,
        array $defaults,
        string $defaultHeaderBg,
        string $defaultAccent,
    ): array {
        $logoUrl = $this->templateData['logo_url']
            ?? $defaults['logo_url']
            ?? $template->logo_url ?? null;

        return [
            'logoUrl' => filled($logoUrl) ? (string) $logoUrl : null,
            'logoAlt' => (string) (
                $this->templateData['logo_alt']
                ?? $defaults['logo_alt']
                ?? $template->logo_alt
                ?? 'Logo'
            ),
            'headerBgColor' => (string) (
                $this->templateData['header_bg_color']
                ?? $defaults['header_bg_color']
                ?? $template->header_bg_color
                ?? $defaultHeaderBg
            ),
            'accentColor' => (string) (
                $this->templateData['accent_color']
                ?? $defaults['accent_color']
                ?? $template->accent_color
                ?? $defaultAccent
            ),
        ];
    }
}
