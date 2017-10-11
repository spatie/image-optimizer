# Easily optimize images using PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)
[![Build Status](https://img.shields.io/travis/spatie/image-optimizer/master.svg?style=flat-square)](https://travis-ci.org/spatie/image-optimizer)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4.svg?style=flat-square)](https://insight.sensiolabs.com/projects/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/image-optimizer.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/image-optimizer)
[![StyleCI](https://styleci.io/repos/96041872/shield?branch=master)](https://styleci.io/repos/96041872)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)

This package can optimize PNGs, JPGs, SVGs and GIFs by running them through a chain of various [image optimization tools](#optimization-tools). Here's how you can use it:

```php
use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain->optimize($pathToImage);
```

The image at `$pathToImage` will be overwritten by an optimized version which should be smaller. The package will automatically detect which optimization binaries are installed on your system and use them.

Here are some [example conversions](#example-conversions) that have been done by this package.

Loving Laravel? Then head over to [the Laravel specific integration](https://github.com/spatie/laravel-image-optimizer).

## Installation

You can install the package via composer:

```bash
composer require spatie/image-optimizer
```

### Optimization tools

The package will use these optimizers if they are present on your system:

- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant 2](https://pngquant.org/)
- [SVGO](https://github.com/svg/svgo)
- [Gifsicle](http://www.lcdf.org/gifsicle/)

Here's how to install all the optimizers on Ubuntu:

```bash
sudo apt-get install jpegoptim
sudo apt-get install optipng
sudo apt-get install pngquant
sudo npm install -g svgo
sudo apt-get install gifsicle
```

And here's how to install the binaries on MacOS (using [Homebrew](https://brew.sh/)):

```bash
brew install jpegoptim
brew install optipng
brew install pngquant
brew install svgo
brew install gifsicle
```

## Which tools will do what?

The package will automatically decide which tools to use on a particular image.

### JPGs

JPGs will be made smaller by running them through [JpegOptim](http://freecode.com/projects/jpegoptim). These options are used:
- `-m85`: this will store the image with 85% quality. This setting [seems to satisfy Google's Pagespeed compression rules](https://webmasters.stackexchange.com/questions/102094/google-pagespeed-how-to-satisfy-the-new-image-compression-rules)
- `--strip-all`: this strips out all text information such as comments and EXIF data
- `--all-progressive`: this will make sure the resulting image is a progressive one, meaning it can be downloaded using multiple passes of progressively higher details.

### PNGs

PNGs will be made smaller by running them through two tools. The first one is [Pngquant 2](https://pngquant.org/), a lossy PNG compressor. We set no extra options, their defaults are used. After that we run the image through a second one: [Optipng](http://optipng.sourceforge.net/). These options are used:
- `-i0`: this will result in a non-interlaced, progressive scanned image
- `-o2`: this set the optimization level to two (multiple IDAT compression trials)

### SVGs

SVGs will be minified by [SVGO](https://github.com/svg/svgo). SVGO's default configuration will be used, with the omission of the `cleanupIDs` plugin because that one is known to cause troubles when displaying multiple optimized SVGs on one page.

Please be aware that SVGO can break your svg. You'll find more info on that in this [excellent blogpost](https://www.sarasoueidan.com/blog/svgo-tools/) by [Sara Soueidan](https://twitter.com/SaraSoueidan).

### GIFs

GIFs will be optimized by [Gifsicle](http://www.lcdf.org/gifsicle/). These options will be used:
- `-O3`: this sets the optimization level to Gifsicle's maximum, which produces the slowest but best results

## Usage

This is the default way to use the package:

``` php
use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain->optimize($pathToImage);
```

The image at `$pathToImage` will be overwritten by an optimized version which should be smaller.

The package will automatically detect which optimization binaries are installed on your system and use them.

To keep the original image, you can pass through a second argument`optimize`:
```php
use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain->optimize($pathToImage, $pathToOutput);
```

In that example the package won't touch `$pathToImage` and write an optimized version to `$pathToOutput`.

### Setting a timeout

You can set the maximum of time in seconds that each indivual optimizer in a chain can use by calling `setTimeout`:

```php
$optimizerChain
    ->setTimeout(10)
    ->optimize($pathToImage);
```

In this example each optimizer in the chain will get a maximum 10 seconds to do it's job.

### Creating your own optimization chains

If you want to customize the chain of optimizers you can do so by adding `Optimizer`s manually to a `OptimizerChain`.

Here's an example where we only want `optipng` and `jpegoptim` to be used:

```php
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

$optimizerChain = (new OptimizerChain)
   ->addOptimizer(new Jpegoptim([
       '--strip-all',
       '--all-progressive',
   ]))

   ->addOptimizer(new Pngquant([
       '--force',
   ]))
```

Notice that you can pass the options an `Optimizer` should use to it's constructor.

### Writing a custom optimizers

Want to use another command line utility to optimize your images? No problem. Just write your own optimizer. An optimizer is any class that implements the `Spatie\ImageOptimizer\Optimizers\Optimizer` interface:

```php
namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

interface Optimizer
{
    /**
     * Returns the name of the binary to be executed.
     *
     * @return string
     */
    public function binaryName(): string;

    /**
     * Determines if the given image can be handled by the optimizer.
     *
     * @param \Spatie\ImageOptimizer\Image $image
     *
     * @return bool
     */
    public function canHandle(Image $image): bool;

    /**
     * Set the path to the image that should be optimized.
     *
     * @param string $imagePath
     *
     * @return $this
     */
    public function setImagePath(string $imagePath);

    /**
     * Set the options the optimizer should use.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = []);

    /**
     * Get the command that should be executed.
     *
     * @return string
     */
    public function getCommand(): string;
}
```

If you want to view an example implementation take a look at [the existing optimizers](https://github.com/spatie/image-optimizer/tree/master/src/Optimizers) shipped with this package.

You can easily add your optimizer by using the `addOptimizer` method on an `OptimizerChain`.

``` php
use Spatie\ImageOptimizer\ImageOptimizerFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain
   ->addOptimizer(new YourCustomOptimizer())
   ->optimize($pathToImage);
```

## Logging the optimization process

By default the package will not throw any errors and just operate silently. To verify what the package is doing you can set a logger:

```php
use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain
   ->useLogger(new MyLogger())
   ->optimize($pathToImage);
```

A logger is a class that implements `Psr\Log\LoggerInterface`. A good logging library that's fully compliant is [Monolog](https://github.com/Seldaek/monolog). The package will write the to log which `Optimizers` are used, which commands are executed and their output.

## Example conversions

Here are some real life example conversions done by this package.

### png

Original: Photoshop 'Save for web' | PNG-24 with transparency<br>
40 Kb

![Original](https://spatie.github.io/image-optimizer/examples/logo.png)

Optimized<br>
16 Kb (40%)

![Optimized](https://spatie.github.io/image-optimizer/examples/logo-optimized.png)

### jpg

Original: Photoshop 'Save for web' | quality 60, optimized<br>
547 Kb

![Original](https://spatie.github.io/image-optimizer/examples/image.jpg)

Optimized<br>
525 Kb (95%)

![Optimized](https://spatie.github.io/image-optimizer/examples/image-optimized.jpg)

credits: Jeff Sheldon, via [Unsplash](https://unsplash.com)

### svg

Original: Illustrator | Web optimized SVG export<br>
26 Kb

![Original](https://spatie.github.io/image-optimizer/examples/graph.svg)

Optimized<br>
20 Kb (76%)

![Optimized](https://spatie.github.io/image-optimizer/examples/graph-optimized.svg)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

This package has been inspired by [psliwa/image-optimizer](https://github.com/psliwa/image-optimizer)

Emotional support provided by [Joke Forment](https://twitter.com/pronneur)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
