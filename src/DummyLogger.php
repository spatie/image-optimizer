<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;

class DummyLogger implements LoggerInterface
{
    public function emergency($message, array $context = []): void
    {
    }

    public function alert($message, array $context = []): void
    {
    }

    public function critical($message, array $context = []): void
    {
    }

    public function error($message, array $context = []): void
    {
    }

    public function warning($message, array $context = []): void
    {
    }

    public function notice($message, array $context = []): void
    {
    }

    public function info($message, array $context = []): void
    {
    }

    public function debug($message, array $context = []): void
    {
    }

    public function log($level, $message, array $context = []): void
    {
    }
}
