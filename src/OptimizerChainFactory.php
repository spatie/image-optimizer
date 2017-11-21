<?php

namespace Spatie\ImageOptimizer;

use PHPUnit\Runner\Exception;
use Spatie\ImageOptimizer\Optimizers\Svgo;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class OptimizerChainFactory
{
    const MODE_JPG_AGGRESSIVE      = '--strip-all';
    const MODE_JPG_PRESERVE_COLORS = '--strip-com --strip-exif --strip-iptc';
    const MODES_JPG                = [
        self::MODE_JPG_AGGRESSIVE,
        self::MODE_JPG_PRESERVE_COLORS,
    ];

    public static function create($mode = self::MODE_JPG_AGGRESSIVE): OptimizerChain
    {
        if (!in_array($mode, self::MODES_JPG)) {
            throw new Exception('Invalid jpg mode parameter used.');
        }

        return (new OptimizerChain())
            ->addOptimizer(new Jpegoptim([
                '-m85',
                $mode,
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
                '--disable=cleanupIDs',
            ]))

            ->addOptimizer(new Gifsicle([
                '-b',
                '-O3',
            ]));
    }
}
