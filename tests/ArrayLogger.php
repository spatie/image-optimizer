<?php

namespace Spatie\ImageOptimizer\Test;

use Psr\Log\LoggerInterface;

class ArrayLogger implements LoggerInterface
{
    protected $logLines = [];

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "emergency: {$message}";
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "alert: {$message}";
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "critical: {$message}";
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "error: {$message}";
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "warning: {$message}";
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "notice: {$message}";
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "info: {$message}";
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "debug: {$message}";
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->logLines[] = "log: {$message}";
    }

    public function getAllLines(): array
    {
        return $this->logLines;
    }

    public function getAllLinesAsString(): string
    {
        return implode(PHP_EOL, $this->getAllLines());
    }
}
