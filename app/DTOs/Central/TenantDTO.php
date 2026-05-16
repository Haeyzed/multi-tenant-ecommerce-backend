<?php

declare(strict_types=1);

namespace App\DTOs\Central;

use App\Enums\Central\TenantStatus;

readonly class TenantDTO
{
    /**
     * Create a new TenantDTO instance.
     *
     * @param string $name Tenant/business name
     * @param string $email Tenant business email
     * @param string|null $phone Tenant business phone
     * @param string $domain Primary domain for the tenant
     * @param TenantStatus $status Tenant status
     * @param int $planId Subscription plan ID
     * @param string $adminName Admin user full name
     * @param string $adminEmail Admin user email (for login)
     * @param string|null $adminPassword Plain text password (null = auto-generate)
     * @param string|null $adminPhone Admin user phone
     * @param array $data Additional tenant metadata
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $domain,
        public TenantStatus $status,
        public int $planId,
        public string $adminName,
        public string $adminEmail,
        public ?string $adminPassword = null,
        public ?string $adminPhone = null,
        public array $data = [],
    ) {}

    /**
     * Create a TenantDTO from validated request data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            domain: strtolower(trim($data['domain'])),
            status: TenantStatus::from($data['status'] ?? 'active'),
            planId: (int) $data['plan_id'],
            adminName: $data['admin_name'],
            adminEmail: $data['admin_email'],
            adminPassword: $data['admin_password'] ?? null,
            adminPhone: $data['admin_phone'] ?? null,
            data: $data['data'] ?? [],
        );
    }

    /**
     * Convert DTO to array for tenant creation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'plan_id' => $this->planId,
            'data' => array_merge($this->data, [
                'admin_name' => $this->adminName,
                'admin_email' => $this->adminEmail,
                'admin_phone' => $this->adminPhone,
            ]),
        ];
    }

    /**
     * Get admin user data for creation.
     *
     * @return array<string, mixed>
     */
    public function adminData(): array
    {
        return [
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'phone' => $this->adminPhone,
        ];
    }
}
