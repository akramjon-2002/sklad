<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
/** @var app\models\Category[] $categories */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'category_id')->dropDownList(
                ArrayHelper::map($categories, 'id', 'name'),
                ['prompt' => 'Выберите категорию']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>
            <small class="form-text text-muted">Оставьте пустым для автогенерации</small>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'price_per_unit')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'unit_type')->dropDownList(
                \app\models\Product::getUnitTypes(),
                ['prompt' => 'Выберите единицу измерения']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'current_stock')->textInput(['type' => 'number', 'step' => '0.001', 'min' => '0']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'is_template')->checkbox() ?>
            <small class="form-text text-muted">Шаблон товара (без учета остатков)</small>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
$(document).ready(function() {
    // Generate barcode if empty
    $('#product-name').on('blur', function() {
        const name = $(this).val();
        const barcodeField = $('#product-barcode');
        
        if (name && !barcodeField.val()) {
            // Simple barcode generation based on name hash
            const hash = name.split('').reduce((a, b) => {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0);
            
            const barcode = '2' + Math.abs(hash).toString().padStart(11, '0').substring(0, 11);
            barcodeField.val(barcode);
        }
    });
});
</script>