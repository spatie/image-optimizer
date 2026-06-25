<?php

use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Test\ArrayLogger;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * A stub optimizer that handles every image and runs a command which exits
 * non-zero, so we can exercise the chain's failure handling without depending
 * on any real binary being installed.
 */
class FailingOptimizer implements Optimizer
{
    public function binaryName(): string
    {
        return 'false';
    }

    public function canHandle(Image $image): bool
    {
        return true;
    }

    public function setImagePath(string $imagePath)
    {
        return $this;
    }

    public function setOptions(array $options = [])
    {
        return $this;
    }

    public function getCommand(): string
    {
        return 'false';
    }

    public function getTmpPath(): ?string
    {
        return null;
    }
}

/**
 * A stub optimizer that handles every image and runs a command which exits
 * zero, used to assert that the chain keeps going after a previous failure.
 */
class SucceedingOptimizer implements Optimizer
{
    public function binaryName(): string
    {
        return 'true';
    }

    public function canHandle(Image $image): bool
    {
        return true;
    }

    public function setImagePath(string $imagePath)
    {
        return $this;
    }

    public function setOptions(array $options = [])
    {
        return $this;
    }

    public function getCommand(): string
    {
        return 'true';
    }

    public function getTmpPath(): ?string
    {
        return null;
    }
}

beforeEach(function () {
    $this->testImage = getTempFilePath('image.jpg');

    $this->optimizerChain = (new OptimizerChain())->useLogger($this->log);
});

it('does not throw by default and keeps optimizing after a failure', function () {
    $this
        ->optimizerChain
        ->setOptimizers([new FailingOptimizer(), new SucceedingOptimizer()])
        ->optimize($this->testImage);

    $logText = $this->log->getAllLinesAsString();

    expect($logText)
        ->toContain('Using optimizer: `FailingOptimizer`')
        ->toContain('Using optimizer: `SucceedingOptimizer`')
        ->toContain('error:');
});

it('aborts the chain when throws() is enabled', function () {
    $this
        ->optimizerChain
        ->throws()
        ->setOptimizers([new FailingOptimizer(), new SucceedingOptimizer()]);

    expect(fn () => $this->optimizerChain->optimize($this->testImage))
        ->toThrow(ProcessFailedException::class);

    expect($this->log->getAllLinesAsString())
        ->not->toContain('Using optimizer: `SucceedingOptimizer`');
});

it('keeps optimizing when throws(false) is set', function () {
    $this
        ->optimizerChain
        ->throws(false)
        ->setOptimizers([new FailingOptimizer(), new SucceedingOptimizer()])
        ->optimize($this->testImage);

    expect($this->log->getAllLinesAsString())
        ->toContain('Using optimizer: `SucceedingOptimizer`');
});

it('lets a custom handler swallow the failure and continue', function () {
    $captured = [];

    $this
        ->optimizerChain
        ->throws(function ($exception, $optimizer, $image) use (&$captured) {
            $captured = [$exception, $optimizer, $image];
        })
        ->setOptimizers([new FailingOptimizer(), new SucceedingOptimizer()])
        ->optimize($this->testImage);

    expect($captured[0])->toBeInstanceOf(ProcessFailedException::class);
    expect($captured[1])->toBeInstanceOf(FailingOptimizer::class);
    expect($captured[2])->toBeInstanceOf(Image::class);
    expect($this->log->getAllLinesAsString())
        ->toContain('Using optimizer: `SucceedingOptimizer`');
});

it('lets a custom handler abort by throwing', function () {
    $this
        ->optimizerChain
        ->throws(function () {
            throw new RuntimeException('aborted');
        })
        ->setOptimizers([new FailingOptimizer(), new SucceedingOptimizer()]);

    expect(fn () => $this->optimizerChain->optimize($this->testImage))
        ->toThrow(RuntimeException::class, 'aborted');

    expect($this->log->getAllLinesAsString())
        ->not->toContain('Using optimizer: `SucceedingOptimizer`');
});

it('rejects a throws() handler that is not a boolean or callable', function () {
    expect(fn () => $this->optimizerChain->throws('nope'))
        ->toThrow(InvalidArgumentException::class);
});

it('logs the exact same lines regardless of the throws() mode', function () {
    // These are the log lines the chain produced before this PR existed: a failing
    // optimizer is logged via logResult() and the chain moves on. `throws()` must only
    // change whether the chain continues or aborts, never what gets logged.
    $expectedLines = [
        "info: Start optimizing {$this->testImage}",
        'info: Using optimizer: `FailingOptimizer`',
        'info: Executing `false`',
        'error: Process errored with ``',
    ];

    $logsFor = function ($throws) {
        $logger = new ArrayLogger();

        $chain = (new OptimizerChain())
            ->useLogger($logger)
            ->setOptimizers([new FailingOptimizer()]);

        // `null` means never call throws() at all (the pre-PR / backwards-compatible default).
        if ($throws !== null) {
            $chain->throws($throws);
        }

        try {
            $chain->optimize($this->testImage);
        } catch (Throwable $exception) {
            // throws(true) and an aborting callback rethrow *after* logging; the log is
            // what we assert on here, so swallow the abort.
        }

        return $logger->getAllLines();
    };

    expect($logsFor(null))->toBe($expectedLines);          // default (pre-PR baseline)
    expect($logsFor(false))->toBe($expectedLines);         // throws(false)
    expect($logsFor(true))->toBe($expectedLines);          // throws(true)
    expect($logsFor(fn () => null))->toBe($expectedLines); // callback that continues
    expect($logsFor(function () {                          // callback that aborts
        throw new RuntimeException('stop');
    }))->toBe($expectedLines);
});

it('passes a ProcessFailedException carrying the failed process to the handler', function () {
    $captured = null;

    $this
        ->optimizerChain
        ->throws(function ($exception) use (&$captured) {
            $captured = $exception;
        })
        ->setOptimizers([new FailingOptimizer()])
        ->optimize($this->testImage);

    expect($captured)->toBeInstanceOf(ProcessFailedException::class);
    expect($captured->getProcess()->isSuccessful())->toBeFalse();
});
