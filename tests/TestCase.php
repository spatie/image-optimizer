<?php

namespace Spatie\ImageOptimizer\Test;

use PHPUnit\Framework\TestCase as BaseTest;

class TestCase extends BaseTest
{
    public function setUp()
    {
        $this->emptyTempDirectory();
    }

    protected function emptyTempDirectory()
    {
        $tempDirPath = __DIR__ . '/temp';

        $files = scandir($tempDirPath);

        foreach ($files as $file) {
            if (!in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }

    public function getTempFilePath(string $fileName)
    {
        $source = __DIR__ . "/testfiles/{$fileName}";

        $destination = __DIR__ . "/temp/{$fileName}";

        copy($source, $destination);

        return $destination;
    }

    public function getTestFilePath(string $fileName)
    {
        return __DIR__ . "/testfiles/{$fileName}";
    }

    public function assertDecreasedFileSize(string $modifiedFilePath, string $originalFilePath)
    {
        $this->assertFileExists($originalFilePath);

        $this->assertFileExists($modifiedFilePath);

        $originalFileSize = filesize($originalFilePath);

        $modifiedFileSize = filesize($modifiedFilePath);

        $this->assertTrue($modifiedFileSize < $originalFileSize,
            "File {$modifiedFilePath} as size {$modifiedFileSize} which is not less than {$originalFileSize}"
        );
    }
}
