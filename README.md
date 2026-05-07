# TurboSMS — канал сповіщень для Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

Відправка SMS-сповіщень через [turbosms.ua](https://turbosms.ua/) у Laravel.

> 🇬🇧 [Documentation in English](README.en.md)

---

## Можливості

- Відправка SMS через [TurboSMS API](https://turbosms.ua/)
- Перевизначення відправника для кожного повідомлення
- Відкладена відправка (планування)
- Тестовий режим — імітація без реального надсилання
- Отримання балансу рахунку
- Подія `NotificationFailed` при помилках

---

## Вимоги

| Залежність | Версія |
|---|---|
| PHP | >= 8.1 |
| Laravel | 10, 11, 12, 13 |

---

## Встановлення

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```

---

## Налаштування

Додайте конфігурацію у `config/services.php`:

```php
'turbosms' => [
    'api_token'       => env('TURBOSMS_API_TOKEN'),
    'sender'          => env('TURBOSMS_SENDER', 'TAXI'),
    'is_test'         => env('TURBOSMS_IS_TEST', false),

    // Необов'язково:
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

> **Безкоштовні тестові відправники:** `TAXI`, `AKCIYA`, `BEAUTY`, `Best-offer`, `Best-Shop`, `BonusShop`, `IT Alarm`, `MAGAZIN`, `Dostavka24`, `SERVIS TAXI`, `BRAND`

---

## Використання

### 1. Створіть клас сповіщення

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
        return TurboSmsMessage::create("Ваше замовлення #{$this->order->id} відправлено!")
            ->from('MyShop');  // необов'язково: перевизначити відправника
    }
}
```

### 2. Додайте метод маршрутизації до моделі

```php
// app/Models/User.php

public function routeNotificationForTurboSms(): string
{
    return $this->phone; // напр. '380991234567'
}
```

### 3. Надішліть сповіщення

```php
$user->notify(new OrderShipped($order));

// або через фасад:
Notification::send($users, new OrderShipped($order));
```

---

## Методи повідомлення

| Метод | Опис |
|---|---|
| `content(string $text)` | Текст SMS |
| `from(string $sender)` | Перевизначити ім'я або номер відправника |
| `time(?int $timestamp)` | Запланувати відправку. Наприклад: `time() + 7*60*60` — через 7 годин |
| `test(bool $test = true)` | Перевизначити тестовий режим для конкретного повідомлення |

**Приклад:**

```php
TurboSmsMessage::create('Привіт!')
    ->from('BRAND')
    ->time(time() + 3600)  // відправити через 1 годину
    ->test(false);
```

---

## Пряме використання API

Можна використовувати `TurboSmsApi` напряму через сервіс-контейнер:

**Отримати баланс:**

```php
$balance = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->getBalance();
// float|null — напр. 123.45
```

**Відправити повідомлення:**

```php
use NotificationChannels\TurboSms\TurboSmsApi;
use NotificationChannels\TurboSms\TurboSmsMessage;

$result = app(TurboSmsApi::class)->sendMessage(
    '380991234567',
    TurboSmsMessage::create('Привіт від Laravel!')
);

// Повертає:
// [
//   'success' => true,
//   'result'  => [...],   // дані відповіді API
//   'info'    => 'TurboSMS response status: OK',
// ]
```

---

## Обробка помилок

При невдалій відправці канал генерує подію `Illuminate\Notifications\Events\NotificationFailed`.

```php
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Event;

Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
    // $event->notifiable   — модель отримувача
    // $event->notification — об'єкт сповіщення
    // $event->data['exception'] — виняток
    logger()->error('TurboSMS помилка: ' . $event->data['message']);
});
```

---

## Підтримка проекту

Якщо пакет вам корисний — підтримайте розробку:

[![Monobank](https://img.shields.io/badge/Donate-Monobank-black)](https://send.monobank.ua/jar/5xsqtHvVrY)
[![Ko-Fi](https://img.shields.io/badge/Donate-Ko--fi-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/fomvasss)
[![USDT TRC20](https://img.shields.io/badge/Donate-USDT%20TRC20-26A17B?logo=tether&logoColor=white)](https://link.trustwallet.com/send?coin=195&address=THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf&token_id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t)

USDT TRC20: `THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf`

---

## Changelog

Дивіться [CHANGELOG](CHANGELOG.md).

## Безпека

Повідомляйте про вразливості на fomvasss@gmail.com, а не через публічний issue.

## Участь у розробці

Дивіться [CONTRIBUTING](CONTRIBUTING.md).

## Автори

- [fomvasss](https://github.com/fomvasss)
- [Всі учасники](../../contributors)

## Ліцензія

MIT — дивіться [LICENSE.md](LICENSE.md).
