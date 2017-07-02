<?php

namespace Spatie\ImageOptimizer;

use Symfony\Component\Process\Process;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optimizer;

class ImageOptimizer
{
    public $optimizers = [];

    public function __construct()
    {
        $this->add(new Jpegoptim());
    }

    public function optimize(string $imagePath)
    {
        $mimeType = mime_content_type($imagePath);

        collect($this->optimizers)
            ->filter(function (Optimizer $optimizer) use ($mimeType) {
                return $optimizer->canHandle($mimeType);
            })
            ->each(function (Optimizer $optimizer) use ($imagePath) {
                $optimizer->setImagePath($imagePath);

                $process = new Process($optimizer->getCommand());

                $process->run();
            });
    }

    public function add(Optimizer $optimizer)
    {
        $this->optimizers[] = $optimizer;
    }
}
