<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

class OptimizerChainTest extends TestCase
{
    /** @var \Spatie\ImageOptimizer\OptimizerChain; */
    protected $optimizerChain;

    public function setUp(): void
    {
        parent::setUp();

        $this->optimizerChain = new OptimizerChain();
    }

    /** @test */
    public function it_will_not_throw_an_exception_when_not_using_a_logger()
    {
        $testImage = $this->getTempFilePath('image.jpg');

        $this->optimizerChain
            ->addOptimizer(new Jpegoptim())
            ->optimize($testImage);

        $this->assertDecreasedFileSize($testImage, $this->getTestFilePath('image.jpg'));
    }

    /** @test */
    public function it_can_set_the_timeout_when_doing_optimization()
    {
        $testImage = $this->getTempFilePath('image.jpg');

        $this->optimizerChain
            ->setTimeout(1)
            ->addOptimizer(new Jpegoptim())
            ->optimize($testImage);

        $this->assertDecreasedFileSize($testImage, $this->getTestFilePath('image.jpg'));
    }

    /** @test */
    public function it_can_get_all_optimizers()
    {
        $this->assertEquals([], $this->optimizerChain->getOptimizers());

        $this->optimizerChain->addOptimizer(new Jpegoptim());

        $this->assertInstanceOf(Jpegoptim::class, $this->optimizerChain->getOptimizers()[0]);
    }

    /** @test */
    public function it_can_replace_all_optimizers_with_other_ones()
    {
        $this->assertEquals([], $this->optimizerChain->getOptimizers());

        $this->optimizerChain->addOptimizer(new Jpegoptim());

        $this->optimizerChain->setOptimizers([
            new Optipng(),
            new Pngquant(),
        ]);

        $this->assertCount(2, $this->optimizerChain->getOptimizers());
        $this->assertInstanceOf(Optipng::class, $this->optimizerChain->getOptimizers()[0]);
        $this->assertInstanceOf(Pngquant::class, $this->optimizerChain->getOptimizers()[1]);
    }

    /** @test */
    public function it_can_log_unknown_error_code()
    {
        $log = new ArrayLogger();
        $this->optimizerChain->useLogger($log);

        $optimizer = new Pngquant([
            '--force',
            '--quality 50-55',
            '--skip-if-larger',
        ]);
        $this->optimizerChain->setOptimizers([
            $optimizer
        ]);

        $filepath = $this->getTempFilePath('logo.png');
        $this->optimizerChain->optimize($filepath);
        $this->optimizerChain->optimize($filepath); // again

        // needle: Process errored with unknown code, `98` (from pngquant)
        // @see: https://github.com/kornelski/pngquant/blob/master/rust/ffi.rs
        // 98 or 99 frequently, according quality settings
        $this->assertStringContainsString('(from ' . $optimizer->binaryName . ')', $log->getAllLinesAsString());
    }
}
