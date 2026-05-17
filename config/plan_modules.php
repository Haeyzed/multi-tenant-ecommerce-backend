<?php

/**
 * Catalog of plan feature modules and usage limit keys for central plan management.
 */
return [

    'modules' => [
        'products' => [
            'label' => 'Product catalog',
            'description' => 'Manage products, variants, and categories.',
        ],
        'orders' => [
            'label' => 'Orders',
            'description' => 'Order processing and fulfillment.',
        ],
        'customers' => [
            'label' => 'Customers',
            'description' => 'Customer profiles and order history.',
        ],
        'inventory' => [
            'label' => 'Inventory',
            'description' => 'Stock tracking and low-stock alerts.',
        ],
        'analytics' => [
            'label' => 'Analytics',
            'description' => 'Sales and traffic reporting.',
        ],
        'api_access' => [
            'label' => 'API access',
            'description' => 'REST API for integrations.',
        ],
        'white_label' => [
            'label' => 'White label',
            'description' => 'Custom branding on storefront and emails.',
        ],
        'priority_support' => [
            'label' => 'Priority support',
            'description' => 'Priority support queue.',
        ],
    ],

    'limit_keys' => [
        'products',
        'orders_per_month',
        'staff',
        'storage_gb',
    ],

];
