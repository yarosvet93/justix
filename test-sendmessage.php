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

$message = $argv[1] ?? 'Тестовое сообщение';

$bot = new TelegramClient($botToken, $proxyUrl);

try {
    $result = $bot->sendMessage($chatId, $message, [], $messageThreadId);

    echo date('Y-m-d H:i:s') . " Message sent successfully\n";
    if ($messageThreadId !== null) {
        echo "Thread ID: {$messageThreadId}\n";
    }
    echo "Message ID: " . ($result['result']['message_id'] ?? 'unknown') . "\n";
} catch (Throwable $e) {
    echo date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n";
    exit(1);
}
