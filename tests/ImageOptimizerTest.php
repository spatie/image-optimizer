<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\ImageOptimizerFactory;

class ImageOptimizerTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\ImageOptimizer */
    protected $imageOptimizer;

    public function setUp()
    {
        parent::setUp();

        $this->imageOptimizer = ImageOptimizerFactory::create()
            ->useLogger($this->log);
    }

    /** @test */
    public function it_can_optimize_a_jpg()
    {
        $tempFilePath = $this->getTempFilePath('test.jpg');

        $this->imageOptimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.jpg'));
    }

    /** @test */
    public function it_can_optimize_a_png()
    {
        $tempFilePath = $this->getTempFilePath('test.png');

        $this->imageOptimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.png'));
    }

    /** @test */
    public function it_can_optimize_a_gif()
    {
        $tempFilePath = $this->getTempFilePath('test.gif');

        $this->imageOptimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.gif'));
    }
}
