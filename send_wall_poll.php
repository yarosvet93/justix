<?php

require_once __DIR__ . '/app/env.php';
require_once __DIR__ . '/app/TelegramClient.php';

$env = env_load(__DIR__ . '/.env');

$botToken = $env['TELEGRAM_BOT_TOKEN'] ?? '';
$chatId = $env['TELEGRAM_CHAT_ID'] ?? '';
$proxyUrl = $env['TELEGRAM_PROXY_URL'] ?? '';

if ($chatId === '') {
    throw new RuntimeException('TELEGRAM_CHAT_ID is empty');
}

$wallTime = $argv[1] ?? '';

if ($wallTime === '') {
    echo "Usage: php send_wall_poll.php HH:MM\n";
    exit(1);
}

$bot = new TelegramClient($botToken, $proxyUrl);

$question = "Стенка в {$wallTime}. Кто участвует?";

$options = [
    'Буду',
    'Не буду',
    'Под вопросом',
];

try {
    $result = $bot->sendPoll(
        $chatId,
        $question,
        $options,
        false,
        false
    );

    echo date('Y-m-d H:i:s') . " Poll sent successfully for {$wallTime}\n";
    echo "Message ID: " . ($result['result']['message_id'] ?? 'unknown') . "\n";
} catch (Throwable $e) {
    echo date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n";
    exit(1);
}
