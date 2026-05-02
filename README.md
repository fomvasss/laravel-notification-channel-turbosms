# TurboSms notifications channel for Laravel

Here's the latest documentation on Laravel's Notifications System: 

https://laravel.com/docs/master/notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/fomvasss/laravel-notification-channel-turbosms.svg?style=flat-square)](https://scrutinizer-ci.com/g/fomvasss/laravel-notification-channel-turbosms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/fomvasss/laravel-notification-channel-turbosms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/fomvasss/laravel-notification-channel-turbosms/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

## Support

If this package is useful to you, consider supporting its development:

[![Monobank](https://img.shields.io/badge/Donate-Monobank-black)](https://send.monobank.ua/jar/5xsqtHvVrY)
[![Ko-Fi](https://img.shields.io/badge/Donate-Ko--fi-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/fomvasss)
[![USDT TRC20](https://img.shields.io/badge/Donate-USDT%20TRC20-26A17B?logo=tether&logoColor=white)](https://link.trustwallet.com/send?coin=195&address=THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf&token_id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t)

> USDT TRC20 address: `THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf`

This package makes it easy to send notifications using [turbosms.ua](https://turbosms.ua/) with Laravel. Send SMS messages, get account balance.

## Installation

Install this package with Composer:

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```
## Configuration

Set API-token, default sender name (or phone number), test mode in `config/services.php`:

```php
'turbosms' => [
    'api_token'  => env('TURBOSMS_API_TOKEN'),
    'sender'  => env('TURBOSMS_SENDER'),        // For testing use TAXI 
    'is_test'  => env('TURBOSMS_IS_TEST'),      // Do not send real SMS if true
    
    // Optional
    'timeout'  => env('TURBOSMS_TIMEOUT'),
    'connect_timeout'  => env('TURBOSMS_CONNECT_TIMEOUT'),
],
```

and `.env` file:

```
TURBOSMS_API_TOKEN=your_api_token_here
TURBOSMS_SENDER=TAXI
TURBOSMS_IS_TEST=false
```

Also possible test senders: `TAXI, AKCIYA, BEAUTY, Best-offer, Best-Shop, BonusShop, IT Alarm, MAGAZIN, Dostavka24, SERVIS TAXI, BRAND`

## Usage via notification

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

### Available notify methods

`from()`: Sets the sender's name or phone number.

`content()`: Set a content of the notification message.

`time()`: Example argument = `time() + 7*60*60` - Postpone shipping for 7 hours.

`test()`: Test SMS sending (log)

## Usage via service-container

Get balance:
```php
app(TurboSmsApi::class)->getBalance(); // null|float, example 123.45
```

Send message:
```php
app(TurboSmsApi::class)->sendMessage('380969416874', new \NotificationChannels\TurboSms\TurboSmsMessage('Hello World with Laravel!')); // array, API response
```

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
