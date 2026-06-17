<?php

namespace SteelAnts\LaravelBoilerplate\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Support\Money;

class MoneyCast implements CastsAttributes
{
    public function __construct(
        protected ?int $scale = 6,
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        return new Money($value, $this->scale);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            return $value->getAmount();
        }

        return (new Money($value, $this->scale))->getAmount();
    }
}
