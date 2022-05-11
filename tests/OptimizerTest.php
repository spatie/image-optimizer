<?php

use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

it('can accept options via the constructor', function () {
    $optimizer = new Jpegoptim(['option1', 'option2']);

    $optimizer->setImagePath('my-image.jpg');

    expect($optimizer->getCommand())
        ->toBe("\"jpegoptim\" option1 option2 'my-image.jpg'");
});

it('can set a binary path', function () {
    $optimizer = (new Jpegoptim())
        ->setImagePath('my-image.jpg')
        ->setBinaryPath('testPath');

    expect($optimizer->getCommand())
        ->toBe("\"testPath/jpegoptim\"  'my-image.jpg'");

    $optimizer = (new Jpegoptim())
        ->setImagePath('my-image.jpg')
        ->setBinaryPath('testPath/');

    expect($optimizer->getCommand())
        ->toBe("\"testPath/jpegoptim\"  'my-image.jpg'");

    $optimizer = (new Jpegoptim())
        ->setImagePath('my-image.jpg')
        ->setBinaryPath('');

    expect($optimizer->getCommand())
        ->toBe("\"jpegoptim\"  'my-image.jpg'");
});

it('can override options', function () {
    $optimizer = (new Jpegoptim(['option1', 'option2']))
        ->setImagePath('my-image.jpg');

    $optimizer->setOptions(['option3', 'option4']);

    expect($optimizer->getCommand())
        ->toBe("\"jpegoptim\" option3 option4 'my-image.jpg'");
});

it('can get jpeg binary name', function () {
    $optimizer = new Jpegoptim(['option1', 'option2']);

    $optimizer->setImagePath('my-image.jpg');

    expect($optimizer->binaryName())
        ->toBe('jpegoptim');
});
