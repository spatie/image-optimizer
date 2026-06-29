<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\SelfHandlingOptimizer;

abstract class BaseSelfHandlingOptimizer extends BaseOptimizer implements SelfHandlingOptimizer
{
    /*
     * A self-handling optimizer has no binary, so these only exist to satisfy the
     * inherited Optimizer contract. The chain delegates to handle() before they
     * would ever be called.
     */
    public function binaryName(): string
    {
        return '';
    }

    public function getCommand(): string
    {
        return '';
    }

    abstract public function handle(Image $image, LoggerInterface $logger): void;
}
