<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case PAYSTACK = 'paystack';
    case FLUTTERWAVE = 'flutterwave';
    case STRIPE = 'stripe';
    case CASH_ON_DELIVERY = 'cash_on_delivery';

    public function label(): string
    {
        return match($this) {
            self::PAYSTACK => 'Paystack',
            self::FLUTTERWAVE => 'Flutterwave',
            self::STRIPE => 'Stripe',
            self::CASH_ON_DELIVERY => 'Cash on Delivery',
        };
    }
}
