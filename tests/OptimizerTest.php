<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class OptimizerTest extends TestCase
{
    /** @test */
    public function it_can_accept_options_via_the_constructor()
    {
        $optimizer = (new Jpegoptim(['option1', 'option2']))->setImagePath('my-image.jpg');

        $this->assertEquals("\"jpegoptim\" option1 option2 'my-image.jpg'", $optimizer->getCommand());
    }

    /** @test */
    public function it_can_override_options()
    {
        $optimizer = (new Jpegoptim(['option1', 'option2']))->setImagePath('my-image.jpg');

        $optimizer->setOptions(['option3', 'option4']);

        $this->assertEquals("\"jpegoptim\" option3 option4 'my-image.jpg'", $optimizer->getCommand());
    }
}
