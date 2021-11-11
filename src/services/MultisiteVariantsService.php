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

use webdna\multisitevariants\MultisiteVariants;
use webdna\multisitevariants\assetbundles\multisitevariants\MultisiteVariantsAsset;

use Craft;
use craft\base\Component;
use craft\records\Element_SiteSettings as Element_SiteSettingsRecord;


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
     * Saves a sitesettings record for an array of variants in one transaction
     * 
     * @param array $variants [$variantId => $enabled]
     * @param int $siteId
     * @return void
     */
    public function saveVariantsForSites(array $variants, $siteId)
    {
        $transaction = Craft::$app->getDb()->beginTransaction();
        foreach ($variants as $variantId => $enabled) {
            $this->saveVariantSiteSettings($variantId, $siteId, $enabled);
        }
        $transaction->commit();
    }

    /**
     * Saves a sitesettings record for a variant
     * 
     * @param int $variantId
     * @param int $siteId
     * @param bool $enabled
     * @return bool
     */
     public function saveVariantSiteSettings(int $variantId,int $siteId, bool $enabled)
     {
        if (!$variantId || !$siteId) {
            return false;
        }

        $siteSettingsRecord = Element_SiteSettingsRecord::findOne([
            'elementId' => $variantId,
            'siteId' => $siteId
        ]);
        if (!$siteSettingsRecord) {
            # create a new one, are we sure we want to do this?
            return false;
        }
    
        $siteSettingsRecord->enabled = $enabled;
        $siteSettingsRecord->save(false);
    
        return true;
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
        Craft::$app->getView()->registerAssetBundle(MultisiteVariantsAsset::class);
        return Craft::$app->getView()->renderTemplate('multisite-variants/SiteEnabledLightSwitch',[
              'variants' => $product->variants
        ]);
      }
}
