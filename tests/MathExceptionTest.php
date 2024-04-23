<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests;

use fab2s\Math\Math;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class MathTest
 */
class MathExceptionTest extends TestCase
{
    public function test_validate_base()
    {
        $testClass = new class(0) extends Math
        {
            public static function validateBaseTest(int $base): void
            {
                self::validateBase($base);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $testClass::validateBaseTest(128);
    }

    public function test_bc_dec_2_base()
    {
        $testClass = new class(0) extends Math
        {
            public static function bcDec2BaseTest(string $number, int $base, string $baseChar): void
            {
                self::bcDec2Base($number, $base, $baseChar);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $testClass::bcDec2BaseTest('$', 42, Math::getBaseChar(62));
    }

    public function test_validate_input_number()
    {
        $testClass = new class(0) extends Math
        {
            public static function validateInputNumberTest(string|int|float|Math $number): void
            {
                self::validateInputNumber($number);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $testClass::validateInputNumberTest('NaN');
    }

    public function test_positive_integer()
    {
        $testClass = new class(0) extends Math
        {
            public static function validatePositiveIntegerTest(string|int $number): void
            {
                self::validatePositiveInteger($number);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $testClass::validatePositiveIntegerTest(0);
    }

    public function test_from_base()
    {
        $this->expectException(InvalidArgumentException::class);
        Math::fromBase('LZ.LZ', 62);
    }

    public function test_to_base()
    {
        $this->expectException(InvalidArgumentException::class);
        Math::make(42.42)->toBase(62);
    }
}
