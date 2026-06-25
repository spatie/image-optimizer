<?php

namespace Spatie\ImageOptimizer;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class OptimizerChain
{
    /* @var \Spatie\ImageOptimizer\Optimizer[] */
    protected $optimizers = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var int */
    protected $timeout = 60;

    /** @var callable|null */
    protected $errorHandler;

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

    /*
     * Control what happens when an optimizer fails. By default (when this is never called) a
     * failing optimizer is logged and the chain continues. Pass true to instead rethrow and
     * abort the chain, false to keep the default swallow-and-continue, or a callable that
     * receives the exception, the optimizer and the image; return from it to continue or
     * throw to abort.
     *
     * Exceptions that were previously thrown straight out of optimize() - such as a
     * ProcessTimedOutException when an optimizer exceeds its timeout - now flow through here
     * too, so they are caught by default and can be inspected or rethrown via this handler.
     *
     * This only covers failures while applying an optimizer. The "Cannot copy file" exception
     * thrown by optimize() when the output file cannot be written happens before any optimizer
     * runs, so it always bubbles up regardless of this setting.
     */
    public function throws($handler = true)
    {
        if (! is_bool($handler) && ! is_callable($handler)) {
            throw new InvalidArgumentException('The handler passed to `throws()` must be a boolean or a callable.');
        }

        if ($handler === false) {
            $this->errorHandler = null;

            return $this;
        }

        if ($handler === true) {
            $handler = function (Throwable $exception) {
                throw $exception;
            };
        }

        $this->errorHandler = $handler;

        return $this;
    }

    public function optimize(string $pathToImage, ?string $pathToOutput = null)
    {
        if ($pathToOutput) {
            $check = copy($pathToImage, $pathToOutput);
            if ($check == false) {
                throw new InvalidArgumentException("Cannot copy file");
            }
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

        try {
            $this->runOptimizer($optimizer, $image);
        } catch (Throwable $exception) {
            $this->handleException($exception, $optimizer, $image);
        } finally {
            if (
                ($tmpPath = $optimizer->getTmpPath()) &&
                file_exists($tmpPath)
            ) {
                unlink($tmpPath);
            }
        }
    }

    protected function runOptimizer(Optimizer $optimizer, Image $image)
    {
        $command = $optimizer->getCommand();

        $this->logger->info("Executing `{$command}`");

        $process = Process::fromShellCommandline($command);

        $process
            ->setTimeout($this->timeout)
            ->run();

        $this->logResult($process);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function logResult(Process $process)
    {
        if (! $process->isSuccessful()) {
            $this->logger->error("Process errored with `{$process->getErrorOutput()}`");

            return;
        }

        $this->logger->info("Process successfully ended with output `{$process->getOutput()}`");
    }

    protected function handleException(Throwable $exception, Optimizer $optimizer, Image $image)
    {
        if ($this->errorHandler) {
            ($this->errorHandler)($exception, $optimizer, $image);
        }
    }
}
