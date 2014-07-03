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
     *
     * @param $imageRelativePath The relative path from 'media' folder
     * @param $width
     * @param null $height
     * @return null|string The URL of the cropped image
     */
    public function crop($imageRelativePath, $width, $height = null)
    {
        // Retrieve image absolute path and basename
        $image      = $this->_getImageAbsolutePath($imageRelativePath);
        $imageName  = $this->_getImageBaseName($image);

        // If source image does not exist
        if (!is_file($image)) {
            return null;
        }

        // Determine the height of the generated image
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

        // Parameters hash
        $parametersHash = $this->_getParametersHash($parameters);

        // Directories
        $baseDir            = $this->_getMediaBaseDir();
        $intermediateDir    = $this->_generateIntermediateDir($width, $height, $parametersHash, $imageName);
        $dir                = $baseDir . DS . $intermediateDir;

        // Check if destination directory exists
        $this->_checkDestinationDir($dir);

        // Get the new image Url
        $imageUrl = $this->_getImageUrl($intermediateDir, $imageName);

        // The new image full path
        $imageFullPath = $dir . DS . $imageName;

        // If the cropped image has been already generated, we return it and skip another useless generation
        if (is_file($imageFullPath)) {
            return $imageUrl;
        }

        /*
         * First, resize the image
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

        /*
         * Then we crop the image previously resized
         */
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
     * Resize the image then return the URL
     *
     * @param $imageRelativePath The relative path from 'media' folder
     * @param $width
     * @param null $height
     * @return null|string The URL of the resized image
     */
    public function resize($imageRelativePath, $width, $height = null)
    {
        // Retrieve image absolute path and basename
        $image      = $this->_getImageAbsolutePath($imageRelativePath);
        $imageName  = $this->_getImageBaseName($image);

        // If source image does not exist
        if (!is_file($image)) {
            return null;
        }

        // Determine the 'height path' value of the generated image
        if ($height === null) {
            $heightPathValue = 0;
        } else {
            $heightPathValue = $height;
        }

        // Misc parameters
        $parameters = [
            'constrainOnly',
            'keepAspectRatio',
            'keepFrame',
            'resize',
        ];

        // Parameters hash
        $parametersHash = $this->_getParametersHash($parameters);

        // Directories
        $baseDir            = $this->_getMediaBaseDir();
        $intermediateDir    = $this->_generateIntermediateDir($width, $heightPathValue, $parametersHash, $imageName);
        $dir                = $baseDir . DS . $intermediateDir;

        // Check if destination directory exists
        $this->_checkDestinationDir($dir);

        // Get the new image Url
        $imageUrl = $this->_getImageUrl($intermediateDir, $imageName);

        // The new image full path
        $imageFullPath = $dir . DS . $imageName;

        // If the resized image has been already generated, we return it and skip another useless generation
        if (is_file($imageFullPath)) {
            return $imageUrl;
        }

        /*
         * Resize the image
         */
        $imageObj  = new Varien_Image($image);

        // Constraints
        $imageObj->constrainOnly(true);
        $imageObj->keepAspectRatio(true);
        $imageObj->keepFrame(false);

        $imageObj->resize($width, $height);
        $imageObj->save($dir, $imageName);
        unset($imageObj);

        // Return the new image URL
        return $imageUrl;
    }

    /**
     * Set the current prefix
     *
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * Retrieve the current prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Generate the intermediate dir path (without )
     *
     * @param $width
     * @param $height
     * @param $parametersHash
     * @param $imageName
     * @return string
     */
    protected function _generateIntermediateDir($width, $height, $parametersHash, $imageName)
    {
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

        return $intermediateDir;
    }

    /**
     * Get parameters hash (md5 based)
     *
     * @param $parameters
     * @return string
     */
    protected function _getParametersHash($parameters)
    {
        return md5(implode('|', $parameters));
    }

    /**
     * Get image absolute path
     *
     * @param $imageRelativePath
     * @return string
     */
    protected function _getImageAbsolutePath($imageRelativePath)
    {
        return Mage::getBaseDir('media') . DS . ltrim($imageRelativePath, '/');
    }

    /**
     * Get image base name
     *
     * @param $imageAbsolutePath
     * @return string
     */
    protected function _getImageBaseName($imageAbsolutePath)
    {
        return basename($imageAbsolutePath);
    }

    /**
     * Get finale image Url
     *
     * @param $intermediateDir
     * @param $imageName
     * @return string
     */
    protected function _getImageUrl($intermediateDir, $imageName)
    {
        return Mage::getBaseUrl('media') . $intermediateDir . '/' . $imageName;
    }

    /**
     * Get 'media' folder base directory
     *
     * @return string
     */
    protected function _getMediaBaseDir()
    {
        return Mage::getBaseDir('media');
    }

    /**
     * Check if destination directory exists or not, and create it if not
     *
     * @param $dir
     */
    protected function _checkDestinationDir($dir)
    {
        if (!@is_dir($dir)) {
            (new Varien_Io_File)
                ->setAllowCreateFolders(true)
                ->createDestinationDir($dir)
            ;
        }
    }

// Monsieur Biz Tag NEW_METHOD

}