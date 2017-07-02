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

        $this->optimizer = new ImageOptimizer();
    }

    /** @test */
    public function it_can_optimize_a_jpg()
    {
        $tempFilePath = $this->getTempFilePath('test.jpg');

        $this->optimizer->optimize($this->getTempFilePath('test.jpg'));

        $this->assertDecreasedFileSize($tempFilePath, $this->getTestFilePath('test.jpg'));
    }
}
