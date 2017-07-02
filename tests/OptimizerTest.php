<?php

namespace Spatie\ImageOptimizer\Test;

class OptimizerTest extends TestCase
{
    /** @test */
    public function it_tests()
    {
        $optimizer = new \Spatie\ImageOptimizer\ImageOptimizer();

        $optimizer->optimize($this->getTestFilePath('test.jpg'));
    }
}
