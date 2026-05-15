<?php

namespace App\DTOs\Central;

use Illuminate\Support\Facades\Hash;

readonly class UserDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string|null $phone
     * @param string $password
     * @param string|null $role
     * @param bool $is_active
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password,
        public ?string $role = 'customer',
        public bool $is_active = true,
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'],
            role: $data['role'] ?? 'customer',
            is_active: $data['is_active'] ?? true,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ];
    }
}
