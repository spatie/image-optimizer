<?php

use function PHPUnit\Framework\assertFileEquals;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

use Spatie\ImageOptimizer\Optimizers\Svgo;

beforeEach(function () {
    $this->optimizerChain = OptimizerChainFactory::create()
            ->useLogger($this->log);
});

it('can optimize a jpg', function () {
    $tempFilePath = getTempFilePath('image.jpg');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('image.jpg'));

    $this->assertOptimizersUsed(Jpegoptim::class);
});

it('can optimize a png', function () {
    $tempFilePath = getTempFilePath('logo.png');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('logo.png'));

    $this->assertOptimizersUsed([
        Optipng::class,
        Pngquant::class,
    ]);
});

it('can optimize an svg', function () {
    $tempFilePath = getTempFilePath('graph.svg');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertOptimizersUsed(Svgo::class);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('graph.svg'));
});

it('can optimize a gif', function () {
    $tempFilePath = getTempFilePath('animated.gif');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('animated.gif'));

    $this->assertOptimizersUsed(Gifsicle::class);
});

it('can optimize a webp', function () {
    $tempFilePath = getTempFilePath('image.webp');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('image.webp'));

    $this->assertOptimizersUsed(Cwebp::class);
});

it('will not not touch a non image file', function () {
    $tempFilePath = getTempFilePath('test.txt');

    $originalContent = file_get_contents($tempFilePath);

    $this->optimizerChain->optimize($tempFilePath);

    $optimizedContent = file_get_contents($tempFilePath);

    expect($originalContent)
        ->toBe($optimizedContent);
});

it('can output to a specified path', function () {
    $tempFilePath = getTempFilePath('logo.png');
    $outputFilePath = __DIR__ . '/temp/output.png';

    $this->optimizerChain->optimize($tempFilePath, $outputFilePath);

    assertFileEquals($tempFilePath, getTestFilePath('logo.png'));

    $this->assertDecreasedFileSize($outputFilePath, getTestFilePath('logo.png'));

    $this->assertOptimizersUsed([
        Optipng::class,
        Pngquant::class,
    ]);
});
