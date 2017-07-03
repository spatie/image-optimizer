<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\ImageOptimizer;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

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
}