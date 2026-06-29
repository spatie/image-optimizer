<?php

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\BaseSelfHandlingOptimizer;

/**
 * A self-handling optimizer that handles every image and records the image it was
 * run with, so we can assert the chain delegated execution to it.
 */
class RecordingSelfHandlingOptimizer extends BaseSelfHandlingOptimizer
{
    public $ranWith = null;

    public $runCount = 0;

    public function canHandle(Image $image): bool
    {
        return true;
    }

    public function handle(Image $image, LoggerInterface $logger): void
    {
        $this->ranWith = $image;
        $this->runCount++;

        $logger->info('Optimizing via API');
    }
}

/**
 * A self-handling optimizer whose handle() throws, to exercise the chain's failure
 * handling for the commandless path.
 */
class ThrowingSelfHandlingOptimizer extends BaseSelfHandlingOptimizer
{
    public function canHandle(Image $image): bool
    {
        return true;
    }

    public function handle(Image $image, LoggerInterface $logger): void
    {
        throw new RuntimeException('self-handling boom');
    }
}

/**
 * A self-handling optimizer that never handles the image, to assert handle() is skipped.
 */
class NonHandlingSelfHandlingOptimizer extends BaseSelfHandlingOptimizer
{
    public $runCount = 0;

    public function canHandle(Image $image): bool
    {
        return false;
    }

    public function handle(Image $image, LoggerInterface $logger): void
    {
        $this->runCount++;
    }
}

/**
 * A self-handling optimizer that exposes a real temp file so we can assert the chain
 * cleans it up after handle(), even when handle() throws.
 */
class TmpFileSelfHandlingOptimizer extends BaseSelfHandlingOptimizer
{
    public $shouldThrow = false;

    public function canHandle(Image $image): bool
    {
        return true;
    }

    public function handle(Image $image, LoggerInterface $logger): void
    {
        if ($this->shouldThrow) {
            throw new RuntimeException('self-handling boom');
        }
    }
}

/**
 * A plain binary-style optimizer running a command that exits zero, used to
 * assert the binary optimizer flow is unchanged when mixed with a self-handling optimizer.
 */
class SucceedingBinaryOptimizer implements Optimizer
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

it('delegates execution to a self-handling optimizer and passes it the image', function () {
    $optimizer = new RecordingSelfHandlingOptimizer();

    $this
        ->optimizerChain
        ->setOptimizers([$optimizer])
        ->optimize($this->testImage);

    expect($optimizer->runCount)->toBe(1);
    expect($optimizer->ranWith)->toBeInstanceOf(Image::class);
    expect($optimizer->ranWith->path())->toBe($this->testImage);

    expect($this->log->getAllLinesAsString())
        ->toContain('Using optimizer: `RecordingSelfHandlingOptimizer`')
        ->toContain('Executing `RecordingSelfHandlingOptimizer`')
        ->toContain('Optimizing via API');
});

it('does not run a self-handling optimizer that cannot handle the image', function () {
    $optimizer = new NonHandlingSelfHandlingOptimizer();

    $this
        ->optimizerChain
        ->setOptimizers([$optimizer])
        ->optimize($this->testImage);

    expect($optimizer->runCount)->toBe(0);

    expect($this->log->getAllLinesAsString())
        ->not->toContain('Using optimizer: `NonHandlingSelfHandlingOptimizer`');
});

it('does not throw by default when a self-handling optimizer fails', function () {
    $this
        ->optimizerChain
        ->setOptimizers([new ThrowingSelfHandlingOptimizer(), new RecordingSelfHandlingOptimizer()])
        ->optimize($this->testImage);

    expect($this->log->getAllLinesAsString())
        ->toContain('Using optimizer: `ThrowingSelfHandlingOptimizer`')
        ->toContain('error: Optimizer errored with `self-handling boom`')
        ->toContain('Using optimizer: `RecordingSelfHandlingOptimizer`');
});

it('aborts the chain when a self-handling optimizer fails and throws() is enabled', function () {
    $this
        ->optimizerChain
        ->throws()
        ->setOptimizers([new ThrowingSelfHandlingOptimizer(), new RecordingSelfHandlingOptimizer()]);

    expect(fn () => $this->optimizerChain->optimize($this->testImage))
        ->toThrow(RuntimeException::class, 'self-handling boom');

    expect($this->log->getAllLinesAsString())
        ->not->toContain('Using optimizer: `RecordingSelfHandlingOptimizer`');
});

it('routes a self-handling optimizer failure to a custom handler', function () {
    $captured = [];

    $this
        ->optimizerChain
        ->throws(function ($exception, $optimizer, $image) use (&$captured) {
            $captured = [$exception, $optimizer, $image];
        })
        ->setOptimizers([new ThrowingSelfHandlingOptimizer()])
        ->optimize($this->testImage);

    expect($captured[0])->toBeInstanceOf(RuntimeException::class);
    expect($captured[1])->toBeInstanceOf(ThrowingSelfHandlingOptimizer::class);
    expect($captured[2])->toBeInstanceOf(Image::class);
});

it('cleans up a self-handling optimizer temp file after running', function () {
    $optimizer = new TmpFileSelfHandlingOptimizer();
    $optimizer->tmpPath = tempnam(sys_get_temp_dir(), 'selfhandling');

    expect(file_exists($optimizer->tmpPath))->toBeTrue();

    $this
        ->optimizerChain
        ->setOptimizers([$optimizer])
        ->optimize($this->testImage);

    expect(file_exists($optimizer->tmpPath))->toBeFalse();
});

it('cleans up a self-handling optimizer temp file even when handle() throws', function () {
    $optimizer = new TmpFileSelfHandlingOptimizer();
    $optimizer->shouldThrow = true;
    $optimizer->tmpPath = tempnam(sys_get_temp_dir(), 'selfhandling');

    expect(file_exists($optimizer->tmpPath))->toBeTrue();

    $this
        ->optimizerChain
        ->setOptimizers([$optimizer])
        ->optimize($this->testImage);

    expect(file_exists($optimizer->tmpPath))->toBeFalse();
});

it('runs binary and self-handling optimizers together, leaving the binary optimizer flow unchanged', function () {
    $selfHandling = new RecordingSelfHandlingOptimizer();

    $this
        ->optimizerChain
        ->setOptimizers([new SucceedingBinaryOptimizer(), $selfHandling])
        ->optimize($this->testImage);

    expect($selfHandling->runCount)->toBe(1);

    expect($this->log->getAllLinesAsString())
        ->toContain('Using optimizer: `SucceedingBinaryOptimizer`')
        ->toContain('Executing `true`')
        ->toContain('Using optimizer: `RecordingSelfHandlingOptimizer`');
});
