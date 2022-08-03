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
use webdna\multisitevariants\behaviors\MultisiteVariantBehavior;

use Craft;
use craft\base\Plugin;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineRulesEvent;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\helpers\ElementHelper;
use craft\services\Plugins;

use craft\commerce\elements\Variant;
use craft\commerce\elements\Order;
use craft\commerce\models\LineItem;

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


        Event::on(
            LineItem::class,
            LineItem::EVENT_DEFINE_RULES,
            function(DefineRulesEvent $event) {
                $lineItem = $event->sender;
                $variant = $lineItem->purchasable;

                $qtyRuleIndex = null;
                foreach ($event->rules as $key => $value) {
                    if ($value[0] == 'qty' ?? null) {
                        $qtyRuleIndex = $key;
                    }
                }

                // Remove stock check rule if item has site stock
                if($variant->hasSiteStock()) {
                    unset($event->rules[$qtyRuleIndex]);
                }
            }
        );

        Event::on(
            Variant::class,
            Variant::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    MultisiteVariantBehavior::class,
                ]);
            }
        );

        // create the stock records before the variant is saved to pass the correct total stock
        Event::on(Variant::class, Element::EVENT_BEFORE_SAVE, function(ModelEvent $e) {
            $request = Craft::$app->getRequest();
            // This is only for CP saves, need to exclude queue jobs
            if ($request->getIsCpRequest() && $request->getBodyParam('variants')) {
                $variant = $e->sender;
                $postData = $request->getBodyParam('variants');
                if (array_key_exists($variant->id, $postData)) {
                    // Need to think about how to save stock for new variant, other than wait for first load
                    $variantData = $request->getBodyParam('variants')[$variant->id];
                    $siteId = (int)$request->getBodyParam('siteId');
                    $stock = (int)(array_key_exists('stock',$variantData) ? $variantData['stock'] : 0); //should this be zero just because its missing from the POST?
                    $unlimited = (bool)(array_key_exists('hasUnlimitedStock',$variantData) ? $variantData['hasUnlimitedStock'] : false);

                    $this->service->saveVariantSiteStock($variant->id, $stock, $unlimited, $siteId);
                    // $variant->stock = $variant->getTotalStock(); -- separate total stock from site stock for the time being
                    $e->sender = $variant;
                }

            }
        });

        // update the site settings after variant save to make sure there is an existing site settings record.
        Event::on(Variant::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $e) {
            $request = Craft::$app->getRequest();
            if ($request->getIsCpRequest() && $request->getBodyParam('variants')) {
                $variant = $e->sender;
                $postData = $request->getBodyParam('variants');
                if (array_key_exists($variant->id, $postData)) {
                    $siteId = (int)$request->getBodyParam('siteId');
                    $variantData = $request->getBodyParam('variants')[$variant->id];
                    $this->service->saveVariantSiteSettings($variant->id, [$siteId => (bool)$variantData['enabledForSite']]);
                }
            }
        });

        // Update the site stock to match the reduced total stock on order complete
        Event::on(Order::class, Order::EVENT_AFTER_COMPLETE_ORDER, function(Event $e) {
            // $order = $e->sender;
            // foreach ($order->lineItems as $lineItem) {
            //     $this->service->afterOrderComplete($lineItem->getPurchasable(), $lineItem->qty);
            // }
        });

        // Make sure the rows are deleted on variant deletion, cascade should take care of this
        Event::on(Variant::class, Element::EVENT_AFTER_DELETE, function(Event $e) {
            $this->service->deleteVariantSiteStock($e->sender->id);
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
