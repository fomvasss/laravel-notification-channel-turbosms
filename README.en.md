# TurboSMS Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

Send SMS notifications via [turbosms.ua](https://turbosms.ua/) in Laravel with ease.

> 🇺🇦 [Документація українською](README.md)

---

## Features

- Send SMS through the [TurboSMS API](https://turbosms.ua/)
- Per-message sender override
- Scheduled message delivery
- Test mode — simulate sending without real SMS
- Get account balance
- `NotificationFailed` event on errors

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | >= 8.1 |
| Laravel | 10, 11, 12, 13 |

---

## Installation

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```

---

## Configuration

Add credentials to `config/services.php`:

```php
'turbosms' => [
    'api_token'       => env('TURBOSMS_API_TOKEN'),
    'sender'          => env('TURBOSMS_SENDER', 'TAXI'),
    'is_test'         => env('TURBOSMS_IS_TEST', false),

    // Optional:
    'timeout'         => env('TURBOSMS_TIMEOUT', 15),
    'connect_timeout' => env('TURBOSMS_CONNECT_TIMEOUT', 10),
],
```

`.env`:

```env
TURBOSMS_API_TOKEN=your_api_token_here
TURBOSMS_SENDER=TAXI
TURBOSMS_IS_TEST=false
```

> **Free test senders:** `TAXI`, `AKCIYA`, `BEAUTY`, `Best-offer`, `Best-Shop`, `BonusShop`, `IT Alarm`, `MAGAZIN`, `Dostavka24`, `SERVIS TAXI`, `BRAND`

---

## Usage

### 1. Create a Notification

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\TurboSms\TurboSmsChannel;
use NotificationChannels\TurboSms\TurboSmsMessage;

class OrderShipped extends Notification
{
    public function __construct(private Order $order) {}

    public function via(mixed $notifiable): array
    {
        return [TurboSmsChannel::class];
    }

    public function toTurboSms(mixed $notifiable): TurboSmsMessage
    {
        return TurboSmsMessage::create("Your order #{$this->order->id} has been shipped!")
            ->from('MyShop');  // optional: override default sender
    }
}
```

### 2. Add Route Method to Your Model

```php
// app/Models/User.php

public function routeNotificationForTurboSms(): string
{
    return $this->phone; // e.g. '380991234567'
}
```

### 3. Send the Notification

```php
$user->notify(new OrderShipped($order));

// or via facade:
Notification::send($users, new OrderShipped($order));
```

---

## Message Methods

| Method | Description |
|---|---|
| `content(string $text)` | Set the SMS text |
| `from(string $sender)` | Override the default sender name or phone number |
| `time(?int $timestamp)` | Schedule sending. E.g.: `time() + 7*60*60` — delay by 7 hours |
| `test(bool $test = true)` | Override test mode for this specific message |

**Example:**

```php
TurboSmsMessage::create('Hello!')
    ->from('BRAND')
    ->time(time() + 3600)  // send in 1 hour
    ->test(false);
```

---

## Direct API Usage

You can use `TurboSmsApi` directly via the service container:

**Get account balance:**

```php
$balance = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->getBalance();
// float|null — e.g. 123.45
```

**Send a message:**

```php
use NotificationChannels\TurboSms\TurboSmsApi;
use NotificationChannels\TurboSms\TurboSmsMessage;

$result = app(TurboSmsApi::class)->sendMessage(
    '380991234567',
    TurboSmsMessage::create('Hello from Laravel!')
);

// Returns:
// [
//   'success' => true,
//   'result'  => [...],   // API response data
//   'info'    => 'TurboSMS response status: OK',
// ]
```

---

## Error Handling

On failure the channel fires an `Illuminate\Notifications\Events\NotificationFailed` event.

```php
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Event;

Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
    // $event->notifiable   — the model
    // $event->notification — the notification object
    // $event->data['exception'] — the exception
    logger()->error('TurboSMS failed: ' . $event->data['message']);
});
```

---

## Support

If this package is useful to you, consider supporting its development:

[![Monobank](https://img.shields.io/badge/Donate-Monobank-black)](https://send.monobank.ua/jar/5xsqtHvVrY)
[![Ko-Fi](https://img.shields.io/badge/Donate-Ko--fi-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/fomvasss)
[![USDT TRC20](https://img.shields.io/badge/Donate-USDT%20TRC20-26A17B?logo=tether&logoColor=white)](https://link.trustwallet.com/send?coin=195&address=THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf&token_id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t)

USDT TRC20: `THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf`

---

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Security

Please report security issues to fomvasss@gmail.com instead of the issue tracker.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md).

## Credits

- [fomvasss](https://github.com/fomvasss)
- [All Contributors](../../contributors)

## License

MIT — see [LICENSE.md](LICENSE.md).
