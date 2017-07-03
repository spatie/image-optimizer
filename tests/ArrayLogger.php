<?php

namespace Spatie\ImageOptimizer\Test;

use Psr\Log\LoggerInterface;

class ArrayLogger implements LoggerInterface
{
    protected $logLines = [];

    public function emergency($message, array $context = [])
    {
        $this->logLines[] = "emergency: {$message}";
    }

    public function alert($message, array $context = [])
    {
        $this->logLines[] = "alert: {$message}";
    }

    public function critical($message, array $context = [])
    {
        $this->logLines[] = "critical: {$message}";
    }

    public function error($message, array $context = [])
    {
        $this->logLines[] = "error: {$message}";
    }

    public function warning($message, array $context = [])
    {
        $this->logLines[] = "warning: {$message}";
    }

    public function notice($message, array $context = [])
    {
        $this->logLines[] = "notice: {$message}";
    }

    public function info($message, array $context = [])
    {
        $this->logLines[] = "info: {$message}";
    }

    public function debug($message, array $context = [])
    {
        $this->logLines[] = "debug: {$message}";
    }

    public function log($level, $message, array $context = [])
    {
        $this->logLines[] = "log: {$message}";
    }

    public function getAllLines(): array
    {
        return $this->logLines;
    }
}
