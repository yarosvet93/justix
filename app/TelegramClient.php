<?php

class TelegramClient
{
    private string $token;
    private string $proxyUrl;

    public function __construct(string $token, string $proxyUrl = '')
    {
        if ($token === '') {
            throw new RuntimeException('Telegram bot token is empty');
        }

        $this->token = $token;
        $this->proxyUrl = $proxyUrl;
    }

    public function request(string $method, array $params): array
    {
        $url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;

        $curlOptions = [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
        ];

        if ($this->proxyUrl !== '') {
            $curlOptions[CURLOPT_PROXY] = $this->proxyUrl;
        }

        $ch = curl_init($url);

        if ($ch === false) {
            throw new RuntimeException('curl_init failed');
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new RuntimeException("Curl error: {$error}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new RuntimeException("Invalid Telegram response. HTTP {$httpCode}: {$response}");
        }

        if (($data['ok'] ?? false) !== true) {
            $description = $data['description'] ?? $response;

            throw new RuntimeException("Telegram error. HTTP {$httpCode}: {$description}");
        }

        return $data;
    }

    public function sendMessage(string $chatId, string $text, array $replyMarkup = [], ?int $messageThreadId = null): array
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($messageThreadId !== null) {
            $params['message_thread_id'] = $messageThreadId;
        }

        if ($replyMarkup !== []) {
            $params['reply_markup'] = $replyMarkup;
        }

        return $this->request('sendMessage', $params);
    }

    public function sendPoll(
        string $chatId,
        string $question,
        array $options,
        bool $anonymous = false,
        bool $multipleAnswers = false,
        ?int $messageThreadId = null
    ): array {
        if (count($options) < 2) {
            throw new RuntimeException('Poll must have at least 2 options');
        }

        $params = [
            'chat_id' => $chatId,
            'question' => $question,
            'options' => $options,
            'is_anonymous' => $anonymous,
            'allows_multiple_answers' => $multipleAnswers,
        ];

        if ($messageThreadId !== null) {
            $params['message_thread_id'] = $messageThreadId;
        }

        return $this->request('sendPoll', $params);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): array
    {
        return $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]);
    }
}
