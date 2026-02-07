# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [2.1.0] - 2025-02-08

### Added

- `MathImmutable` — immutable variant of `Math` where every operation returns a new instance, leaving the original unchanged. Extends `Math` and is accepted anywhere `Math` is type-hinted.
- `declare(strict_types=1)` in all source files
- `phpstan-tests.neon` — dedicated PHPStan configuration for tests at level 5

### Changed

- `__toString()` bypasses redundant regex validation for better performance
- Cross-type argument passing between `Math` and `MathImmutable` instances
- PHPStan level 9 compliance for `src/`
- GitHub Actions workflows updated to latest action versions

### Fixed

- `format()` now works correctly with `Math` subclasses that override mutating methods
- `bcDec2Base()` properly initializes result as `'0'`

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

[Unreleased]: https://github.com/fab2s/Math/compare/2.1.0...HEAD
[2.1.0]: https://github.com/fab2s/Math/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/fab2s/Math/compare/1.0.1...2.0.0
[1.0.1]: https://github.com/fab2s/Math/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/fab2s/Math/releases/tag/1.0.0
