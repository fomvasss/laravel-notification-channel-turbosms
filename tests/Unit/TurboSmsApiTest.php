<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms\Tests\Unit;

use NotificationChannels\TurboSms\Tests\TestCase;
use NotificationChannels\TurboSms\TurboSmsApi;
use NotificationChannels\TurboSms\TurboSmsMessage;

class TurboSmsApiTest extends TestCase
{
    private TurboSmsApi $api;

    protected function setUp(): void
    {
        parent::setUp();

        // Test mode enabled via config — no real HTTP requests
        $this->api = new TurboSmsApi('test-token', 'TEST', ['is_test' => true]);
    }

    public function test_send_message_in_test_mode_returns_test_response(): void
    {
        $message = new TurboSmsMessage('Hello World');
        $result = $this->api->sendMessage('380991234567', $message);

        $this->assertFalse($result['success']);
        $this->assertSame('turbosms.test_mode', $result['info']);
        $this->assertArrayHasKey('body', $result['result']);
    }

    public function test_send_message_uses_message_sender_over_default(): void
    {
        $message = (new TurboSmsMessage('Text'))->from('CUSTOM');
        $result = $this->api->sendMessage('380991234567', $message);

        $this->assertSame('CUSTOM', $result['result']['body']['sms']['sender']);
    }

    public function test_send_message_uses_default_sender_when_no_from(): void
    {
        $message = new TurboSmsMessage('Text');
        $result = $this->api->sendMessage('380991234567', $message);

        $this->assertSame('TEST', $result['result']['body']['sms']['sender']);
    }

    public function test_send_message_includes_start_time_when_set(): void
    {
        $time = time() + 3600;
        $message = (new TurboSmsMessage('Text'))->time($time);
        $result = $this->api->sendMessage('380991234567', $message);

        $this->assertSame($time, $result['result']['body']['start_time']);
    }

    public function test_send_message_recipient_is_correct(): void
    {
        $message = new TurboSmsMessage('Hi');
        $result = $this->api->sendMessage('380991234567', $message);

        $this->assertContains('380991234567', $result['result']['body']['recipients']);
    }

    public function test_get_balance_in_test_mode_returns_null(): void
    {
        $balance = $this->api->getBalance();

        $this->assertNull($balance);
    }

    public function test_get_response_test_mode_returns_expected_structure(): void
    {
        $result = $this->api->getResponse('https://api.turbosms.ua/test', ['foo' => 'bar']);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('info', $result);
        $this->assertFalse($result['success']);
    }
}

