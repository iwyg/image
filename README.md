# Image processing

## Installation

Require `thapp/image` in your project directory

```
> composer require thapp/image

```
or add this to your `composer.json`

```
{
	"require": {
		"thapp/image": "dev-development"
	}
}

```


## Usage

```php

<?php

use \Thapp\Image\Image;

$image = Image::create();

```

### Resizing and Scaling 

```php

<?php

// proportionally resize the image have a width of 200px:

$image->from('path/to/image.jpg')->resize(200, 0);

// resize the image have a width and height of 200px (ignores aspect ratio):
$image->from('path/to/image.jpg')->resize(200, 200);

// crop 500px * 500px of the image from the center, creates a frame if image is smaller.
$image->from('path/to/myimage.jpg')->crop(500, 500, 5)->save('target.jpg');

// You may also specify a background color for the frame:
$image->from('path/to/myimage.jpg')->crop(500, 500, 5, 'fff');

// crop 500px * 500px of the image from the center, resize image if image is smaller:
$image->from('path/to/myimage.jpg')->cropAndResize(500, 500, 5);

// resize the image to best fit within the given sizes:
$image->from('path/to/myimage.jpg')->fit(200, 200);

// crop 200px * 200px of the image from the center, resize image if image is smaller and apply a greyscale filter:
$image->from('path/to/myimage.jpg')->filter('grey_scale')->cropAndResize(200, 200, 5);

// Percentual scale the image:
$image->from('path/to/myimage.jpg')->scale(50);

// Limit the image to max. 200000px:
$image->from('path/to/myimage.jpg')->pixel(200000);

// Convert png to jpg:
$image->from('path/to/myimage.png')->toJpeg();
// or
$image->from('path/to/myimage.jpg')->filter('convert', ['f' => 'jpg']);

// Convert png to png:
$image->from('path/to/myimage.jpg')->toPng();
// or
$image->from('path/to/myimage.jpg')->filter('convert', ['f' => 'png']);

// Convert png to gif:
$image->from('path/to/myimage.jpg')->toGif();
// or
$image->from('path/to/myimage.jpg')->filter('convert', ['f' => 'gif']);

```

### Storing the image

```php
<?php

$image->save('target.jpg');

```

## Advanced Usage

### Adding or altering resource loaders

### Adding or altering resource writers

## Filters
