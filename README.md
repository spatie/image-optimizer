**DO NOT USE YET, WORK IN PROGRESS**

# Easily optimize images using PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)
[![Build Status](https://img.shields.io/travis/spatie/image-optimizer/master.svg?style=flat-square)](https://travis-ci.org/spatie/image-optimizer)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4.svg?style=flat-square)](https://insight.sensiolabs.com/projects/5e00b329-08b4-41c7-ba3b-2a3a2b2594f4)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/image-optimizer.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/image-optimizer)
[![StyleCI](https://styleci.io/repos/96041872/shield?branch=master)](https://styleci.io/repos/96041872)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/image-optimizer.svg?style=flat-square)](https://packagist.org/packages/spatie/image-optimizer)

This package can optimize gifs, pngs and jpgs by running them to tools such as `jpegoptim`, `optipng`, `pngquant`. Here's how you can use it:

```php
use Spatie\ImageOptimizer\ImageOptimizerFactory;

$imageOptimizer = ImageOptimizerFactory::create();

$imageOptimizer->optimize($pathToImage);
```

The image at `$pathToImage` will be overwritten by an optimized version which should be smaller. 

The package will automatically detect which optimization binaries are installed on your system and use them.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/image-optimizer
```

The package will use these optimizers if they are present on your system:

- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [JpegTran](http://jpegclub.org/jpegtran/)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant](https://pngquant.org/)

Here's how to install all the optimizers on Ubuntu:

```bash
sudo apt-get install gifsicle
sudo apt-get install jpegoptim
sudo apt-get install libjpeg-prog #jpegtran
sudo apt-get install optipng
sudo apt-get install pngquant
```

And here's how to install the on MacOS (requireds both `brew` and `npm`):

```php
brew install install gifsicle
brew install jpegoptim
npm install --global jpegtran-bin
brew install install optipng
brew install install pngquant
```

## Usage

``` php
use Spatie\ImageOptimizer\ImageOptimizerFactory;

$imageOptimizer = ImageOptimizerFactory::create();

$imageOptimizer->optimize($pathToImage);
```

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

## About Spatie

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
