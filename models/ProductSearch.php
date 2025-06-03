<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $categoryName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id'], 'integer'],
            [['name', 'barcode', 'unit_type', 'description', 'categoryName'], 'safe'],
            [['price_per_unit', 'current_stock'], 'number'],
            [['is_template'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()->joinWith('category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        $dataProvider->sort->attributes['categoryName'] = [
            'asc' => ['category.name' => SORT_ASC],
            'desc' => ['category.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product.id' => $this->id,
            'category_id' => $this->category_id,
            'price_per_unit' => $this->price_per_unit,
            'current_stock' => $this->current_stock,
            'is_template' => $this->is_template,
        ]);

        $query->andFilterWhere(['ilike', 'product.name', $this->name])
            ->andFilterWhere(['ilike', 'barcode', $this->barcode])
            ->andFilterWhere(['=', 'unit_type', $this->unit_type])
            ->andFilterWhere(['ilike', 'product.description', $this->description])
            ->andFilterWhere(['ilike', 'category.name', $this->categoryName]);

        return $dataProvider;
    }

    /**
     * Search by barcode
     *
     * @param string $barcode
     * @return Product|null
     */
    public static function findByBarcode($barcode)
    {
        return Product::find()->where(['barcode' => $barcode])->one();
    }
}