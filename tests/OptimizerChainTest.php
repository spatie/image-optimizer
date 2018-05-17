<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class OptimizerChainTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\OptimizerChain; */
    protected $optimizerChain;

    public function setUp()
    {
        parent::setUp();

        $this->optimizerChain = new OptimizerChain();
    }

    /** @test */
    public function it_will_not_throw_an_exception_when_not_using_a_logger()
    {
        $testImage = $this->getTempFilePath('image.jpg');

        $this->optimizerChain
            ->addOptimizer(new Jpegoptim())
            ->optimize($testImage);

        $this->assertDecreasedFileSize($testImage, $this->getTestFilePath('image.jpg'));
    }

    /** @test */
    public function it_can_set_the_timeout_when_doing_optimization()
    {
        $testImage = $this->getTempFilePath('image.jpg');

        $this->optimizerChain
            ->setTimeout(1)
            ->addOptimizer(new Jpegoptim())
            ->optimize($testImage);

        $this->assertDecreasedFileSize($testImage, $this->getTestFilePath('image.jpg'));
    }

    /** @test */
    public function it_can_get_all_optimizers()
    {
        $this->assertEquals([], $this->optimizerChain->getOptimizers());

        $this->optimizerChain->addOptimizer(new Jpegoptim());

        $this->assertInstanceOf(Jpegoptim::class, $this->optimizerChain->getOptimizers()[0]);
    }

    /** @test */
    public function it_can_replace_all_optimizers_with_other_ones()
    {
        $this->assertEquals([], $this->optimizerChain->getOptimizers());

        $this->optimizerChain->addOptimizer(new Jpegoptim());

        $this->optimizerChain->setOptimizers([
            new Optipng(),
            new Pngquant(),
        ]);

        $this->assertCount(2, $this->optimizerChain->getOptimizers());
        $this->assertInstanceOf(Optipng::class, $this->optimizerChain->getOptimizers()[0]);
        $this->assertInstanceOf(Pngquant::class, $this->optimizerChain->getOptimizers()[1]);
    }
}
