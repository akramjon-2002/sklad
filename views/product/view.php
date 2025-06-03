<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этот товар?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'name',
                'label' => 'Название',
            ],
            [
                'attribute' => 'barcode',
                'label' => 'Штрихкод',
            ],
            [
                'attribute' => 'category_id',
                'value' => $model->category ? $model->category->name : 'Не указана',
                'label' => 'Категория',
            ],
            [
                'attribute' => 'price_per_unit',
                'value' => number_format($model->price_per_unit, 0, '.', ' ') . ' ₽',
                'label' => 'Цена за единицу',
            ],
            [
                'attribute' => 'unit_type',
                'label' => 'Единица измерения',
            ],
            [
                'attribute' => 'current_stock',
                'label' => 'Остаток на складе',
            ],
            [
                'attribute' => 'is_template',
                'value' => $model->is_template ? 'Да' : 'Нет',
                'label' => 'Шаблон',
            ],
            [
                'attribute' => 'description',
                'label' => 'Описание',
            ],
            [
                'attribute' => 'created_at',
                'value' => date('d.m.Y H:i', strtotime($model->created_at)),
                'label' => 'Создано',
            ],
            [
                'attribute' => 'updated_at',
                'value' => date('d.m.Y H:i', strtotime($model->updated_at)),
                'label' => 'Обновлено',
            ],
        ],
    ]) ?>

</div>