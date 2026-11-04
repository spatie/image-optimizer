<?php

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Avifenc;
use Symfony\Component\Process\Exception\ProcessFailedException;

it('reserves the decoder output until the command runs', function () {
    $optimizer = (new Avifenc())->setImagePath(getTestFilePath('image.avif'));

    try {
        $optimizer->getCommand();

        expect($optimizer->getTmpPath())->toEndWith('.png');
        expect(is_file($optimizer->getTmpPath()))->toBeTrue();
        expect(fileperms($optimizer->getTmpPath()) & 0777)->toBe(0600);
    } finally {
        if (is_file($optimizer->getTmpPath())) {
            unlink($optimizer->getTmpPath());
        }

        $basePath = substr($optimizer->getTmpPath(), 0, -4);

        if (is_file($basePath)) {
            unlink($basePath);
        }
    }
});

it('optimizes AVIF images without leaving temporary files', function () {
    $image = getTempFilePath('image.avif');
    $optimizer = new Avifenc(['--min 40', '--max 40', '--speed 10', '-j 1']);
    $existingFiles = glob(sys_get_temp_dir().'/avifdec*');

    try {
        (new OptimizerChain())->useLogger($this->log)->addOptimizer($optimizer)->throws()->optimize($image);

        $this->assertOptimizersUsed(Avifenc::class);
        $this->assertDecreasedFileSize($image, getTestFilePath('image.avif'));
        expect((new finfo(FILEINFO_MIME_TYPE))->file($image))->toBe('image/avif');
        expect(glob(sys_get_temp_dir().'/avifdec*'))->toBe($existingFiles);
    } finally {
        $basePath = substr($optimizer->getTmpPath(), 0, -4);

        if (is_file($basePath)) {
            unlink($basePath);
        }
    }
});

it('cleans up after an AVIF command fails without touching existing files', function (string $failure, bool $throws) {
    $image = getTempFilePath('image.avif');
    $original = file_get_contents($image);
    $optimizer = new Avifenc();

    if ($failure === 'decoder missing') {
        $optimizer->decodeBinaryName = 'missing-avifdec-for-cleanup-test';
    }

    if ($failure === 'encoder missing') {
        $optimizer->binaryName = 'missing-avifenc-for-cleanup-test';
    }

    if ($failure === 'encoder error') {
        $optimizer->setOptions(['--invalid-option-for-cleanup-test']);
    }

    $unrelatedFile = tempnam(sys_get_temp_dir(), 'avifdec');
    file_put_contents($unrelatedFile, 'unrelated file');
    $existingFiles = glob(sys_get_temp_dir().'/avifdec*');
    $chain = (new OptimizerChain())->useLogger($this->log)->addOptimizer($optimizer)->throws($throws);

    try {
        if ($throws) {
            expect(fn () => $chain->optimize($image))->toThrow(ProcessFailedException::class);
        } else {
            $chain->optimize($image);
        }

        expect($this->log->getAllLinesAsString())->toContain('error:');
        expect(file_get_contents($image))->toBe($original);
        expect(file_get_contents($unrelatedFile))->toBe('unrelated file');
        expect(glob(sys_get_temp_dir().'/avifdec*'))->toBe($existingFiles);
    } finally {
        unlink($unrelatedFile);
        $basePath = substr($optimizer->getTmpPath(), 0, -4);

        if (is_file($basePath)) {
            unlink($basePath);
        }
    }
})->with(['decoder missing', 'encoder missing', 'encoder error'])->with([false, true]);

it('does not allocate AVIF temporary files for other image types', function () {
    $image = getTempFilePath('image.jpg');
    $original = file_get_contents($image);
    $optimizer = new Avifenc();

    (new OptimizerChain())->addOptimizer($optimizer)->throws()->optimize($image);

    expect($optimizer->getTmpPath())->toBeNull();
    expect(file_get_contents($image))->toBe($original);
});
