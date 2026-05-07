<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms\Tests\Unit;

use NotificationChannels\TurboSms\Tests\TestCase;
use NotificationChannels\TurboSms\TurboSmsMessage;

class TurboSmsMessageTest extends TestCase
{
    public function test_can_be_created_via_constructor(): void
    {
        $message = new TurboSmsMessage('Hello');

        $this->assertSame('Hello', $message->content);
        $this->assertNull($message->from);
        $this->assertNull($message->time);
        $this->assertNull($message->test);
    }

    public function test_can_be_created_via_static_factory(): void
    {
        $message = TurboSmsMessage::create('World');

        $this->assertSame('World', $message->content);
    }

    public function test_content_method_returns_self(): void
    {
        $message = new TurboSmsMessage();
        $result = $message->content('Test content');

        $this->assertSame($message, $result);
        $this->assertSame('Test content', $message->content);
    }

    public function test_from_method_sets_sender(): void
    {
        $message = (new TurboSmsMessage())->from('MySender');

        $this->assertSame('MySender', $message->from);
    }

    public function test_time_method_sets_scheduled_time(): void
    {
        $time = time() + 3600;
        $message = (new TurboSmsMessage())->time($time);

        $this->assertSame($time, $message->time);
    }

    public function test_test_method_enables_test_mode(): void
    {
        $message = (new TurboSmsMessage())->test();

        $this->assertTrue($message->test);
    }

    public function test_test_method_can_disable_test_mode(): void
    {
        $message = (new TurboSmsMessage())->test(false);

        $this->assertFalse($message->test);
    }

    public function test_fluent_chaining(): void
    {
        $message = TurboSmsMessage::create()
            ->content('SMS text')
            ->from('SENDER')
            ->test(true);

        $this->assertSame('SMS text', $message->content);
        $this->assertSame('SENDER', $message->from);
        $this->assertTrue($message->test);
    }
}

