<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\SelfHandlingOptimizer;

abstract class BaseSelfHandlingOptimizer implements SelfHandlingOptimizer
{
    public $options = [];

    public $imagePath = '';

    public $tmpPath = null;

    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    public function setImagePath(string $imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    public function getTmpPath(): ?string
    {
        return $this->tmpPath;
    }

    /*
     * Only exists to satisfy the inherited Optimizer contract. A self-handling
     * optimizer has no binary, so this is never called.
     */
    public function binaryName(): string
    {
        return '';
    }

    /*
     * Only exists to satisfy the inherited Optimizer contract. A self-handling
     * optimizer has no command: the chain delegates to handle() instead, so this
     * is never called.
     */
    public function getCommand(): string
    {
        return '';
    }

    abstract public function canHandle(Image $image): bool;

    abstract public function handle(Image $image, LoggerInterface $logger): void;
}
