<?php

declare(strict_types=1);

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Laravel\Exception;

use fab2s\ContextException\ContextException;
use Illuminate\Database\Eloquent\Model;

class NotNullableException extends ContextException
{
    public static function make(string $field, Model $model): self
    {
        $modelClass = get_class($model);

        return (new self("Field {$field} is not nullable in model {$modelClass}"))
            ->setContext([
                'model' => $modelClass,
                'data'  => $model->toArray(),
            ])
        ;
    }
}
