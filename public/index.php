<?php

http_response_code(200);
header('Content-Type: text/html; charset=utf-8');

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Justix Bot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #111;
            color: #eee;
        }

        .box {
            max-width: 720px;
            padding: 24px;
            border: 1px solid #333;
            border-radius: 12px;
            background: #181818;
        }

        h1 {
            margin-top: 0;
        }

        code {
            color: #9cdcfe;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Justix Bot</h1>
        <p>Сервис работает.</p>
        <p>Telegram-бот используется для опросов клана Justix.</p>
        <p><code>public/index.php</code></p>
    </div>
</body>
</html>
