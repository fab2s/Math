<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Class Math
 */
class Math extends MathOpsAbstract implements JsonSerializable, Stringable
{
    public function __construct(string|int|float|Math $number)
    {
        if (isset(static::$globalPrecision)) {
            $this->precision = static::$globalPrecision;
        }

        $this->number = static::validateInputNumber($number);
    }

    public function __toString(): string
    {
        return static::normalizeNumber($this->number);
    }

    public static function number(string|int|float|Math $number): static
    {
        return static::make($number);
    }

    public static function make(string|int|float|Math $number): static
    {
        return new static($number);
    }

    /**
     * convert any based value bellow or equals to 64 to its decimal value
     */
    public static function fromBase(string|int $number, int $base): static
    {
        // trim base 64 padding char, only positive
        $number = trim($number, ' =-');
        if ($number === '' || str_contains($number, '.')) {
            throw new InvalidArgumentException('Argument number is not an integer');
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

    public function gte(string|int|float|Math $number): bool
    {
        return bccomp($this->number, static::validateInputNumber($number), $this->precision) >= 0;
    }

    public function gt(string|int|float|Math $number): bool
    {
        return bccomp($this->number, static::validateInputNumber($number), $this->precision) === 1;
    }

    public function lte(string|int|float|Math $number): bool
    {
        return bccomp($this->number, static::validateInputNumber($number), $this->precision) <= 0;
    }

    public function lt(string|int|float|Math $number): bool
    {
        return bccomp($this->number, static::validateInputNumber($number), $this->precision) === -1;
    }

    public function eq(string|int|float|Math $number): bool
    {
        return bccomp($this->number, static::validateInputNumber($number), $this->precision) === 0;
    }

    /**
     * convert decimal value to any other base bellow or equals to 64
     */
    public function toBase(string|int $base): string
    {
        if ($this->normalize()->hasDecimals()) {
            throw new InvalidArgumentException('Argument number is not an integer');
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

    public function format(string|int $decimals = 0, string $decPoint = '.', string $thousandsSep = ' '): string
    {
        $decimals = max(0, (int) $decimals);
        $dec      = '';
        // do not mutate
        $number = (new static($this))->round($decimals)->normalize();
        $sign   = $number->isPositive() ? '' : '-';
        if ($number->abs()->hasDecimals()) {
            [$number, $dec] = explode('.', (string) $number);
        }

        if ($decimals) {
            $dec = sprintf("%'0-" . $decimals . 's', $dec);
        }

        return $sign . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", $thousandsSep, $number) . ($decimals ? $decPoint . $dec : '');
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}

// OMG a dynamic static anti pattern ^^
Math::gmpSupport();
