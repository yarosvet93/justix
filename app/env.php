<?php

function env_load(string $path): array
{
    if (!file_exists($path)) {
        throw new RuntimeException("Env file not found: {$path}");
    }

    $env = [];

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $env[trim($key)] = trim($value);
    }

    return $env;
}
