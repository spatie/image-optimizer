<?php

namespace Spatie\ImageOptimizer;

use Spatie\ImageOptimizer\Optimizers\Svgo;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class OptimizerChainFactory
{
    public static function create(): OptimizerChain
    {
        return (new OptimizerChain())
            ->addOptimizer(new Jpegoptim([
                '-m85',
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

            ->addOptimizer(new Svgo([
                '--disable={cleanupIDs,removeViewBox}',
            ]))

            ->addOptimizer(new Gifsicle([
                '-b',
                '-O3',
            ]));
    }
}
