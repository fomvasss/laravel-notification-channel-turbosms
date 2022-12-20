# TurboSms notifications channel for Laravel 5.5+

Here's the latest documentation on Laravel's Notifications System: 

https://laravel.com/docs/master/notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/fomvasss/laravel-notification-channel-turbo-sms/master.svg?style=flat-square)](https://travis-ci.org/fomvasss/laravel-notification-channel-turbo-sms)
[![StyleCI](https://styleci.io/repos/:style_ci_id/shield)](https://styleci.io/repos/:style_ci_id)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/fomvasss/laravel-notification-channel-turbosms.svg?style=flat-square)](https://scrutinizer-ci.com/g/fomvasss/laravel-notification-channel-turbosms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/fomvasss/laravel-notification-channel-turbosms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/fomvasss/laravel-notification-channel-turbosms/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

This package makes it easy to send notifications using [turbosms.ua](https://turbosms.ua/) with Laravel 5.5+.

## Contents

- [Installation](#installation)
    - [Setting up the TurboSms service](#setting-up-the-TurboSms-service)
- [Usage](#usage)
    - [Available Message methods](#available-methods)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package with Composer:

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```

The service provider gets loaded automatically. Or you can do this manually:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\TurboSms\TurboSmsServiceProvider::class,
],
```

### Setting up the TurboSms service

Add your TurboSms token, default sender name (or phone number), test mode to your `config/services.php`:

```php
// config/services.php
...
'turbosms' => [
    'api_token'  => env('TURBOSMS_API_TOKEN'),
    'sender'  => env('TURBOSMS_SENDER'),
    'is_test'  => env('TURBOSMS_IS_TEST'),
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\TurboSms\TurboSmsMessage;
use NotificationChannels\TurboSms\TurboSmsChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [TurboSmsChannel::class];
    }

    public function toTurboSms($notifiable)
    {
        return (new TurboSmsMessage())->content("Hello SMS!!!")->test(true);
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForTurboSms()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForTurboSms()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's name or phone number.

`content()`: Set a content of the notification message.

`time()`: Example argument = `time() + 7*60*60` - Postpone shipping for 7 hours.

`test()`: Test SMS sending (log)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email fomvasss@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [fomvasss](https://github.com/fomvasss)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
