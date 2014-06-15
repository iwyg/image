# Image processing

[![Build Status](https://api.travis-ci.org/iwyg/image.png?branch=development)](https://travis-ci.org/iwyg/image)
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
		"thapp/image": "dev-development"
	}
}

```


## Quick Usage

Creating an image instance is easy using the ImageFactory `Image::create()`.

The `create` function takes 2 arguments, `$source`, and `$driver`. If a source
is given, the Image will immedialtly load the source file. The default driver
is set to imagick. Use `Image::DRIVER_GD` to use [gd](http://php.net/manual/en/book.image.php), or `Image::DRIVER_IM` to
use [imagemagick](http://www.imagemagick.org/).

```php

<?php

use \Thapp\Image\Image;

// creates an image instance with a imagick driver:
$image = Image::create(); 

// creates an image instance with a imagemagick driver:
$image = Image::create(null, Image::DRIVER_IM); 

```

### Resizing and Scaling 

Note that this is just a syntax demonstration. You have to call `save($target)`
in order to process and save the image. Instead of `load()`, you may also call
`source()`.

```php

<?php

// proportionally resize the image have a width of 200px:

$image->load('path/to/image.jpg')->resize(200, 0);

// resize the image have a width and height of 200px (ignores aspect ratio):
$image->load('path/to/image.jpg')->resize(200, 200);

// crop 500px * 500px of the image from the center, creates a frame if image is smaller.
$image->load('path/to/myimage.jpg')->crop(500, 500, 5)->save('target.jpg');

// You may also specify a background color for the frame:
$image->load('path/to/myimage.jpg')->crop(500, 500, 5, 'fff');

// crop 500px * 500px of the image from the center, resize image if image is smaller:
$image->load('path/to/myimage.jpg')->cropAndResize(500, 500, 5);

// resize the image to best fit within the given sizes:
$image->load('path/to/myimage.jpg')->fit(200, 200);

// crop 200px * 200px of the image from the center, resize image if image is smaller and apply a greyscale filter:
$image->load('path/to/myimage.jpg')->filter('grey_scale')->cropAndResize(200, 200, 5);

// Percentual scale the image:
$image->load('path/to/myimage.jpg')->scale(50);

// Limit the image to max. 200000px:
$image->load('path/to/myimage.jpg')->pixel(200000);

// Convert png to jpg:
$image->load('path/to/myimage.png')->toJpeg();
// or
$image->load('path/to/myimage.jpg')->filter('convert', ['f' => 'jpg']);

// Convert png to png:
$image->load('path/to/myimage.jpg')->toPng();
// or
$image->load('path/to/myimage.jpg')->filter('convert', ['f' => 'png']);

// Convert png to gif:
$image->load('path/to/myimage.jpg')->toGif();
// or
$image->load('path/to/myimage.jpg')->filter('convert', ['f' => 'gif']);

```

### Storing the image

```php
<?php

$image->save('target.jpg');

```

## Advanced Usage

### Loading source files

By default, the image processor will load both, local files and remote files
reachable via http. 

### Adding or altering resource loaders

You can utilize loaders or create your own loader. Loaders must implememt
`Thapp\Image\Loader\LoaderInterface`.

On the `*Factory` class, set the `setLoaderInstantiator`

```php
<?php

use \Thapp\Image\Factory\ImagickFactory;
use \Thapp\Image\Loader\FilesystemLoader;

ImagickFactory::setLoaderInstantiator(function () {
	return new FilesystemLoader;		
});
```

```php
<?php

use \Acme\CustomLoader;
use \Thapp\Image\Factory\ImagickFactory;
use \Thapp\Image\Loader\DelegatingLoader;

ImagickFactory::setLoaderInstantiator(function () {
	return new DelegatingLoader([
		 new CustomLoader,		
		 new FilesystemLoader
	 ]);		
});

```

To manually instantiate the Image, you'll have to pass an instance of
`Thapp\Image\Processor` as the first Argument. The simplest scenario is
something like this:

```php

<?php

use \Thapp\Image\Image;
use \Thapp\Image\Processor;

// ...

$driver = new ImagickDriver(new FilesystemLoader)
$proc   = new Processor(new ImagickDriver($loader));

$image = new Image($proc);

// ...

```

```php

<?php

$driver = new ImagickDriver(new FilesystemLoader)
$proc   = new Processor(new ImagickDriver($loader));

$proc->setQuality(80);
$proc->load('/source/file.jpg');
$proc->process($params);
$proc->writeToFile('/path/to/target.jpg');

```

### Adding or altering resource writers

### Using the Processor

The processor takes an array of parameters containing information about the
processing mode, the image dimensions, image gravity, and so on.

```php

<?php

use \Thapp\Image\Processor;
use \Thapp\Image\Driver\Parameters;

$params = [
	'mode'       => 1,
	'width'      => 100,
	'heigh'      => 0,
	'gravity'    => null,
	'background' => null,
];

// or 

$pararms = new Parameters::fromString('1/100/0');

// ...

$proc->load($source);

$proc->process($params);

$proc->writeToFile($target);

```

## Filters

This Package comes bundled with a couple of filters: `Convert`, `Greyscale`,
`Overlay`, and `Circle`.

These Filters will always be avaliable.
