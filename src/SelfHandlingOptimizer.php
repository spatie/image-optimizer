<?php

namespace Spatie\ImageOptimizer;

use Psr\Log\LoggerInterface;

interface SelfHandlingOptimizer extends Optimizer
{
    /**
     * Handle the optimization directly, without a binary or shell command.
     *
     * The OptimizerChain delegates execution to this method instead of
     * building and running a process, so the optimizer can do its own
     * work (for example, calling an external optimization API). The
     * chain's logger is passed in so the optimizer can log its progress.
     *
     * @param \Spatie\ImageOptimizer\Image $image
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function handle(Image $image, LoggerInterface $logger): void;
}
