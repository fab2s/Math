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
        $result   = $this->mutate();
        $exponent = static::validatePositiveInteger($exponent);

        if (static::$gmpSupport && ! $result->hasDecimals()) {
            $result->number = gmp_strval(gmp_pow(gmp_init($result->number), (int) $exponent)); // @phpstan-ignore assign.propertyType
        } else {
            $result->number = bcpow($result->number, $exponent, $result->precision);
        }

        return $result;
    }

    public function mod(string|int $modulus): static
    {
        $result  = $this->mutate();
        $modulus = static::validatePositiveInteger($modulus);

        if (static::$gmpSupport) {
            $result->number = gmp_strval(gmp_mod(gmp_init($result->number), gmp_init($modulus))); // @phpstan-ignore assign.propertyType
        } else {
            $result->number = bcmod($result->number, $modulus);
        }

        return $result;
    }

    public function powMod(string|int $exponent, string|int $modulus): static
    {
        $result   = $this->mutate();
        $exponent = static::validatePositiveInteger($exponent);
        $modulus  = static::validatePositiveInteger($modulus);

        if (static::$gmpSupport) {
            $result->number = gmp_strval(gmp_powm(gmp_init($result->number), gmp_init($exponent), gmp_init($modulus))); // @phpstan-ignore assign.propertyType
        } else {
            $result->number = bcpowmod($result->number, $exponent, $modulus);
        }

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

    public function negate(): static
    {
        $result = $this->mutate();
        if ($result->number[0] === '-') {
            $result->number = substr($result->number, 1); // @phpstan-ignore assign.propertyType
        } elseif (trim($result->number, '+-0.') !== '') {
            $result->number = '-' . $result->number; // @phpstan-ignore assign.propertyType
        }

        return $result;
    }

    public function clamp(string|int|float|Math $min, string|int|float|Math $max): static
    {
        return $this->max($min)->min($max);
    }

    /**
     * @return array{static, static}
     */
    public function quotientAndRemainder(string|int|float|Math $divisor): array
    {
        $divisor   = static::validateInputNumber($divisor);
        $number    = $this->number;
        $quotient  = $this->mutate();
        $remainder = clone $this;

        $quotient->number  = bcdiv($number, $divisor, 0);
        $remainder->number = bcmod($number, $divisor, $this->precision);

        return [$quotient, $remainder];
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
