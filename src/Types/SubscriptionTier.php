<?php

namespace SteelAnts\LaravelBoilerplate\Types;

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
                'limit_name'   => 100,
				'limit_name_2' => 20,
                // Add more limits if needed
            ],
            self::TIER_2 => ['limit_name' => 1000],
            self::TIER_3 => ['limit_name' => 10000],
        ];
    }

    public static function getNames()
    {
        return [
            self::TIER_1 => __('Tier 1'),
            self::TIER_2 => __('Tier 2'),
            self::TIER_3 => __('Tier 3'),
        ];
    }

    public static function getName($type)
    {
        return self::getNames()[$type] ?? __('undefined');
    }
}
