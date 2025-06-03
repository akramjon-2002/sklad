<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%income}}`.
 */
class m250603_111417_create_income_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%income}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 3)->notNull(),
            'price_per_unit' => $this->decimal(10, 2)->notNull(),
            'total_cost' => $this->decimal(10, 2)->notNull(),
            'notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-income-product', '{{%income}}', 'product_id');
        $this->createIndex('idx-income-created', '{{%income}}', 'created_at');
        
        $this->addForeignKey(
            'fk-income-product_id',
            '{{%income}}',
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
        $this->dropTable('{{%income}}');
    }
}
