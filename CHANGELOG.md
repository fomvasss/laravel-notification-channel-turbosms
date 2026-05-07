# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-05-07

### Added
- `declare(strict_types=1)` in all PHP files
- Full return type hints and property types across all classes
- `guzzlehttp/guzzle` as explicit `require` dependency
- `orchestra/testbench`, `phpunit/phpunit`, `mockery/mockery` as `require-dev`
- `phpunit.xml` configuration file
- Unit tests: `TurboSmsMessageTest`, `TurboSmsApiTest`, `TurboSmsChannelTest` (21 tests)
- `README.uk.md` — Ukrainian documentation
- JSON decode validation in `TurboSmsApi::getResponse()`

### Changed
- PHP requirement bumped to `>=8.1`
- Laravel requirement narrowed to `^10|^11|^12`
- `TurboSmsChannel::send()` now catches `\Throwable` instead of unqualified `Exception` (critical bug fix)
- `TurboSmsChannel::getRecipient()` uses `routeNotificationFor('TurboSms', null)` (Laravel-compatible signature)
- `$this->events->fire()` replaced with `$this->events->dispatch()` (deprecated method removed)
- `TurboSmsMessage::$from` default changed from `''` to `null` (prevents empty sender in API call)
- `TurboSmsMessage::test()` default parameter changed from `false` to `true` (more intuitive)
- `TurboSmsApi` sender logic simplified: `$message->from ?? $this->smsSender`
- `TurboSmsServiceProvider` handles missing config gracefully (no fatal error if config absent)
- `CouldNotSendNotification::invalidReceiver()` error message corrected (was referencing wrong method name `routeNotificationForSmsru`)
- `CouldNotSendNotification::invalidMessageObject()` error message fixed (was incomplete string)
- Exceptions use `\RuntimeException` instead of base `\Exception`
- Updated README.md with full documentation, method table, error handling section

### Removed
- Lumen support (deprecated in favor of pure Laravel)

## [1.0.0] - 2022-12-09

### Added
- Initial release
