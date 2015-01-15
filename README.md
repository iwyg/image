## Image processing

[![Build Status](https://api.travis-ci.org/iwyg/image.png?branch=master)](https://travis-ci.org/iwyg/image)
[![Latest Stable Version](https://poser.pugx.org/thapp/image/v/stable.png)](https://packagist.org/packages/thapp/image) 
[![Latest Unstable Version](https://poser.pugx.org/thapp/image/v/unstable.png)](https://packagist.org/packages/thapp/image) 
[![License](https://poser.pugx.org/thapp/image/license.png)](https://packagist.org/packages/thapp/image)
[![HHVM Status](http://hhvm.h4cc.de/badge/thapp/image.png)](http://hhvm.h4cc.de/package/thapp/image)

## Installation

Require `thapp/image` in your project directory

```sh
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

```php
<?php

use Thapp\Image\Metrics\Box;
use Thapp\Image\Driver\Imagick\Source;

$source = new Source;
$image = $source->load('image.jpg');

$image->crop(new Box(100, 100));

$image->save('newimage.jpg');

```
