<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "income".
 *
 * @property int $id
 * @property int $product_id
 * @property float $quantity
 * @property float $price_per_unit
 * @property float $total_cost
 * @property string|null $notes
 * @property string $created_at
 *
 * @property Product $product
 */
class Income extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'income';
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
            [['quantity', 'price_per_unit', 'total_cost'], 'number'],
            [['notes'], 'string'],
            [['created_at'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
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
            'total_cost' => 'Общая стоимость',
            'notes' => 'Примечания',
            'created_at' => 'Дата прихода',
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
     * Calculate total cost before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->total_cost = $this->quantity * $this->price_per_unit;
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
            $this->product->addStock($this->quantity);
        }
    }
}