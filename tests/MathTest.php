<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests;

use fab2s\Math\Math;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class MathTest
 */
class MathTest extends \PHPUnit\Framework\TestCase
{
    public static function number_formatData(): array
    {
        return [
            // number, expected [, precision[, dec_point[, thousands_sep]]]
            ['255173029255255255', '255 173 029 255 255 255'],
            ['255173029255255255.98797', '255 173 029 255 255 256'],
            ['255173029255255255.98797', '255 173 029 255 255 255.98797', 5],
            ['255173029255255255.98797', '255 173 029 255 255 255.988', 3],
            ['-255173029255255255.98797', '-255 173 029 255 255 255.988', 3],
            ['-255173029255255255.98797', '-255 173 029 255 255 255.98797000', 8],
            ['-255173029255255255.98797', '-255 173 029 255 255 255,98797000', 8, ','],
            ['-255173029255255255.98797', '-255,173,029,255,255,255.98797000', 8, '.', ','],
            ['0.000000001', '0.00000000', 8],
            ['0.000000001', '0.000000001', 9],
            ['-0', '0.00000000', 8],
            ['+0', '0.00000000', 8],
            ['0', '0'],
        ];
    }

    #[DataProvider('number_formatData')]
    public function test_number_format(string|int|Math $number, string $expected, int $decimals = 0, string $dec_point = '.', string $thousands_sep = ' ')
    {
        $this->assertSame($expected, (string) Math::number($number)->format($decimals, $dec_point, $thousands_sep));
    }

    public static function compData(): array
    {
        return [
            [
                'left'     => '255173029255255255',
                'operator' => '<',
                'right'    => '255173',
                'expected' => false,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '>',
                'right'    => '255173',
                'expected' => true,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '=',
                'right'    => '255173',
                'expected' => false,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '=',
                'right'    => '255173029255255255',
                'expected' => true,
            ],
            [
                'left'     => '255173029255255255.' . str_repeat('0', Math::PRECISION + 1) . '1',
                'operator' => '>',
                'right'    => '255173029255255255.00',
                'expected' => false,
            ],
            [
                'left'     => '255173029255255255.' . str_repeat('0', Math::PRECISION - 1) . '2',
                'operator' => '>',
                'right'    => '255173029255255255.00' . str_repeat('0', Math::PRECISION - 1) . '1',
                'expected' => true,
            ],
            [
                'left'     => '54',
                'operator' => '>=',
                'right'    => '0',
                'expected' => true,
            ],
            [
                'left'     => '54',
                'operator' => '>=',
                'right'    => '-0',
                'expected' => true,
            ],
            [
                'left'     => '54',
                'operator' => '>=',
                'right'    => '-32',
                'expected' => true,
            ],
            [
                'left'     => '-23',
                'operator' => '>=',
                'right'    => '-32',
                'expected' => true,
            ],
            [
                'left'     => '-23',
                'operator' => '>=',
                'right'    => '22',
                'expected' => false,
            ],
            [
                'left'     => '54',
                'operator' => '<=',
                'right'    => '0',
                'expected' => false,
            ],
            [
                'left'     => '54',
                'operator' => '<=',
                'right'    => '-0',
                'expected' => false,
            ],
            [
                'left'     => '54',
                'operator' => '<=',
                'right'    => '-32',
                'expected' => false,
            ],
            [
                'left'     => '-23',
                'operator' => '<=',
                'right'    => '-32',
                'expected' => false,
            ],
            [
                'left'     => '-23',
                'operator' => '<=',
                'right'    => '22',
                'expected' => true,
            ],
            [
                'left'     => '0',
                'operator' => '<',
                'right'    => '0',
                'expected' => false,
            ],
            [
                'left'     => '+0',
                'operator' => '>',
                'right'    => '-0',
                'expected' => false,
            ],
            [
                'left'     => '-0',
                'operator' => '>',
                'right'    => '0',
                'expected' => false,
            ],
            [
                'left'     => '-0',
                'operator' => '=',
                'right'    => 0,
                'expected' => true,
            ],
            [
                'left'     => '0000042.420000',
                'operator' => '=',
                'right'    => '42.42',
                'expected' => true,
            ],
            [
                'left'     => '0000042.420000',
                'operator' => '!=',
                'right'    => '42.42',
                'expected' => false,
            ],
            [
                'left'     => '-42.420000',
                'operator' => '!=',
                'right'    => Math::number('-00042.4200'),
                'expected' => false,
            ],
        ];
    }

