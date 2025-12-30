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

    public function __construct(string $apiToken, string $sender, array $configs = [])
    {
        $this->apiToken = $apiToken;
        $this->smsSender = $sender;
        $this->isTest = $configs['is_test'] ?? false;
        
        $this->client = new HttpClient([
            'timeout' => intval($configs['timeout'] ?? 15),
            'connect_timeout' => intval($configs['connect_timeout'] ?? 10),
        ]);
    }

    /**
     * @param $recipient
     * @param TurboSmsMessage $message
     * @return array
     */
    public function sendMessage(string $recipient, TurboSmsMessage $message)
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
     * @return float|null
     * @throws \Exception
     */
    public function getBalance(): float|null
    {
        $url = $this->baseUri . 'user/balance.json';

        $res = $this->getResponse($url);
        
        if (isset($res['success']) && $res['success']) {
            return $res['result']['balance'] ?? null;
        }

        return null;
    }

    /**
     * @param string $url
     * @param array $body
     * @return array
     */
    public function getResponse(string $url, array $body = [])
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
