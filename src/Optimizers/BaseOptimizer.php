<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Optimizer;
use Symfony\Component\Process\Process;
use Spatie\ImageOptimizer\OptimizerChain;

abstract class BaseOptimizer implements Optimizer
{
    /**
     * Options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Image path.
     *
     * @var string
     */
    public $imagePath = '';

    /**
     * Binary path.
     *
     * @var string
     */
    protected $binaryPath = '';

    public function __construct($options = [])
    {
        $this->setOptions($options);
    }


    /**
     * Set binary Path.
     *
     * @param string|array $binaryPath
     * @return string
     */
    public function setBinaryPath($binaryPath)
    {
        $this->binaryPath = $binaryPath;

        return $this;
    }

    /**
     * Get binary path.
     *
     * @return string|array
     */
    public function binaryPath()
    {
        return $this->binaryPath;
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



    /**
     * Authomatically detect the path where the image optimizes is installed.
     *
     * @return $this
     */
    public function checkBinary()
    {
        // check binary by a given list of binary path
        if (is_array($this->binaryPath())) {
            foreach ($this->binaryPath() as $path) {
                $path = rtrim($path, '/').'/';
                $process = new Process('which -a '.$path.''.$this->binaryName());
                $process->setTimeout(null);
                $process->run();

                if ($process->isSuccessful()) {
                    $this->setBinaryPath($path);
                    return $this;
                }
            }

            $binaryPath = implode(',', array_values($this->binaryPath()));

            // if we come so far, it means the binary could not be found
            (new OptimizerChain())->getLogger()->error('Binary could not be found in any of the following configured paths: '. $binaryPath .'');

            // Although a given list of possible binary path has been given, the binary may exists
            // in the global environment. Therefore, we will unset binary path list so we can later
            // check if it exists the global environment
            $this->setBinaryPath('');
        }

        // check if binary exists in the global environment
        $process = new Process('which -a '.$this->binaryName());
        $process->setTimeout(null);
        $process->run();
        if ($process->isSuccessful()) {
            return $this;
        }else{
            (new OptimizerChain())->getLogger()->error('Binary could not be found: `'.$this->binaryName().'`');
        }

        return $this;
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);
        $fullBinaryPath = $this->checkBinary()->binaryPath().$this->binaryName();

        return "\"{$fullBinaryPath}\" {$optionString} ".escapeshellarg($this->imagePath);
    }
}
