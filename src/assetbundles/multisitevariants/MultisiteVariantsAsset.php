<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariants\assetbundles\multisitevariants;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Web DNA
 * @package   MultisiteVariants
 * @since     1.0.0
 */
class MultisiteVariantsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@webdna/multisitevariants/assetbundles/multisitevariants/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/multisite-variants.js',
        ];

        $this->css = [
            'css/multisite-variants.css',
        ];

        parent::init();
    }
}
