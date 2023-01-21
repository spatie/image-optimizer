<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Optimizer;

abstract class BaseOptimizer implements Optimizer
{
    /** @var string[] */
    public $options = [];

    /** @var string */
    public $imagePath = '';

    /** @var string */
    public $binaryPath = '';

    /** @var string */
    public $binaryName = '';

    /**
     * @param string[] $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    public function binaryName(): string
    {
        return $this->binaryName;
    }

    /**
     * @return static
     */
    public function setBinaryPath(string $binaryPath)
    {
        if (strlen($binaryPath) > 0 && substr($binaryPath, -1) !== DIRECTORY_SEPARATOR) {
            $binaryPath = $binaryPath.DIRECTORY_SEPARATOR;
        }

        $this->binaryPath = $binaryPath;

        return $this;
    }

    public function setImagePath(string $imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @param string[] $options
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString} ".escapeshellarg($this->imagePath);
    }
}
