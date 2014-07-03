# Mbiz_ImageCrop

Simple helper to crop and/or resize images in Magento

## Howto Crop

```php
<?php
$width = 100; // required argument
$height = 200; // optional argument
$imagePath = 'foo/bar.png'; // Your image is in the /media directory. In /media/foo/ precisely.

// The method of the helper
// crop($imageRelativePath, $width, $height = null);

$imageUrl = Mage::helper('mbiz_imagecrop')->crop($imagePath, $width, $height);
```

## Howto Resize

```php
<?php
$width = 100; // required argument
$height = 200; // optional argument
$imagePath = 'foo/bar.png'; // Your image is in the /media directory. In /media/foo/ precisely.

// The method of the helper
// resize($imageRelativePath, $width, $height = null);

$imageUrl = Mage::helper('mbiz_imagecrop')->resize($imagePath, $width, $height);
```

You can also set a prefix of the new image directory. The prefix is a directory.
By default, our new image will be saved in `/media/cache/100x200/hash/b/a/bar.png`.
But if you set a prefix, like:

```php
<?php
$imageUrl = Mage::helper('mbiz_imagecrop')->setPrefix('baz')->crop($imagePath, $width, $height);
```
or

```php
<?php
$imageUrl = Mage::helper('mbiz_imagecrop')->setPrefix('baz')->resize($imagePath, $width, $height);
```

The image will be saved in `/media/baz/cache/100x200/hash/b/a/bar.png`.

Have fun!