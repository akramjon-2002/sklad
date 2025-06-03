<?php

use app\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Экспорт', ['export'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                }
            ],
            'barcode',
            [
                'attribute' => 'categoryName',
                'label' => 'Категория',
                'value' => 'category.name',
            ],
            [
                'attribute' => 'price_per_unit',
                'label' => 'Цена',
                'format' => ['currency', 'RUB'],
            ],
            [
                'attribute' => 'current_stock',
                'label' => 'Остаток',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = 'badge ';
                    if ($model->current_stock == 0) {
                        $class .= 'bg-danger';
                    } elseif ($model->current_stock <= 10) {
                        $class .= 'bg-warning';
                    } else {
                        $class .= 'bg-success';
                    }
                    return Html::tag('span', $model->current_stock . ' ' . $model->getUnitTypeLabel(), ['class' => $class]);
                }
            ],
            [
                'attribute' => 'unit_type',
                'label' => 'Единица',
                'filter' => Product::getUnitTypes(),
                'value' => function ($model) {
                    return $model->getUnitTypeLabel();
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Создан',
                'format' => ['date', 'php:d.m.Y'],
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {barcode} {delete}',
                'buttons' => [
                    'barcode' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-barcode"></i>', ['barcode', 'id' => $model->id], [
                            'title' => 'Штрихкод',
                            'target' => '_blank',
                            'class' => 'btn btn-sm btn-outline-info'
                        ]);
                    },
                ],
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
        'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
    ]); ?>

</div>

<style>
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>