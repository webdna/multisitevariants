<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariants\behaviors;

use webdna\multisitevariants\MultisiteVariants;

use Craft;

use craft\commerce\elements\Variant;
use yii\base\Behavior;


/**
 * @author    Web DNA
 * @package   MultisiteVariants
 * @since     1.0.0
 */
class MultisiteVariantBehavior extends Behavior
{
    /** @var Variant */
    public $owner;

    /**
     * Return variant stock for given siteId, or current site if null
     * 
     * @param int $siteId
     * @return int
     */
    public function getSiteStock(int $siteId = null)
    {
        return MultiSiteVariants::$plugin->service->getVariantSiteStock($this->owner->id, $siteId);
    }

    /**
     * Return total variant stock for all sites
     * 
     * @param int $siteId
     * @return int
     */
    public function getTotalStock()
    {
        return MultiSiteVariants::$plugin->service->getTotalVariantStock($this->owner->id);
    }

    /**
     * Return whether stock is unlimited for given siteId or current site if null
     * 
     * @param int $siteId
     * @return bool
     */
    public function getSiteHasUnlimitedStock(int $siteId = null)
    {
        return MultiSiteVariants::$plugin->service->getVariantSiteStockUnlimited($this->owner->id, $siteId);
    }

    /**
     * Save variant stock for given siteId, or current site if null and update total
     * 
     * @param int $stock
     * @param bool $unlimited
     * @param int $siteId
     * 
     * @return void
     */
    public function saveSiteStock(int $stock, bool $unlimited = false, int $siteId = null)
    {
        MultiSiteVariants::$plugin->service->saveVariantSiteStock($this->owner->id, $stock, $unlimited, $siteId);
    }

    /**
     * Save variant siteSettings in the same way setEnabledForSite() should
     * 
     * @param array $sites - ['sideId' => enabledstatus]
     * 
     * @return void
     */
    public function saveSiteSettings(array $sites)
    {
        MultiSiteVariants::$plugin->service->saveVariantSiteSettings($this->owner->id, $sites);
    }

}