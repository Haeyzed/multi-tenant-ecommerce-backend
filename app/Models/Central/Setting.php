<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'site_name',
        'support_email',
        'currency',
        'maintenance_mode',
        'trial_days',
        'default_plan_id',
        'email_notifications',
        'sms_notifications',
    ];

    /**
     * @return array<string, string>
     */
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
}
