<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\Tests\Laravel;

use fab2s\Math\Laravel\Exception\NotNullableException;
use fab2s\Math\Laravel\MathCast;
use fab2s\Math\Laravel\MathMutableCast;
use fab2s\Math\Math;
use fab2s\Math\MathMutable;
use fab2s\Math\Tests\Laravel\Artifacts\CastModel;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MathCastTest extends TestCase
{
    protected function setUp(): void
    {
        // Turn on error reporting
        error_reporting(E_ALL);
        parent::setUp();
    }

    /**
     * @throws NotNullableException
     */
    #[DataProvider('castProvider')]
    public function test_math_cast_get(
        Math|string|int|float|null $value,
        Math|string|null $expected,
        array $options = [],
        string $castClass = MathCast::class,
    ): void {
        $cast = new $castClass(...$options);

        switch (true) {
            case is_object($expected):
                $result = $cast->get(new CastModel, 'key', $value, []);
                $this->assertTrue($expected->eq($result));
                $this->assertSame(get_class($expected), get_class($result));
                break;
            case is_string($expected):
                $this->expectException(NotNullableException::class);
                $cast->get(new CastModel, 'key', $value, []);
                break;
            default:
                $this->assertNull($cast->get(new CastModel, 'key', $value, []));
                break;
        }
    }

    /**
     * @throws NotNullableException
     */
    #[DataProvider('castProvider')]
    public function test_math_cast_set(
        Math|string|int|float|null $value,
        Math|string|null $expected,
        array $options = [],
        string $castClass = MathCast::class,
    ): void {
        $cast = new $castClass(...$options);

        switch (true) {
            case is_object($expected):
                $this->assertSame((string) $expected, $cast->set(new CastModel, 'key', $value, []));
                break;
            case is_string($expected):
                $this->expectException(NotNullableException::class);
                $cast->set(new CastModel, 'key', $value, []);
                break;
            default:
                $this->assertSame(null, $cast->set(new CastModel, 'key', $value, []));
                break;
        }
    }

    public static function castProvider(): array
    {
        return [
            [
                'value'    => null,
                'expected' => null,
                'options'  => ['nullable'],
            ],
            [
                'value'    => Math::number(42.42),
                'expected' => Math::number(42.42),
                'options'  => ['nullable'],
            ],
            [
                'value'    => Math::number(42.42),
                'expected' => Math::number(42.42),
            ],
            [
                'value'    => null,
                'expected' => NotNullableException::class,
            ],
            [
                'value'    => '42.4200000',
                'expected' => Math::number(42.42),
                'options'  => ['nullable'],
            ],
            [
                'value'    => 42.42,
                'expected' => Math::number(42.42),
                'options'  => ['nullable'],
            ],
            [
                'value'    => 42,
                'expected' => Math::number(42),
                'options'  => ['nullable'],
            ],
            // MathMutableCast cases
            [
                'value'     => null,
                'expected'  => null,
                'options'   => ['nullable'],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => MathMutable::number(42.42),
                'expected'  => MathMutable::number(42.42),
                'options'   => ['nullable'],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => MathMutable::number(42.42),
                'expected'  => MathMutable::number(42.42),
                'options'   => [],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => null,
                'expected'  => NotNullableException::class,
                'options'   => [],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => '42.4200000',
                'expected'  => MathMutable::number(42.42),
                'options'   => ['nullable'],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => 42.42,
                'expected'  => MathMutable::number(42.42),
                'options'   => ['nullable'],
                'castClass' => MathMutableCast::class,
            ],
            [
                'value'     => 42,
                'expected'  => MathMutable::number(42),
                'options'   => ['nullable'],
                'castClass' => MathMutableCast::class,
            ],
        ];
    }
}
