<?php

declare(strict_types=1);

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

/**
 * Abstract class MathOpsAbstract
 */
abstract class MathOpsAbstract extends MathBaseAbstract
{
    public function add(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            $result->number = bcadd($result->number, static::validateInputNumber($number), $result->precision);
        }

        return $result;
    }

    public function sub(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            $result->number = bcsub($result->number, static::validateInputNumber($number), $result->precision);
        }

        return $result;
    }

    public function mul(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            $result->number = bcmul($result->number, static::validateInputNumber($number), $result->precision);
        }

        return $result;
    }

    public function div(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            $result->number = bcdiv($result->number, static::validateInputNumber($number), $result->precision);
        }

        return $result;
    }

    public function sqrt(): static
    {
        $result         = $this->mutate();
        $result->number = bcsqrt($result->number, $result->precision);

        return $result;
    }

    public function pow(string|int $exponent): static
    {
        $result         = $this->mutate();
        $result->number = bcpow($result->number, static::validatePositiveInteger($exponent), $result->precision);

        return $result;
    }

    public function mod(string|int $modulus): static
    {
        $result         = $this->mutate();
        $result->number = bcmod($result->number, static::validatePositiveInteger($modulus));

        return $result;
    }

    public function powMod(string|int $exponent, string|int $modulus): static
    {
        $result         = $this->mutate();
        $result->number = bcpowmod($result->number, static::validatePositiveInteger($exponent), static::validatePositiveInteger($modulus));

        return $result;
    }

    public function round(string|int $precision = 0): static
    {
        $precision = max(0, (int) $precision);
        $result    = $this->mutate();
        if ($result->hasDecimals()) {
            /** @var numeric-string $offset */ // @phpstan-ignore varTag.nativeType
            $offset = '0.' . str_repeat('0', $precision) . '5';
            if ($result->isPositive()) {
                $result->number = bcadd($result->number, $offset, $precision);

                return $result;
            }

            $result->number = bcsub($result->number, $offset, $precision);
        }

        return $result;
    }

    public function ceil(): static
    {
        $result = $this->mutate();
        if ($result->hasDecimals()) {
            if ($result->isPositive()) {
                $result->number = bcadd($result->number, (preg_match('`\.[0]*$`', $result->number) ? '0' : '1'), 0);

                return $result;
            }

            $result->number = bcsub($result->number, '0', 0);
        }

        return $result;
    }

    public function floor(): static
    {
        $result = $this->mutate();
        if ($result->hasDecimals()) {
            if ($result->isPositive()) {
                $result->number = bcadd($result->number, '0', 0);

                return $result;
            }

            $result->number = bcsub($result->number, (preg_match('`\.[0]*$`', $result->number) ? '0' : '1'), 0);
        }

        return $result;
    }

    public function abs(): static
    {
        $result         = $this->mutate();
        $result->number = ltrim($result->number, '-');

        return $result;
    }

    /**
     * returns the highest number among all arguments
     */
    public function max(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            if (bccomp($number = static::validateInputNumber($number), $result->number, $result->precision) === 1) {
                $result->number = $number;
            }
        }

        return $result;
    }

    /**
     * returns the smallest number among all arguments
     */
    public function min(string|int|float|Math ...$numbers): static
    {
        $result = $this->mutate();
        foreach ($numbers as $number) {
            if (bccomp($number = static::validateInputNumber($number), $result->number, $result->precision) === -1) {
                $result->number = $number;
            }
        }

        return $result;
    }
}
