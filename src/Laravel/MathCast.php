<?php

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

class MathCast implements CastsAttributes
{
    protected bool $isNullable = false;

    public function __construct(...$options)
    {
        $this->isNullable = in_array('nullable', $options);
    }

    /**
     * Cast the given value.
     *
     * @param Model $model
     *
     * @throws NotNullableException
     */
    public function get($model, string $key, $value, array $attributes): ?Math
    {
        return Math::isNumber($value) ? Math::number($value) : $this->handleNullable($model, $key);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     *
     * @throws NotNullableException
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        return Math::isNumber($value) ? (string) Math::number($value) : $this->handleNullable($model, $key);
    }

    /**
     * @throws NotNullableException
     */
    protected function handleNullable(Model $model, string $key)
    {
        return $this->isNullable ? null : throw NotNullableException::make($key, $model);
    }
}
