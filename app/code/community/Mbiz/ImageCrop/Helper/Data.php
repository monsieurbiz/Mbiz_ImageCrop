<?php
/**
 * This file is part of Mbiz_ImageCrop for Magento.
 *
 * @license All rights reserved
 * @author Jacques Bodin-Hullin <@jacquesbh> <j.bodinhullin@monsieurbiz.com>
 * @category Mbiz
 * @package Mbiz_ImageCrop
 * @copyright Copyright (c) 2014 Monsieur Biz (http://monsieurbiz.com/)
 */

/**
 * Data Helper
 * @package Mbiz_ImageCrop
 */
class Mbiz_ImageCrop_Helper_Data extends Mage_Core_Helper_Abstract
{

// Monsieur Biz Tag NEW_CONST

    /**
     * The prefix directory (under media)
     * @var string
     */
    protected $_prefix = null;

// Monsieur Biz Tag NEW_VAR

    /**
     * Crop the image then return the URL
     * @return string The URL of the image croped
     */
    public function crop($mediaFilename, $width, $height = null)
    {
        // The image
        $image = Mage::getBaseDir('media') . DS . ltrim($mediaFilename, '/');
        $imageName = basename($image);

        // Not found
        if (!is_file($image)) {
            return null;
        }

        // The dimensions of the destination
        if ($height === null) {
            $height = $width;
        }

        // Misc parameters
        $parameters = [
            'constrainOnly',
            'keepAspectRatio',
            'keepFrame',
            'crop',
        ];
        $parametersHash = md5(implode('|', $parameters));

        // The directories
        $baseDir         = Mage::getBaseDir('media');
        $intermediateDir = '';

        // Prefix the directory
        if ($prefix = $this->getPrefix()) {
            $intermediateDir .= $prefix . DS;
        }

        $intermediateDir .= 'cache' . DS
                          . $width . 'x' . $height . DS
                          . $parametersHash . DS
                          . strtolower($imageName[0]) . DS
                          . strtolower(isset($imageName[1]) && $imageName[1] !== '.' ? $imageName[1] : $imageName[0])
        ;
        $dir = $baseDir . DS . $intermediateDir;

        // We create the directory
        if (!@is_dir($dir)) {
            (new Varien_Io_File)
                ->setAllowCreateFolders(true)
                ->createDestinationDir($dir)
            ;
        }

        // The final image URL
        $imageUrl = Mage::getBaseUrl('media') . $intermediateDir . '/' . $imageName;

        // The new image full path
        $imageFullPath = $dir . DS . $imageName;

        // If we already have the image croped, skip it.
        if (is_file($imageFullPath)) {
            return $imageUrl;
        }

        /*
         * Crop!
         */
        $imageObj  = new Varien_Image($image);
        $oldHeight = $imageObj->getOriginalHeight();
        $oldWidth  = $imageObj->getOriginalWidth();

        // Constraints
	$imageObj->constrainOnly(true);
	$imageObj->keepAspectRatio(true);
	$imageObj->keepFrame(false);

        // Resize in the good way
        if (($oldWidth / $oldHeight) < ($width / $height)) {
            $imageObj->resize($width, null);
        } else {
            $imageObj->resize(null, $height);
        }

	$imageObj->save($dir, $imageName);
        unset($imageObj);

        $imageObj2 = new Varien_Image($imageFullPath);
        $top       = ($imageObj2->getOriginalHeight() - $height) / 2;
        $left      = ($imageObj2->getOriginalWidth() - $width) / 2;

        $imageObj2->crop($top, $left, $left, $top);
	$imageObj2->save($imageFullPath);

        unset($imageObj2);

        // Return the new image URL
        return $imageUrl;
    }

    /**
     * Set the prefix
     * @return Mbiz_ImageCrop_Helper_Data
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * Retrieve the prefix
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

// Monsieur Biz Tag NEW_METHOD

}