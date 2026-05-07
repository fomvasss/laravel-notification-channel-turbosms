<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class TurboSmsApi
{
    protected HttpClient $client;
    protected bool $isTest;

    protected string $baseUri = 'https://api.turbosms.ua/';

    public function __construct(
        protected string $apiToken,
        protected string $smsSender,
        array $configs = [],
    ) {
        $this->isTest = (bool) ($configs['is_test'] ?? false);

        $this->client = new HttpClient([
            'timeout' => (int) ($configs['timeout'] ?? 15),
            'connect_timeout' => (int) ($configs['connect_timeout'] ?? 10),
        ]);
    }

    /**
     * Send an SMS message to a recipient.
     *
     * @throws \RuntimeException|GuzzleException
     */
    public function sendMessage(string $recipient, TurboSmsMessage $message): array
    {
        $url = $this->baseUri . 'message/send.json';

        $body = [
            'recipients' => [$recipient],
            'sms' => [
                'sender' => $message->from ?? $this->smsSender,
                'text' => $message->content,
            ],
        ];

        if (! is_null($message->test)) {
            $this->isTest = $message->test;
        }

        if (! is_null($message->time)) {
            $body['start_time'] = $message->time;
        }

        return $this->getResponse($url, $body);
    }

    /**
     * Get the account balance.
     *
     * @throws \RuntimeException|GuzzleException
     */
    public function getBalance(): ?float
    {
        $url = $this->baseUri . 'user/balance.json';

        $res = $this->getResponse($url);

        if (isset($res['success']) && $res['success']) {
            return isset($res['result']['balance']) ? (float) $res['result']['balance'] : null;
        }

        return null;
    }

    /**
     * Perform a POST request to the TurboSMS API.
     *
     * @throws \RuntimeException|GuzzleException
     */
    public function getResponse(string $url, array $body = []): array
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
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
            'json' => $body,
        ]);

        $answer = json_decode((string) $response->getBody(), true);

        if (! is_array($answer)) {
            throw new \RuntimeException('TurboSMS returned an invalid JSON response.');
        }

        if (isset($answer['error'])) {
            throw new \RuntimeException($answer['error']);
        }

        if (empty($answer['response_result'])) {
            $status = $answer['response_status'] ?? 'unknown';
            throw new \RuntimeException('TurboSMS response status: ' . $status);
        }

        return [
            'success' => true,
            'result' => $answer['response_result'],
            'info' => 'TurboSMS response status: ' . ($answer['response_status'] ?? ''),
        ];
    }
}
