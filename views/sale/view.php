<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sale */

$this->title = 'Продажа #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Продажи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sale-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту продажу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'product_id',
                'value' => $model->product ? $model->product->name : 'Товар не найден',
                'label' => 'Товар',
            ],
            [
                'attribute' => 'product.barcode',
                'label' => 'Штрихкод товара',
            ],
            'quantity',
            'price_per_unit:currency',
            [
                'attribute' => 'total_amount',
                'value' => number_format($model->total_amount, 2) . ' ₽',
                'label' => 'Общая сумма',
            ],
            'notes:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>