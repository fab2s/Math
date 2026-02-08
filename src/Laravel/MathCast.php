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

/**
 * @template TMath of Math
 *
 * @implements CastsAttributes<TMath, TMath|string|int|float>
 */
class MathCast implements CastsAttributes
{
    protected bool $isNullable = false;

    /** @var class-string<Math> */
    protected string $mathFqn = Math::class;

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
     * @return TMath|null
     *
     * @throws NotNullableException
     */
    public function get($model, string $key, mixed $value, array $attributes): ?Math
    {
        /** @var string|int|float|null $value */
        return $this->mathFqn::isNumber($value) ? $this->mathFqn::number((string) $value) : $this->handleNullable($model, $key); // @phpstan-ignore return.type
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model                       $model
     * @param TMath|string|int|float|null $value
     * @param array<string,mixed>         $attributes
     *
     * @throws NotNullableException
     */
    public function set($model, string $key, mixed $value, array $attributes): ?string
    {
        /** @var string|int|float|null $value */
        return $this->mathFqn::isNumber($value) ? (string) $this->mathFqn::number((string) $value) : $this->handleNullable($model, $key);
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
