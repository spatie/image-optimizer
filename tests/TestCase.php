<?php

namespace Spatie\ImageOptimizer\Test;

use PHPUnit\Framework\TestCase as BaseTest;

class TestCase extends BaseTest
{
    /** @var \Spatie\ImageOptimizer\Test\ArrayLogger */
    public $log;

    /** @var \Monolog\Logger */
    public $logger;

    public function setUp()
    {
        $this->emptyTempDirectory();

        $this->log = new ArrayLogger();
    }

    protected function emptyTempDirectory()
    {
        $tempDirPath = __DIR__.'/temp';

        $files = scandir($tempDirPath);

        foreach ($files as $file) {
            if (! in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }

    public function getTempFilePath(string $fileName)
    {
        $source = __DIR__."/testfiles/{$fileName}";

        $destination = __DIR__."/temp/{$fileName}";

        copy($source, $destination);

        return $destination;
    }

    public function getTestFilePath(string $fileName)
    {
        return __DIR__."/testfiles/{$fileName}";
    }

    public function assertDecreasedFileSize(string $modifiedFilePath, string $originalFilePath)
    {
        $this->assertFileExists($originalFilePath);

        $this->assertFileExists($modifiedFilePath);

        $originalFileSize = filesize($originalFilePath);

        $modifiedFileSize = filesize($modifiedFilePath);

        $this->assertTrue($modifiedFileSize < $originalFileSize,
            "File {$modifiedFilePath} as size {$modifiedFileSize} which is not less than {$originalFileSize}. Log: {$this->log->getAllLinesAsString()}"
        );

        $this->assertTrue($modifiedFileSize > 0, "File {$modifiedFilePath} had a filesize of zero. Something must have gone wrong...");
    }

    public function assertOptimizersUsed($optimizerClasses)
    {
        if (! is_array($optimizerClasses)) {
            $optimizerClasses = [$optimizerClasses];
        }

        $logText = $this->log->getAllLinesAsString();

        foreach ($optimizerClasses as $optimizerClass) {
            $searchString = "Using optimizer: `{$optimizerClass}`";

            $this->assertContains($searchString, $logText, "Optimizer `{$optimizerClass}` was not used");
        }

        $this->assertNotContains('error', $logText, "The log contained errors: `$logText`");
    }
}
