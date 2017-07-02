<?php

namespace Spatie\ImageOptimizer\Test;

use Psr\Log\LoggerInterface;

class ArrayLogger implements LoggerInterface
{
    public $log = [];

    public function emergency($message, array $context = [])
    {
        $log[] = "emergency: {$message}";
    }

    public function alert($message, array $context = [])
    {
        $log[] = "alert: {$message}";
    }

    public function critical($message, array $context = [])
    {
        $log[] = "critical: {$message}";
    }

    public function error($message, array $context = [])
    {
        $log[] = "error: {$message}";
    }

    public function warning($message, array $context = [])
    {
        $log[] = "warning: {$message}";
    }

    public function notice($message, array $context = [])
    {
        $log[] = "notice: {$message}";
    }

    public function info($message, array $context = [])
    {
        $log[] = "info: {$message}";
    }

    public function debug($message, array $context = [])
    {
        $log[] = "debug: {$message}";
    }

    public function log($level, $message, array $context = [])
    {
        $log[] = "log: {$message}";
    }
}
