<?php

declare(strict_types=1);

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Laravel;

use fab2s\Math\Laravel\Exception\NotNullableException;
use fab2s\Math\MathMutable;

/** @extends MathCast<MathMutable> */
class MathMutableCast extends MathCast
{
    /** @var class-string<MathMutable> */
    protected string $mathFqn = MathMutable::class;

    /**
     * @param array<string,mixed> $attributes
     *
     * @throws NotNullableException
     */
    public function get($model, string $key, mixed $value, array $attributes): ?MathMutable
    {
        /** @var MathMutable|null */
        return parent::get($model, $key, $value, $attributes);
    }
}
