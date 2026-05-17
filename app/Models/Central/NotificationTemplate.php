<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class NotificationTemplate extends Model
{
    use CentralConnection;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event',
        'channel',
        'subject',
        'body',
        'greeting',
        'closing',
        'sign_off',
        'logo_url',
        'logo_alt',
        'header_bg_color',
        'accent_color',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }
}
