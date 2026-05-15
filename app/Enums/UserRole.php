<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case TENANT_ADMIN = 'tenant_admin';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::TENANT_ADMIN => 'Tenant Admin',
            self::MANAGER => 'Manager',
            self::STAFF => 'Staff',
            self::CUSTOMER => 'Customer',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => ['*'],
            self::TENANT_ADMIN => [
                'users.*', 'products.*', 'orders.*', 'customers.*',
                'categories.*', 'brands.*', 'inventory.*', 'settings.*'
            ],
            self::MANAGER => [
                'products.*', 'orders.view', 'orders.update',
                'customers.view', 'inventory.*'
            ],
            self::STAFF => [
                'products.view', 'orders.view', 'customers.view'
            ],
            self::CUSTOMER => [
                'orders.view-own', 'profile.*'
            ],
        };
    }
}
