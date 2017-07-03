<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\ImageOptimizer;

class OptimizerTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\ImageOptimizer */
    protected $optimizer;

    public function setUp()
    {
        parent::setUp();

        $this->optimizer = (new ImageOptimizer())
            ->useLogger($this->log);
    }

    /** @test */
    public function it_can_optimize_a_jpg()
    {
        $tempFilePath = $this->getTempFilePath('test.jpg');

        $this->optimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.jpg'));
    }

    /** @test */
    public function it_can_optimize_a_png()
    {
        $tempFilePath = $this->getTempFilePath('test.png');

        $this->optimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.png'));
    }
}
