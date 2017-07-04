**DO NOT USE YET, WORK IN PROGRESS**

# Easily optimize images using PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)
[![Build Status](https://img.shields.io/travis/spatie/image-optimizer/master.svg?style=flat-square)](https://travis-ci.org/spatie/image-optimizer)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4.svg?style=flat-square)](https://insight.sensiolabs.com/projects/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/image-optimizer.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/image-optimizer)
[![StyleCI](https://styleci.io/repos/96041872/shield?branch=master)](https://styleci.io/repos/96041872)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)

This package can optimize `gif`s, `png`s and `jpg`s by running them through a chain of various [image optimization tools](#image-optimization-tools). Here's how you can use it:

```php
use Spatie\ImageOptimizer\ImageOptimizerFactory;

$imageOptimizer = ImageOptimizerFactory::create();

$imageOptimizer->optimize($pathToImage);
```

The image at `$pathToImage` will be overwritten by an optimized version which should be smaller. 

The package will automatically detect which optimization binaries are installed on your system and use them.

## Example

Here's an example of a `png` which was optimized by this package.

Original (size: 179 Kb)
![Original](https://spatie.github.io/image-optimizer/examples/example.png)

Optimized (size: 64 Kb)
![Optimized](https://spatie.github.io/image-optimizer/examples/example-optimized.png)

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/image-optimizer
```

### Optimization tools

The package will use these optimizers if they are present on your system:

- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant](https://pngquant.org/)

Here's how to install all the optimizers on Ubuntu:

```bash
sudo apt-get install gifsicle
sudo apt-get install jpegoptim
sudo apt-get install optipng
sudo apt-get install pngquant
```

And here's how to install the on MacOS (using [Homebrew](https://brew.sh/)):

```php
brew install gifsicle
brew install jpegoptim
brew install optipng
brew install pngquant
```

## Usage

This is the default way to use the package:

``` php
use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$optimizerChain->optimize($pathToImage);
```

The image at `$pathToImage` will be overwritten by an optimized version which should be smaller. 

The package will automatically detect which optimization binaries are installed on your system and use them.

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
     * Determines if the given mimetype can be handled by the optimizer.
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
   ->setLogger(new MyLogger())
   ->optimize($pathToImage);
```

A logger is a class that implements `Psr\Log\LoggerInterface`. A good logging library that's fully compliant is [Monolog](https://github.com/Seldaek/monolog). The package will write the to log which `Optimizers` are used, which commands are executed and their output.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

This package has been inspired by [psliwa/image-optimizer](https://github.com/psliwa/image-optimizer)

Emotional support provided by [Joke Forment](https://twitter.com/pronneur)

## About Spatie

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
