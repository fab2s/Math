<?php

/*
 * This file is part of Math.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math;

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
     * base <= 64 charlist
     */
    const BASECHAR_64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

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
    const BASE_MAX = 64;

    /**
     * base char cache for all supported bases (bellow 64)
     *
     * @var string[]
     */
    protected static $baseChars = [
        36 => self::BASECHAR_36,
        62 => self::BASECHAR_62,
        64 => self::BASECHAR_64,
    ];

    /**
     *  if set, will be used as default for all consecutive instances
     *
     * @var int
     */
    protected static $globalPrecision;

    /**
     * Used in static context, aligned with $globalPrecision, default to self::PRECISION
     *
     * @var int
     */
    protected static $staticPrecision = self::PRECISION;

    /**
     * @var bool
     */
    protected static $gmpSupport;

    /**
     * @var string
     */
    protected $number;

    /**
     * Instance precision, initialized with globalPrecision, default to self::PRECISION
     *
     * @var int
     */
    protected $precision = self::PRECISION;

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->number[0] !== '-';
    }

    /**
     * @return bool
     */
    public function hasDecimals(): bool
    {
        return strpos($this->number, '.') !== false;
    }

    /**
     * @return $this
     */
    public function normalize(): self
    {
        $this->number = static::normalizeReal($this->number);

        return $this;
    }

    /**
     * @param string|int $precision
     *
     * @return $this
     */
    public function setPrecision($precision): self
    {
        // even INT_32 should be enough precision
        $this->precision = max(0, (int) $precision);

        return $this;
    }

    /**
     * @param string|int $precision
     */
    public static function setGlobalPrecision($precision)
    {
        // even INT_32 should be enough precision
        static::$globalPrecision = max(0, (int) $precision);
        static::$staticPrecision = static::$globalPrecision;
    }

    /**
     * @param bool $disable
     *
     * @return bool
     */
    public static function gmpSupport(bool $disable = false): bool
    {
        if ($disable) {
            return static::$gmpSupport = false;
        }

        return static::$gmpSupport = function_exists('gmp_init');
    }

    /**
     * There is no way around it, if you want to trust bcmath
     * you need to feed it with VALID numbers
     * Things like '1.1.1' or '12E16'are all 0 in bcmath world
     *
     * @param string|int $number
     *
     * @return bool
     */
    public static function isNumber($number): bool
    {
        return (bool) preg_match('`^[+-]?([0-9]+(\.[0-9]+)?|\.[0-9]+)$`', $number);
    }

    /**
     * Validation flavour of normalization logic
     *
     * @param string|int      $number
     * @param string|int|null $default
     *
     * @return string|null
     */
    public static function normalizeNumber($number, $default = null): ? string
    {
        if (!static::isNumber($number)) {
            return $default;
        }

        return static::normalizeReal($number);
    }

    /**
     * @param string|int $base
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getBaseChar($base): string
    {
        if (isset(static::$baseChars[$base])) {
            return static::$baseChars[$base];
        }

        static::validateBase($base = (int) static::validatePositiveInteger($base));

        if ($base > 62) {
            return static::$baseChars[$base] = substr(static::BASECHAR_64, 0, $base);
        }

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
     * @param string|int $number
     * @param string|int $fromBase
     * @param string|int $toBase
     *
     * @return string
     *
     * @internal param int $base
     */
    public static function baseConvert($number, $fromBase = 10, $toBase = 62): string
    {
        return gmp_strval(gmp_init($number, $fromBase), $toBase);
    }

    /**
     * Normalize a valid real number
     * removes preceding / trailing 0 and +
     *
     * @param string $number
     *
     * @return string
     */
    protected static function normalizeReal(string $number): string
    {
        $sign   = $number[0] === '-' ? '-' : '';
        $number = ltrim($number, '0+-');

        if (strpos($number, '.') !== false) {
            // also clear trailing 0 / .0000
            list($number, $dec) = explode('.', $number);
            $dec                = ($dec = rtrim($dec, '0.')) ? '.' . $dec : '';
            $number             = ($number ?: '0') . $dec;
        }

        return $number ? $sign . $number : '0';
    }

    /**
     * @param int $base
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateBase(int $base): void
    {
        if ($base < 2 || $base > self::BASE_MAX || $base > 64) {
            throw new \InvalidArgumentException('Argument base is not valid, base 2 to 64 are supported');
        }
    }

    /**
     * @param string|int $number
     * @param string|int $base
     * @param string     $baseChar
     *
     * @return string
     */
    protected static function bcDec2Base($number, $base, string $baseChar)
    {
        $result    = '';
        $numberLen = strlen($number);
        // Now loop through each digit in the number
        for ($i = $numberLen - 1; $i >= 0; --$i) {
            $char = $number[$i]; // extract the last char from the number
            $ord  = strpos($baseChar, $char); // get the decimal value
            if ($ord === false || $ord > $base) {
                throw new \InvalidArgumentException('Argument number is invalid');
            }

            // Now convert the value+position to decimal
            $result = bcadd($result, bcmul($ord, bcpow($base, ($numberLen - $i - 1))));
        }

        return $result ? $result : '0';
    }

    /**
     * @param string|int|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected static function validateInputNumber($number): string
    {
        if ($number instanceof static) {
            return $number->getNumber();
        }

        $number = trim($number);
        if (!static::isNumber($number)) {
            throw new \InvalidArgumentException('Argument number is not valid');
        }

        return $number;
    }

    /**
     * @param string|int $integer up to INT_32|64 since it's only used for things
     *                            like exponents, it should be enough
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected static function validatePositiveInteger($integer)
    {
        $integer = max(0, (int) $integer);
        if (!$integer) {
            throw new \InvalidArgumentException('Argument number is not valid');
        }

        return (string) $integer;
    }
}
