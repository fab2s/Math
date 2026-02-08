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
use Brick\Math\RoundingMode;
use fab2s\Math\Math;
use fab2s\Math\MathMutable;
use PhpBench\Attributes as Bench;

/**
 * Compares Math (immutable) with MathMutable and brick/math BigDecimal.
 */
#[Bench\Warmup(3)]
#[Bench\Iterations(5)]
#[Bench\Revs(1000)]
class MutableBench
{
    // ─── Mutable vs Immutable (fab2s internal comparison) ────────

    #[Bench\Subject]
    #[Bench\Groups(['mutability'])]
    public function fab2s_mutable_chain(): void
    {
        MathMutable::number('1000.00')
            ->mul('1.21')
            ->add('50.00')
            ->sub('100.00')
            ->div('3')
            ->round(2)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['mutability'])]
    public function fab2s_immutable_chain(): void
    {
        Math::number('1000.00')
            ->mul('1.21')
            ->add('50.00')
            ->sub('100.00')
            ->div('3')
            ->round(2)
        ;
    }

    #[Bench\Subject]
    #[Bench\Groups(['mutability'])]
    public function brick_immutable_chain(): void
    {
        BigDecimal::of('1000.00')
            ->multipliedBy('1.21')
            ->plus('50.00')
            ->minus('100.00')
            ->dividedBy('3', 9, RoundingMode::HALF_UP)
            ->toScale(2, RoundingMode::HALF_UP)
        ;
    }

    // ─── Accumulation ───────────────────────────────────────────

    #[Bench\Subject]
    #[Bench\Groups(['accumulation'])]
    #[Bench\Revs(100)]
    public function fab2s_mutable_accumulate_100(): void
    {
        $sum = MathMutable::number('0');
        for ($i = 0; $i < 100; $i++) {
            $sum->add($i . '.99');
        }
    }

    #[Bench\Subject]
    #[Bench\Groups(['accumulation'])]
    #[Bench\Revs(100)]
    public function fab2s_immutable_accumulate_100(): void
    {
        $sum = Math::number('0');
        for ($i = 0; $i < 100; $i++) {
            $sum = $sum->add($i . '.99');
        }
    }

    #[Bench\Subject]
    #[Bench\Groups(['accumulation'])]
    #[Bench\Revs(100)]
    public function brick_accumulate_100(): void
    {
        $sum = BigDecimal::of('0');
        for ($i = 0; $i < 100; $i++) {
            $sum = $sum->plus($i . '.99');
        }
    }

    // ─── Repeated operations on same base value ──────────────────

    #[Bench\Subject]
    #[Bench\Groups(['branch'])]
    public function fab2s_mutable_branch(): void
    {
        $price     = MathMutable::number('99.99');
        $withTax10 = MathMutable::number($price)->mul('1.10');
        $withTax20 = MathMutable::number($price)->mul('1.20');
        $withTax10->add('5.00')->round(2);
        $withTax20->add('5.00')->round(2);
    }

    #[Bench\Subject]
    #[Bench\Groups(['branch'])]
    public function fab2s_immutable_branch(): void
    {
        $price     = Math::number('99.99');
        $withTax10 = $price->mul('1.10');
        $withTax20 = $price->mul('1.20');
        $withTax10->add('5.00')->round(2);
        $withTax20->add('5.00')->round(2);
    }

    #[Bench\Subject]
    #[Bench\Groups(['branch'])]
    public function brick_branch(): void
    {
        $price     = BigDecimal::of('99.99');
        $withTax10 = $price->multipliedBy('1.10');
        $withTax20 = $price->multipliedBy('1.20');
        $withTax10->plus('5.00')->toScale(2, RoundingMode::HALF_UP);
        $withTax20->plus('5.00')->toScale(2, RoundingMode::HALF_UP);
    }
}
