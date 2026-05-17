<?php

declare(strict_types=1);

namespace App\DTOs\Central;

readonly class UserDTO
{
    /**
     * @param list<string> $only Keys present on update requests (empty = create, send all fields)
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password,
        public ?string $role = null,
        public bool $is_active = true,
        public array $only = [],
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'],
            role: $data['role'] ?? null,
            is_active: (bool) ($data['is_active'] ?? true),
        );
    }

    public static function fromUpdateRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? null,
            password: $data['password'] ?? '',
            role: $data['role'] ?? null,
            is_active: (bool) ($data['is_active'] ?? true),
            only: array_values(array_diff(array_keys($data), ['permissions', 'password_confirmation'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ];

        if ($this->password !== '') {
            $attributes['password'] = $this->password;
        }

        if ($this->only === []) {
            return $attributes;
        }

        $filtered = [];

        foreach ($this->only as $key) {
            if ($key === 'password' && $this->password !== '') {
                $filtered['password'] = $this->password;
            } elseif (array_key_exists($key, $attributes)) {
                $filtered[$key] = $attributes[$key];
            }
        }

        return $filtered;
    }
}
