# TurboSms Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

This package makes it easy to send SMS notifications using [turbosms.ua](https://turbosms.ua/) with Laravel. Supports sending messages and retrieving account balance.

> Laravel Notifications documentation: https://laravel.com/docs/notifications

---

## Support

If this package is useful to you, consider supporting its development:

[![Monobank](https://img.shields.io/badge/Donate-Monobank-black)](https://send.monobank.ua/jar/5xsqtHvVrY)
[![Ko-Fi](https://img.shields.io/badge/Donate-Ko--fi-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/fomvasss)
[![USDT TRC20](https://img.shields.io/badge/Donate-USDT%20TRC20-26A17B?logo=tether&logoColor=white)](https://link.trustwallet.com/send?coin=195&address=THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf&token_id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t)

> USDT TRC20 address: `THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf`

---

## Requirements

- PHP >= 8.1
- Laravel 10, 11 or 12

## Installation

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```

## Configuration

Add TurboSMS credentials to `config/services.php`:

```php
'turbosms' => [
    'api_token'       => env('TURBOSMS_API_TOKEN'),
    'sender'          => env('TURBOSMS_SENDER', 'TAXI'),    // default sender name
    'is_test'         => env('TURBOSMS_IS_TEST', false),    // if true — no real SMS sent

    // Optional HTTP client timeouts (seconds):
    'timeout'         => env('TURBOSMS_TIMEOUT', 15),
    'connect_timeout' => env('TURBOSMS_CONNECT_TIMEOUT', 10),
],
```

`.env` example:

```env
TURBOSMS_API_TOKEN=your_api_token_here
TURBOSMS_SENDER=TAXI
TURBOSMS_IS_TEST=false
```

Free test senders available in TurboSMS: `TAXI`, `AKCIYA`, `BEAUTY`, `Best-offer`, `Best-Shop`, `BonusShop`, `IT Alarm`, `MAGAZIN`, `Dostavka24`, `SERVIS TAXI`, `BRAND`.

## Usage via Notification

Implement the `toTurboSms()` method in your notification class:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\TurboSms\TurboSmsChannel;
use NotificationChannels\TurboSms\TurboSmsMessage;

class OrderShipped extends Notification
{
    public function via(mixed $notifiable): array
    {
        return [TurboSmsChannel::class];
    }

    public function toTurboSms(mixed $notifiable): TurboSmsMessage
    {
        return TurboSmsMessage::create("Your order #{$this->order->id} has been shipped!")
            ->from('MyShop')      // optional: override default sender
            ->test(false);        // optional: override test mode
    }
}
```

Add `routeNotificationForTurboSms()` to your notifiable model:

```php
public function routeNotificationForTurboSms(): string
{
    return $this->phone;
}
```

## Message Methods

| Method | Description |
|---|---|
| `content(string $text)` | Set the SMS text |
| `from(string $sender)` | Override the default sender name or phone |
| `time(?int $timestamp)` | Schedule sending (Unix timestamp). E.g. `time() + 7*60*60` for +7h |
| `test(bool $test = true)` | Enable/disable test mode per message (no real SMS sent) |

## Usage via Service Container

**Get balance:**
```php
$balance = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->getBalance();
// returns float|null, e.g. 123.45
```

**Send message directly:**
```php
$result = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->sendMessage(
    '380991234567',
    \NotificationChannels\TurboSms\TurboSmsMessage::create('Hello Laravel!')
);
// returns array with 'success', 'result', 'info' keys
```

## Error Handling

On failure the channel fires a `NotificationFailed` event with the exception details. You can listen to it:

```php
use Illuminate\Notifications\Events\NotificationFailed;

Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
    logger()->error('TurboSMS notification failed', $event->data);
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Security

If you discover any security related issues, please email fomvasss@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [fomvasss](https://github.com/fomvasss)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
