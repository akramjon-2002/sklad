<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $barcode
 * @property int|null $category_id
 * @property string $unit_type
 * @property float $price_per_unit
 * @property float $current_stock
 * @property string|null $description
 * @property bool $is_template
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property Income[] $incomes
 * @property Sale[] $sales
 */
class Product extends ActiveRecord
{
    const UNIT_PIECE = 'piece';
    const UNIT_KG = 'kg';
    const UNIT_LITER = 'liter';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
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
                'updatedAtAttribute' => 'updated_at',
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
            [['name', 'unit_type', 'price_per_unit'], 'required'],
            [['category_id'], 'integer'],
            [['price_per_unit', 'current_stock'], 'number'],
            [['description'], 'string'],
            [['is_template'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'barcode'], 'string', 'max' => 255],
            [['unit_type'], 'string', 'max' => 20],
            [['barcode'], 'unique'],
            [['unit_type'], 'in', 'range' => [self::UNIT_PIECE, self::UNIT_KG, self::UNIT_LITER]],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'barcode' => 'Штрихкод',
            'category_id' => 'Категория',
            'unit_type' => 'Единица измерения',
            'price_per_unit' => 'Цена за единицу',
            'current_stock' => 'Остаток на складе',
            'description' => 'Описание',
            'is_template' => 'Шаблон',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for associated category.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for associated incomes.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIncomes()
    {
        return $this->hasMany(Income::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for associated sales.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sale::class, ['product_id' => 'id']);
    }

    /**
     * Get unit types for dropdown
     *
     * @return array
     */
    public static function getUnitTypes()
    {
        return [
            self::UNIT_PIECE => 'Штука',
            self::UNIT_KG => 'Килограмм',
            self::UNIT_LITER => 'Литр',
        ];
    }

    /**
     * Get unit type label
     *
     * @return string
     */
    public function getUnitTypeLabel()
    {
        $types = self::getUnitTypes();
        return $types[$this->unit_type] ?? $this->unit_type;
    }

    /**
     * Generate barcode if not set
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->barcode)) {
                $this->barcode = $this->generateBarcode();
            }
            return true;
        }
        return false;
    }

    /**
     * Generate unique barcode
     *
     * @return string
     */
    protected function generateBarcode()
    {
        do {
            $barcode = '2' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (self::find()->where(['barcode' => $barcode])->exists());
        
        return $barcode;
    }

    /**
     * Update stock after income
     *
     * @param float $quantity
     */
    public function addStock($quantity)
    {
        $this->current_stock += $quantity;
        $this->save(false);
    }

    /**
     * Update stock after sale
     *
     * @param float $quantity
     * @return bool
     */
    public function removeStock($quantity)
    {
        if ($this->current_stock >= $quantity) {
            $this->current_stock -= $quantity;
            $this->save(false);
            return true;
        }
        return false;
    }

    /**
     * Check if product has enough stock
     *
     * @param float $quantity
     * @return bool
     */
    public function hasEnoughStock($quantity)
    {
        return $this->current_stock >= $quantity;
    }
}