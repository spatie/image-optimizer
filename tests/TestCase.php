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
        $tempDirPath = __DIR__.'/temp';

        $files = scandir($tempDirPath);

        foreach ($files as $file) {
            if (! in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }

    public function getTestFilePath(string $fileName)
    {
        $source = __DIR__."/testfiles/{$fileName}";

        $destination = __DIR__."/temp/{$fileName}";

        copy($source, $destination);

        return $destination;
    }
}
