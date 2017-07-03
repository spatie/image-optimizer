<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegtran;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Symfony\Component\Process\Process;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optimizer;

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
                '-quiet'
            ]))

            ->addOptimizer(new Gifsicle([
                '-b',
                '-O5',
            ]));
    }
}
