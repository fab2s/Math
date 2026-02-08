# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [3.0.0] - 2026-02-08

### Changed

- **BREAKING:** `Math` is now immutable by default — every operation returns a new instance, leaving the original unchanged
- `MathImmutable` replaced by `MathMutable` — mutability is now the explicit opt-in for performance-sensitive hot loops
- `__toString()` bypasses redundant regex validation for better performance
- PHPStan level 9 compliance for `src/`
- GitHub Actions workflows updated to latest action versions

### Added

- `MathMutable` — mutable variant of `Math` where operations modify the instance in place. Extends `Math` and is accepted anywhere `Math` is type-hinted.
- `negate()` — flip sign, zero stays zero
- `clamp($min, $max)` — clip value between bounds
- `quotientAndRemainder($divisor)` — returns `[$quotient, $remainder]` in one call
- `isZero()` — precision-aware zero check
- `isNegative()` — complement to `isPositive()`
- `isEven()` / `isOdd()` — integer parity checks, return `false` for non-integers
- `getScale()` — number of meaningful decimal places (normalized)
- `getIntegralPart()` — part before the decimal point (normalized, `-0` becomes `0`)
- `getFractionalPart()` — part after the decimal point (normalized, trailing zeros stripped)
- `declare(strict_types=1)` in all source files
- `phpstan-tests.neon` — dedicated PHPStan configuration for tests at level 5

### Fixed

- `format()` now works correctly with immutable default (captures `abs()` return value)
- `bcDec2Base()` properly initializes result as `'0'`

### Removed

- `MathImmutable` — no longer needed, `Math` itself is now immutable

## [2.0.0] - 2024-04-24

### Added

- `MathCast` — Laravel Eloquent cast for `Math` instances with nullable support

### Changed

- Minimum PHP version raised to 8.1

### Removed

- PHP < 8.1 support

## [1.0.1] - 2021-06-16

### Added

- PHP 8.0 support

## [1.0.0] - 2019-07-31

Initial release.

[Unreleased]: https://github.com/fab2s/Math/compare/3.0.0...HEAD
[3.0.0]: https://github.com/fab2s/Math/compare/2.0.0...3.0.0
[2.0.0]: https://github.com/fab2s/Math/compare/1.0.1...2.0.0
[1.0.1]: https://github.com/fab2s/Math/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/fab2s/Math/releases/tag/1.0.0
