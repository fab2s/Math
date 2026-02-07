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
echo (1.4 - 1) * 100;           // 40.000000000000006
echo 0.7 + 0.1 - 0.8;           // -1.1102230246252E-16
```

> `bcmath` supports numbers of any size and precision up to 2,147,483,647 decimals, represented as strings.

## Installation

```bash
composer require fab2s/math
```

### Requirements

- PHP 8.1+
- ext-bcmath (required)
- ext-gmp (optional, enables ~20x faster base conversions)

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

> **Important:** `Math` instances are **mutable**. Operations modify the instance in place and return `$this` for chaining. To preserve the original value, use `MathImmutable` or wrap it in a new instance:
>
> ```php
> $original = Math::number('100');
> $modified = $original->add('50'); // $original is now also '150'
>
> // To keep $original unchanged:
> $original = Math::number('100');
> $modified = Math::number($original)->add('50'); // $original stays '100'
> ```

### Immutable Variant

`MathImmutable` provides the same API but every operation returns a new instance, leaving the original unchanged:

```php
use fab2s\Math\MathImmutable;

$a = MathImmutable::number('100');
$b = $a->add('50');   // $a is still '100', $b is '150'
$c = $b->mul('2');    // $b is still '150', $c is '300'

// Works everywhere Math is accepted
function calculateTax(Math $price): Math { /* ... */ }
calculateTax($a); // MathImmutable extends Math
```

`MathImmutable` extends `Math`, so it inherits all factory methods (`number()`, `make()`, `fromBase()`) and is accepted anywhere `Math` is type-hinted. The overhead is a single `clone` per operation (two properties: a string and an int).

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

// Rounding
$n->round(2);        // Round to 2 decimals
$n->floor();         // Round down
$n->ceil();          // Round up

// Limits
$n->min('50', '200'); // 50
$n->max('50', '200'); // 200
```

### Comparisons

```php
$n = Math::number('42');

$n->eq('42');    // true  — equal
$n->gt('40');    // true  — greater than
$n->gte('42');   // true  — greater than or equal
$n->lt('50');    // true  — less than
$n->lte('42');   // true  — less than or equal
```

### Base Conversion (2-62)

Uses GMP when available for faster conversions:

```php
// From base X to base 10
Math::fromBase('LZ', 62);      // '1337'
Math::fromBase('101010', 2);   // '42'
Math::fromBase('FF', 16);      // '255'

// From base 10 to base X
Math::number('1337')->toBase(62);  // 'LZ'
Math::number('42')->toBase(2);     // '101010'
Math::number('255')->toBase(16);   // 'FF'
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

$total = Math::number($price)->add($price->mul($tax));
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

## API Reference

### Factory Methods

| Method | Description |
|--------|-------------|
| `Math::number($n)` | Create mutable instance |
| `Math::make($n)` | Alias for `number()` |
| `Math::fromBase($n, $base)` | Create from base 2-62 |
| `MathImmutable::number($n)` | Create immutable instance |
| `MathImmutable::make($n)` | Alias for `number()` |
| `MathImmutable::fromBase($n, $base)` | Create immutable from base 2-62 |

### Arithmetic

| Method | Description |
|--------|-------------|
| `add(...$n)` | Addition |
| `sub(...$n)` | Subtraction |
| `mul(...$n)` | Multiplication |
| `div(...$n)` | Division |
| `mod($n)` | Modulo |
| `pow($n)` | Power |
| `powMod($exp, $mod)` | Modular exponentiation |
| `sqrt()` | Square root |
| `abs()` | Absolute value |

### Rounding

| Method | Description |
|--------|-------------|
| `round($precision)` | Round to precision |
| `floor()` | Round down |
| `ceil()` | Round up |

### Comparison

| Method | Description |
|--------|-------------|
| `eq($n)` | Equal |
| `gt($n)` | Greater than |
| `gte($n)` | Greater than or equal |
| `lt($n)` | Less than |
| `lte($n)` | Less than or equal |
| `min(...$n)` | Minimum value |
| `max(...$n)` | Maximum value |

### Conversion & Output

| Method | Description |
|--------|-------------|
| `toBase($base)` | Convert to base 2-62 |
| `format($dec, $point, $sep)` | Format with separators |
| `getNumber()` | Get raw (non-normalized) number |
| `(string)` | Get normalized number |

### Precision

| Method | Description |
|--------|-------------|
| `setPrecision($p)` | Set instance precision |
| `getPrecision()` | Get instance precision |
| `Math::setGlobalPrecision($p)` | Set default for new instances |
| `Math::getGlobalPrecision()` | Get global precision |

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

## License

Math is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
