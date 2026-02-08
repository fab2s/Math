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
use PHPUnit\Framework\TestCase;

/**
 * Class MathTest
 */
class MathTest extends TestCase
{
    public function test_precision()
    {
        $this->assertSame(
            '42.42',
            (string) Math::number(42.42)
                ->setPrecision(2)
                ->add('0.0042'),
        );

        $this->assertSame(
            '42.4242',
            (string) Math::number(42.42)
                ->add('0.0042'),
        );

        Math::setGlobalPrecision(2);

        $this->assertSame(
            '42.42',
            (string) Math::number(42.42)
                ->add('0.0042'),
        );

        Math::setGlobalPrecision(Math::PRECISION);

        $this->assertSame(
            '42.4242',
            (string) Math::number(42.42)
                ->add('0.0042'),
        );
    }

    public function test_json_serialize()
    {
        $number = Math::number(42);
        $this->assertSame((string) $number, $number->jsonSerialize());

        $this->assertSame(json_encode((string) $number), json_encode(Math::number(42)));
    }

    public function test_normalize_number()
    {
        $this->assertSame('42', Math::normalizeNumber('000042.0000'));
        $this->assertSame('42', Math::normalizeNumber(null, 42));
    }

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
            // negative values that round to zero must not produce -0
            ['-0.001', '0.00', 2],
            ['-0.004', '0.00', 2],
            ['-0.000000001', '0.00000000', 8],
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
            // negative zero variants with decimal padding
            [
                'number'   => '-0.00',
                'expected' => '0',
            ],
            [
                'number'   => '-0.000000000',
                'expected' => '0',
            ],
            [
                'number'   => '-00.00',
                'expected' => '0',
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
                'base'   => 62,
            ],
            [
                'number' => '0',
                'base'   => 36,
            ],
            [
                'number' => '10',
                'base'   => 62,
            ],
            [
                'number' => '10',
                'base'   => 36,
            ],
            [
                'number' => '62',
                'base'   => 62,
            ],
            [
                'number' => '36',
                'base'   => 36,
            ],
            [
                'number' => '000255173029255255255',
                'base'   => 16,
            ],
            [
                'number' => '00025517302925525525',
                'base'   => 28,
            ],
            [
                'number' => '000255173029255255255',
                'base'   => 8,
            ],
            [
                'number' => '000255173029255255255',
                'base'   => 36,
            ],
            [
                'number' => '255173029255255255',
                'base'   => 2,
            ],
            [
                'number' => '25517993029255255255',
                'base'   => 37,
            ],
            [
                'number' => '25517993029255255255',
                'base'   => 35,
            ],
            [
                'number' => '0',
                'base'   => 48,
            ],
            [
                'number' => '9856565',
                'base'   => 61,
            ],
            // negative base conversion
            [
                'number' => '-42',
                'base'   => 16,
            ],
            [
                'number' => '-42',
                'base'   => 2,
            ],
            [
                'number' => '-1337',
                'base'   => 62,
            ],
            [
                'number' => '-255',
                'base'   => 36,
            ],
            [
                'number' => '-255173029255255255',
                'base'   => 16,
            ],
            [
                'number' => '-25517993029255255255',
                'base'   => 37,
            ],
        ];
    }

    #[DataProvider('baseConvertData')]
    public function test_base_convert(string|int|Math $number, int $base)
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

        $this->assertSame(
            (string) Math::number($number),
            (string) Math::fromBase(Math::number($number)->toBase($base), $base),
        );

        Math::gmpSupport(null);
    }

    public function test_from_base_case_insensitive()
    {
        // bases <= 36: case should not matter
        $this->assertSame('255', (string) Math::fromBase('FF', 16));
        $this->assertSame('255', (string) Math::fromBase('ff', 16));
        $this->assertSame('1337', (string) Math::fromBase('115', 36));
        $this->assertSame('-255', (string) Math::fromBase('-FF', 16));
        $this->assertSame('-255', (string) Math::fromBase('-ff', 16));

        if (! Math::gmpSupport()) {
            return;
        }

        // verify bcmath path produces the same results
        Math::gmpSupport(true);
        $this->assertSame('255', (string) Math::fromBase('FF', 16));
        $this->assertSame('255', (string) Math::fromBase('ff', 16));
        $this->assertSame('-255', (string) Math::fromBase('-FF', 16));
        Math::gmpSupport(null);
    }

    #[DataProvider('modData')]
    public function test_mod_gmp_toggle(string|int|Math $number, string $mod, string $expected)
    {
        if (! Math::gmpSupport()) {
            $this->markTestSkipped('GMP not available');
        }

        $gmpResult = (string) Math::number($number)->mod($mod);

        Math::gmpSupport(true);
        $bcResult = (string) Math::number($number)->mod($mod);
        Math::gmpSupport(null);

        $this->assertSame($gmpResult, $bcResult);
    }

    #[DataProvider('powModData')]
    public function test_pow_mod_gmp_toggle(string|int|Math $number, string $pow, string $mod)
    {
        if (! Math::gmpSupport()) {
            $this->markTestSkipped('GMP not available');
        }

        $gmpResult = (string) Math::number($number)->powMod($pow, $mod);

        Math::gmpSupport(true);
        $bcResult = (string) Math::number($number)->powMod($pow, $mod);
        Math::gmpSupport(null);

        $this->assertSame($gmpResult, $bcResult);
    }

    public static function powGmpData(): array
    {
        return [
            [
                'number'   => '2',
                'exponent' => '10',
                'expected' => '1024',
            ],
            [
                'number'   => '7',
                'exponent' => '5',
                'expected' => '16807',
            ],
            [
                'number'   => '123456789',
                'exponent' => '3',
                'expected' => '1881676371789154860897069',
            ],
            [
                'number'   => '-3',
                'exponent' => '3',
                'expected' => '-27',
            ],
            [
                'number'   => '1',
                'exponent' => '100',
                'expected' => '1',
            ],
            [
                'number'   => '0',
                'exponent' => '5',
                'expected' => '0',
            ],
        ];
    }

    #[DataProvider('powGmpData')]
    public function test_pow_gmp_toggle(string|int|Math $number, string $exponent, string $expected)
    {
        if (! Math::gmpSupport()) {
            $this->markTestSkipped('GMP not available');
        }

        $gmpResult = (string) Math::number($number)->pow($exponent);
        $this->assertSame($expected, $gmpResult);

        Math::gmpSupport(true);
        $bcResult = (string) Math::number($number)->pow($exponent);
        Math::gmpSupport(null);

        $this->assertSame($gmpResult, $bcResult);
    }

    public function test_pow_decimal_uses_bcmath()
    {
        // Decimal base should use bcmath path even with GMP available
        $result = (string) Math::number('2.5')->pow('3');
        $this->assertSame('15.625', $result);
    }

    public function test_to_string()
    {
        $this->assertSame('33.33', (string) Math::number('33.33'));
        $this->assertSame('33.33', Math::number('33.33')->jsonSerialize());
    }

    public function test_is_zero()
    {
        $this->assertTrue(Math::number('0')->isZero());
        $this->assertTrue(Math::number('-0')->isZero());
        $this->assertTrue(Math::number('+0')->isZero());
        $this->assertTrue(Math::number('0.000000000')->isZero());
        $this->assertFalse(Math::number('1')->isZero());
        $this->assertFalse(Math::number('-1')->isZero());
        $this->assertFalse(Math::number('0.000000001')->isZero());
    }

    public function test_is_negative()
    {
        $this->assertTrue(Math::number('-1')->isNegative());
        $this->assertTrue(Math::number('-0.001')->isNegative());
        $this->assertFalse(Math::number('0')->isNegative());
        $this->assertFalse(Math::number('-0')->isNegative());
        $this->assertFalse(Math::number('+0')->isNegative());
        $this->assertFalse(Math::number('0.000000000')->isNegative());
        $this->assertFalse(Math::number('1')->isNegative());
        $this->assertFalse(Math::number('+42')->isNegative());
    }

    public function test_is_even()
    {
        $this->assertTrue(Math::number('0')->isEven());
        $this->assertTrue(Math::number('2')->isEven());
        $this->assertTrue(Math::number('42')->isEven());
        $this->assertTrue(Math::number('-8')->isEven());
        $this->assertFalse(Math::number('1')->isEven());
        $this->assertFalse(Math::number('3')->isEven());
        $this->assertFalse(Math::number('-7')->isEven());
        // non-integers are neither even nor odd
        $this->assertFalse(Math::number('42.5')->isEven());
        $this->assertFalse(Math::number('2.0001')->isEven());
        $this->assertFalse(Math::number('-3.14')->isEven());
    }

    public function test_is_odd()
    {
        $this->assertTrue(Math::number('1')->isOdd());
        $this->assertTrue(Math::number('3')->isOdd());
        $this->assertTrue(Math::number('-7')->isOdd());
        $this->assertFalse(Math::number('0')->isOdd());
        $this->assertFalse(Math::number('2')->isOdd());
        $this->assertFalse(Math::number('42')->isOdd());
        // non-integers are neither even nor odd
        $this->assertFalse(Math::number('42.5')->isOdd());
        $this->assertFalse(Math::number('1.001')->isOdd());
        $this->assertFalse(Math::number('-3.14')->isOdd());
    }

    public function test_is_even_odd_after_operations()
    {
        // after operations, bcmath pads with zeros — should still work
        $this->assertTrue(Math::number('1')->add('1')->isEven());
        $this->assertTrue(Math::number('2')->add('1')->isOdd());
        $this->assertFalse(Math::number('1')->div('3')->isEven());
        $this->assertFalse(Math::number('1')->div('3')->isOdd());
    }

    public function test_get_scale()
    {
        $this->assertSame(0, Math::number('42')->getScale());
        $this->assertSame(2, Math::number('42.99')->getScale());
        $this->assertSame(9, Math::number('1.123456789')->getScale());
        $this->assertSame(3, Math::number('-0.001')->getScale());
        // after operations, bcmath padding should be stripped
        $this->assertSame(0, Math::number('1')->add('1')->getScale());
        $this->assertSame(0, Math::number('1.5')->add('0.5')->getScale());
        $this->assertSame(1, Math::number('1.5')->add('0.3')->getScale());
    }

    public function test_get_integral_part()
    {
        $this->assertSame('42', Math::number('42')->getIntegralPart());
        $this->assertSame('42', Math::number('42.99')->getIntegralPart());
        $this->assertSame('0', Math::number('-0.001')->getIntegralPart());
        $this->assertSame('0', Math::number('0')->getIntegralPart());
        $this->assertSame('0', Math::number('-0')->getIntegralPart());
        // after operations
        $this->assertSame('2', Math::number('1')->add('1')->getIntegralPart());
    }

    public function test_get_fractional_part()
    {
        $this->assertSame('', Math::number('42')->getFractionalPart());
        $this->assertSame('99', Math::number('42.99')->getFractionalPart());
        $this->assertSame('001', Math::number('-0.001')->getFractionalPart());
        $this->assertSame('123456789', Math::number('1.123456789')->getFractionalPart());
        // after operations, padding should be stripped
        $this->assertSame('', Math::number('1')->add('1')->getFractionalPart());
    }

    public function test_negate()
    {
        $this->assertSame('-42', (string) Math::number('42')->negate());
        $this->assertSame('42', (string) Math::number('-42')->negate());
        $this->assertSame('0', (string) Math::number('0')->negate());
        $this->assertSame('0', (string) Math::number('-0')->negate());
        $this->assertSame('0', (string) Math::number('+0')->negate());
        $this->assertSame('0', (string) Math::number('0.000000000')->negate());
        $this->assertSame('-0.5', (string) Math::number('0.5')->negate());
        $this->assertSame('0.5', (string) Math::number('-0.5')->negate());
        // beyond precision — should still negate
        $this->assertSame('-0.0000000001', (string) Math::number('0.0000000001')->negate());
    }

    public function test_negate_is_immutable()
    {
        $a = Math::number('42');
        $b = $a->negate();

        $this->assertSame('42', (string) $a);
        $this->assertSame('-42', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_clamp()
    {
        $this->assertSame('5', (string) Math::number('5')->clamp('0', '10'));
        $this->assertSame('0', (string) Math::number('-5')->clamp('0', '10'));
        $this->assertSame('10', (string) Math::number('15')->clamp('0', '10'));
        $this->assertSame('0', (string) Math::number('0')->clamp('0', '10'));
        $this->assertSame('10', (string) Math::number('10')->clamp('0', '10'));
        $this->assertSame('-5', (string) Math::number('-5')->clamp('-10', '-1'));
    }

    public function test_clamp_is_immutable()
    {
        $a = Math::number('15');
        $b = $a->clamp('0', '10');

        $this->assertSame('15', (string) $a);
        $this->assertSame('10', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_quotient_and_remainder()
    {
        [$q, $r] = Math::number('17')->quotientAndRemainder('5');
        $this->assertSame('3', (string) $q);
        $this->assertSame('2', (string) $r);

        [$q, $r] = Math::number('100')->quotientAndRemainder('10');
        $this->assertSame('10', (string) $q);
        $this->assertSame('0', (string) $r);

        [$q, $r] = Math::number('-17')->quotientAndRemainder('5');
        $this->assertSame('-3', (string) $q);
        $this->assertSame('-2', (string) $r);
    }

    public function test_quotient_and_remainder_is_immutable()
    {
        $a       = Math::number('17');
        [$q, $r] = $a->quotientAndRemainder('5');

        $this->assertSame('17', (string) $a);
        $this->assertNotSame($a, $q);
        $this->assertNotSame($a, $r);
    }
}
