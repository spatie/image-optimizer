<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class OptimizerChain
{
    /* @var \Spatie\ImageOptimizer\Optimizer[] */
    protected $optimizers = [];

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

        if ($this->is_cli()) {
            $process = new Process($command);
            } else {
            $process = Process::fromShellCommandline($command);
        }
        
        $process
            ->setTimeout($this->timeout)
            ->run();

        $this->logResult($process);
    }
     
    /*
     * Confirms cli execution environment with no server, cron, or cgi context
     */
    protected function isCli() {

        return (! isset($_SERVER['SERVER_SOFTWARE']) && 
        (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && 
        $_SERVER['argc'] > 0)));
    }

    protected function logResult(Process $process)
    {
        if (! $process->isSuccessful()) {
            $this->logger->error("Process errored with `{$process->getErrorOutput()}`");

            return;
        }

        $this->logger->info("Process successfully ended with output `{$process->getOutput()}`");
    }
}
