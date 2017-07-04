<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Spatie\ImageOptimizer\Optimizers\Optimizer;

class ImageOptimizer
{
    protected $optimizers = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct()
    {
        $this->useLogger(new DummyLogger());
    }

    public function getOptimizers(): array
    {
        return $this->optimizers;
    }

    public function addOptimizer(Optimizer $optimizer)
    {
        $this->optimizers[] = $optimizer;

        return $this;
    }

    public function setOptimizers(array $optimizers)
    {
        $this->optimizers = [];

        foreach ($optimizers as $optimizer) {
            $this->addOptimizer($optimizer);
        }

        return $this;
    }

    public function useLogger(LoggerInterface $log)
    {
        $this->logger = $log;

        return $this;
    }

    public function optimize(string $imagePath)
    {
        $this->logger->info("Start optimizing {$imagePath}");

        $mimeType = mime_content_type($imagePath);

        $optimizers = array_filter($this->optimizers, function (Optimizer $optimizer) use ($mimeType) {
            return $optimizer->canHandle($mimeType);
        });

        foreach ($optimizers as $optimizer) {
            $optimizerClass = get_class($optimizer);

            $this->logger->info("Using optimizer: `{$optimizerClass}`");

            $optimizer->setImagePath($imagePath);

            $command = $optimizer->getCommand();

            $this->logger->info("Executing `{$command}`");

            $process = new Process($command);

            $process->run();

            $this->logResult($process);
        }
    }

    public function logResult(Process $process)
    {
        if ($process->isSuccessful()) {
            $this->logger->info("Process successfully ended with output `{$process->getOutput()}`");

            return;
        }

        $this->logger->error("Process errored with `{$process->getErrorOutput()}`}");
    }
}
