<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приход товаров';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить приход', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'product_id',
                'value' => function($model) {
                    return $model->product ? $model->product->name : 'Товар не найден';
                },
                'label' => 'Товар',
            ],
            'quantity',
            'price_per_unit:currency',
            [
                'attribute' => 'total_amount',
                'value' => function($model) {
                    return number_format($model->total_amount, 2) . ' ₽';
                },
                'label' => 'Общая сумма',
            ],
            'notes:ntext',
            'created_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>