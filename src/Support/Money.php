<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Number;

final class Money implements \JsonSerializable
{
    protected string $amount;

    public function __construct(string|int|float|Money $amount, protected int $scale = 6)
    {
        if ($amount instanceof self) {
            $this->amount = $amount->getAmount();
        } elseif (is_float($amount)) {
            $this->amount = number_format($amount, $this->scale, '.', '');
        } else {
            $this->amount = (string) $amount;
        }

        $this->amount = bcadd($this->amount, '0', $this->scale);
    }

    public static function create(string|int|float|Money $amount): static
    {
        return new self($amount);
    }

    public function add(Money $other): static
    {
        return new static(bcadd($this->amount, $other->amount, $this->scale), $this->scale);
    }

    public function sub(Money $other): static
    {
        return new static(bcsub($this->amount, $other->amount, $this->scale), $this->scale);
    }

    public function mul(string|int|float $multiplier): static
    {
        return new static(bcmul($this->amount, (string) $multiplier, $this->scale), $this->scale);
    }

    public function div(string|int|float $divisor): static
    {
        if (bccomp((string) $divisor, '0', $this->scale) === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        return new static(bcdiv($this->amount, (string) $divisor, $this->scale), $this->scale);
    }

    public function withVat(string|int|float $vatRate): static
    {
        $multiplier = bcadd('1', bcdiv((string) $vatRate, '100', $this->scale), $this->scale);

        return new static(bcmul($this->amount, $multiplier, $this->scale), $this->scale);
    }

    public function vatAmount(string|int|float $vatRate): static
    {
        $withVat = $this->withVat($vatRate);

        return new static(bcsub($withVat->amount, $this->amount, $this->scale), $this->scale);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function toFloat(): float
    {
        return (float) $this->amount;
    }

    public function format(int $precision = 2, string $currency = 'CZK'): string
    {
        return Number::currency($this->toFloat(), $currency, config('app.locale'), $precision);
    }

    public function round(int $precision = 0): static
    {
        $factor = bcpow('10', (string) $precision, 0);
        $shifted = bcmul($this->amount, $factor, $this->scale);

        if (bccomp($this->amount, '0', $this->scale) >= 0) {
            $truncated = bcadd($shifted, '0.5', 0);
        } else {
            $truncated = bcsub($shifted, '0.5', 0);
        }

        return new static(bcdiv($truncated, $factor, $this->scale), $this->scale);
    }

    public function gt(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, $this->scale) === 1;
    }

    public function lt(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, $this->scale) === -1;
    }

    public function eq(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, $this->scale) === 0;
    }

    public function gte(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, $this->scale) >= 0;
    }

    public function lte(Money $other): bool
    {
        return bccomp($this->amount, $other->amount, $this->scale) <= 0;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    public function jsonSerialize(): string
    {
        return $this->amount;
    }

    public static function sum(Collection|array $collection, string $column): static
    {
        $result = new static(0);

        foreach ($collection as $item) {
            $value = data_get($item, $column);

            if (!$value instanceof static) {
                $value = new static($value);
            }

            $result = $result->add($value);
        }

        return $result;
    }
}
