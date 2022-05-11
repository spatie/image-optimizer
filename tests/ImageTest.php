<?php

use Spatie\ImageOptimizer\Image;

it('will throw an exception when given a non existing file', function () {
    new Image('non existing file');
})->throws(InvalidArgumentException::class);

it('can get type mime type', function () {
    $image = new Image(getTestFilePath('image.jpg'));

    expect($image->mime())
        ->toBe('image/jpeg');
});

it('can get the path', function () {
    $path = getTestFilePath('image.jpg');

    $image = new Image($path);

    expect($image->path())
        ->toBe($path);
});

it('can get the extension', function () {
    $image = new Image(getTestFilePath('image.jpg'));

    expect($image->extension())
        ->toBe('jpg');
});
