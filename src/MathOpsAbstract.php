<?php

/*
 * This file is part of Math.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

/**
 * Abstract class MathOpsAbstract
 */
abstract class MathOpsAbstract extends MathBaseAbstract
{
    /**
     * @param (string|int|static)[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function add(...$numbers): self
    {
        foreach ($numbers as $number) {
            $this->number = bcadd($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param (string|int|static)[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function sub(...$numbers): self
    {
        foreach ($numbers as $number) {
            $this->number = bcsub($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param (string|int|static)[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function mul(...$numbers): self
    {
        foreach ($numbers as $number) {
            $this->number = bcmul($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param (string|int|static)[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function div(...$numbers): self
    {
        foreach ($numbers as $number) {
            $this->number = bcdiv($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function sqrt(): self
    {
        $this->number = bcsqrt($this->number, $this->precision);

        return $this;
    }

    /**
     * @param string|int $exponent
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function pow($exponent): self
    {
        $this->number = bcpow($this->number, static::validatePositiveInteger($exponent), $this->precision);

        return $this;
    }

    /**
     * @param string|int $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function mod($modulus): self
    {
        $this->number = bcmod($this->number, static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param string|int $exponent
     * @param string|int $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function powMod($exponent, $modulus): self
    {
        $this->number = bcpowmod($this->number, static::validatePositiveInteger($exponent), static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param string|int $precision
     *
     * @return static
     */
    public function round($precision = 0): self
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

    /**
     * @return static
     */
    public function ceil(): self
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

    /**
     * @return static
     */
    public function floor(): self
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

    /**
     * @return static
     */
    public function abs(): self
    {
        $this->number = ltrim($this->number, '-');

        return $this;
    }

    /**
     * returns the highest number among all arguments
     *
     * @param (string|int|static)[] $numbers
     *
     * @return static
     */
    public function max(...$numbers): self
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
     *
     * @param (string|int|static)[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function min(...$numbers): self
    {
        foreach ($numbers as $number) {
            if (bccomp($number = static::validateInputNumber($number), $this->number, $this->precision) === -1) {
                $this->number = $number;
            }
        }

        return $this;
    }
}
