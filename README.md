## Image processing

[![Build Status](https://api.travis-ci.org/iwyg/image.png?branch=master)](https://travis-ci.org/iwyg/image)
[![Code Climate](https://codeclimate.com/github/iwyg/image/badges/gpa.svg)](https://codeclimate.com/github/iwyg/image)
[![Coverage Status](https://coveralls.io/repos/iwyg/image/badge.svg?branch=master)](https://coveralls.io/r/iwyg/image?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/thapp/image.png)](http://hhvm.h4cc.de/package/thapp/image)
[![Latest Stable Version](https://poser.pugx.org/thapp/image/v/stable.png)](https://packagist.org/packages/thapp/image) 
[![Latest Unstable Version](https://poser.pugx.org/thapp/image/v/unstable.png)](https://packagist.org/packages/thapp/image) 
[![License](https://poser.pugx.org/thapp/image/license.png)](https://packagist.org/packages/thapp/image)

This module was create for the usage in Thapp\JitImage.

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

$image->crop(new Box(100, 100));

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
$content = file_get_cotnents('image.jpg');
$image = $source->create($content);

```
