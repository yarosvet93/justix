<?php

require_once __DIR__ . '/app/env.php';
require_once __DIR__ . '/app/TelegramClient.php';

function stickerDigits(string $value): string
{
    return strtr($value, [
        '0' => '0⃣',
        '1' => '1⃣',
        '2' => '2⃣',
        '3' => '3⃣',
        '4' => '4⃣',
        '5' => '5⃣',
        '6' => '6⃣',
        '7' => '7⃣',
        '8' => '8⃣',
        '9' => '9⃣',
    ]);
}

function stickerDate(): string
{
    return implode('   ', [
        stickerDigits(date('d')),
        stickerDigits(date('m')),
        stickerDigits(date('Y')),
    ]);
}

$env = env_load(__DIR__ . '/.env');

$botToken = $env['TELEGRAM_BOT_TOKEN'] ?? '';
$chatId = $env['TELEGRAM_TEST_CHAT_ID'] ?? '';
$proxyUrl = $env['TELEGRAM_PROXY_URL'] ?? '';
$messageThreadId = isset($env['TELEGRAM_MESSAGE_THREAD_ID']) && $env['TELEGRAM_MESSAGE_THREAD_ID'] !== ''
    ? (int) $env['TELEGRAM_MESSAGE_THREAD_ID']
    : null;

if ($chatId === '') {
    throw new RuntimeException('TELEGRAM_CHAT_ID is empty');
}

$bot = new TelegramClient($botToken, $proxyUrl);
$question = implode("\n", [
    stickerDate(),
    '🛡 Стенка',
    '⏰ ' . ($argv[1] ?? date('H:i')),
]);

try {
    $result = $bot->sendPoll(
        $chatId,
        $question,
        [
            'Да',
            'Нет',
            'Позже',
        ],
        false,
        false
        #$messageThreadId
    );

    echo "Poll sent successfully\n";
    echo "Question:\n{$question}\n";
    echo "Message ID: " . ($result['result']['message_id'] ?? 'unknown') . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
