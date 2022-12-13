<?php

namespace NotificationChannels\TurboSms;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use NotificationChannels\TurboSms\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;

class TurboSmsChannel
{
    /**
     * @var TurboSmsApi
     */
    protected $smsApi;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * TurboSmsChannel constructor.
     * @param TurboSmsApi $smsApi
     * @param Dispatcher $events
     */
    public function __construct(TurboSmsApi $smsApi, Dispatcher $events)
    {
        $this->smsApi = $smsApi;
        $this->events = $events;
    }
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @throws CouldNotSendNotification
     *
     * @return array|null
     */
    public function send($notifiable, Notification $notification)
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

        } catch (Exception $exception) {
            $event = new NotificationFailed($notifiable, $notification, 'TurboSms', ['message' => $exception->getMessage(), 'exception' => $exception]);

            if (function_exists('event')) { // Use event helper when possible to add Lumen support
                event($event);
            } else {
                $this->events->fire($event);
            }
        }
    }

    protected function getRecipient($notifiable)
    {
        if ($notifiable->routeNotificationFor('TurboSms')) {
            return $notifiable->routeNotificationFor('TurboSms');
        }

        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}
