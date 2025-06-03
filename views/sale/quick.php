<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\QuickSaleForm $model */

$this->title = 'Быстрая продажа';
$this->params['breadcrumbs'][] = ['label' => 'Продажи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register QuaggaJS for barcode scanning
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="sale-quick">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Сканирование штрихкода</h5>
                </div>
                <div class="card-body">
                    <!-- Camera Scanner -->
                    <div class="mb-3">
                        <button id="start-scanner" class="btn btn-primary btn-lg">
                            <i class="fas fa-camera"></i> Включить камеру
                        </button>
                        <button id="stop-scanner" class="btn btn-secondary btn-lg" style="display: none;">
                            <i class="fas fa-stop"></i> Остановить
                        </button>
                    </div>
                    
                    <div id="scanner-container" style="display: none;">
                        <div id="interactive" class="viewport" style="width: 100%; height: 300px; border: 2px solid #ddd;"></div>
                    </div>

                    <!-- Manual Form -->
                    <?php $form = ActiveForm::begin([
                        'id' => 'quick-sale-form',
                        'options' => ['class' => 'mt-4']
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'barcode')->textInput([
                                'placeholder' => 'Отсканируйте или введите штрихкод',
                                'id' => 'barcode-input',
                                'autocomplete' => 'off'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'quantity')->textInput([
                                'type' => 'number',
                                'step' => '0.001',
                                'min' => '0.001',
                                'value' => 1
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'price_per_unit')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0.01',
                                'placeholder' => 'Автоматически'
                            ]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'notes')->textarea(['rows' => 2, 'placeholder' => 'Дополнительные примечания']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Оформить продажу', [
                            'class' => 'btn btn-success btn-lg',
                            'id' => 'submit-sale'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Product Info -->
            <div class="card" id="product-info" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Информация о товаре</h5>
                </div>
                <div class="card-body">
                    <div id="product-details"></div>
                </div>
            </div>

            <!-- Sales Statistics -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Статистика продаж</h5>
                </div>
                <div class="card-body">
                    <div id="sales-stats">
                        <div class="d-flex justify-content-between">
                            <span>Сегодня:</span>
                            <span id="today-sales">Загрузка...</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>За месяц:</span>
                            <span id="month-sales">Загрузка...</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Транзакций:</span>
                            <span id="today-count">Загрузка...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#interactive {
    position: relative;
}

#interactive video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.drawingBuffer {
    position: absolute;
    top: 0;
    left: 0;
}

@media (max-width: 768px) {
    .col-md-4 {
        margin-top: 20px;
    }
}
</style>

<script>
$(document).ready(function() {
    let scannerActive = false;
    
    // Load sales statistics
    loadSalesStats();
    
    // Start scanner
    $('#start-scanner').click(function() {
        startScanner();
    });
    
    // Stop scanner
    $('#stop-scanner').click(function() {
        stopScanner();
    });
    
    // Barcode input change
    $('#barcode-input').on('input', function() {
        const barcode = $(this).val();
        if (barcode.length >= 8) {
            searchProduct(barcode);
        }
    });
    
    // Form submission
    $('#quick-sale-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        submitSale();
        return false;
    });
    
    function startScanner() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            $('#scanner-container').show();
            $('#start-scanner').hide();
            $('#stop-scanner').show();
            
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
                }
            }, function(err) {
                if (err) {
                    console.log(err);
                    alert('Ошибка доступа к камере: ' + err.message);
                    stopScanner();
                    return;
                }
                Quagga.start();
                scannerActive = true;
            });
            
            Quagga.onDetected(function(data) {
                const barcode = data.codeResult.code;
                $('#barcode-input').val(barcode);
                searchProduct(barcode);
                stopScanner();
            });
        } else {
            alert('Камера не поддерживается в этом браузере');
        }
    }
    
    function stopScanner() {
        if (scannerActive) {
            Quagga.stop();
            scannerActive = false;
        }
        $('#scanner-container').hide();
        $('#start-scanner').show();
        $('#stop-scanner').hide();
    }
    
    function searchProduct(barcode) {
        $.get('<?= \yii\helpers\Url::to(['/product/search-by-barcode']) ?>', {barcode: barcode})
            .done(function(data) {
                if (data.success) {
                    showProductInfo(data.product);
                    $('#quicksaleform-price_per_unit').val(data.product.price_per_unit);
                } else {
                    hideProductInfo();
                    alert(data.message);
                }
            })
            .fail(function() {
                alert('Ошибка поиска товара');
            });
    }
    
    function showProductInfo(product) {
        const html = `
            <h6>${product.name}</h6>
            <p class="mb-1"><strong>Штрихкод:</strong> ${product.barcode}</p>
            <p class="mb-1"><strong>Цена:</strong> ${product.price_per_unit} ₽</p>
            <p class="mb-1"><strong>Остаток:</strong> ${product.current_stock} ${product.unit_type_label}</p>
        `;
        $('#product-details').html(html);
        $('#product-info').show();
    }
    
    function hideProductInfo() {
        $('#product-info').hide();
    }
    
    function submitSale() {
        const formData = $('#quick-sale-form').serialize();
        
        $.post('<?= \yii\helpers\Url::to(['/sale/process-quick']) ?>', formData)
            .done(function(data) {
                if (data.success) {
                    alert(`Продажа оформлена!\nТовар: ${data.product}\nСумма: ${data.total} ₽`);
                    $('#quick-sale-form')[0].reset();
                    $('#quicksaleform-quantity').val(1);
                    hideProductInfo();
                    loadSalesStats();
                } else {
                    let errorMsg = 'Ошибка при оформлении продажи:\n';
                    for (let field in data.errors) {
                        errorMsg += data.errors[field].join('\n') + '\n';
                    }
                    alert(errorMsg);
                }
            })
            .fail(function() {
                alert('Ошибка сервера');
            });
    }
    
    function loadSalesStats() {
        $.get('<?= \yii\helpers\Url::to(['/sale/stats']) ?>')
            .done(function(data) {
                $('#today-sales').text(data.today_sales.toLocaleString() + ' ₽');
                $('#month-sales').text(data.month_sales.toLocaleString() + ' ₽');
                $('#today-count').text(data.today_count);
            })
            .fail(function() {
                $('#today-sales').text('Ошибка');
                $('#month-sales').text('Ошибка');
                $('#today-count').text('Ошибка');
            });
    }
});
</script>