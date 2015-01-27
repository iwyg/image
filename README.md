## Image processing

[![Build Status](https://api.travis-ci.org/iwyg/image.png?branch=master)](https://travis-ci.org/iwyg/image)
[![Code Climate](https://codeclimate.com/github/iwyg/image/badges/gpa.svg)](https://codeclimate.com/github/iwyg/image)
[![Coverage Status](https://coveralls.io/repos/iwyg/image/badge.svg?branch=master)](https://coveralls.io/r/iwyg/image?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/thapp/image.png)](http://hhvm.h4cc.de/package/thapp/image)
[![Latest Stable Version](https://poser.pugx.org/thapp/image/v/stable.png)](https://packagist.org/packages/thapp/image) 
[![Latest Unstable Version](https://poser.pugx.org/thapp/image/v/unstable.png)](https://packagist.org/packages/thapp/image) 
[![License](https://poser.pugx.org/thapp/image/license.png)](https://packagist.org/packages/thapp/image)

This module was created for the usage in Thapp\JitImage, but can be used as
a standalone library for manipulating images. It's highly inspired by the Imagine
library, but resolves a views flaws, but also way more limited. 

## Installation

Require `thapp/image` in your project directory

```bash
$ composer require thapp/image
```
or add this to your `composer.json`

```json
{
	"require": {
		"thapp/image": "dev-master"
	}
}
```

## Usage

### Quick Example

```php
<?php

use Thapp\Image\Metrics\Box;
use Thapp\Image\Driver\Imagick\Source;

$source = new Source;
$image = $source->load('image.jpg');

$image->edit()->crop(new Box(100, 100));

$image->save('newimage.jpg');

```

### Loading sources

The `Source` object is able to create `Image` instances from either filepaths,
filehandles, or binary strings:

```php
<?php

use Thapp\Image\Driver\Imagick\Source;

$source = new Source;
$image = $source->load('image.jpg');
// or read the file from a file handle:
$handle = fopen('image.jpg', 'r+');
$image = $source->read($handle);
// or read the file from a binary string:
$content = file_get_contents('image.jpg');
$image = $source->create($content);

```

The `Source` class takes an instance of
`Thapp\Image\Info\MetaDataReaderInterface` as its first argument. The `$reader`
is used to read meta information about the image. This is useful e.g. if you
want to autorotate the image based on its orientation. 

By default, a new instance of `Thapp\Image\Info\ImageReader` is created for
you. `ImageReader` is capable of reading basic image information derived from
the php [`getimagesize()`](http://php.net/manual/en/function.getimagesize.php) function.

You may use the `Thapp\Image\Info\ImageReader` class instead, which provides
a wider range of information (e.g. needed for `GD` and `Gmagick` drivers to
determine the correct image orientation).

```php
<?php

use Thapp\Image\Info\ExifReader;
use Thapp\Image\Driver\Imagick\Source;

$source = new Source(new ExifReader);

// ...

$image = $source->load('image.jpg');

```
