<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;

class OptimizerChainFactoryTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\OptimizerChain */
    protected $optimizerChain;

    public function setUp(): void
    {
        parent::setUp();

        $this->optimizerChain = OptimizerChainFactory::create()
            ->useLogger($this->log);
    }

    /** @test */
    public function it_can_optimize_a_jpg()
    {
        $optimizer = Jpegoptim::class;

        $tempFilePath = $this->getTempFilePath('image.jpg');

        $result = $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('image.jpg'));

        $this->assertOptimizersUsed($optimizer);

        $this->assertTrue($result[(new $optimizer)->binaryName]);
    }

    /** @test */
    public function it_can_optimize_a_png()
    {
        $optimizer = [
            Optipng::class,
            Pngquant::class,
        ];

        $tempFilePath = $this->getTempFilePath('logo.png');

        $result = $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('logo.png'));

        $this->assertOptimizersUsed($optimizer);

        $this->assertOptimized($optimizer, $result);
    }

    /** @test */
    public function it_can_optimize_an_svg()
    {
        $optimizer = Svgo::class;

        $tempFilePath = $this->getTempFilePath('graph.svg');

        $result = $this->optimizerChain->optimize($tempFilePath);

        $this->assertOptimizersUsed($optimizer);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('graph.svg'));

        $this->assertOptimized($optimizer, $result);
    }

    /** @test */
    public function it_can_optimize_a_gif()
    {
        $optimizer = Gifsicle::class;

        $tempFilePath = $this->getTempFilePath('animated.gif');

        $result = $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('animated.gif'));

        $this->assertOptimizersUsed($optimizer);

        $this->assertOptimized($optimizer, $result);
    }

    /** @test */
    public function it_can_optimize_a_webp()
    {
        $optimizer = Cwebp::class;

        $tempFilePath = $this->getTempFilePath('image.webp');

        $result = $this->optimizerChain->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('image.webp'));

        $this->assertOptimizersUsed(Cwebp::class);

        $this->assertOptimized($optimizer, $result);
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
}
