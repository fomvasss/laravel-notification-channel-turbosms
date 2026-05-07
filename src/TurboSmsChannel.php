<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\TurboSms\Exceptions\CouldNotSendNotification;

class TurboSmsChannel
{
    public function __construct(
        protected TurboSmsApi $smsApi,
        protected Dispatcher $events,
    ) {}

    /**
     * Send the given notification.
     *
     * @throws CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): ?array
    {
        try {
            $recipient = $this->getRecipient($notifiable);
            $message = $notification->toTurboSms($notifiable);

            if (is_string($message)) {
                $message = new TurboSmsMessage($message);
            }

            if (! $message instanceof TurboSmsMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->smsApi->sendMessage($recipient, $message);

        } catch (\Throwable $exception) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'TurboSms',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            $this->events->dispatch($event);
        }

        return null;
    }

    protected function getRecipient(mixed $notifiable): string
    {
        $phone = $notifiable->routeNotificationFor('TurboSms', null);

        if ($phone) {
            return $phone;
        }

        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}
