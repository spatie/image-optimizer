<?php

namespace Spatie\ImageOptimizer\Test;

use Spatie\ImageOptimizer\Exceptions\UnableToGetRestrictedSizeException;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\SizeRestrictedImageOptimizer;

class SizeRestrictedImageOptimizerTest extends TestCase
{
    const ALLOWED_ACHIEVABLE_SIZE = 200 * 1000;
    const ALLOWED_UNACHIEVABLE_SIZE = 100 * 1000;

    /**
     * Initial size - 540kb
     * Allowed size - 200kb
     * Must optimize.
     */
    public function testCanOptimizeImage()
    {
        $optimizer = new SizeRestrictedImageOptimizer(
            self::ALLOWED_ACHIEVABLE_SIZE,
            (function (int $startQuality = 85, int $qualityStep = 5) {
                for ($quality = $startQuality; $quality >= 0; $quality -= $qualityStep) {
                    yield OptimizerChainFactory::create(['quality' => $quality]);
                }
            })());

        $tempFilePath = $this->getTempFilePath('image.jpg');

        $optimizer->optimize($tempFilePath);

        $this->assertDecreasedFileSize($tempFilePath,
            $this->getTestFilePath('image.jpg'));

        $this->assertLessThan(self::ALLOWED_ACHIEVABLE_SIZE,
            filesize($tempFilePath));
    }

    /**
     * Initial size - 540kb
     * Allowed size - 100kb
     * Cannot optimize to 100kb because image is too big.
     * Must finish without errors and shrink image to 5% quality.
     */
    public function testCanExitAtZeroQualitySoft()
    {
        $generator = (function (int $startQuality = 85, int $qualityStep = 5) {
            $lastQuality = $startQuality;
            for ($quality = $startQuality; $quality > 0; $quality -= $qualityStep) {
                $lastQuality = $quality;
                yield OptimizerChainFactory::create(['quality' => $quality]);
            }

            return $lastQuality;
        })();
        $optimizer = new SizeRestrictedImageOptimizer(self::ALLOWED_UNACHIEVABLE_SIZE,
            $generator);

        $tempFilePath = $this->getTempFilePath('image.jpg');

        $optimizer->optimize($tempFilePath);

        $this->assertSame(5, $generator->getReturn());
    }

    /**
     * Initial size - 540kb
     * Allowed size - 100kb
     * Cannot optimize to 100kb because image is too big.
     * @throws UnableToGetRestrictedSizeException
     */
    public function testCanExitAtZeroQualityStrict()
    {
        $optimizer = new SizeRestrictedImageOptimizer(
            self::ALLOWED_UNACHIEVABLE_SIZE,
            (function (int $startQuality = 85, int $qualityStep = 5) {
                for ($quality = $startQuality; $quality > 0; $quality -= $qualityStep) {
                    yield OptimizerChainFactory::create(['quality' => $quality]);
                }
            })());

        $tempFilePath = $this->getTempFilePath('image.jpg');

        $this->expectException(UnableToGetRestrictedSizeException::class);

        $optimizer->optimize($tempFilePath, true);
    }

    /**
     * Initial size - 540kb
     * Allowed size - 1mb
     * Must skip optimization.
     */
    public function testCanSkipSmallImage()
    {
        $tempFilePath = $this->getTempFilePath('image.jpg');

        $optimizer = new SizeRestrictedImageOptimizer(
            1000 * 1000,
            (function (int $startQuality = 85, int $qualityStep = 5) {
                for ($quality = $startQuality; $quality > 0; $quality -= $qualityStep) {
                    yield OptimizerChainFactory::create(['quality' => $quality]);
                }
            })());

        $originalContent = file_get_contents($tempFilePath);

        $optimizer->optimize($tempFilePath);

        $optimizedContent = file_get_contents($tempFilePath);

        $this->assertEquals($optimizedContent, $originalContent);
    }

    public function testThrowsExceptionAtInvalidAllowedSize()
    {
        $this->expectException(\UnexpectedValueException::class);

        new SizeRestrictedImageOptimizer(
            -1,
            (function (int $startQuality = 85, int $qualityStep = 5) {
                for ($quality = $startQuality; $quality > 0; $quality -= $qualityStep) {
                    yield OptimizerChainFactory::create(['quality' => $quality]);
                }
            })());
    }
}
