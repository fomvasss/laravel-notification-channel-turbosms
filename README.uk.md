# TurboSms — канал сповіщень для Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-notification-channel-turbo-sms.svg?style=flat-square)](https://packagist.org/packages/fomvasss/laravel-notification-channel-turbo-sms)

Пакет для відправки SMS-сповіщень через [turbosms.ua](https://turbosms.ua/) у Laravel. Підтримує надсилання повідомлень та отримання балансу рахунку.

> 🇬🇧 [Documentation in English](README.md)

> Документація Laravel Notifications: https://laravel.com/docs/notifications

---

## Підтримка проекту

Якщо пакет вам корисний — підтримайте розробку:

[![Monobank](https://img.shields.io/badge/Donate-Monobank-black)](https://send.monobank.ua/jar/5xsqtHvVrY)
[![Ko-Fi](https://img.shields.io/badge/Donate-Ko--fi-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/fomvasss)
[![USDT TRC20](https://img.shields.io/badge/Donate-USDT%20TRC20-26A17B?logo=tether&logoColor=white)](https://link.trustwallet.com/send?coin=195&address=THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf&token_id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t)

> USDT TRC20 адреса: `THLgp6DxiAtbNHvgnKV56vk1L38UuUagKf`

---

## Вимоги

- PHP >= 8.1
- Laravel 10, 11 або 12

## Встановлення

```bash
composer require fomvasss/laravel-notification-channel-turbo-sms
```

## Налаштування

Додайте конфігурацію TurboSMS у `config/services.php`:

```php
'turbosms' => [
    'api_token'       => env('TURBOSMS_API_TOKEN'),
    'sender'          => env('TURBOSMS_SENDER', 'TAXI'),    // ім'я відправника за замовчуванням
    'is_test'         => env('TURBOSMS_IS_TEST', false),    // true — SMS не відправляються реально

    // Необов'язково — таймаути HTTP-клієнта (секунди):
    'timeout'         => env('TURBOSMS_TIMEOUT', 15),
    'connect_timeout' => env('TURBOSMS_CONNECT_TIMEOUT', 10),
],
```

Приклад `.env`:

```env
TURBOSMS_API_TOKEN=your_api_token_here
TURBOSMS_SENDER=TAXI
TURBOSMS_IS_TEST=false
```

Безкоштовні тестові імена відправників у TurboSMS: `TAXI`, `AKCIYA`, `BEAUTY`, `Best-offer`, `Best-Shop`, `BonusShop`, `IT Alarm`, `MAGAZIN`, `Dostavka24`, `SERVIS TAXI`, `BRAND`.

## Використання через Notification

Реалізуйте метод `toTurboSms()` у класі сповіщення:

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
        return TurboSmsMessage::create("Ваше замовлення #{$this->order->id} відправлено!")
            ->from('MyShop')   // необов'язково: перевизначити відправника
            ->test(false);     // необов'язково: перевизначити тестовий режим
    }
}
```

Додайте метод `routeNotificationForTurboSms()` до моделі notifiable:

```php
public function routeNotificationForTurboSms(): string
{
    return $this->phone;
}
```

## Методи повідомлення

| Метод | Опис |
|---|---|
| `content(string $text)` | Текст SMS |
| `from(string $sender)` | Перевизначити ім'я або номер відправника |
| `time(?int $timestamp)` | Запланувати відправку (Unix timestamp). Наприклад `time() + 7*60*60` — через 7 годин |
| `test(bool $test = true)` | Увімкнути/вимкнути тестовий режим для конкретного повідомлення |

## Використання через Service Container

**Отримати баланс:**
```php
$balance = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->getBalance();
// повертає float|null, наприклад 123.45
```

**Відправити повідомлення напряму:**
```php
$result = app(\NotificationChannels\TurboSms\TurboSmsApi::class)->sendMessage(
    '380991234567',
    \NotificationChannels\TurboSms\TurboSmsMessage::create('Привіт від Laravel!')
);
// повертає масив з ключами 'success', 'result', 'info'
```

## Обробка помилок

При невдалій відправці канал генерує подію `NotificationFailed`. Підпишіться на неї для обробки:

```php
use Illuminate\Notifications\Events\NotificationFailed;

Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
    logger()->error('TurboSMS сповіщення не відправлено', $event->data);
});
```

## Changelog

Дивіться [CHANGELOG](CHANGELOG.md) для інформації про зміни.

## Безпека

Якщо ви виявили вразливість — напишіть на fomvasss@gmail.com замість публічного issue.

## Участь у розробці

Дивіться [CONTRIBUTING](CONTRIBUTING.md).

## Автори

- [fomvasss](https://github.com/fomvasss)
- [Всі учасники](../../contributors)

## Ліцензія

MIT License. Дивіться [License File](LICENSE.md).

