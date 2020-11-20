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
    public function a_binary_path_can_be_set()
    {
        $optimizer = (new Jpegoptim())
            ->setImagePath('my-image.jpg')
            ->setBinaryPath('testPath');

        $this->assertEquals("\"testPath/jpegoptim\"  'my-image.jpg'", $optimizer->getCommand());

        $optimizer = (new Jpegoptim())
            ->setImagePath('my-image.jpg')
            ->setBinaryPath('testPath/');

        $this->assertEquals("\"testPath/jpegoptim\"  'my-image.jpg'", $optimizer->getCommand());

        $optimizer = (new Jpegoptim())
            ->setImagePath('my-image.jpg')
            ->setBinaryPath('');

        $this->assertEquals("\"jpegoptim\"  'my-image.jpg'", $optimizer->getCommand());
    }

    /** @test */
    public function it_can_override_options()
    {
        $optimizer = (new Jpegoptim(['option1', 'option2']))->setImagePath('my-image.jpg');

        $optimizer->setOptions(['option3', 'option4']);

        $this->assertEquals("\"jpegoptim\" option3 option4 'my-image.jpg'", $optimizer->getCommand());
    }

    /** @test */
    public function it_can_get_jpeg_binary_name()
    {
        $optimizer = (new Jpegoptim(['option1', 'option2']))->setImagePath('my-image.jpg');

        $optimizer->setOptions(['option3', 'option4']);

        $this->assertEquals('jpegoptim', $optimizer->binaryName());
    }
}
