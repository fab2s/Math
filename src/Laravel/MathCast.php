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
use fab2s\Math\Math;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Math|null, Math|string|int|float|null> */
class MathCast implements CastsAttributes
{
    protected bool $isNullable = false;

    public function __construct(string ...$options)
    {
        $this->isNullable = in_array('nullable', $options);
    }

    /**
     * Cast the given value.
     *
     * @param Model               $model
     * @param array<string,mixed> $attributes
     *
     * @throws NotNullableException
     */
    public function get($model, string $key, mixed $value, array $attributes): ?Math
    {
        /** @var string|int|float|null $value */
        return Math::isNumber($value) ? Math::number((string) $value) : $this->handleNullable($model, $key);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model               $model
     * @param array<string,mixed> $attributes
     *
     * @throws NotNullableException
     */
    public function set($model, string $key, mixed $value, array $attributes): ?string
    {
        /** @var string|int|float|null $value */
        return Math::isNumber($value) ? (string) Math::number((string) $value) : $this->handleNullable($model, $key);
    }

    /**
     * @return null
     *
     * @throws NotNullableException
     */
    protected function handleNullable(Model $model, string $key): mixed
    {
        return $this->isNullable ? null : throw NotNullableException::make($key, $model);
    }
}
