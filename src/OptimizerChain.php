<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Exceptions\OptimizerNotInstalledException;
use Symfony\Component\Process\Process;

class OptimizerChain
{
    /* @var \Spatie\ImageOptimizer\Optimizer[] */
    protected $optimizers = [];

    protected $optimized = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var int */
    protected $timeout = 60;

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

    /*
     * Set the amount of seconds each separate optimizer may use.
     */
    public function setTimeout(int $timeoutInSeconds)
    {
        $this->timeout = $timeoutInSeconds;

        return $this;
    }

    public function useLogger(LoggerInterface $log)
    {
        $this->logger = $log;

        return $this;
    }

    public function optimize(string $pathToImage, string $pathToOutput = null)
    {
        if ($pathToOutput) {
            copy($pathToImage, $pathToOutput);

            $pathToImage = $pathToOutput;
        }

        $image = new Image($pathToImage);

        $this->logger->info("Start optimizing {$pathToImage}");

        foreach ($this->optimizers as $optimizer) {
            $this->applyOptimizer($optimizer, $image);
        }

        return $this->optimized;
    }

    protected function applyOptimizer(Optimizer $optimizer, Image $image)
    {
        if (! $optimizer->canHandle($image)) {
            return;
        }

        $optimizerClass = get_class($optimizer);

        $this->logger->info("Using optimizer: `{$optimizerClass}`");

        $optimizer->setImagePath($image->path());

        $command = $optimizer->getCommand();

        $this->logger->info("Executing `{$command}`");

        $process = Process::fromShellCommandline($command);

        $process
            ->setTimeout($this->timeout)
            ->run();

        $this->logResult($process, $optimizer);
    }

    protected function logResult(Process $process, Optimizer $optimizer)
    {
        if (! $process->isSuccessful()) {
            $optimized = false;

            $this->logger->error("Process errored with `{$process->getErrorOutput()}`");

            if (strpos($process->getErrorOutput(), $optimizer->binaryName() . ': ' . strtolower(Process::$exitCodes[127])) !== false)
            {
                throw new OptimizerNotInstalledException("Optimized not installed!");

                return;
            }
        }else{
            $optimized = true;

            $this->logger->info("Process successfully ended with output `{$process->getOutput()}`");
        }

        $this->setOptimizerStatus($optimizer, $optimized);

        return;
    }

    private function setOptimizerStatus(Optimizer $optimizer, $status)
    {
        $this->optimized[$optimizer->binaryName()] = $status;
    }
}
