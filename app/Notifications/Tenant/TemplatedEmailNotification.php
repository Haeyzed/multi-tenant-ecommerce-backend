<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Models\Tenant\NotificationPreference;
use App\Models\Tenant\NotificationTemplate;
use App\Support\Notifications\TenantNotificationTemplateCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TemplatedEmailNotification
 *
 * Sends dynamic, database-driven email notifications by fetching a template
 * for a given event, parsing {variable} placeholders, and rendering through
 * a custom Blade view. Falls back to a generic message if no active template
 * is found.
 *
 * @package App\Notifications\Tenant
 */
class TemplatedEmailNotification extends Notification
{
    /**
     * Default email subject when no template subject is provided.
     *
     * @var string
     */
    private const string DEFAULT_SUBJECT = 'New Notification';

    /**
     * Default greeting line when no template or data greeting is provided.
     *
     * @var string
     */
    private const string DEFAULT_GREETING = 'Hello,';

    /**
     * Default closing line when no template or data closing is provided.
     *
     * @var string
     */
    private const string DEFAULT_CLOSING = 'Best regards,';

    /**
     * Default header background color for the email template.
     *
     * @var string
     */
    private const string DEFAULT_HEADER_BG = '#1e2b2e';

    /**
     * Default accent color for the email template borders and highlights.
     *
     * @var string
     */
    private const string DEFAULT_ACCENT_COLOR = '#73bc1c';

    /**
     * Create a new templated email notification instance.
     *
     * @param  string  $event  The unique event identifier used to look up the
     *                         corresponding notification template in the database.
     *                         Example: 'tenant_registered', 'plan_expiring',
     *                         'facility_onboarded'.
     * @param  array<string, mixed>  $templateData  Key-value pairs used to replace
     *                                              {variable} placeholders within the
     *                                              template subject and body.
     */
    public function __construct(
        public readonly string $event,
        public readonly array $templateData = []
    ) {}

    /**
     * Determine the delivery channels for this notification.
     *
     * Currently, returns 'mail' by default. Preference-based filtering can be
     * enabled by uncommenting the shouldSendViaChannel logic.
     *
     * @param  mixed  $notifiable  The entity receiving the notification.
     * @return array<int, string>  List of channel names (e.g., ['mail']).
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];

        // Uncomment below to enable preference-based channel filtering:
        // $channels = [];
        //
        // if ($this->shouldSendViaChannel($notifiable, 'email')) {
        //     $channels[] = 'mail';
        // }
        //
        // return $channels;
    }

    /**
     * Build the mail representation of the notification.
     *
     * Fetches the active email template for the configured event, parses all
     * {variable} placeholders using the provided template data, and renders
     * the final email through the custom `emails.notification` Blade view.
     *
     * Falls back to a generic MailMessage if no active template is found.
     *
     * @param  mixed  $notifiable  The entity receiving the notification.
     * @return MailMessage  The configured mail message instance.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $template = $this->resolveTemplate();

        if ($template === null) {
            return $this->fallbackMailMessage();
        }

        $subject = $this->parseVariables(
            $template->subject ?? self::DEFAULT_SUBJECT,
            $this->templateData
        );

        $body = $this->parseVariables($template->body, $this->templateData);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'body' => nl2br($body, false),
                'subject' => $subject,
                'greeting' => $this->resolveGreeting($template),
                'closing' => $this->resolveClosing($template),
                'signOff' => $this->resolveSignOff($template),
                'logoUrl' => $template->logo_url ?? $this->templateData['logo_url'] ?? null,
                'logoAlt' => $template->logo_alt ?? $this->templateData['logo_alt'] ?? 'Logo',
                'headerBgColor' => $template->header_bg_color ?? $this->templateData['header_bg_color'] ?? self::DEFAULT_HEADER_BG,
                'accentColor' => $template->accent_color ?? $this->templateData['accent_color'] ?? self::DEFAULT_ACCENT_COLOR,
            ]);
    }

    /**
     * Fetch the active email template for the current event from the database.
     *
     * Queries for a template matching the event name, email channel, and
     * active status.
     *
     * @return NotificationTemplate|null  The matching template, or null if none found.
     */
    private function resolveTemplate(): ?NotificationTemplate
    {
        $template = NotificationTemplate::query()
            ->where('event', $this->event)
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        if ($template !== null) {
            return $template;
        }

        TenantNotificationTemplateCatalog::syncMissing();

        $template = NotificationTemplate::query()
            ->where('event', $this->event)
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        if ($template === null) {
            Log::warning('Tenant notification template not found after sync.', [
                'event' => $this->event,
                'channel' => 'email',
            ]);
        }

        return $template;
    }

