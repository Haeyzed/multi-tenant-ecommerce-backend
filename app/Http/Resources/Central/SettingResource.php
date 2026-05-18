<?php

declare(strict_types=1);

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'site_name' => $this->site_name,
            'site_logo_url' => $this->site_logo_url,
            'primary_color' => $this->primary_color,
            'accent_color' => $this->accent_color,
            'secondary_color' => $this->secondary_color,
            'support_email' => $this->support_email,
            'currency' => $this->currency,
            'maintenance_mode' => $this->maintenance_mode,
            'trial_days' => $this->trial_days,
            'default_plan_id' => $this->default_plan_id,
            'default_plan' => PlanResource::make($this->whenLoaded('defaultPlan')),
            'email_notifications' => $this->email_notifications,
            'sms_notifications' => $this->sms_notifications,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
