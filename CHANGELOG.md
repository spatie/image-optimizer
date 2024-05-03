# Changelog

All notable changes to `image-optimizer` will be documented in this file

## 1.7.3 - 2024-05-03

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/spatie/image-optimizer/pull/203
* Fix OptimizerChainFactory's missing config processor by @0xb4lint in https://github.com/spatie/image-optimizer/pull/216
* Fix the bug related to Deserialization of Untrusted Data by @Sonicrrrr in https://github.com/spatie/image-optimizer/pull/211

### New Contributors

* @Sonicrrrr made their first contribution in https://github.com/spatie/image-optimizer/pull/211

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.7.2...1.7.3

## 1.7.2 - 2023-11-03

### What's Changed

- Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/spatie/image-optimizer/pull/202
- Add PHP 8.2 to the GitHub CI test matrix by @javiereguiluz in https://github.com/spatie/image-optimizer/pull/207
- Allow using Symfony 7 packages by @javiereguiluz in https://github.com/spatie/image-optimizer/pull/206

### New Contributors

- @javiereguiluz made their first contribution in https://github.com/spatie/image-optimizer/pull/207

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.7.1...1.7.2

## 1.7.1 - 2023-07-27

### What's Changed

- libavif version note by @0xb4lint in https://github.com/spatie/image-optimizer/pull/199

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.7.0...1.7.1

## 1.7.0 - 2023-07-22

### What's Changed

- README.md file size fixes, DSSIM score, optimized webp replaced by @0xb4lint in https://github.com/spatie/image-optimizer/pull/197
- added AVIF support by @0xb4lint in https://github.com/spatie/image-optimizer/pull/198

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.6.4...1.7.0

## 1.6.4 - 2023-03-10

### What's Changed

- SVGO 3 Support by @l-alexandrov in https://github.com/spatie/image-optimizer/pull/186

### New Contributors

- @l-alexandrov made their first contribution in https://github.com/spatie/image-optimizer/pull/186

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.6.3...1.6.4

## 1.6.3 - 2023-02-28

### What's Changed

- Update .gitattributes by @PaolaRuby in https://github.com/spatie/image-optimizer/pull/161
- Feature: Convert PHPUnit tests to Pest by @mansoorkhan96 in https://github.com/spatie/image-optimizer/pull/167
- Add dependabot automation by @patinthehat in https://github.com/spatie/image-optimizer/pull/173
- Allow Pest Composer Plugin (fix failing tests) by @patinthehat in https://github.com/spatie/image-optimizer/pull/176
- Update Dependabot Automation by @patinthehat in https://github.com/spatie/image-optimizer/pull/175
- DOC: adding SilverStripe link by @sunnysideup in https://github.com/spatie/image-optimizer/pull/177
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/image-optimizer/pull/183
- WebP Quality Option by @jan-tricks in https://github.com/spatie/image-optimizer/pull/185
- Bump actions/checkout from 2 to 3 by @dependabot in https://github.com/spatie/image-optimizer/pull/174

### New Contributors

- @PaolaRuby made their first contribution in https://github.com/spatie/image-optimizer/pull/161
- @mansoorkhan96 made their first contribution in https://github.com/spatie/image-optimizer/pull/167
- @patinthehat made their first contribution in https://github.com/spatie/image-optimizer/pull/173
- @sunnysideup made their first contribution in https://github.com/spatie/image-optimizer/pull/177
- @dependabot made their first contribution in https://github.com/spatie/image-optimizer/pull/183
- @jan-tricks made their first contribution in https://github.com/spatie/image-optimizer/pull/185

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.6.2...1.6.3

## 1.6.2 - 2021-12-21

## What's Changed

- add support for Symfony 6 by @Nielsvanpach in https://github.com/spatie/image-optimizer/pull/155

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.6.1...1.6.2

## 1.6.1 - 2021-11-17

## What's Changed

- Add PHP 8.1 support by @freekmurze in https://github.com/spatie/image-optimizer/pull/154

**Full Changelog**: https://github.com/spatie/image-optimizer/compare/1.5.0...1.6.1

## 1.5.0 - 2021-10-18

- Support new releases of psr/log (#150)

## 1.4.0 - 2021-04-22

- use `--skip-if-larger` pngquant option by default (#140)

## 1.3.2 - 2020-11-28

- improve gifsicle (#131)

## 1.3.1 - 2020-10-20

- fix empty string setBinaryPath() (#129)

## 1.3.0 - 2020-10-10

- add support for php 8.0

## 1.2.1 - 2019-11-23

- allow symfony 5 components

## 1.2.0 - 2019-08-28

- add support for webp

## 1.1.6 - 2019-08-26

- do not export docs directory

## 1.1.5 - 2019-01-15

- fix for svg's
- make compatible with PHPUnit 8

## 1.1.4 - 2019-01-14

- fix deprecation warning for passing strings to processes

## 1.1.3 - 2018-11-19

- require the fileinfo extension

## 1.1.2 - 2018-10-10

- make sure all optimizers use `binaryPath`

## 1.1.1 - 2018-09-10

- fix logger output

## 1.1.0 - 2018-06-05

- add `setBinaryPath`

## 1.0.14 - 2018-03-07

- support more symfony versions

## 1.0.13 - 2018-02-26

- added `text/plain` to the list of valid svg mime types

## 1.0.12. - 2018-02-21

- added `image/svg+xml` mime type

## 1.0.11 - 2018-02-08

- SVG mime type detection in PHP 7.2

## 1.0.10 - 2018-02-08

- Support symfony ^4.0
- Support phpunit ^7.0

## 1.0.9 - 2017-11-03

- fix shell command quotes

## 1.0.8 - 2017-09-14

- allow Symfony 2 components
- make Google Pagespeed tests pass

## 1.0.7 - 2017-07-29

- lower requirements of dependencies

## 1.0.6 - 2017-07-10

- fix `jpegoptim` parameters

## 1.0.4 - 2017-07-07

- make `setTimeout` chainable

## 1.0.3 - 2017-07-06

- fix `composer.json`

## 1.0.2 - 2017-07-06

- fix for Laravel 5.5 users

## 1.0.1 - 2017-07-06

- improve security

## 1.0.0 - 2017-07-05

- initial release
