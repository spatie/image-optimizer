<?php

namespace Spatie\ImageOptimizer\Optimizers;

abstract class BaseOptimizer implements Optimizer
{
    /** @var array */
    public $options = [];

    /** @var string */
    public $imagePath = '';

    public function setImagePath(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function binaryName(): string
    {
        return $this->binaryName;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    public function getCommand()
    {
        $optionString = implode(' ', $this->options);

        return "'{$this->binaryName}' {$optionString} '{$this->imagePath}'";
    }
}