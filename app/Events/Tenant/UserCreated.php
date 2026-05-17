<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly ?string $plainPassword = null,
    ) {}
}
