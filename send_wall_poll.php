<?php

require_once __DIR__ . '/app/env.php';
require_once __DIR__ . '/app/TelegramClient.php';

$env = env_load(__DIR__ . '/.env');

$botToken = $env['TELEGRAM_BOT_TOKEN'] ?? '';
$chatId = $env['TELEGRAM_CHAT_ID'] ?? '';
$proxyUrl = $env['TELEGRAM_PROXY_URL'] ?? '';
$messageThreadId = isset($env['TELEGRAM_MESSAGE_THREAD_ID']) && $env['TELEGRAM_MESSAGE_THREAD_ID'] !== ''
    ? (int) $env['TELEGRAM_MESSAGE_THREAD_ID']
    : null;

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
$disableNotification = in_array($wallTime, ['02:00', '06:00'], true);

$options = [
    'Могу собрать',
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
        false,
        $messageThreadId,
        $disableNotification
    );

    echo date('Y-m-d H:i:s') . " Poll sent successfully for {$wallTime}\n";
    echo "Disable notification: " . ($disableNotification ? 'yes' : 'no') . "\n";
    echo "Message ID: " . ($result['result']['message_id'] ?? 'unknown') . "\n";
} catch (Throwable $e) {
    echo date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n";
    exit(1);
}