    /**
     * Build a fallback mail message when no database template is available.
     *
     * Provides a minimal, generic notification so the user still receives
     * something meaningful.
     *
     * @return MailMessage  A simple mail message with the event name.
     */
    private function fallbackMailMessage(): MailMessage
    {
        return (new MailMessage)
            ->subject("Notification: {$this->event}")
            ->line("You have a new notification regarding: {$this->event}.");
    }

    /**
     * Resolve the greeting text for the email.
     *
     * Priority: template greeting > templateData['greeting'] > default.
     *
     * @param  NotificationTemplate  $template  The resolved database template.
     * @return string  The greeting line to display in the email.
     */
    private function resolveGreeting(NotificationTemplate $template): string
    {
        $greeting = $template->greeting
            ?? $this->templateData['greeting']
            ?? self::DEFAULT_GREETING;

        return $this->parseVariables($greeting, $this->templateData);
    }

    /**
     * Resolve the closing text for the email.
     *
     * Priority: template closing > templateData['closing'] > default.
     *
     * @param  NotificationTemplate  $template  The resolved database template.
     * @return string  The closing line to display in the email.
     */
    private function resolveClosing(NotificationTemplate $template): string
    {
        $closing = $template->closing
            ?? $this->templateData['closing']
            ?? self::DEFAULT_CLOSING;

        return $this->parseVariables($closing, $this->templateData);
    }

    /**
     * Resolve the sign-off text for the email.
     *
     * Priority: template sign_off > templateData['sign_off'] > app name.
     *
     * @param  NotificationTemplate  $template  The resolved database template.
     * @return string  The sign-off line (e.g., company name).
     */
    private function resolveSignOff(NotificationTemplate $template): string
    {
        $signOff = $template->sign_off
            ?? $this->templateData['sign_off']
            ?? config('app.name');

        return $this->parseVariables($signOff, $this->templateData);
    }

    /**
     * Parse and replace {variable} placeholders with actual values.
     *
     * Iterates over the provided data array and replaces all occurrences
     * of {key} in the text with the corresponding value.
     *
     * @param  string  $text  The template text containing {variable} placeholders.
     * @param  array<string, mixed>  $data  Key-value pairs for replacement.
     * @return string  The text with all placeholders replaced.
     */
    private function parseVariables(string $text, array $data): string
    {
        $replacements = [];

        foreach ($data as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }

        return strtr($text, $replacements);
    }

    /**
     * Check if the notification should be sent via the specified channel.
     *
     * Looks up the notifiable entity's notification preferences. If no
     * preference record exists, defaults to sending. If a record exists,
     * respects the enabled flag.
     *
     * @param  mixed  $notifiable  The entity receiving the notification.
     * @param  string  $channel  The channel name to check (e.g., 'email').
     * @return bool  True if the notification should be sent, false otherwise.
     */
    private function shouldSendViaChannel(mixed $notifiable, string $channel): bool
    {
        if (! $notifiable instanceof Model) {
            return true;
        }

        $preference = NotificationPreference::query()
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->getKey())
            ->where('event', $this->event)
            ->where('channel', $channel)
            ->first();

        return $preference === null || (bool) $preference->enabled;
    }
}
