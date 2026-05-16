<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'event',
        'channel',
        'enabled',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'bool',
        ];
    }

    /**
     * Get the entity that this preference belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
