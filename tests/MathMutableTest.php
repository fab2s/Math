<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests;

use fab2s\Math\Math;
use fab2s\Math\MathMutable;
use PHPUnit\Framework\TestCase;

class MathMutableTest extends TestCase
{
    public function test_add_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->add('5');

        $this->assertSame('10', (string) $a);
        $this->assertSame('15', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_sub_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->sub('3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('7', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_mul_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->mul('3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('30', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_div_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->div('2');

        $this->assertSame('10', (string) $a);
        $this->assertSame('5', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_sqrt_is_immutable()
    {
        $a = Math::number('9');
        $b = $a->sqrt();

        $this->assertSame('9', (string) $a);
        $this->assertSame('3', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_pow_is_immutable()
    {
        $a = Math::number('2');
        $b = $a->pow(3);

        $this->assertSame('2', (string) $a);
        $this->assertSame('8', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_mod_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->mod(3);

        $this->assertSame('10', (string) $a);
        $this->assertSame('1', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_round_is_immutable()
    {
        $a = Math::number('10.555');
        $b = $a->round(2);

        $this->assertSame('10.555', (string) $a);
        $this->assertSame('10.56', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_ceil_is_immutable()
    {
        $a = Math::number('10.1');
        $b = $a->ceil();

        $this->assertSame('10.1', (string) $a);
        $this->assertSame('11', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_floor_is_immutable()
    {
        $a = Math::number('10.9');
        $b = $a->floor();

        $this->assertSame('10.9', (string) $a);
        $this->assertSame('10', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_abs_is_immutable()
    {
        $a = Math::number('-10');
        $b = $a->abs();

        $this->assertSame('-10', (string) $a);
        $this->assertSame('10', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_max_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->max('20');

        $this->assertSame('10', (string) $a);
        $this->assertSame('20', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_min_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->min('5');

        $this->assertSame('10', (string) $a);
        $this->assertSame('5', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_normalize_is_immutable()
    {
        $a = Math::number('010.100');
        $b = $a->normalize();

        $this->assertSame('010.100', $a->getNumber());
        $this->assertSame('10.1', (string) $b);
        $this->assertNotSame($a, $b);
    }

    public function test_set_precision_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->setPrecision(2);

        $this->assertNotSame($a, $b);
        $this->assertSame('3.33', (string) $b->div(3));
        // original precision unchanged
        $this->assertSame('3.333333333', (string) $a->div(3));
    }

    public function test_chaining_is_immutable()
    {
        $a = Math::number('10');
        $b = $a->add('5')->mul('2');

        $this->assertSame('10', (string) $a);
        $this->assertSame('30', (string) $b);
    }

    public function test_variadic_immutability()
    {
        $a = Math::number('10');
        $b = $a->add('1', '2', '3');

        $this->assertSame('10', (string) $a);
        $this->assertSame('16', (string) $b);
    }

    public function test_operations_return_math()
    {
        $a = Math::number('10');

        $this->assertInstanceOf(Math::class, $a->add('1'));
        $this->assertInstanceOf(Math::class, $a->sub('1'));
        $this->assertInstanceOf(Math::class, $a->mul('2'));
        $this->assertInstanceOf(Math::class, $a->div('2'));
        $this->assertInstanceOf(Math::class, $a->pow(2));
        $this->assertInstanceOf(Math::class, $a->mod(3));
        $this->assertInstanceOf(Math::class, $a->abs());
        $this->assertInstanceOf(Math::class, $a->round(2));
        $this->assertInstanceOf(Math::class, $a->ceil());
        $this->assertInstanceOf(Math::class, $a->floor());
        $this->assertInstanceOf(Math::class, $a->normalize());
        $this->assertInstanceOf(Math::class, $a->setPrecision(4));
        $this->assertInstanceOf(Math::class, $a->max('20'));
        $this->assertInstanceOf(Math::class, $a->min('5'));
    }

    // ─── MathMutable is mutable ───────────────────────────────────

    public function test_mutable_instanceof_math()
    {
        $this->assertInstanceOf(Math::class, MathMutable::number('42'));
    }

    public function test_mutable_add_is_mutable()
    {
        $a = MathMutable::number('10');
        $b = $a->add('5');

        $this->assertSame('15', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_sub_is_mutable()
    {
        $a = MathMutable::number('10');
        $b = $a->sub('3');

        $this->assertSame('7', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_mul_is_mutable()
    {
        $a = MathMutable::number('10');
        $b = $a->mul('3');

        $this->assertSame('30', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_div_is_mutable()
    {
        $a = MathMutable::number('10');
        $b = $a->div('2');

        $this->assertSame('5', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_normalize_is_mutable()
    {
        $a = MathMutable::number('010.100');
        $b = $a->normalize();

        $this->assertSame($a, $b);
    }

    public function test_mutable_set_precision_is_mutable()
    {
        $a = MathMutable::number('10');
        $b = $a->setPrecision(2);

        $this->assertSame($a, $b);
    }

    public function test_mutable_factory_methods()
    {
        $a = MathMutable::number('42');
        $this->assertInstanceOf(MathMutable::class, $a);

        $b = MathMutable::make('42');
        $this->assertInstanceOf(MathMutable::class, $b);
    }

    public function test_mutable_operations_return_mutable()
    {
        $a = MathMutable::number('10');

        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->add('1'));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->sub('1'));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->mul('2'));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->div('2'));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->pow(2));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->mod(3));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('-10')->abs());
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10.5')->round(0));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10.5')->ceil());
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10.5')->floor());
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->normalize());
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->setPrecision(4));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->max('20'));
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('10')->min('5'));
    }

    public function test_cross_type_operations()
    {
        $immutable = Math::number('10');
        $mutable   = MathMutable::number('5');

        // Math accepting MathMutable argument
        $result = $immutable->add($mutable);
        $this->assertSame('15', (string) $result);
        $this->assertSame('10', (string) $immutable);
        $this->assertInstanceOf(Math::class, $result);

        // MathMutable accepting Math argument
        $result2 = $mutable->add($immutable);
        $this->assertSame('15', (string) $result2);
        $this->assertInstanceOf(MathMutable::class, $result2);
    }

    // ─── MathMutable edge cases for new operations ───────────────

    public function test_mutable_negate_is_mutable()
    {
        $a = MathMutable::number('42');
        $b = $a->negate();

        $this->assertSame('-42', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_clamp_is_mutable()
    {
        $a = MathMutable::number('15');
        $b = $a->clamp('0', '10');

        $this->assertSame('10', (string) $a);
        $this->assertSame($a, $b);
    }

    public function test_mutable_quotient_and_remainder()
    {
        $a       = MathMutable::number('17');
        [$q, $r] = $a->quotientAndRemainder('5');

        // quotient is $this (mutated in place)
        $this->assertSame($a, $q);
        $this->assertSame('3', (string) $q);
        // remainder is a separate clone
        $this->assertNotSame($a, $r);
        $this->assertSame('2', (string) $r);
        $this->assertInstanceOf(MathMutable::class, $r);
    }

    public function test_mutable_negate_operations_return_mutable()
    {
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('42')->negate());
        $this->assertInstanceOf(MathMutable::class, MathMutable::number('15')->clamp('0', '10'));

        [$q, $r] = MathMutable::number('17')->quotientAndRemainder('5');
        $this->assertInstanceOf(MathMutable::class, $q);
        $this->assertInstanceOf(MathMutable::class, $r);
    }
}
