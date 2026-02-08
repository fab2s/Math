<?php

declare(strict_types=1);

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Benchmarks;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use fab2s\Math\Math;
use PhpBench\Attributes as Bench;

#[Bench\Warmup(3)]
#[Bench\Iterations(5)]
#[Bench\Revs(1000)]
class MathBench
{
    // ─── Instantiation ───────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['instantiation'])]
    public function fab2s_instantiate_int(): void
    {
        Math::number(123456789);
    }

    #[Bench\Subject]
    #[Bench\Groups(['instantiation'])]
    public function brick_instantiate_int(): void
    {
        BigDecimal::of(123456789);
    }

    #[Bench\Subject]
    #[Bench\Groups(['instantiation'])]
    public function fab2s_instantiate_string(): void
    {
        Math::number('123456789.123456789');
    }

    #[Bench\Subject]
    #[Bench\Groups(['instantiation'])]
    public function brick_instantiate_string(): void
    {
        BigDecimal::of('123456789.123456789');
    }

    // ─── Addition ────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['addition'])]
    public function fab2s_add(): void
    {
        Math::number('123456789.123456789')
            ->add('987654321.987654321')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['addition'])]
    public function brick_add(): void
    {
        BigDecimal::of('123456789.123456789')
            ->plus(BigDecimal::of('987654321.987654321'))
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['addition'])]
    public function fab2s_add_variadic(): void
    {
        Math::number('100')
            ->add('200', '300', '400', '500')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['addition'])]
    public function brick_add_variadic(): void
    {
        BigDecimal::of('100')
            ->plus('200')
            ->plus('300')
            ->plus('400')
            ->plus('500')
        ;
    }

    // ─── Subtraction ─────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['subtraction'])]
    public function fab2s_sub(): void
    {
        Math::number('987654321.987654321')
            ->sub('123456789.123456789')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['subtraction'])]
    public function brick_sub(): void
    {
        BigDecimal::of('987654321.987654321')
            ->minus(BigDecimal::of('123456789.123456789'))
        ;
    }

    // ─── Multiplication ──────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['multiplication'])]
    public function fab2s_mul(): void
    {
        Math::number('123456789.123456789')
            ->mul('9.87654321')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['multiplication'])]
    public function brick_mul(): void
    {
        BigDecimal::of('123456789.123456789')
            ->multipliedBy(BigDecimal::of('9.87654321'))
        ;
    }

    // ─── Division ────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['division'])]
    public function fab2s_div(): void
    {
        Math::number('987654321.987654321')
            ->div('123.456789')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['division'])]
    public function brick_div(): void
    {
        BigDecimal::of('987654321.987654321')
            ->dividedBy(BigDecimal::of('123.456789'), 9, RoundingMode::HALF_UP)
        ;
    }

    // ─── Power ───────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['power'])]
    public function fab2s_pow(): void
    {
        Math::number('12345.6789')
            ->pow(10)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['power'])]
    public function brick_pow(): void
    {
        BigDecimal::of('12345.6789')
            ->power(10)
        ;
    }

    // ─── Modulo ──────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['modulo'])]
    public function fab2s_mod(): void
    {
        Math::number('987654321')
            ->mod('12345')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['modulo'])]
    public function brick_mod(): void
    {
        BigDecimal::of('987654321')
            ->remainder(BigDecimal::of('12345'))
        ;
    }

    // ─── Square root ─────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['sqrt'])]
    public function fab2s_sqrt(): void
    {
        Math::number('987654321.123456789')
            ->sqrt()
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['sqrt'])]
    public function brick_sqrt(): void
    {
        BigDecimal::of('987654321123456789')
            ->toBigInteger()
            ->sqrt()
        ;
    }

    // ─── Abs ─────────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['abs'])]
    public function fab2s_abs(): void
    {
        Math::number('-987654321.123456789')
            ->abs()
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['abs'])]
    public function brick_abs(): void
    {
        BigDecimal::of('-987654321.123456789')
            ->abs()
        ;
    }

    // ─── Rounding ────────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function fab2s_round(): void
    {
        Math::number('123456.789012345')
            ->round(4)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function brick_round(): void
    {
        BigDecimal::of('123456.789012345')
            ->toScale(4, RoundingMode::HALF_UP)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function fab2s_ceil(): void
    {
        Math::number('123456.789012345')
            ->ceil()
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function brick_ceil(): void
    {
        BigDecimal::of('123456.789012345')
            ->toScale(0, RoundingMode::CEILING)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function fab2s_floor(): void
    {
        Math::number('123456.789012345')
            ->floor()
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['rounding'])]
    public function brick_floor(): void
    {
        BigDecimal::of('123456.789012345')
            ->toScale(0, RoundingMode::FLOOR)
        ;
    }

    // ─── Comparison ──────────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['comparison'])]
    public function fab2s_comparisons(): void
    {
        $a = Math::number('123456789.123456789');
        $a->gt('123456789.123456788');
        $a->gte('123456789.123456789');
        $a->lt('123456789.123456790');
        $a->lte('123456789.123456789');
        $a->eq('123456789.123456789');
    }

    #[Bench\Subject]
    #[Bench\Groups(['comparison'])]
    public function brick_comparisons(): void
    {
        $a  = BigDecimal::of('123456789.123456789');
        $b1 = BigDecimal::of('123456789.123456788');
        $b2 = BigDecimal::of('123456789.123456789');
        $b3 = BigDecimal::of('123456789.123456790');
        $a->isGreaterThan($b1);
        $a->isGreaterThanOrEqualTo($b2);
        $a->isLessThan($b3);
        $a->isLessThanOrEqualTo($b2);
        $a->isEqualTo($b2);
    }

    // ─── String conversion ───────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['conversion'])]
    public function fab2s_to_string(): void
    {
        (string) Math::number('123456789.123456789');
    }

    #[Bench\Subject]
    #[Bench\Groups(['conversion'])]
    public function brick_to_string(): void
    {
        (string) BigDecimal::of('123456789.123456789');
    }

    // ─── Chained operations (realistic workflow) ─────────────────

    #[Bench\Subject]
    #[Bench\Groups(['chained'])]
    public function fab2s_chained_workflow(): void
    {
        Math::number('1000.00')
            ->mul('1.21')            // apply tax
            ->add('50.00')           // add shipping
            ->sub('100.00')          // discount
            ->round(2)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['chained'])]
    public function brick_chained_workflow(): void
    {
        BigDecimal::of('1000.00')
            ->multipliedBy('1.21')
            ->plus('50.00')
            ->minus('100.00')
            ->toScale(2, RoundingMode::HALF_UP)
        ;
    }

    // ─── Large number arithmetic ─────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['large'])]
    public function fab2s_large_number_ops(): void
    {
        Math::number('999999999999999999999999999999.999999999')
            ->add('999999999999999999999999999999.999999999')
            ->mul('2.5')
            ->div('3')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['large'])]
    public function brick_large_number_ops(): void
    {
        BigDecimal::of('999999999999999999999999999999.999999999')
            ->plus('999999999999999999999999999999.999999999')
            ->multipliedBy('2.5')
            ->dividedBy('3', 9, RoundingMode::HALF_UP)
        ;
    }

    // ─── Many small operations (accumulation) ────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['accumulation'])]
    #[Bench\Revs(100)]
    public function fab2s_accumulate_100_additions(): void
    {
        $sum = Math::number('0');
        for ($i = 0; $i < 100; $i++) {
            $sum->add((string) $i . '.99');
        }
    }

    #[Bench\Subject]
    #[Bench\Groups(['accumulation'])]
    #[Bench\Revs(100)]
    public function brick_accumulate_100_additions(): void
    {
        $sum = BigDecimal::of('0');
        for ($i = 0; $i < 100; $i++) {
            $sum = $sum->plus($i . '.99');
        }
    }

    // ─── Base conversion (fab2s specialty) ────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['base_conversion'])]
    public function fab2s_base_convert_to_62(): void
    {
        Math::number('9999999999999999')
            ->toBase(62)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['base_conversion'])]
    public function brick_base_convert_to_62(): void
    {
        BigInteger::of('9999999999999999')
            ->toArbitraryBase('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['base_conversion'])]
    public function fab2s_base_convert_to_16(): void
    {
        Math::number('9999999999999999')
            ->toBase(16)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['base_conversion'])]
    public function brick_base_convert_to_16(): void
    {
        BigInteger::of('9999999999999999')
            ->toBase(16)
        ;
    }

    // ─── Integer operations (BigInteger comparison) ──────────────

    #[Bench\Subject]
    #[Bench\Groups(['integer'])]
    public function fab2s_integer_mul(): void
    {
        Math::number('123456789012345678901234567890')
            ->mul('987654321098765432109876543210')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['integer'])]
    public function brick_integer_mul(): void
    {
        BigInteger::of('123456789012345678901234567890')
            ->multipliedBy('987654321098765432109876543210')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['integer'])]
    public function fab2s_integer_powmod(): void
    {
        Math::number('123456789')
            ->powMod(100, '9999999999')
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['integer'])]
    public function brick_integer_powmod(): void
    {
        BigInteger::of('123456789')
            ->modPow('100', '9999999999')
        ;
    }

    // ─── Memory: object creation overhead ────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['memory'])]
    #[Bench\Revs(100)]
    public function fab2s_create_1000_instances(): void
    {
        $objects = [];
        for ($i = 0; $i < 1000; $i++) {
            $objects[] = Math::number((string) $i . '.123456789');
        }
    }

    #[Bench\Subject]
    #[Bench\Groups(['memory'])]
    #[Bench\Revs(100)]
    public function brick_create_1000_instances(): void
    {
        $objects = [];
        for ($i = 0; $i < 1000; $i++) {
            $objects[] = BigDecimal::of($i . '.123456789');
        }
    }
}
