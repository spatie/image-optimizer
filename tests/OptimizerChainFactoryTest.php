<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\Optimizers\Svgo;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class OptimizerChainFactoryTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\OptimizerChain */
    protected $optimizerChain;

    public function setUp()
    {
        parent::setUp();

        $this->optimizerChain = OptimizerChainFactory::create()
            ->useLogger($this->log);
    }

    /** @test */
    public function it_can_optimize_a_jpg()
    {
        $tempFilePath = $this->getTempFilePath('image.jpg');

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('image.jpg'));

        $this->assertOptimizersUsed(Jpegoptim::class);
    }

    /** @test */
    public function it_can_optimize_a_png()
    {
        $tempFilePath = $this->getTempFilePath('logo.png');

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('logo.png'));

        $this->assertOptimizersUsed([
            Optipng::class,
            Pngquant::class,
        ]);
    }

    /** @test */
    public function it_can_optimize_an_svg()
    {
        $tempFilePath = $this->getTempFilePath('graph.svg');

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertOptimizersUsed(Svgo::class);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('graph.svg'));
    }

    /** @test */
    public function it_can_optimize_a_gif()
    {
        $tempFilePath = $this->getTempFilePath('animated.gif');

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('animated.gif'));

        $this->assertOptimizersUsed(Gifsicle::class);
    }

    /** @test */
    public function it_will_not_not_touch_a_non_image_file()
    {
        $tempFilePath = $this->getTempFilePath('test.txt');

        $originalContent = file_get_contents($tempFilePath);

        $this->optimizerChain->optimize($tempFilePath);

        $optimizedContent = file_get_contents($tempFilePath);

        $this->assertEquals($optimizedContent, $originalContent);
    }

    /** @test */
    public function it_can_output_to_a_specified_path()
    {
        $tempFilePath = $this->getTempFilePath('logo.png');
        $outputFilePath = __DIR__.'/temp/output.png';

        $this->optimizerChain->optimize($tempFilePath, $outputFilePath);

        $this->assertFileEquals($tempFilePath, $this->getTestFilePath('logo.png'));
        $this->assertDecreasedFileSize($outputFilePath, $this->getTestFilePath('logo.png'));

        $this->assertOptimizersUsed([
            Optipng::class,
            Pngquant::class,
        ]);
    }

    /** @test */
    public function it_can_optimize_a_jpg_to_webp()
    {
        $tempFilePath = $this->getTempFilePath('image.jpg');
        $webpTempFilePath = preg_replace(
            '/'.pathinfo($tempFilePath, PATHINFO_EXTENSION).'$/',
            'webp',
            $tempFilePath
        );

        $this->optimizerChain->setOptimizers([new Cwebp()]);

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($webpTempFilePath, $this->getTestFilePath('image.jpg'));

        $this->assertOptimizersUsed(Cwebp::class);
    }

    /** @test */
    public function it_can_optimize_a_png_to_webp()
    {
        $tempFilePath = $this->getTempFilePath('logo.png');
        $webpTempFilePath = preg_replace(
            '/'.pathinfo($tempFilePath, PATHINFO_EXTENSION).'$/',
            'webp',
            $tempFilePath
        );

        $this->optimizerChain->setOptimizers([new Cwebp()]);

        $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($webpTempFilePath, $this->getTestFilePath('logo.png'));

        $this->assertOptimizersUsed(Cwebp::class);
    }
}
