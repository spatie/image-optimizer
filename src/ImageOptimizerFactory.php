<?php

namespace Spatie\ImageOptimizer;

use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class ImageOptimizerFactory
{
    public static function create(): ImageOptimizer
    {
        return (new ImageOptimizer())
            ->addOptimizer(new Jpegoptim([
                '--strip-all',
                '--all-progressive',
            ]))

            ->addOptimizer(new Pngquant([
                '--force',
            ]))

            ->addOptimizer(new Optipng([
                '-i0',
                '-o2',
                '-quiet',
            ]))

            ->addOptimizer(new Gifsicle([
                '-b',
                '-O5',
            ]));
    }
}
