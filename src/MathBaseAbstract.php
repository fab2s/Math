<?php

declare(strict_types=1);

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

use InvalidArgumentException;

/**
 * Abstract class MathBaseAbstract
 */
abstract class MathBaseAbstract
{
    /**
     * Default precision
     */
    const PRECISION = 9;

    /**
     * base <= 62 char list
     */
    const BASECHAR_62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * base <= 36 charlist
     */
    const BASECHAR_36 = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * highest base supported
     */
    const BASE_MAX = 62;

    /**
     * base char cache for all supported bases (up to 62)
     *
     * @var array<int,string>
     */
    protected static array $baseChars = [
        36 => self::BASECHAR_36,
        62 => self::BASECHAR_62,
    ];

    /**
     *  if set, will be used as default for all consecutive instances
     */
    protected static int $globalPrecision;

    /**
     * Used in static context, aligned with $globalPrecision, default to self::PRECISION
     */
    protected static int $staticPrecision = self::PRECISION;
    protected static ?bool $gmpSupport    = null;

    /** @var numeric-string */
    protected string $number;

    /**
     * Instance precision, initialized with globalPrecision, default to self::PRECISION
     */
    protected int $precision = self::PRECISION;

    /** @return numeric-string */
    public function getNumber(): string
    {
        return $this->number;
    }

    public function isPositive(): bool
    {
        return $this->number[0] !== '-';
    }

    public function isNegative(): bool
    {
        return $this->number[0] === '-' && trim($this->number, '-0.') !== '';
    }

    public function isZero(): bool
    {
        return trim($this->number, '+-0.') === '';
    }

    public function isEven(): bool
    {
        $number = static::normalizeReal($this->number);

        return ! str_contains($number, '.') && bcmod($number, '2') === '0';
    }

    public function isOdd(): bool
    {
        $number = static::normalizeReal($this->number);

        return ! str_contains($number, '.') && bcmod($number, '2') !== '0';
    }

    public function hasDecimals(): bool
    {
        return str_contains($this->number, '.');
    }

    public function getScale(): int
    {
        $number = static::normalizeReal($this->number);
        $pos    = strpos($number, '.');

        return $pos === false ? 0 : strlen($number) - $pos - 1;
    }

    public function getIntegralPart(): string
    {
        $number = static::normalizeReal($this->number);
        $pos    = strpos($number, '.');
        $result = $pos === false ? $number : substr($number, 0, $pos);

        return $result === '-0' ? '0' : $result;
    }

    public function getFractionalPart(): string
    {
        $number = static::normalizeReal($this->number);
        $pos    = strpos($number, '.');

        return $pos === false ? '' : substr($number, $pos + 1);
    }

    public function normalize(): static
    {
        $result         = $this->mutate();
        $result->number = static::normalizeReal($result->number);

        return $result;
    }

    public function setPrecision(string|int $precision): static
    {
        // even INT_32 should be enough precision
        $result            = $this->mutate();
        $result->precision = max(0, (int) $precision);

        return $result;
    }

    protected function mutate(): static
    {
        return clone $this;
    }

    public static function setGlobalPrecision(string|int $precision): void
    {
        // even INT_32 should be enough precision
        static::$globalPrecision = max(0, (int) $precision);
        static::$staticPrecision = static::$globalPrecision;
    }

    public static function gmpSupport(?bool $disable = false): bool
    {
        if ($disable || $disable === null) {
            static::$gmpSupport = $disable ? false : null;
        }

        return static::$gmpSupport = static::$gmpSupport !== null
                ? static::$gmpSupport
                : function_exists('gmp_init');
    }

    /**
     * There is no way around it, if you want to trust bcmath
     * you need to feed it with VALID numbers
     * Things like '1.1.1' or '12E16'are all 0 in bcmath world
     */
    public static function isNumber(string|int|float|Math|null $number): bool
    {
        return (bool) preg_match('`^[+-]?([0-9]+(\.[0-9]+)?|\.[0-9]+)$`', (string) $number);
    }

    /**
     * Validation flavour of normalization logic
     */
    public static function normalizeNumber(string|int|float|Math|null $number, Math|string|int|float|null $default = null): ?string
    {
        if (! static::isNumber($number)) {
            return $default !== null ? (string) $default : null;
        }

        return static::normalizeReal((string) $number);
    }

    public static function getBaseChar(string|int $base): string
    {
        if (isset(static::$baseChars[$base])) {
            return static::$baseChars[$base];
        }

        static::validateBase($base = (int) static::validatePositiveInteger($base));

        if ($base > 36) {
            return static::$baseChars[$base] = substr(static::BASECHAR_62, 0, $base);
        }

        return static::$baseChars[$base] = substr(static::BASECHAR_36, 0, $base);
    }

    /**
     * Convert a from a given base (up to 62) to base 10.
     *
     * WARNING This method requires ext-gmp
     *
     * @internal param int $base
     */
    public static function baseConvert(string|int $number, string|int $fromBase = 10, string|int $toBase = 62): string
    {
        return gmp_strval(gmp_init($number, (int) $fromBase), (int) $toBase);
    }

    /**
     * Normalize a valid real number
     * removes preceding / trailing 0 and +
     *
     * @return numeric-string
     */
    protected static function normalizeReal(string|int $number): string
    {
        $number = (string) $number;
        $sign   = $number[0] === '-' ? '-' : '';
        $number = ltrim($number, '0+-');

        if (str_contains($number, '.')) {
            // also clear trailing 0 / .0000
            [$number, $dec] = explode('.', $number);
            $dec            = ($dec = rtrim($dec, '0.')) ? '.' . $dec : '';
            $number         = ($number ?: '0') . $dec;
        }

        /** @var numeric-string */ // @phpstan-ignore varTag.nativeType
        return $number ? $sign . $number : '0';
    }

    /**
     * @throws InvalidArgumentException
     */
    protected static function validateBase(int $base): void
    {
        if ($base < 2 || $base > self::BASE_MAX) {
            throw new InvalidArgumentException('Argument base is not valid, base 2 to ' . self::BASE_MAX . ' are supported');
        }
    }

    protected static function bcDec2Base(string $number, int $base, string $baseChar): string
    {
        $result    = '0';
        $numberLen = strlen($number);
        $base      = (string) $base;
        // Now loop through each digit in the number
        for ($i = $numberLen - 1; $i >= 0; $i--) {
            $char = $number[$i]; // extract the last char from the number
            $ord  = strpos($baseChar, $char); // get the decimal value
            if ($ord === false || $ord > $base) {
                throw new InvalidArgumentException('Argument number is invalid');
            }

            // Now convert the value+position to decimal
            $result = bcadd($result, bcmul((string) $ord, bcpow($base, (string) ($numberLen - $i - 1))));
        }

        return $result;
    }

    /** @return numeric-string */
    protected static function validateInputNumber(string|int|float|Math $number): string
    {
        if ($number instanceof self) {
            return $number->getNumber();
        }

        $number = trim((string) $number);
        if (! static::isNumber($number)) {
            throw new InvalidArgumentException('Argument number is not valid');
        }

        /** @var numeric-string */
        return $number;
    }

    /**
     * @param string|int $integer up to INT_32|64 since it's only used for things
     *                            like exponents, it should be enough
     *
     * @return numeric-string
     */
    protected static function validatePositiveInteger(string|int $integer): string
    {
        $integer = max(0, (int) $integer);
        if (! $integer) {
            throw new InvalidArgumentException('Argument number is not valid');
        }

        return (string) $integer;
    }
}
