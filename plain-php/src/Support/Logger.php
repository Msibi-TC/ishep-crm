<?php

namespace Ishep\Support;

final class Logger
{
    public function __construct(private string $file) {}
    public function log(string $level, string $message, array $context = []): void
    {
        $redact = static fn($k) => preg_match('/password|token|secret|key|credential/i', (string) $k);
        foreach ($context as $key => $value) { if ($redact($key)) $context[$key] = '[REDACTED]'; elseif (is_string($value)) $context[$key] = preg_replace('/(password|token|secret|key|credential)(\s*[=:]\s*)[^\s;]+/i', '$1$2[REDACTED]', $value); }
        $row = json_encode(['time' => gmdate('c'), 'level' => $level, 'message' => $message, 'context' => $context], JSON_UNESCAPED_SLASHES).PHP_EOL;
        @file_put_contents($this->file, $row, FILE_APPEND | LOCK_EX);
    }
}
