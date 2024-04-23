<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests\Laravel\Artifacts;

use fab2s\Math\Laravel\MathCast;
use Illuminate\Database\Eloquent\Model;

class CastModel extends Model
{
    protected $table   = 'table';
    protected $guarded = [];
    protected $casts   = [
        'not_nullable' => MathCast::class,
        'nullable'     => MathCast::class . ':nullable',
    ];
}
