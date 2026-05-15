<?php

namespace App\DTOs\Central;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password,
        public ?string $role = 'customer',
        public bool $is_active = true,
    ) {}

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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => bcrypt($this->password),
            'is_active' => $this->is_active,
        ];
    }
}
