# Mbiz_ImageCrop

Simple helper to crop images in Magento

## Howto

```php
<?php
$width = 100; // argument required
$height = 200; // argument optional
$imagePath = 'foo/bar.png'; // Your image is in the /media directory. In /media/foo/ precisely.

// The method of the helper
// crop($mediaFilename, $width, $height = null);

$imageUrl = Mage::helper('mbiz_imagecrop')->crop($imagePath, $width, $height);
```

You can also set a prefix of the new image directory. The prefix is a firectory.  
By default our new image is saved in `/media/cache/100x200/hash/b/a/bar.png`.  
But if you set a prefix, like:

```php
<?php

$imageUrl = Mage::helper('mbiz_imagecrop')->setPrefix('baz')->crop($imagePath, $width, $height);  
```

The image will be saved in `/media/baz/cache/100x200/hash/b/a/bar.png`.


Have fun!
