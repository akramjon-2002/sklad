<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m250603_111401_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'barcode' => $this->string(255)->unique(),
            'category_id' => $this->integer(),
            'unit_type' => $this->string(20)->notNull()->defaultValue('piece'), // piece, kg, liter
            'price_per_unit' => $this->decimal(10, 2)->notNull(),
            'current_stock' => $this->decimal(10, 3)->defaultValue(0),
            'description' => $this->text(),
            'is_template' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-product-barcode', '{{%product}}', 'barcode');
        $this->createIndex('idx-product-category', '{{%product}}', 'category_id');
        $this->createIndex('idx-product-template', '{{%product}}', 'is_template');
        
        $this->addForeignKey(
            'fk-product-category_id',
            '{{%product}}',
            'category_id',
            '{{%category}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }
}
