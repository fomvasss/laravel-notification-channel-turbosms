<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms\Tests\Unit;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use NotificationChannels\TurboSms\Exceptions\CouldNotSendNotification;
use NotificationChannels\TurboSms\Tests\TestCase;
use NotificationChannels\TurboSms\TurboSmsApi;
use NotificationChannels\TurboSms\TurboSmsChannel;
use NotificationChannels\TurboSms\TurboSmsMessage;

class TurboSmsChannelTest extends TestCase
{
    private TurboSmsChannel $channel;
    private TurboSmsApi $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = new TurboSmsApi('token', 'SENDER', ['is_test' => true]);
        $this->channel = new TurboSmsChannel($this->api, $this->app->make(Dispatcher::class));
    }

    public function test_send_notification_with_message_object(): void
    {
        $notifiable = $this->makeNotifiable('380991234567');
        $notification = $this->makeNotification(new TurboSmsMessage('Test SMS'));

        $result = $this->channel->send($notifiable, $notification);

        $this->assertIsArray($result);
        $this->assertSame('turbosms.test_mode', $result['info']);
    }

    public function test_send_notification_with_string_message(): void
    {
        $notifiable = $this->makeNotifiable('380991234567');
        $notification = $this->makeNotification('Simple string message');

        $result = $this->channel->send($notifiable, $notification);

        $this->assertIsArray($result);
    }

    public function test_send_fires_notification_failed_event_on_invalid_receiver(): void
    {
        $notifiable = new class {
            // no phone, no routeNotificationFor
            public function routeNotificationFor(string $channel, mixed $notification = null): mixed
            {
                return null;
            }
        };

        $notification = $this->makeNotification(new TurboSmsMessage('Test'));

        $eventFired = false;
        $this->app->make(Dispatcher::class)->listen(
            \Illuminate\Notifications\Events\NotificationFailed::class,
            function () use (&$eventFired) {
                $eventFired = true;
            }
        );

        $this->channel->send($notifiable, $notification);

        $this->assertTrue($eventFired);
    }

    public function test_get_recipient_from_route_notification_for(): void
    {
        $notifiable = $this->makeNotifiable('380991111111');
        $notification = $this->makeNotification(new TurboSmsMessage('Hi'));

        $result = $this->channel->send($notifiable, $notification);

        $this->assertContains('380991111111', $result['result']['body']['recipients']);
    }

    public function test_exception_class_invalid_receiver_message(): void
    {
        $exception = CouldNotSendNotification::invalidReceiver();

        $this->assertStringContainsString('routeNotificationForTurboSms', $exception->getMessage());
    }

    public function test_exception_class_invalid_message_object(): void
    {
        $exception = CouldNotSendNotification::invalidMessageObject(new \stdClass());

        $this->assertStringContainsString('stdClass', $exception->getMessage());
        $this->assertStringContainsString(TurboSmsMessage::class, $exception->getMessage());
    }

    private function makeNotifiable(string $phone): object
    {
        return new class ($phone) {
            public function __construct(private string $phone) {}

            public function routeNotificationFor(string $channel, mixed $notification = null): ?string
            {
                return $this->phone;
            }
        };
    }

    private function makeNotification(mixed $return): Notification
    {
        return new class ($return) extends Notification {
            public function __construct(private mixed $return) {}

            public function toTurboSms(mixed $notifiable): mixed
            {
                return $this->return;
            }
        };
    }
}

