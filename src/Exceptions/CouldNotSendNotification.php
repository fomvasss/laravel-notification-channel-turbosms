<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms\Exceptions;

use NotificationChannels\TurboSms\TurboSmsMessage;

class CouldNotSendNotification extends \Exception
{
    public static function missingFrom(): static
    {
        return new static('Notification was not sent. Missing `from` number.');
    }

    public static function invalidReceiver(): static
    {
        return new static(
            'The notifiable did not have a receiving phone number. Add a `routeNotificationForTurboSms` method or a `phone` attribute to your notifiable.'
        );
    }

    public static function invalidMessageObject(mixed $message): static
    {
        $className = is_object($message) ? get_class($message) : gettype($message);

        return new static(
            "Notification was not sent. Message object class `{$className}` is invalid. It should be an instance of `" . TurboSmsMessage::class . '`.'
        );
    }
}
