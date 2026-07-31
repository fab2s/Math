# Math

[![CI](https://github.com/fab2s/Math/actions/workflows/ci.yml/badge.svg)](https://github.com/fab2s/Math/actions/workflows/ci.yml)
[![QA](https://github.com/fab2s/Math/actions/workflows/qa.yml/badge.svg)](https://github.com/fab2s/Math/actions/workflows/qa.yml)
[![codecov](https://codecov.io/gh/fab2s/Math/graph/badge.svg?token=6JD33CQLE3)](https://codecov.io/gh/fab2s/Math)
[![Latest Stable Version](https://poser.pugx.org/fab2s/math/v/stable)](https://packagist.org/packages/fab2s/math)
[![Total Downloads](https://poser.pugx.org/fab2s/math/downloads)](https://packagist.org/packages/fab2s/math)
[![Monthly Downloads](https://poser.pugx.org/fab2s/math/d/monthly)](https://packagist.org/packages/fab2s/math)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com)
[![License](https://poser.pugx.org/fab2s/math/license)](https://packagist.org/packages/fab2s/math)

A fluent, high-precision arithmetic library for PHP built on [bcmath](https://php.net/bcmath). Designed for financial calculations, scientific computing, and anywhere floating-point errors are unacceptable.

## The Problem

Floating-point arithmetic has well-known precision limitations:

```php
var_dump((0.1 + 0.7) == 0.8);   // false
var_dump((1.4 - 1) * 100);      // 39.99999999999999
var_dump(0.7 + 0.1 - 0.8);      // -1.1102230246251565E-16
```

> `bcmath` supports numbers of any size and precision up to 2,147,483,647 decimals, represented as strings.

## Installation

```bash
composer require fab2s/math
```

### Requirements

- PHP 8.2+
- ext-bcmath (required)
- ext-gmp (optional, faster base conversions, mod, pow and powMod)

## Features

### Fluent API

Chain operations naturally with variadic argument support:

```php
use fab2s\Math\Math;

$result = Math::number('100')
    ->add('10', '20', '30')   // 160
    ->mul('2')                 // 320
    ->div('4')                 // 80
    ->sub('38');               // 42

echo $result; // '42'
```

`Math` is **immutable** — every operation returns a new instance, leaving the original unchanged:

```php
$a = Math::number('100');
$b = $a->add('50');   // $a is still '100', $b is '150'
$c = $b->mul('2');    // $b is still '150', $c is '300'
```

The overhead is a single `clone` per operation (two properties: a string and an int).

### Mutable Variant

For performance-sensitive hot loops, `MathMutable` modifies the instance in place:

```php
use fab2s\Math\MathMutable;

$sum = MathMutable::number('0');
for ($i = 0; $i < 1000; $i++) {
    $sum->add($i . '.99'); // modifies $sum in place, no clone
}
```

`MathMutable` extends `Math`, so it is accepted anywhere `Math` is type-hinted.

### Strict Validation

Math rejects ambiguous inputs that bcmath would silently convert to `0`:

```php
// Valid
Math::number('42');
Math::number('-0.005');
Math::number('.5');

// Throws exception
Math::number('1E12');   // Exponential notation
Math::number('3,14');   // Comma separator
Math::number('$100');   // Currency symbols
```

### Full Arithmetic Operations

```php
$n = Math::number('100');

// Basic
$n->add(...$nums);   // Addition
$n->sub(...$nums);   // Subtraction
$n->mul(...$nums);   // Multiplication
$n->div(...$nums);   // Division

// Advanced
$n->sqrt();          // Square root
$n->pow('2');        // Power
$n->mod('7');        // Modulo
$n->powMod($e, $m);  // Modular exponentiation
$n->abs();           // Absolute value
$n->negate();        // Flip sign

// Division
$n->quotientAndRemainder('7');       // [$quotient, $remainder]

// Rounding
$n->round(2);        // Round to 2 decimals
$n->floor();         // Round down
$n->ceil();          // Round up

// Limits
$n->min('50', '200');     // 50
$n->max('50', '200');     // 200
$n->clamp('10', '90');    // Clip between bounds
```

### Comparisons & Inspection

```php
$n = Math::number('42');

$n->eq('42');    // true  — equal
$n->gt('40');    // true  — greater than
$n->gte('42');   // true  — greater than or equal
$n->lt('50');    // true  — less than
$n->lte('42');   // true  — less than or equal

$n->isZero();      // false
$n->isPositive();  // true
$n->isNegative();  // false
$n->isEven();      // true
$n->isOdd();       // false

$n = Math::number('42.99');
$n->getScale();          // 2
$n->getIntegralPart();   // '42'
$n->getFractionalPart(); // '99'
```

### Base Conversion (2-62)

Uses GMP when available for faster conversions:

```php
// From base X to base 10
Math::fromBase('LZ', 62);      // '1337'
Math::fromBase('101010', 2);   // '42'
Math::fromBase('ff', 16);      // '255' (case-insensitive for bases <= 36)

// From base 10 to base X
Math::number('1337')->toBase(62);  // 'LZ'
Math::number('42')->toBase(2);     // '101010'
Math::number('255')->toBase(16);   // 'ff'

// Negative numbers preserve their sign
Math::number('-42')->toBase(16);     // '-2a'
Math::fromBase('-LZ', 62);          // '-1337'
```

### Formatting

Formatting does not mutate the internal number:

```php
$n = Math::number('1234567.891');

echo $n->format(2);             // '1234567.89'
echo $n->format(2, ',', ' ');   // '1 234 567,89'
echo $n;                        // '1234567.891' (unchanged)
```

### Precision Control

Default precision is 9 decimal places. Control it globally or per-instance:

```php
// Global (affects new instances)
Math::setGlobalPrecision(18);

// Per-instance
$n = Math::number('100')->setPrecision(4);
echo $n->div('3'); // '33.3333'
```

> Precision is not handled via `bcscale()` to avoid global state issues in long-running processes.

### Normalized Output

Results are automatically normalized for accurate comparisons:

```php
echo Math::number('0000042.000'); // '42'
echo Math::number('-0');          // '0'
echo Math::number('+.500');       // '0.5'

// Raw access when needed
Math::number('0042.00')->getNumber(); // '0042.00'
```

### Instance Reuse

Pass Math instances directly to avoid re-validation:

```php
$tax = Math::number('0.20');
$price = Math::number('99.99');

$total = $price->add($price->mul($tax));
```

## Laravel Integration

Cast Eloquent model attributes to Math instances:

```php
use fab2s\Math\Laravel\MathCast;

class Order extends Model
{
    protected $casts = [
        'total'    => MathCast::class,
        'discount' => MathCast::class . ':nullable',
    ];
}

$order = new Order;
$order->total = '99.99';
$order->total->mul('1.2')->format(2); // '119.99'

$order->discount = null;  // OK (nullable)
$order->total = null;     // Throws NotNullableException
```

### Mutable Cast

Use `MathMutableCast` to get `MathMutable` instances instead of immutable `Math`:

```php
use fab2s\Math\Laravel\MathCast;
use fab2s\Math\Laravel\MathMutableCast;

class Order extends Model
{
    protected $casts = [
        'total'    => MathMutableCast::class,
        'discount' => MathMutableCast::class . ':nullable',
        'tax'      => MathCast::class,              // immutable (default)
    ];
}

$order = new Order;
$order->total = '99.99';
$order->total->add('10'); // modifies in place
```

Using separate cast classes enables proper static type resolution — Larastan/PHPStan will resolve `MathCast` properties to `Math` and `MathMutableCast` properties to `MathMutable`.

### Upgrading from v2

In v2, `Math` was mutable, so `MathCast` attributes behaved as mutable values. In v3, `Math` is immutable by default — existing code that mutates cast attributes in place will silently lose changes:

```php
// v2: works — Math was mutable
// v3: $order->total is unchanged — Math is now immutable
$order->total->add('10');
```

To restore the previous behavior, switch to `MathMutableCast`:

```php
use fab2s\Math\Laravel\MathMutableCast;

protected $casts = [
    'total'    => MathMutableCast::class,
    'discount' => MathMutableCast::class . ':nullable',
];
```

## API Reference

### Factory Methods

| Method | Description |
|--------|-------------|
| `Math::number($n)` | Create immutable instance |
| `Math::make($n)` | Alias for `number()` |
| `Math::fromBase($n, $base)` | Create from base 2-62 |
| `MathMutable::number($n)` | Create mutable instance |
| `MathMutable::make($n)` | Alias for `number()` |
| `MathMutable::fromBase($n, $base)` | Create mutable from base 2-62 |

### Arithmetic

| Method | Description |
|--------|-------------|
| `add(...$n)` | Addition |
| `sub(...$n)` | Subtraction |
| `mul(...$n)` | Multiplication |
| `div(...$n)` | Division |
| `quotientAndRemainder($n)` | Returns `[$quotient, $remainder]` |
| `mod($n)` | Modulo |
| `pow($n)` | Power |
| `powMod($exp, $mod)` | Modular exponentiation |
| `sqrt()` | Square root |
| `abs()` | Absolute value |
| `negate()` | Flip sign |
| `clamp($min, $max)` | Clip between bounds |

### Rounding

| Method | Description |
|--------|-------------|
| `round($precision)` | Round to precision |
| `floor()` | Round down |
| `ceil()` | Round up |

### Comparison & Inspection

| Method | Description |
|--------|-------------|
| `eq($n)` | Equal |
| `gt($n)` | Greater than |
| `gte($n)` | Greater than or equal |
| `lt($n)` | Less than |
| `lte($n)` | Less than or equal |
| `min(...$n)` | Minimum value |
| `max(...$n)` | Maximum value |
| `isZero()` | Check if zero |
| `isPositive()` | Check if positive |
| `isNegative()` | Check if negative |
| `isEven()` | Check if even integer |
| `isOdd()` | Check if odd integer |

### Conversion & Output

| Method | Description |
|--------|-------------|
| `toBase($base)` | Convert to base 2-62 |
| `format($dec, $point, $sep)` | Format with separators |
| `getNumber()` | Get raw (non-normalized) number |
| `getScale()` | Number of decimal places |
| `getIntegralPart()` | Part before the decimal point |
| `getFractionalPart()` | Part after the decimal point |
| `(string)` | Get normalized number |

### Precision

| Method | Description |
|--------|-------------|
| `setPrecision($p)` | Set instance precision |
| `getPrecision()` | Get instance precision |
| `Math::setGlobalPrecision($p)` | Set default for new instances |
| `Math::getGlobalPrecision()` | Get global precision |

## Benchmarks

Compared against [brick/math](https://github.com/brick/math) (PHP 8.5, opcache off, GMP enabled). The **bold** value is the faster one in each row, and _Factor_ shows how many times faster it is.

| Operation | fab2s/math | brick/math | Factor |
|---|---:|---:|---:|
| instantiate int | **0.117μs (±15.1%)** | 0.134μs (±8.7%) | 1.14x |
| instantiate string | **0.107μs (±2.4%)** | 0.351μs (±1.5%) | 3.29x |
| add | **0.343μs (±0.8%)** | 1.177μs (±1.7%) | 3.43x |
| add variadic | **0.674μs (±2.4%)** | 3.261μs (±1.3%) | 4.84x |
| sub | **0.345μs (±4.7%)** | 1.155μs (±0.5%) | 3.35x |
| mul | **0.382μs (±2.2%)** | 1.162μs (±1.6%) | 3.04x |
| div | **0.365μs (±6.5%)** | 2.055μs (±5.6%) | 5.63x |
| pow | **0.406μs (±24.3%)** | 0.728μs (±0.8%) | 1.79x |
| mod | **0.416μs (±0.8%)** | 1.270μs (±7.8%) | 3.06x |
| sqrt | **1.123μs (±0.9%)** | 1.380μs (±1.3%) | 1.23x |
| abs | **0.185μs (±0.6%)** | 0.517μs (±1.1%) | 2.80x |
| negate | **0.215μs (±1.8%)** | 0.466μs (±1.0%) | 2.17x |
| clamp | **0.478μs (±0.7%)** | 2.117μs (±5.6%) | 4.43x |
| quotient & remainder | **0.453μs (±0.9%)** | 1.306μs (±0.8%) | 2.89x |
| inspection | **1.065μs (±3.1%)** | 1.460μs (±2.5%) | 1.37x |
| round | **0.342μs (±3.3%)** | 1.483μs (±1.1%) | 4.33x |
| ceil | **0.320μs (±0.5%)** | 1.298μs (±3.1%) | 4.06x |
| floor | **0.289μs (±1.9%)** | 1.174μs (±1.2%) | 4.06x |
| comparisons | **0.644μs (±1.5%)** | 2.777μs (±1.2%) | 4.31x |
| to string | **0.326μs (±1.6%)** | 0.511μs (±0.5%) | 1.57x |
| chained workflow | **0.965μs (±1.2%)** | 3.683μs (±0.9%) | 3.82x |
| large number ops | **0.903μs (±1.7%)** | 3.433μs (±1.1%) | 3.80x |
| accumulate 100 additions | **21.421μs (±1.8%)** | 73.261μs (±1.2%) | 3.42x |
| base convert to 62 | **0.615μs (±1.3%)** | 2.580μs (±1.5%) | 4.19x |
| base convert to 16 | 0.607μs (±1.3%) | **0.458μs (±1.9%)** | 0.75x |
| integer mul | **0.424μs (±1.4%)** | 1.014μs (±11.0%) | 2.39x |
| integer powmod | **0.551μs (±0.7%)** | 1.410μs (±1.3%) | 2.56x |
| create 1000 instances | **127.530μs (±1.8%)** | 385.754μs (±1.1%) | 3.02x |

All operations above use immutable `Math` (the default). fab2s/math wins every operation except base-16 conversion, where brick/math delegates to GMP's native hex output. The speed advantage comes from keeping bcmath's C-level string arithmetic as the hot path for decimal operations, while brick/math pays for an extra object-wrapping layer on top of GMP. Integer-only operations (`mod`, `pow`, `powMod`, base conversion) use GMP directly when the extension is available, combining the best of both backends. Realistic workflows like chained calculations or 100-iteration accumulations show a consistent 3-5x advantage, with immutability costing only a lightweight `clone` per operation (two properties: a string and an int).

`MathMutable` eliminates the clone overhead entirely for hot loops:

| Operation | MathMutable | Math (immutable) | brick/math |
|---|---:|---:|---:|
| chained workflow | **1.018μs (±0.2%)** | 1.189μs (±3.0%) | 5.352μs (±1.2%) |
| accumulate 100 | **17.499μs (±1.1%)** | 20.765μs (±2.3%) | 74.043μs (±0.5%) |
| branch | 1.457μs (±6.2%) | **1.388μs (±2.7%)** | 5.596μs (±1.1%) |

Run benchmarks yourself:

```bash
composer bench                              # ASCII table
composer bench-md                           # Markdown table
composer bench-md -- --group=integer        # Filter by group
```

## Compatibility

| PHP | Laravel |
|-----|---------|
| 8.1 | 10 |
| 8.2 | 10, 11, 12 |
| 8.3 | 10, 11, 12 |
| 8.4 | 10, 11, 12 |

## Related

`Math` is also included in [OpinHelpers](https://github.com/fab2s/OpinHelpers), a collection of utilities for common PHP challenges.

## Contributing

Contributions are welcome. Please open issues and submit pull requests.

```shell
# fix code style
composer fix

# run tests
composer test

# run tests with coverage
composer cov

# static analysis (src, level 9)
composer stan

# static analysis (tests, level 5)
composer stan-tests
```

## License

Math is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
