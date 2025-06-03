<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Quick sale form for barcode scanning
 */
class QuickSaleForm extends Model
{
    public $barcode;
    public $quantity = 1;
    public $price_per_unit;
    public $notes;

    private $_product;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barcode', 'quantity'], 'required'],
            [['quantity', 'price_per_unit'], 'number', 'min' => 0.001],
            [['barcode', 'notes'], 'string'],
            [['barcode'], 'validateProduct'],
            [['quantity'], 'validateStock'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'barcode' => 'Штрихкод',
            'quantity' => 'Количество',
            'price_per_unit' => 'Цена за единицу',
            'notes' => 'Примечания',
        ];
    }

    /**
     * Validate that product exists
     */
    public function validateProduct($attribute, $params)
    {
        $this->_product = Product::find()->where(['barcode' => $this->barcode])->one();
        if (!$this->_product) {
            $this->addError($attribute, 'Товар с таким штрихкодом не найден');
        } else {
            // Set default price if not provided
            if (empty($this->price_per_unit)) {
                $this->price_per_unit = $this->_product->price_per_unit;
            }
        }
    }

    /**
     * Validate stock availability
     */
    public function validateStock($attribute, $params)
    {
        if ($this->_product && !$this->_product->hasEnoughStock($this->quantity)) {
            $this->addError($attribute, 'Недостаточно товара на складе. Доступно: ' . $this->_product->current_stock . ' ' . $this->_product->getUnitTypeLabel());
        }
    }

    /**
     * Get the product
     *
     * @return Product|null
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Process the sale
     *
     * @return bool
     */
    public function processSale()
    {
        if (!$this->validate()) {
            return false;
        }

        $sale = new Sale([
            'product_id' => $this->_product->id,
            'quantity' => $this->quantity,
            'price_per_unit' => $this->price_per_unit,
            'notes' => $this->notes,
        ]);

        return $sale->save();
    }

    /**
     * Get total amount
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->quantity * $this->price_per_unit;
    }
}