<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Optimizer;

abstract class BaseOptimizer implements Optimizer
{
    public $options = [];

    public $imagePath = '';

    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    public function binaryName(): string
    {
        return $this->binaryName;
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

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryName}\" {$optionString} ".escapeshellarg($this->imagePath);
    }
}
