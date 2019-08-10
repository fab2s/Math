<?php

/*
 * This file is part of Math.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

/**
 * Class Math
 */
class Math extends MathOpsAbstract
{
    /**
     * Math constructor.
     *
     * @param string|static $number
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($number)
    {
        if (isset(static::$globalPrecision)) {
            $this->precision = static::$globalPrecision;
        }

        $this->number = static::validateInputNumber($number);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return static::normalizeNumber($this->number);
    }

    /**
     * @param string|int $number
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function number($number): self
    {
        return new static($number);
    }

    /**
     * convert any based value bellow or equals to 64 to its decimal value
     *
     * @param string|int $number
     * @param int        $base
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function fromBase($number, int $base): self
    {
        // trim base 64 padding char, only positive
        $number = trim($number, ' =-');
        if ($number === '' || strpos($number, '.') !== false) {
            throw new \InvalidArgumentException('Argument number is not an integer');
        }

        $baseChar = static::getBaseChar($base);
        if (trim($number, $baseChar[0]) === '') {
            return new static('0');
        }

        if (static::$gmpSupport && $base <= 62) {
            return new static(static::baseConvert($number, $base, 10));
        }

        // By now we know we have a correct base and number
        return new static(static::bcDec2Base($number, $base, $baseChar));
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gte($number): bool
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) >= 0);
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gt($number): bool
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 1);
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lte($number): bool
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) <= 0);
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lt($number): bool
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === -1);
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function eq($number): bool
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 0);
    }

    /**
     * convert decimal value to any other base bellow or equals to 64
     *
     * @param string|int $base
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function toBase($base): string
    {
        if ($this->normalize()->hasDecimals()) {
            throw new \InvalidArgumentException('Argument number is not an integer');
        }

        // do not mutate, only support positive integers
        $number = ltrim((string) $this, '-');
        if (static::$gmpSupport && $base <= 62) {
            return static::baseConvert($number, 10, $base);
        }

        $result   = '';
        $baseChar = static::getBaseChar($base);
        while (bccomp($number, 0) != 0) { // still data to process
            $rem    = bcmod($number, $base); // calc the remainder
            $number = bcdiv(bcsub($number, $rem), $base);
            $result = $baseChar[$rem] . $result;
        }

        $result = $result ? $result : $baseChar[0];

        return (string) $result;
    }

    /**
     * @param string|int $decimals
     * @param string     $decPoint
     * @param string     $thousandsSep
     *
     * @return string
     */
    public function format($decimals = 0, string $decPoint = '.', string $thousandsSep = ' '): string
    {
        $decimals = max(0, (int) $decimals);
        $dec      = '';
        // do not mutate
        $number   = (new static($this))->round($decimals)->normalize();
        $sign     = $number->isPositive() ? '' : '-';
        if ($number->abs()->hasDecimals()) {
            list($number, $dec) = explode('.', (string) $number);
        }

        if ($decimals) {
            $dec = sprintf("%'0-" . $decimals . 's', $dec);
        }

        return $sign . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", $thousandsSep, $number) . ($decimals ? $decPoint . $dec : '');
    }
}

// OMG a dynamic static anti pattern ^^
Math::gmpSupport();
