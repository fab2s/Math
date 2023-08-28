<?php

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
        foreach ($numbers as $number) {
            $this->number = bcadd($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    public function sub(string|int|float|Math ...$numbers): static
    {
        foreach ($numbers as $number) {
            $this->number = bcsub($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    public function mul(string|int|float|Math ...$numbers): static
    {
        foreach ($numbers as $number) {
            $this->number = bcmul($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    public function div(string|int|float|Math ...$numbers): static
    {
        foreach ($numbers as $number) {
            $this->number = bcdiv($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    public function sqrt(): static
    {
        $this->number = bcsqrt($this->number, $this->precision);

        return $this;
    }

    public function pow(string|int $exponent): static
    {
        $this->number = bcpow($this->number, static::validatePositiveInteger($exponent), $this->precision);

        return $this;
    }

    public function mod(string|int $modulus): static
    {
        $this->number = bcmod($this->number, static::validatePositiveInteger($modulus));

        return $this;
    }

    public function powMod(string|int $exponent, string|int $modulus): static
    {
        $this->number = bcpowmod($this->number, static::validatePositiveInteger($exponent), static::validatePositiveInteger($modulus));

        return $this;
    }

    public function round(string|int $precision = 0): static
    {
        $precision = max(0, (int) $precision);
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);

                return $this;
            }

            $this->number = bcsub($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }

        return $this;
    }

    public function ceil(): static
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);

                return $this;
            }

            $this->number = bcsub($this->number, '0', 0);
        }

        return $this;
    }

    public function floor(): static
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, 0, 0);

                return $this;
            }

            $this->number = bcsub($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);
        }

        return $this;
    }

    public function abs(): static
    {
        $this->number = ltrim($this->number, '-');

        return $this;
    }

    /**
     * returns the highest number among all arguments
     */
    public function max(string|int|float|Math ...$numbers): static
    {
        foreach ($numbers as $number) {
            if (bccomp($number = static::validateInputNumber($number), $this->number, $this->precision) === 1) {
                $this->number = $number;
            }
        }

        return $this;
    }

    /**
     * returns the smallest number among all arguments
     */
    public function min(string|int|float|Math ...$numbers): static
    {
        foreach ($numbers as $number) {
            if (bccomp($number = static::validateInputNumber($number), $this->number, $this->precision) === -1) {
                $this->number = $number;
            }
        }

        return $this;
    }
}
