<?php

namespace Spatie\ImageOptimizer\Test;

use Psr\Log\LoggerInterface;

class ArrayLogger implements LoggerInterface
{
    public $log = [];

    public function emergency($message, array $context = array())
    {
        $log[] = "emergency: {$message}";
    }

    public function alert($message, array $context = array())
    {
        $log[] = "alert: {$message}";
    }

    public function critical($message, array $context = array())
    {
        $log[] = "critical: {$message}";
    }

    public function error($message, array $context = array())
    {
        $log[] = "error: {$message}";
    }

    public function warning($message, array $context = array())
    {
        $log[] = "warning: {$message}";
    }

    public function notice($message, array $context = array())
    {
        $log[] = "notice: {$message}";
    }

    public function info($message, array $context = array())
    {
        $log[] = "info: {$message}";
    }

    public function debug($message, array $context = array())
    {
        $log[] = "debug: {$message}";
    }

    public function log($level, $message, array $context = array())
    {
        $log[] = "log: {$message}";
    }
}