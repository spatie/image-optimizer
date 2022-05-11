<?php

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

beforeEach(function () {
    $this->optimizerChain = new OptimizerChain();
});

it('will not throw an exception when not using a logger', function () {
    $testImage = getTempFilePath('image.jpg');

    $this
        ->optimizerChain
        ->addOptimizer(new Jpegoptim())
        ->optimize($testImage);

    $this->assertDecreasedFileSize($testImage, getTestFilePath('image.jpg'));
});

it('can set the timeout when doing optimization', function () {
    $testImage = getTempFilePath('image.jpg');

    $this
        ->optimizerChain
        ->setTimeout(1)
        ->addOptimizer(new Jpegoptim())
        ->optimize($testImage);

    $this->assertDecreasedFileSize($testImage, getTestFilePath('image.jpg'));
});

it('can get all optimizers', function () {
    expect($this->optimizerChain->getOptimizers())
        ->toBe([]);

    $this->optimizerChain->addOptimizer(new Jpegoptim());


    expect($this->optimizerChain->getOptimizers()[0])
        ->toBeInstanceOf(Jpegoptim::class);
});

it('can replace all optimizers with other ones', function () {
    expect($this->optimizerChain->getOptimizers())
        ->toBe([]);

    $this->optimizerChain->addOptimizer(new Jpegoptim());

    $this->optimizerChain->setOptimizers([
        new Optipng(),
        new Pngquant(),
    ]);

    $optimizers = $this->optimizerChain->getOptimizers();

    expect($optimizers)
        ->toHaveCount(2)
        ->sequence(
            function ($optimizer) {
                $optimizer->toBeInstanceOf(Optipng::class);
            },
            function ($optimizer) {
                $optimizer->toBeInstanceOf(Pngquant::class);
            }
        );
});
