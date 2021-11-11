<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariants;

use webdna\multisitevariants\services\MultisiteVariantsService as MultisiteVariantsServiceService;

use Craft;
use craft\base\Plugin;
use craft\base\Element;
use craft\services\Plugins;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\helpers\ElementHelper;

use craft\commerce\elements\Variant;
use craft\commerce\elements\Product;

use yii\base\Event;

/**
 * Class MultisiteVariants
 *
 * @author    Web DNA
 * @package   MultisiteVariants
 * @since     1.0.0
 *
 * @property  MultisiteVariantsServiceService $multisiteVariantsService
 */
class MultisiteVariants extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var MultisiteVariants
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        $this->setComponents([
            'service' => MultisiteVariantsServiceService::class
        ]);

        Event::on(Product::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $e) {
            // @var Entry $entry
            $product = $e->sender;
            if (ElementHelper::isDraftOrRevision($product)) {
                return;
            }
            $request = Craft::$app->getRequest();
            $siteId = (int)$request->getBodyParam('siteId');
            $variantsParam = $request->getBodyParam('variants');
            $variants =[];  
            foreach ($variantsParam as $id => $value) {
                $variants[$id] = (bool)$value['enabledForSite'];
            }
            $this->service->saveVariantsForSites($variants,$siteId);
        });

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Craft::$app->getView()->hook('cp.commerce.product.edit.details', [$this->service, 'addVariantEnabledFields']);
        }


        Craft::info(
            Craft::t(
                'multisite-variants',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
