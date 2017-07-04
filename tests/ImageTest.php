<?php

namespace Spatie\ImageOptimizer\Test;

use InvalidArgumentException;
use Spatie\ImageOptimizer\Image;

class ImageTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_when_given_a_non_existing_file()
    {
        $this->expectException(InvalidArgumentException::class);

        new Image('non existing file');
    }

    /** @test */
    public function it_can_get_type_mime_type()
    {
        $image = new Image($this->getTestFilePath('image.jpg'));

        $this->assertEquals('image/jpeg', $image->mime());
    }

    /** @test */
    public function it_can_get_the_path()
    {
        $path = $this->getTestFilePath('image.jpg');

        $image = new Image($path);

        $this->assertEquals($path, $image->path());
    }

    /** @test */
    public function it_can_get_the_extension()
    {
        $image = new Image($this->getTestFilePath('image.jpg'));

        $this->assertEquals('jpg', $image->extension());
    }
}
