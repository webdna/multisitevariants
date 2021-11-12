<?php

namespace webdna\multisitevariants\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use craft\helpers\MigrationHelper;

use craft\commerce\db\Table as CommerceTable;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%commerce_variants_sites}}', [
            'id' => $this->primaryKey(),
            'variantId' => $this->integer()->notNull(),
            'siteId' => $this->integer()->notNull(),
            'stock' => $this->integer()->defaultValue(0),
            'hasUnlimitedStock' => $this->boolean(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);
        $this->createIndex(null, '{{%commerce_variants_sites}}', ['variantId', 'siteId'], false);
        $this->addForeignKey(null, '{{%commerce_variants_sites}}', ['variantId'], CommerceTable::VARIANTS, ['id'], 'CASCADE','CASCADE');
        $this->addForeignKey(null, '{{%commerce_variants_sites}}', ['siteId'], CraftTable::SITES, ['id'], 'CASCADE','CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        MigrationHelper::dropAllForeignKeysToTable('{{commerce_variants_sites}}', $this);
        $this->dropTableIfExists('{{%commerce_variants_sites}}');

    }
}
