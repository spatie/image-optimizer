<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Psr\Log\LoggerInterface;
use Spatie\ImageOptimizer\DummyLogger;
use Spatie\ImageOptimizer\Optimizer;
use Symfony\Component\Process\Process;

abstract class BaseOptimizer implements Optimizer
{
    public $options = [];

    public $imagePath = '';

    /**
     * List of binary paths to check for commands.
     * @var array $binaryPathList
     */
    protected $binaryPathList = [
      '/usr/local',
      '/usr/local/bin',
      '/usr/bin',
      '/usr/sbin',
      '/usr/local/bin',
      '/usr/local/sbin',
      '/bin',
      '/sbin'
    ];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * Binary name of the optimizer.
     * @var string $binaryName
     */
    protected $binaryName;

    /**
     * Binary path.
     *
     * @var string $binaryPath
     */
    protected $binaryPath = '';

    public function __construct($options = [])
    {
        $this->useLogger(new DummyLogger());
        $this->setOptions($options);
    }


    /**
     * Set binary Path
     *
     * Useful in case your commands are not accessible by global environment. ex. /usr/bin/local
     *
     * @param string $binaryName
     * @return string
     */
    public function setBinaryPath(string $binaryPath)
    {
        $this->binaryPath = $binaryPath;

        return $this;
    }

    /**
     * Get binary path
     *
     * @return string
     */
    public function binaryPath(): string
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

    public function useLogger(LoggerInterface $log)
    {
        $this->logger = $log;

        return $this;
    }


    /**
     * Get Binary path list
     *
     * @return array
     */
    public function binaryPathList(){
        return $this->binaryPathList;
    }

    /**
     * Authomatically detect the path where the image optimizes is installed
     *
     * @return $this
     */
    public function detectBinaryPath(){
        // first check if comman is executed in a global environment
        $process = new Process("which -a " .$this->binaryName());
        $process->setTimeout(null);
        $process->run();
        if ($process->isSuccessful()) {
            return $this;
        }

        // add custom path (if given in config.php)
        if($this->binaryPath()) {
            $this->binaryPathList = [
              $this->binaryPath()
            ];
        }

        // check if command is found in every given path
        foreach ($this->binaryPathList() as $path) {
            $path = rtrim($path, '/') . '/';
            $process = new Process("which -a " . $path . '' . $this->binaryName());
            $process->setTimeout(null);
            $process->run();
            if ($process->isSuccessful()) {
                $this->setBinaryPath($path);
                return $this;
            }
        }

        $this->logger->error("Command could not be executed in any of the following binary path: `".implode(",", array_values($this->binaryPathList))."`}");

        return $this;
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);
        $fullBinaryPath = $this->detectBinaryPath()->binaryPath().$this->binaryName();

        return "\"{$fullBinaryPath}\" {$optionString} ".escapeshellarg($this->imagePath);
    }
}
