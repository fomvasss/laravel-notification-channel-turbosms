<?php

namespace NotificationChannels\TurboSms;

use GuzzleHttp\Client as HttpClient;

class TurboSmsApi
{
    /** @var HttpClient */
    protected $client;
    protected $apiToken;
    protected $smsSender;
    protected $isTest;

    protected $baseUri = 'https://api.turbosms.ua/';

    public function __construct($apiToken, $sender, $isTest)
    {
        $this->apiToken = $apiToken;
        $this->smsSender = $sender;
        $this->isTest = $isTest;

        $this->client = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * @param $recipient
     * @param TurboSmsMessage $message
     * @return array
     */
    public function sendMessage($recipient, TurboSmsMessage $message)
    {
        $url = $this->baseUri . 'message/send.json';
        $body = [
            'recipients' => [$recipient],
            'sms' => [
                'text' => $message->content,
            ],
        ];

        if ($this->smsSender) {
            $body['sms']['sender'] = $this->smsSender;
        }
        if ($message->from) {
            $body['sms']['sender'] = $message->from;
        }

        if (!is_null($message->test)) {
            $this->isTest = $message->test;
        }

        if (!is_null($message->time)) {
            $body['start_time'] = $message->time;
        }

        return $this->getResponse($url, $body);
    }

    /**
     * @param string $url
     * @param array $body
     * @return array
     */
    public function getResponse($url, $body)
    {
        if ($this->isTest) {
            return [
                'success' => false,
                'result' => [
                    'url' => $url,
                    'body' => $body,
                ],
                'info' => 'turbosms.test_mode',
            ];
        }

        $response = $this->client->request('POST', $url, [
            'headers'        => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
            'json' => $body
        ]);

        $answer = \json_decode((string) $response->getBody(), true);

        if (isset($answer['error'])) {
            throw new \Exception($answer['error'] ?? 'Erorr TurboSMS.');
        }

        if (!isset($answer['response_result']) || !$answer['response_result']) {
            $error = 'TurboSMS  response status: ' . ($answer['response_status'] ?? 'null');

            throw new \Exception($answer['response_status'] ?? $error);
        }

        $info = 'TurboSMS  response status: ' . ($answer['response_status'] ?? '');

        return [
            'success' => true,
            'result' => $answer['response_result'],
            'info' => $info,
        ];
    }
}