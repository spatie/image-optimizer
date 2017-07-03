<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\ImageOptimizer;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

class ImageOptimizerTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\ImageOptimizer; */
    protected $imageOptimizer;

    public function setUp()
    {
        parent::setUp();

        $this->imageOptimizer = new ImageOptimizer();
    }

    /** @test */
    public function it_will_not_throw_an_exception_when_not_using_a_logger()
    {
        $testImage = $this->getTempFilePath('test.jpg');

        $this->imageOptimizer
            ->addOptimizer(new Jpegoptim())
            ->optimize($testImage);

        $this->assertDecreasedFileSize($testImage, $this->getTestFilePath('test.jpg'));
    }

    /** @test */
    public function it_can_get_all_optimizers()
    {
        $this->assertEquals([], $this->imageOptimizer->getOptimizers());

        $this->imageOptimizer->addOptimizer(new Jpegoptim());

        $this->assertInstanceOf(Jpegoptim::class, $this->imageOptimizer->getOptimizers()[0]);
    }

    /** @test */
    public function it_can_replace_all_optimizers_with_other_ones()
    {
        $this->assertEquals([], $this->imageOptimizer->getOptimizers());

        $this->imageOptimizer->addOptimizer(new Jpegoptim());

        $this->imageOptimizer->setOptimizers([
            new Optipng(),
            new Pngquant(),
        ]);

        $this->assertCount(2, $this->imageOptimizer->getOptimizers());
        $this->assertInstanceOf(Optipng::class, $this->imageOptimizer->getOptimizers()[0]);
        $this->assertInstanceOf(Pngquant::class, $this->imageOptimizer->getOptimizers()[1]);
    }
}