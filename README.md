# Math

[![Build Status](https://travis-ci.com/fab2s/Math.svg?branch=master)](https://travis-ci.com/fab2s/Math) [![Total Downloads](https://poser.pugx.org/fab2s/math/downloads)](//packagist.org/packages/fab2s/math) [![Monthly Downloads](https://poser.pugx.org/fab2s/math/d/monthly)](//packagist.org/packages/fab2s/math) [![Latest Stable Version](https://poser.pugx.org/fab2s/math/v/stable)](https://packagist.org/packages/fab2s/math) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fab2s/Math/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fab2s/Math/?branch=master) [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com) [![License](https://poser.pugx.org/fab2s/math/license)](https://packagist.org/packages/fab2s/math)

A fluent [bcmath](https://php.net/bcmath) based _Helper_ to handle high precision calculus in base 10 with a rather strict approach (want precision for something right?).
It does not try to be smart and just fails without `bcmath`, but it does auto detect [GMP](https://php.net/GMP) for faster base conversions.

> `Bcmath` supports numbers of any size and precision up to 2 147 483 647 (or 0x7FFFFFFF) decimals, if there is sufficient memory, represented as strings. 

## Installation

Math can be installed using composer :

```
composer require "fab2s/math"
```

`Math` is also included in [OpinHelper](https://github.com/fab2s/OpinHelpers) which packages several bellow "Swiss Army Knife" level Helpers covering some of the most annoying aspects of php programing, such as UTF8 string manipulation, high precision Mathematics or properly locking a file

## Prerequisites

`Math` requires [bcmath](https://php.net/bcmath), [GMP](https://php.net/GMP) is auto detected and used when available for faster base conversions (up to 62). 

## In practice

As `Math` is meant to be used where precision matters, it is pretty strict with input numbers : it will throw an exception whenever an input number does not match `^[+-]?([0-9]+(\.[0-9]+)?|\.[0-9]+)$` after passing though `trim()`.

In practice this means that "-.0051" and "00028.34" are ok, but "1E12", "3,14" or "1.1.1" will throw an exception. This is done so because in `bcmath` world, "1E12", "1.1.1" and "abc" are all "0", which could result in some disaster if you where to do nothing.

A `Math` instance is just initialized with a valid base 10 number. From there you can do the math and just cast the instance as string to get the current result at any stage. 

```php
// instance way
$number = new Math('42');

// fluent grammar
$result = (string) $number->add('1')->sub(2)->div(1)->add(1)->mul(-1); // '-42'

// factory way: number
$result = (string) Math::number('42')->add('1')->sub(2)->div('1')->add(1)->mul(-1); // '-42'

// factory way: fromBase
$result = (string) Math::fromBase('LZ', 62); // '1337'
$result = (string) Math::fromBase('LZ', 62)->sub(1295); // '42'

// combos
$number = Math::number('42')
    ->add(Math::fromBase('LZ', 62), '-42')
    ->sub('1337', '42')
    ->mul(3, 4, 1)
    ->div(4, 3)
    ->sub('.1')
    ->abs()
    ->round(0)
    ->floor()
    ->ceil()
    ->min('512', '256')
    ->max('8', '16', '32');

// formatting does not mutate internal number
$result = (string) $number->format(2); // '42.00'
$result = (string) $number; // '42';
// and you can continue calculating after string cast
$result = (string) $number->add('1295')->toBase(62); // 'LZ'

// toBase does not mutate base 10 internal representation
$result = (string) $number; // '1337';
```

The string form of any such calculus is normalized (things like '-0', '+.0' or '0.00' to '0'), which means that you can accurately compare `Math` instances results:

```php
$result = (string) Math::number('0000042.000000'); // '42'

// raw form
$result = Math::number('0000042.000000')->getNumber(); // '0000042.000000'

// with some tolerance
$result = Math::number('  42.0000 ')->getNumber(); // '42.0000'

// at all time
if ((string) $number1 === (string) $number2) {
    // both instance numbers are equals
}

// same as (internally using bccomp)
if ($number1->eq($number2)) {
    // both instance numbers are equals
}
```

You can transparently re-use partial $calculus directly as instance when calculating:

```php
$number = new Math('42');
// same as
$number = Math::number('42');

// in constructor
$result = (string) (new Math($number))->div('2'); // '21'
// same as
$result = (string) Math::number($number)->div('2'); // '21'

// in calc method
$result = (string) Math::number('42')->add($number)->sub('42')->div('2'); // '21'
```

Doing so is actually faster than casting a pre-existing instance to string because it does not trigger a normalization (internal number state is only normalized when exporting result) nor a number validation, as internal $number is already valid at all times.

Arguments should be string or `Math`, but it is _ok_ to use integers up to `INT_(32|64)`. 

**DO NOT** use `floats` as casting them to `string` may result in local dependent format, such as using a coma instead of a dot for decimals or just turn them exponential notation which is not supported by bcmath.
The way floats are handled in general and by PHP in particular is the very the reason why `bcmath` exists, so even if you trust your locale settings, using floats still kinda defeats the purpose of using such lib.

## Internal precision

Precision handling does not rely on [bcscale](https://php.net/bcscale) as it is not so reliable IRL. As it is a global setup, it may affect or be affected by far away/unrelated code (with fpm it can actually spread to all PHP processes).

`Math` handle precisions at both instance and global (limited to the current PHP process) precision. The global precision is stored in a static variable. When set, each new instance will start with this global precision as its own precision (you can still set the instance precision after instantiation). When no global precision is set, initial instance precision defaults to `Math::PRECISION` (currently 9, or 9 digits after the dot)

```php
// set global precision
Math::setGlobalPrecision(18);

$number = (new Math('100'))->div('3'); // uses precision 18
$number->setPrecision(14); // will use precision 14 for any further calculations
```

## Requirements

`Math` is tested against php 7.1, 7.2, 7.3, 7.4 and 8.0

## Contributing

Contributions are welcome, do not hesitate to open issues and submit pull requests.

## License

`Math` is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).