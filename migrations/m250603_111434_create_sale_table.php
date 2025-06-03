<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sale}}`.
 */
class m250603_111434_create_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sale}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 3)->notNull(),
            'price_per_unit' => $this->decimal(10, 2)->notNull(),
            'total_amount' => $this->decimal(10, 2)->notNull(),
            'notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-sale-product', '{{%sale}}', 'product_id');
        $this->createIndex('idx-sale-created', '{{%sale}}', 'created_at');
        
        $this->addForeignKey(
            'fk-sale-product_id',
            '{{%sale}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sale}}');
    }
}
