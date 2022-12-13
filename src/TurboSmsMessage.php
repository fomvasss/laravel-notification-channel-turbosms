<?php

namespace NotificationChannels\TurboSms;

use Illuminate\Support\Arr;

class TurboSmsMessage
{
    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from = '';
    /**
     * The message content.
     *
     * @var string
     */
    public $content = '';

    /**
     * Time of sending a message.
     *
     * @var int
     */
    public $time;

    /**
     * @var bool
     */
    public $test;


    /**
     * Create a new message instance.
     *
     * @param  string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }
    /**
     * @param  string  $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }
    /**
     * Set the message content.
     *
     * @param  string  $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the test SMS - imitation sending.
     *
     * @param  string  $from
     *
     * @return $this
     */
    public function test(bool $test = false)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Set the phone number or sender name the message should be sent from.
     *
     * @param  string  $from
     *
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Postpone shipping for -n- sec.
     *
     * @param null $time
     * @return $this
     */
    public function time(int $time = null)
    {
        $this->time = $time;

        return $this;
    }
}
