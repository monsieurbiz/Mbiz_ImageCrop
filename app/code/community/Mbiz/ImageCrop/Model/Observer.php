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
 * Observer Model
 * @package Mbiz_ImageCrop
 */
class Mbiz_ImageCrop_Model_Observer extends Mage_Core_Model_Abstract
{

// Antoine Kociuba Tag NEW_CONST

// Antoine Kociuba Tag NEW_VAR

    /**
     * Flush image cache folders, when the catalog image cache is flushed
     *
     * @param Varien_Event_Observer $observer
     * @return Mbiz_ImageCrop_Model_Observer
     */
    public function cleanImageCache(Varien_Event_Observer $observer)
    {
        // Flush /media/cache folder
        $directory = Mage::getBaseDir('media') . DS . 'cache' . DS;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);

        // Flush /media/*/cache folders (folders with prefix)
        foreach(glob(Mage::getBaseDir('media') . DS . '*' . DS . 'cache' . DS) as $directory) {
            $io->rmdir($directory, true);
        }

        return $this;
    }

// Antoine Kociuba Tag NEW_METHOD

}