    #[DataProvider('compData')]
    public function test_comp(string|int|Math $left, string $operator, $right, bool $expected)
    {
        switch ($operator) {
            case '<':
                $this->assertSame(
                    $expected,
                    Math::number($left)->lt($right),
                );
                break;
            case '<=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->lte($right),
                );
                break;
            case '>':
                $this->assertSame(
                    $expected,
                    Math::number($left)->gt($right),
                );
                break;
            case '>=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->gte($right),
                );
                break;
            case '=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->eq($right),
                );
                break;
            case '!=':
                $this->assertSame(
                    $expected,
                    ! Math::number($left)->eq($right),
                );
                break;
        }
    }

    public static function roundData(): array
    {
        return [
            [
                'number'    => '255173029255255255.98797',
                'precision' => 0,
                'expected'  => '255173029255255256',
            ],
            [
                'number'    => '255173029255255255.98797',
                'precision' => 2,
                'expected'  => '255173029255255255.99',
            ],
            [
                'number'    => '1000',
                'precision' => 0,
                'expected'  => '1000',
            ],
            [
                'number'    => '1000.000',
                'precision' => 0,
                'expected'  => '1000',
            ],
            [
                'number'    => '54',
                'precision' => 0,
                'expected'  => '54',
            ],
            [
                'number'    => '54.001',
                'precision' => 0,
                'expected'  => '54',
            ],
            [
                'number'    => '54.99',
                'precision' => 0,
                'expected'  => '55',
            ],
            [
                'number'    => '54.99',
                'precision' => 1,
                'expected'  => '55',
            ],
            [
                'number'    => '54.5',
                'precision' => 1,
                'expected'  => '54.5',
            ],
            [
                'number'    => '54.55',
                'precision' => 1,
                'expected'  => '54.6',
            ],
            [
                'number'    => '-3.4',
                'precision' => 1,
                'expected'  => '-3.4',
            ],
            [
                'number'    => '-3.6',
                'precision' => 1,
                'expected'  => '-3.6',
            ],
            [
                'number'    => '-3.6',
                'precision' => 2,
                'expected'  => '-3.6',
            ],
            [
                'number'    => '-3.6',
                'precision' => 0,
                'expected'  => '-4',
            ],
            [
                'number'    => -8,
                'precision' => 0,
                'expected'  => '-8',
            ],
            [
                'number'    => Math::number('-3.501'),
                'precision' => 0,
                'expected'  => '-4',
            ],
        ];
    }

    #[DataProvider('roundData')]
    public function test_round(string|int|Math $number, int $precision, string $expected)
    {
        $this->assertSame($expected, (string) Math::number($number)->round($precision));
    }

    public static function maxMinData(): array
    {
        return [
            [
                'param' => ['54', '32', '23', '0', '255173029255255255', '255173029255255256', '.0'],
                'min'   => '0',
                'max'   => '255173029255255256',
            ],
            [
                'param' => ['54', '32', '23', '0', '255173029255255255'],
                'min'   => '0',
                'max'   => '255173029255255255',
            ],
            [
                'param' => ['54', '32', '23', '0'],
                'min'   => '0',
                'max'   => '54',
            ],
            [
                'param' => ['54', '-32', '23', '0'],
                'min'   => '-32',
                'max'   => '54',
            ],
            [
                'param' => ['-54', '-32', '-23', '0'],
                'min'   => '-54',
                'max'   => '0',
            ],
            [
                'param' => ['-54', '-32', '-23', '-0'],
                'min'   => '-54',
                'max'   => '0',
            ],
            [
                'param' => ['53.28', '52.65', '53.27', '52.64'],
                'min'   => '52.64',
                'max'   => '53.28',
            ],
            [
                'param' => [42, '52.65', Math::number(1337), '52.64'],
                'min'   => '42',
                'max'   => '1337',
            ],
        ];
    }

    /**
     * @param (string|int|Math)[] $param
     */
    #[DataProvider('maxMinData')]
    public function test_max(array $param, string|int|Math $min, string|int|Math $max)
    {
        $first = $param[0];
        unset($param[0]);
        $this->assertSame(
            $max,
            (string) Math::number($first)->max(...$param),
        );
    }

    /**
     * @param (string|int|Math)[] $param
     */
    #[DataProvider('maxMinData')]
    public function test_min(array $param, string|int|Math $min, string|int|Math $max)
    {
        $first = $param[0];
        unset($param[0]);
        $this->assertSame(
            $min,
            (string) Math::number($first)->min(...$param),
        );
    }

    public static function normalizeData(): array
    {
        return [
            [
                'number'   => '000255173029255255255.00000005',
                'expected' => '255173029255255255.00000005',
            ],
            [
                'number'   => '000255173029255255255.000',
                'expected' => '255173029255255255',
            ],
            [
                'number'   => '255173029255255255',
                'expected' => '255173029255255255',
            ],
            [
                'number'   => '1000',
                'expected' => '1000',
            ],
            [
                'number'   => '.000',
                'expected' => '0',
            ],
            [
                'number'   => '-.000',
                'expected' => '0',
            ],
            [
                'number'   => '+.000',
                'expected' => '0',
            ],
            [
                'number'   => '-000.000',
                'expected' => '0',
            ],
            [
                'number'   => '+000.000',
                'expected' => '0',
            ],
            [
                'number'   => '.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '-.0001',
                'expected' => '-0.0001',
            ],
            [
                'number'   => '0000.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '-0000.0001',
                'expected' => '-0.0001',
            ],
            [
                'number'   => '+.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '00100.0001',
                'expected' => '100.0001',
            ],
            [
                'number'   => '-00100.0001',
                'expected' => '-100.0001',
            ],
            [
                'number'   => '+00100.0001',
                'expected' => '100.0001',
            ],
            [
                'number'   => 0,
                'expected' => '0',
            ],
            [
                'number'   => Math::number(' 000100.0001 '),
                'expected' => '100.0001',
            ],
        ];
    }

    #[DataProvider('normalizeData')]
    public function test_normalize(string|int|Math $number, string $expected)
    {
        $this->assertSame($expected, (string) Math::number($number));
    }

    #[DataProvider('addData')]
    public function test_add(string|int|Math $left, string|int|Math $right, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($left)->add($right),
        );
    }

    public static function addData(): array
    {
        return [
            [
                'left'     => '1',
                'right'    => '0',
                'expected' => '1',
            ],
            [
                'left'     => '1',
                'right'    => '-1',
                'expected' => '0',
            ],
            [
                'left'     => '.9',
                'right'    => '+0.1',
                'expected' => '1',
            ],
            [
                'left'     => '.9',
                'right'    => '41.1',
                'expected' => '42',
            ],
            [
                'left'     => 27,
                'right'    => Math::number(15),
                'expected' => '42',
            ],
        ];
    }

    #[DataProvider('subData')]
    public function test_sub(string|int|Math $left, string|int|Math $right, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($left)->sub($right),
        );
    }

    public static function subData(): array
    {
        return [
            [
                'left'     => '1',
                'right'    => '0',
                'expected' => '1',
            ],
            [
                'left'     => '1',
                'right'    => '-1',
                'expected' => '2',
            ],
            [
                'left'     => '-1',
                'right'    => '27',
                'expected' => '-28',
            ],
            [
                'left'     => '.9',
                'right'    => '+.1',
                'expected' => '0.8',
            ],
            [
                'left'     => '.8',
                'right'    => '-41.2',
                'expected' => '42',
            ],
            [
                'left'     => 1337,
                'right'    => Math::number(1295),
                'expected' => '42',
            ],
        ];
    }

    #[DataProvider('mulDivData')]
    public function test_mul_div(string|int|Math $left, string|int|Math $right, string $expected)
    {
        $result = Math::number($left)->mul($right);
        $this->assertSame(
            $expected,
            (string) $result,
        );

        $this->assertSame(
            (string) Math::number($left),
            (string) $result->div($right),
        );
    }

    public static function mulDivData(): array
    {
        return [
            [
                'left'     => '2',
                'right'    => '21',
                'expected' => '42',
            ],
            [
                'left'     => '0',
                'right'    => '42',
                'expected' => '0',
            ],
            [
                'left'     => '-546.2255',
                'right'    => '42',
                'expected' => '-22941.471',
            ],
            [
                'left'     => 0,
                'right'    => Math::number('42'),
                'expected' => '0',
            ],
        ];
    }

    #[DataProvider('sqrtData')]
    public function test_sqrt(string|int|Math $number, string $expected)
    {
        $result = Math::number($number)->sqrt();
        $this->assertSame(
            $expected,
            (string) $result,
        );

        $this->assertSame(
            (string) Math::number($number),
            (string) $result->pow(2),
        );
    }

    public static function sqrtData(): array
    {
        $result = [
            [
                'number'   => '64',
                'expected' => '8',
            ],
            [
                'number'   => '9.8596',
                'expected' => '3.14',
            ],
            [
                'number'   => 4,
                'expected' => '2',
            ],
        ];

        for ($i = 1; $i < 50; $i++) {
            $number   = mt_rand(1, 10000) . '.' . mt_rand(0, 10000);
            $result[] = [
                'number'   => Math::number($number)->pow(2),
                'expected' => (string) Math::number($number),
            ];
        }

        return $result;
    }

    #[DataProvider('modData')]
    public function test_mod(string|int|Math $number, string $mod, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->mod($mod),
        );
    }

    public static function modData(): array
    {
        $result = [
            [
                'number'   => '64',
                'mod'      => '8',
                'expected' => '0',
            ],
            [
                'number'   => '42',
                'mod'      => '7',
                'expected' => '0',
            ],
            [
                'number'   => '42',
                'mod'      => '4',
                'expected' => '2',
            ],
        ];

        for ($i = 1; $i < 50; $i++) {
            $number   = mt_rand(1, 10000);
            $mod      = mt_rand(1, 100);
            $result[] = [
                'number'   => Math::number($number),
                'mod'      => (string) $mod,
                'expected' => (string) ($number % $mod),
            ];
        }

        return $result;
    }

    #[DataProvider('powModData')]
    public function test_pow_mod(string|int|Math $number, string $pow, string $mod)
    {
        $this->assertSame(
            (string) Math::number($number)->powMod($pow, $mod),
            (string) Math::number($number)->pow($pow)->mod($mod),
        );
    }

    public static function powModData(): array
    {
        $result = [];
        for ($i = 1; $i < 50; $i++) {
            $result[] = [
                'number' => (string) mt_rand(1, 100000),
                'pow'    => (string) mt_rand(1, 1000),
                'mod'    => (string) mt_rand(1, 1000),
            ];
        }

        return $result;
    }

    #[DataProvider('ceilData')]
    public function test_ceil(string|int|Math $number, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->ceil(),
        );
    }

    public static function ceilData(): array
    {
        return [
            [
                'number'   => '1',
                'expected' => '1',
            ],
            [
                'number'   => '1.000001',
                'expected' => '2',
            ],
            [
                'number'   => '1.000000000000',
                'expected' => '1',
            ],
            [
                'number'   => '1.' . str_repeat('0', 2 * Math::PRECISION) . '1',
                'expected' => '2',
            ],
            [
                'number'   => '-1.' . str_repeat('9', 2 * Math::PRECISION),
                'expected' => '-1',
            ],
            [
                'number'   => '-6.99',
                'expected' => '-6',
            ],
            [
                'number'   => '-0',
                'expected' => '0',
            ],
            [
                'number'   => '+0',
                'expected' => '0',
            ],
        ];
    }

    #[DataProvider('floorData')]
    public function test_floor(string|int|Math $number, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->floor(),
        );
    }

    public static function floorData(): array
    {
        return [
            [
                'number'   => '1',
                'expected' => '1',
            ],
            [
                'number'   => '1.000001',
                'expected' => '1',
            ],
            [
                'number'   => '1.000000000000',
                'expected' => '1',
            ],
            [
                'number'   => '1.' . str_repeat('0', 2 * Math::PRECISION) . '1',
                'expected' => '1',
            ],
            [
                'number'   => '-1.' . str_repeat('9', 2 * Math::PRECISION),
                'expected' => '-2',
            ],
            [
                'number'   => '-6.99',
                'expected' => '-7',
            ],
            [
                'number'   => '-0',
                'expected' => '0',
            ],
            [
                'number'   => '+0',
                'expected' => '0',
            ],
        ];
    }

    #[DataProvider('absData')]
    public function test_abs(string|int|Math $number, string $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->abs(),
        );
    }

    public static function absData(): array
    {
        return [
            [
                'number'   => '-42',
                'expected' => '42',
            ],
            [
                'number'   => '+42',
                'expected' => '42',
            ],
        ];
    }

    #[DataProvider('isNumberData')]
    public function test_is_number(string|int|float|Math|null $number, bool $expected)
    {
        $this->assertSame(
            $expected,
            Math::isNumber($number),
        );
    }

    public static function isNumberData(): array
    {
        return [
            [
                'number'   => null,
                'expected' => false,
            ],
            [
                'number'   => '-42',
                'expected' => true,
            ],
            [
                'number'   => -42,
                'expected' => true,
            ],
            [
                'number'   => -42.42,
                'expected' => true,
            ],
            [
                'number'   => 42.42,
                'expected' => true,
            ],
            [
                'number'   => '',
                'expected' => false,
            ],
            [
                'number'   => '+42',
                'expected' => true,
            ],
            [
                'number'   => '+00004200000',
                'expected' => true,
            ],
            [
                'number'   => '-000042000.00',
                'expected' => true,
            ],
            [
                'number'   => '-000042000.',
                'expected' => false,
            ],
            [
                'number'   => '000.042000.',
                'expected' => false,
            ],
            [
                'number'   => '.042000.',
                'expected' => false,
            ],
            [
                'number'   => '42e64',
                'expected' => false,
            ],
            [
                'number'   => ' 42',
                'expected' => false,
            ],
            [
                'number'   => '4 2',
                'expected' => false,
            ],
            [
                'number'   => '42 ',
                'expected' => false,
            ],
            [
                'number'   => new Math('42 '),
                'expected' => true,
            ],
            [
                'number'   => '000',
                'expected' => true,
            ],
            [
                'number'   => '000.',
                'expected' => false,
            ],
            [
                'number'   => '.000',
                'expected' => true,
            ],
            [
                'number'   => '-.000',
                'expected' => true,
            ],
            [
                'number'   => '+.000',
                'expected' => true,
            ],
            [
                'number'   => '--27',
                'expected' => false,
            ],
            [
                'number'   => '++27',
                'expected' => false,
            ],
        ];
    }

    public static function baseConvertData(): array
    {
        return [
            [
                'number' => '0',
                'base'   => '62',
            ],
            [
                'number' => '0',
                'base'   => '36',
            ],
            [
                'number' => '10',
                'base'   => '62',
            ],
            [
                'number' => '10',
                'base'   => '36',
            ],
            [
                'number' => '62',
                'base'   => '62',
            ],
            [
                'number' => '36',
                'base'   => '36',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '16',
            ],
            [
                'number' => '00025517302925525525',
                'base'   => '28',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '8',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '36',
            ],
            [
                'number' => '255173029255255255',
                'base'   => '2',
            ],
            [
                'number' => '25517993029255255255',
                'base'   => '37',
            ],
            [
                'number' => '25517993029255255255',
                'base'   => '35',
            ],
            [
                'number' => '0',
                'base'   => '48',
            ],
            [
                'number' => '9856565',
                'base'   => '61',
            ],
        ];
    }

    #[DataProvider('baseConvertData')]
    public function test_base_convert(string|int|Math $number, string $base)
    {
        $this->assertSame(
            (string) Math::number($number),
            (string) Math::fromBase(Math::number($number)->toBase($base), $base),
        );

        if (! Math::gmpSupport()) {
            return;
        }

        $this->assertSame(
            (string) Math::number($number),
            Math::normalizeNumber(Math::baseConvert(Math::baseConvert($number, 10, $base), $base, 10)),
        );

        $expected = gmp_strval(gmp_init((string) Math::number($number)), $base);
        $this->assertSame(
            $expected,
            Math::baseConvert($number, 10, $base),
        );

        Math::gmpSupport(true);
        $this->assertSame(
            $expected,
            Math::number($number)->toBase($base),
        );

        Math::gmpSupport(null);
    }

    public function test_to_string()
    {
        $this->assertSame('33.33', (string) Math::number('33.33'));
        $this->assertSame('33.33', Math::number('33.33')->jsonSerialize());
    }
}
