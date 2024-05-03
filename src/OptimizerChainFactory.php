<?php

namespace Spatie\ImageOptimizer;

use Spatie\ImageOptimizer\Optimizers\Avifenc;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;

class OptimizerChainFactory
{
    public static function create(array $config = []): OptimizerChain
    {
        $optimizers = self::getOptimizers($config);
        $optimizerChain = new OptimizerChain();

        foreach ($optimizers as $optimizer => $optimizerConfig) {
            $optimizerChain->addOptimizer(new $optimizer($optimizerConfig));
        }

        return $optimizerChain;
    }

    /**
     * @return array<class-string, array>
     */
    private static function getOptimizers(array $config): array
    {
        if (self::configHasOptimizer($config)) {
            return $config;
        }

        return [
            Jpegoptim::class => [
                '-m' . ($config['quality'] ?? 85),
                '--force',
                '--strip-all',
                '--all-progressive',
            ],
            Pngquant::class => [
                '--quality=' . ($config['quality'] ?? 85),
                '--force',
            ],
            Optipng::class => [
                '-i0',
                '-o2',
                '-quiet',
            ],
            Svgo::class => [],
            Gifsicle::class => [
                '-b',
                '-O3',
            ],
            Cwebp::class => [
                '-m 6',
                '-pass 10',
                '-mt',
                '-q ' . ($config['quality'] ?? 90),
            ],
            Avifenc::class => [
                '-a cq-level=' . (isset($config['quality']) ? round(63 - $config['quality'] * 0.63) : 23),
                '-j all',
                '--min 0',
                '--max 63',
                '--minalpha 0',
                '--maxalpha 63',
                '-a end-usage=q',
                '-a tune=ssim',
            ],
        ];
    }

    private static function configHasOptimizer(array $config): bool
    {
        return (bool)array_diff_key($config, [
            Jpegoptim::class,
            Pngquant::class,
            Optipng::class,
            Svgo::class,
            Gifsicle::class,
            Cwebp::class,
            Avifenc::class,
        ]);
    }
}
