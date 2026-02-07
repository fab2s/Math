<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests;

use fab2s\Math\Math;
use fab2s\Math\MathImmutable;
use PHPUnit\Framework\TestCase;

class MathImmutableTest extends TestCase
{
    public function test_instanceof_math()
    {
        $this->assertInstanceOf(Math::class, MathImmutable::number('42'));
    }

    public function test_add_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->add('5');

        $this->assertSame('10', (string) $a);
        $this->assertSame('15', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_sub_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->sub('3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('7', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_mul_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->mul('3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('30', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_div_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->div('2');

        $this->assertSame('10', (string) $a);
        $this->assertSame('5', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_sqrt_is_immutable()
    {
        $a = MathImmutable::number('9');
        $b = $a->sqrt();

        $this->assertSame('9', (string) $a);
        $this->assertSame('3', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_pow_is_immutable()
    {
        $a = MathImmutable::number('2');
        $b = $a->pow(3);

        $this->assertSame('2', (string) $a);
        $this->assertSame('8', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_mod_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->mod(3);

        $this->assertSame('10', (string) $a);
        $this->assertSame('1', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_round_is_immutable()
    {
        $a = MathImmutable::number('10.555');
        $b = $a->round(2);

        $this->assertSame('10.555', (string) $a);
        $this->assertSame('10.56', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_ceil_is_immutable()
    {
        $a = MathImmutable::number('10.1');
        $b = $a->ceil();

        $this->assertSame('10.1', (string) $a);
        $this->assertSame('11', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_floor_is_immutable()
    {
        $a = MathImmutable::number('10.9');
        $b = $a->floor();

        $this->assertSame('10.9', (string) $a);
        $this->assertSame('10', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_abs_is_immutable()
    {
        $a = MathImmutable::number('-10');
        $b = $a->abs();

        $this->assertSame('-10', (string) $a);
        $this->assertSame('10', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_max_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->max('20');

        $this->assertSame('10', (string) $a);
        $this->assertSame('20', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_min_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->min('5');

        $this->assertSame('10', (string) $a);
        $this->assertSame('5', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_normalize_is_immutable()
    {
        $a = MathImmutable::number('010.100');
        $b = $a->normalize();

        $this->assertSame('010.100', $a->getNumber());
        $this->assertSame('10.1', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_set_precision_is_immutable()
    {
        $a = MathImmutable::number('10');
        $b = $a->setPrecision(2);

        $this->assertNotSame($a, $b);
        $this->assertSame('3.33', (string) $b->div(3));
        // original precision unchanged
        $this->assertSame('3.333333333', (string) $a->div(3));
    }

    public function test_chaining()
    {
        $a = MathImmutable::number('10');
        $b = $a->add('5')->mul('2');

        $this->assertSame('10', (string) $a);
        $this->assertSame('30', (string) $b);
    }

    public function test_factory_methods()
    {
        $a = MathImmutable::number('42');
        $this->assertInstanceOf(MathImmutable::class, $a);

        $b = MathImmutable::make('42');
        $this->assertInstanceOf(MathImmutable::class, $b);
    }

    public function test_operations_return_immutable_math()
    {
        $a = MathImmutable::number('10');

        $this->assertInstanceOf(MathImmutable::class, $a->add('1'));
        $this->assertInstanceOf(MathImmutable::class, $a->sub('1'));
        $this->assertInstanceOf(MathImmutable::class, $a->mul('2'));
        $this->assertInstanceOf(MathImmutable::class, $a->div('2'));
        $this->assertInstanceOf(MathImmutable::class, $a->pow(2));
        $this->assertInstanceOf(MathImmutable::class, $a->mod(3));
        $this->assertInstanceOf(MathImmutable::class, $a->abs());
        $this->assertInstanceOf(MathImmutable::class, $a->round(2));
        $this->assertInstanceOf(MathImmutable::class, $a->ceil());
        $this->assertInstanceOf(MathImmutable::class, $a->floor());
        $this->assertInstanceOf(MathImmutable::class, $a->normalize());
        $this->assertInstanceOf(MathImmutable::class, $a->setPrecision(4));
        $this->assertInstanceOf(MathImmutable::class, $a->max('20'));
        $this->assertInstanceOf(MathImmutable::class, $a->min('5'));
    }

    public function test_cross_type_operations()
    {
        $immutable = MathImmutable::number('10');
        $mutable   = Math::number('5');

        // MathImmutable accepting Math argument
        $result = $immutable->add($mutable);
        $this->assertSame('15', (string) $result);
        $this->assertSame('10', (string) $immutable);
        $this->assertInstanceOf(MathImmutable::class, $result);

        // Math accepting MathImmutable argument
        $result2 = $mutable->add($immutable);
        $this->assertSame('15', (string) $result2);
        $this->assertInstanceOf(Math::class, $result2);
    }

    public function test_variadic_immutability()
    {
        $a = MathImmutable::number('10');
        $b = $a->add('1', '2', '3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('16', (string) $b);
    }
}
