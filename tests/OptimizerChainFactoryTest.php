<?php

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFileEquals;

use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Avifenc;
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

it('can use config', function () {
    $this->optimizerChain = OptimizerChainFactory::create([
        Jpegoptim::class => ['--foo'],
        Pngquant::class => ['--foo'],
        Optipng::class => ['--foo'],
        Svgo::class => ['--foo'],
        Gifsicle::class => ['--foo'],
        Cwebp::class => ['--foo'],
        Avifenc::class => ['--foo'],
    ])
    ->useLogger($this->log);

    assertEquals(
        [
            new Jpegoptim(['--foo']),
            new Pngquant(['--foo']),
            new Optipng(['--foo']),
            new Svgo(['--foo']),
            new Gifsicle(['--foo']),
            new Cwebp(['--foo']),
            new Avifenc(['--foo']),
        ],
        $this->optimizerChain->getOptimizers()
    );
});

it('can use default config', function () {
    assertEquals(
        [
           Jpegoptim::class,
           Pngquant::class,
           Optipng::class,
           Svgo::class,
           Gifsicle::class,
           Cwebp::class,
           Avifenc::class,
        ],
        array_map(
            function (Optimizer $optimizer) {
                return get_class($optimizer);
            },
            $this->optimizerChain->getOptimizers()
        )
    );
});

it('can use quality parameter with default config', function () {
    $this->optimizerChain = OptimizerChainFactory::create(['quality' => 50])
            ->useLogger($this->log);

    assertEquals(
        [
            new Jpegoptim([
                '-m50',
                '--force',
                '--strip-all',
                '--all-progressive',
            ]),
            new Pngquant([
                '--quality=50',
                '--force',
            ]),
            new Optipng([
                '-i0',
                '-o2',
                '-quiet',
            ]),
            new Svgo([]),
            new Gifsicle([
                '-b',
                '-O3',
            ]),
            new Cwebp([
                '-m 6',
                '-pass 10',
                '-mt',
                '-q 50',
            ]),
            new Avifenc([
                '-a cq-level=32',
                '-j all',
                '--min 0',
                '--max 63',
                '--minalpha 0',
                '--maxalpha 63',
                '-a end-usage=q',
                '-a tune=ssim',
            ]),
        ],
        $this->optimizerChain->getOptimizers()
    );
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

it('can optimize an avif', function () {
    $tempFilePath = getTempFilePath('image.avif');

    $this->optimizerChain->optimize($tempFilePath);

    $this->assertDecreasedFileSize($tempFilePath, getTestFilePath('image.avif'));

    $this->assertOptimizersUsed(Avifenc::class);
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
