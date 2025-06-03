<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Income */

$this->title = 'Добавить приход';
$this->params['breadcrumbs'][] = ['label' => 'Приход товаров', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>