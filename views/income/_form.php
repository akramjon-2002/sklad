<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\Income */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="income-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->dropDownList(
        ArrayHelper::map(Product::find()->all(), 'id', 'name'),
        ['prompt' => 'Выберите товар']
    ) ?>

    <?= $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '0.001', 'min' => '0']) ?>

    <?= $form->field($model, 'price_per_unit')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>

    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>