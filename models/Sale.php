<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "sale".
 *
 * @property int $id
 * @property int $product_id
 * @property float $quantity
 * @property float $price_per_unit
 * @property float $total_amount
 * @property string|null $notes
 * @property string $created_at
 *
 * @property Product $product
 */
class Sale extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sale';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'price_per_unit'], 'required'],
            [['product_id'], 'integer'],
            [['quantity', 'price_per_unit', 'total_amount'], 'number'],
            [['notes'], 'string'],
            [['created_at'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['quantity'], 'validateStock'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Товар',
            'quantity' => 'Количество',
            'price_per_unit' => 'Цена за единицу',
            'total_amount' => 'Общая сумма',
            'notes' => 'Примечания',
            'created_at' => 'Дата продажи',
        ];
    }

    /**
     * Gets query for associated product.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Validate if there's enough stock
     */
    public function validateStock($attribute, $params)
    {
        if ($this->product && !$this->product->hasEnoughStock($this->quantity)) {
            $this->addError($attribute, 'Недостаточно товара на складе. Доступно: ' . $this->product->current_stock);
        }
    }

    /**
     * Calculate total amount before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->total_amount = $this->quantity * $this->price_per_unit;
            return true;
        }
        return false;
    }

    /**
     * Update product stock after save
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            $this->product->removeStock($this->quantity);
        }
    }
}