<?php

namespace App\Types;

class SubscriptionTier
{
    const TIER_1 = 1;
    const TIER_2 = 2;
    const TIER_3 = 3;
    // Add more tiers if needed

    public function getLimits()
    {
        return [
            self::TIER_1 => [
                'limit_name' => 100,
                // Add more limits if needed
            ],
            self::TIER_2 => ['limit_name' => 1000],
            self::TIER_3 => ['limit_name' => 10000],
        ];
    }

    public static function getNames()
    {
        return [
            self::TIER_1 => __('boilerplate::subscriptions.tier_1.title'),
            self::TIER_2 => __('boilerplate::subscriptions.tier_2.title'),
            self::TIER_3 => __('boilerplate::subscriptions.tier_3.title'),
        ];
    }

    public static function getName($type)
    {
        return self::getNames()[$type] ?? 'undefined';
    }
}
