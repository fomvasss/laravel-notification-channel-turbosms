<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms;

class TurboSmsMessage
{
    /**
     * The sender name or phone number.
     */
    public ?string $from = null;

    /**
     * The message content.
     */
    public string $content = '';

    /**
     * Scheduled send time (Unix timestamp).
     */
    public ?int $time = null;

    /**
     * Whether this is a test send (no real SMS).
     */
    public ?bool $test = null;

    public static function create(string $content = ''): static
    {
        return new static($content);
    }

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function from(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Schedule message sending. Example: time() + 7*60*60 to delay by 7 hours.
     */
    public function time(?int $time = null): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Enable or disable test mode (no real SMS is sent).
     */
    public function test(bool $test = true): static
    {
        $this->test = $test;

        return $this;
    }
}
