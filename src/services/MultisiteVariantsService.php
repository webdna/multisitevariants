<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariants\services;

use webdna\multisitevariants\assetbundles\multisitevariants\MultisiteVariantsAsset;
use webdna\multisitevariants\records\MultisiteVariantRecord;

use Craft;
use craft\base\Component;
use craft\records\Element_SiteSettings as Element_SiteSettingsRecord;
use craft\commerce\elements\Variant;

/**
 * @author    Web DNA
 * @package   MultisiteVariants
 * @since     1.0.0
 */
class MultisiteVariantsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Return stock for given variant id and site id, uses current site if $siteId is null
     *
     * @param int $variantId
     * @param int $siteId
     * @return int
     */
    public function getVariantSiteStock(int $variantId, int $siteId = null):int
    {
        if ($siteId === null) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }

        $record = MultisiteVariantRecord::findOne(['siteId' => $siteId, 'variantId' => $variantId]);

        if(!$record) {
            return 0;
        }
        return $record->stock;
    }

    /**
     * Returns if there is stock available for the given variant id and site id, uses current site if $siteId is null
     *
     * @param int $variantId
     * @param int $siteId
     * @return bool
     */
    public function hasVariantSiteStock(int $variantId, int $siteId = null): bool
    {
        $stock = $this->getVariantSiteStock($variantId, $siteId);
        $unlimitedStock = $this->getVariantSiteStockUnlimited($variantId, $siteId);

        return $stock > 0 || $unlimitedStock;
    }

    /**
     * Return total stock for given variant id across all sites
     *
     * @param int $variantId
     * @return int
     */
    public function getTotalVariantStock(int $variantId):int
    {
        $records = MultisiteVariantRecord::findAll(['variantId' => $variantId]);

        $totalStock = 0;

        foreach ($records as $record) {
            $totalStock = $totalStock + $record->stock;
        }

        return $totalStock;
    }

    /**
     * Return hasUnlimitedStock value for given variant id and site id, uses current site if $siteId is null
     *
     * @param int $variantId
     * @param int $siteId
     * @return bool
     */
    public function getVariantSiteStockUnlimited(int $variantId, int $siteId = null)
    {
        if ($siteId === null) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }
        $record = MultisiteVariantRecord::findOne(['siteId' => $siteId, 'variantId' => $variantId]);

        if(!$record) {
            return false;
        }
        return (bool)$record->hasUnlimitedStock;
    }

    /**
     * Save the stock value for a given variant id and site id, uses current site if $site is null
     *
     * @param int $variantId
     * @param int $stock
     * @param bool $unlimited
     * @param int $siteId
     * @return void
     */
    public function saveVariantSiteStock(int $variantId, int $stock, bool $unlimited, int $siteId = null)
    {
        if ($siteId === null) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }
        $record = MultisiteVariantRecord::findOne(['siteId' => $siteId, 'variantId' => $variantId]);

        if(!$record) {
            $record = new MultisiteVariantRecord;
            $record->variantId = $variantId;
            $record->siteId = $siteId;
        }
        $record->stock = $stock;
        $record->hasUnlimitedStock = $unlimited;
        $record->save();
    }

    /**
     * Sets whether the variant is enabled for the current site.
     *
     * This can also be set to an array of site ID/site-enabled mappings.
     *
     * @param int $variantId
     * @param bool|bool[] $enabledForSite
     * @return bool
     */
    public function saveVariantSiteSettings(int $variantId, $enabledForSite)
    {
        if (!$variantId) {
            return false;
        }
        if (is_array($enabledForSite)) {
            foreach ($enabledForSite as &$value) {
                $value = (bool)$value;
            }
        } else {
            $enabledForSite = [Craft::$app->getSites()->currentSite->id => (bool)$enabledForSite];
        }

        foreach ($enabledForSite as $siteId => $enabled) {
            $siteSettingsRecord = Element_SiteSettingsRecord::findOne([
                'elementId' => $variantId,
                'siteId' => $siteId
            ]);
            if(!$siteSettingsRecord) {
                continue;
            }
            $siteSettingsRecord->enabled = $enabled;
            $siteSettingsRecord->save(false);
        }

        return true;
    }

    /**
     * Updates Stock count from completed order. -- recreates craft\commerce\elemenets\Variant::afterOrderComplete
     *
     * @param Variant $variant
     * @param int $qty
     * @param int $siteId
     * @return void
     */
    public function afterOrderComplete(Variant $variant, int $qty, int $siteId = null)
    {
        // Is it unlimited for this site?
        if(!$this->getVariantSiteStockUnlimited($variant->id)) {
            // let's check it's not already done
            if ($variant->stock == ($this->getTotalVariantStock($variant->id) - $qty)) {
                $oldstock = $this->getVariantSiteStock($variant->id);
                $newstock = $oldstock - $qty;
                $this->saveVariantSiteStock($variant->id, $newstock, false);
            }
        }
    }

    /**
     * Deletes all variant rows from commerce_variants_sites table
     *
     * @param int $variantId
     * @return void
     */
    public function deleteVariantSiteStock(int $variantId)
    {
        MultisiteVariantRecord::deleteAll(['variantId' => $variantId]);
    }

    /**
     * Adds the variant enabled for site switches to the template
     *
     * @param array $context
     * @return string
     */
    public function addVariantEnabledFields(array &$context)
    {
        $product = $context['product'];
        $site = $context['site'];

        Craft::$app->getView()->registerAssetBundle(MultisiteVariantsAsset::class);
        return Craft::$app->getView()->renderTemplate('multisite-variants/MultiSiteFields',[
            'variants' => $product->variants,
            'site' => $site->id
        ]);
    }
}
