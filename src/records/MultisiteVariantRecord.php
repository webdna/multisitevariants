<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariants\records;

use craft\db\ActiveRecord;

/**
 * Site Variant record.
 *
 * @property int $id
 * @property int $variantId
 * @property int $siteId
 * @property int $stock
 * @property bool $hasUnlimitedStock
 */
class MultisiteVariantRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%commerce_variants_sites}}';
    }

}
