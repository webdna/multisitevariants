<?php
/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Enable or disable variants based on site
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2021 Web DNA
 */

namespace webdna\multisitevariantstests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use webdna\multisitevariants\MultisiteVariants;

/**
 * ExampleUnitTest
 *
 *
 * @author    Web DNA
 * @package   MultisiteVariants
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertInstanceOf(
            MultisiteVariants::class,
            MultisiteVariants::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
