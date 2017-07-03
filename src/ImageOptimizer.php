<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Symfony\Component\Process\Process;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optimizer;

class ImageOptimizer
{
    public $optimizers = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct()
    {
        $this->useLogger(new DummyLogger());
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
        $this->logger->info("start optimizing {$imagePath}");

        $mimeType = mime_content_type($imagePath);

        collect($this->optimizers)
            ->filter(function (Optimizer $optimizer) use ($mimeType) {
                return $optimizer->canHandle($mimeType);
            })
            ->each(function (Optimizer $optimizer) use ($imagePath) {

                $optimizerClass = get_class($optimizer);

                $this->logger->info("Using optimizer: `{$optimizerClass}`");

                $optimizer->setImagePath($imagePath);

                $command = $optimizer->getCommand();

                $this->logger->info("Executing `{$command}`");

                $process = new Process($optimizer->getCommand());

                $process->run();

                $this->logResult($process);
            });
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
