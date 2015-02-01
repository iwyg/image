## Image processing

[![Build Status](https://api.travis-ci.org/iwyg/image.png?branch=develop)](https://travis-ci.org/iwyg/image)
[![Code Climate](https://codeclimate.com/github/iwyg/image/badges/gpa.svg)](https://codeclimate.com/github/iwyg/image)
[![Coverage Status](https://coveralls.io/repos/iwyg/image/badge.svg?branch=develop)](https://coveralls.io/r/iwyg/image?branch=develop)
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
		"thapp/image": "1.0.*"
	}
}
```

## Usage

### Quick Example

```php
<?php

use Thapp\Image\Geometry\Size;
use Thapp\Image\Driver\Imagick\Source;

$source = new Source;
$image = $source->load('image.jpg');

$image->edit()->crop(new Size(100, 100));

// Save the image to a new file:
$image->save('newimage.jpg');

// Write the image contents to a stream.
$image->write($stream);

// Write the image contents to a file.
file_put_contents($path, $image->getBlob());

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

### Editing images

```php
<?php

use Thapp\Image\Driver\Imagick\Edit;

$mod = $image->edit(); // Thapp\Image\Driver\EditInterface

// or

$mod = new Edit($image);


```

#### Resize and scaling

Calling the `size()` method on an `$image`object returns a `$size` object of
type `Thapp\Image\Geometry\SizeInterface`. This object can be helpful to
compute the desired new size of an image.

All methods used for manipulating the geometry of the image are taking a size
object as their first argument. 

The `resize()` method of the `$image` object can be used to modify the geometry
of the image including its content. 

**Resize an image ignoring its aspect ratio**
```php
<?php

use Thapp\Image\Geometry\Size;

// Resize the image unporoportionaly to fit in the given size object.

$size = new Size(200, 200);

$image->edit()->resize($size);

```

**Scale an image proportionally**
```php
<?php

// Doubles with and height of the image. 

$size = $image->getSize()->scale(200);

$image->edit()->resize($size);

```

**Scale by pixel count**
```php
<?php

// Caclulates the image new size while not exceeding the given pixel count

$size = $image->getSize()->pixel(500000);

$image->edit()->resize($size);

```

**Scale by increasing the width of the image**
```php
<?php

// increase image width by 100px and calculate its height

$size = $image->getSize()->increaseByWidth(100);

$image->edit()->resize($size);

```

**Scale by increasing the height of the image**
```php
<?php

// increase image height by 100px and calculate its width

$size = $image->getSize()->increaseByHeight(100);

$image->edit()->resize($size);

```

**Fit an image into to a given size**
```php
<?php

use Thapp\Image\Geometry\Size;

// fits the image into a 400 x 400 box. 

$size = new Size(400, 400);

$image->edit()->fit($size);

```

**Fill a given size with the image size**
The image will be at least as hight and wide as the `$size` object.
```php
<?php

use Thapp\Image\Geometry\Size;

// fill a given box of 400 x 400.

$size = new Size(400, 400);

$image->edit()->fill($size);

```

**Modifying the geometry of the image with out resizing its content**

`extent()` will increase or decrease the "canvas" of the image.

```php
<?php

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;

// Extent the image with a canvas size of 800 by 800.

$image->edit()->extent(new Size(800, 800), new Point(200, 200));

```

```php
<?php

use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;

// Extent the image with a canvas size of 800 by 800.

$image->setGravity(new Gravity(Gravity::GRAVITY_CENTER));
$image->edit()->extent(new Size(800, 800));

```

#### Croping

```php
<?php

use Thapp\Image\Geometry\Size;

$crop = new Size(200, 200);
$start = new Point(50, 50);

$image->edit()->crop($crop, $point);

```

Using gravity you can place your crop according to the images gravity setting. There're 9 gravity positions: 

1. `GRAVITY_NORTHWEST`  left top      
2. `GRAVITY_NORTH`      center top    
3. `GRAVITY_NORTHEAST`  right top     
4. `GRAVITY_WEST`       left center   
5. `GRAVITY_CENTER`     center center 
6. `GRAVITY_EAST`       right center  
7. `GRAVITY_SOUTHWEST`  left bottom   
8. `GRAVITY_SOUTH`      center bottom 
9. `GRAVITY_SOUTHEAST`  right bottom  

```php
<?php

use Thapp\Image\Geometry\Gravity;

$crop = new Size(200, 200);

$image->setGravity(new Gravity(Gravity::GRAVITY_CENTER));
$image->edit()->crop($crop);

```

#### Rotating

To rotate an image, you'll have to pass the rotation angel in degrees (not radians). The `rotate` method takes a second argument `$color` which defaults to a white color. The color is needed to fill the canvas for rotation angles that are not devidable by 90. `$color` must be an instance of `Thapp\Image\Color\ColorInterface`.

```php
<?php

use Thapp\Image\Filter\Autorotate;

$color = $image->palette()->getColor('#ff000'); // red background
$image->edit()->rotate(45, $color);

```

#### Auto rotating according to its orientation

If you image contains EXIF data and you've loaded the image with the `ExifReader`, you can use a filter to autororate the image according to its orientation. Using the imagick image driver, the ExifReader is not necessary. 

```php
<?php

use Thapp\Image\Filter\Autorotate;

$image->filter(new AutoRotate));

```
