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

<div class="sale-quick fade-in">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 text-gradient mb-2"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Сканируйте штрихкод или введите его вручную</p>
        </div>
        <div class="badge bg-success fs-6 px-3 py-2">
            📱 Быстрая продажа
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="scanner-container">
                <h5 class="mb-4 d-flex align-items-center">
                    <span class="me-2">📷</span>
                    Сканирование штрихкода
                </h5>
                
                <div class="text-center mb-4">
                    <div class="scanner-overlay mb-4">
                        <div id="interactive" class="viewport scanner-video" style="width: 100%; height: 300px; display: none;"></div>
                        <div id="scanner-placeholder" class="d-flex align-items-center justify-content-center" style="width: 100%; height: 300px; background: var(--gray-100); border-radius: var(--radius-lg); border: 2px dashed var(--gray-300);">
                            <div class="text-center">
                                <div style="font-size: 4rem; margin-bottom: 1rem;">📷</div>
                                <p class="text-muted">Нажмите "Включить камеру" для сканирования</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-center mb-4">
                        <button id="start-scanner" class="btn btn-primary btn-lg">
                            📹 Включить камеру
                        </button>
                        <button id="stop-scanner" class="btn btn-outline-secondary btn-lg" style="display: none;">
                            ⏹️ Остановить
                        </button>
                    </div>
                    
                    <div class="alert alert-info border-0">
                        <div class="d-flex align-items-center">
                            <span class="me-2">💡</span>
                            <span>Наведите камеру на штрихкод товара для автоматического сканирования</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <span class="me-2">🛒</span>
                        Оформление продажи
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'quick-sale-form',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true]
                    ]); ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'barcode')->textInput([
                            'placeholder' => 'Отсканируйте или введите штрихкод',
                            'class' => 'form-control form-control-lg',
                            'id' => 'barcode-input',
                            'autocomplete' => 'off'
                        ])->label('Штрихкод товара', ['class' => 'form-label fw-semibold']) ?>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <?= $form->field($model, 'quantity')->textInput([
                                'type' => 'number',
                                'min' => '0.01',
                                'step' => '0.01',
                                'value' => '1',
                                'class' => 'form-control'
                            ])->label('Количество', ['class' => 'form-label fw-semibold']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'price_per_unit')->textInput([
                                'type' => 'number',
                                'min' => '0',
                                'step' => '0.01',
                                'class' => 'form-control',
                                'placeholder' => 'Цена за единицу'
                            ])->label('Цена за единицу (₽)', ['class' => 'form-label fw-semibold']) ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton('💰 Оформить продажу', [
                            'class' => 'btn btn-success btn-lg',
                            'id' => 'submit-sale'
                        ]) ?>
                        
                        <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-outline-secondary">
                            ← Вернуться к списку продаж
                        </a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Product Info Card -->
            <div id="product-info" class="card mt-4" style="display: none;">
                <div class="card-header bg-light">
                    <h6 class="mb-0 d-flex align-items-center">
                        <span class="me-2">📦</span>
                        Информация о товаре
                    </h6>
                </div>
                <div class="card-body">
                    <div id="product-details"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.scanner-overlay {
    position: relative;
    display: inline-block;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.scanner-overlay::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 2px;
    background: var(--danger);
    box-shadow: 0 0 10px var(--danger);
    animation: scanLine 2s ease-in-out infinite;
    z-index: 10;
}

@keyframes scanLine {
    0%, 100% {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}

.viewport {
    border-radius: var(--radius-lg);
    overflow: hidden;
}

#barcode-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-lg {
    padding: var(--space-4) var(--space-6);
    font-size: 1.1rem;
}
</style>

<?php
$this->registerJs("
let scanner = null;
let isScanning = false;

// Start scanner
document.getElementById('start-scanner').addEventListener('click', function() {
    startScanner();
});

// Stop scanner
document.getElementById('stop-scanner').addEventListener('click', function() {
    stopScanner();
});

function startScanner() {
    if (isScanning) return;
    
    document.getElementById('scanner-placeholder').style.display = 'none';
    document.getElementById('interactive').style.display = 'block';
    document.getElementById('start-scanner').style.display = 'none';
    document.getElementById('stop-scanner').style.display = 'inline-block';
    
    Quagga.init({
        inputStream: {
            name: 'Live',
            type: 'LiveStream',
            target: document.querySelector('#interactive'),
            constraints: {
                width: 640,
                height: 480,
                facingMode: 'environment'
            }
        },
        decoder: {
            readers: [
                'code_128_reader',
                'ean_reader',
                'ean_8_reader',
                'code_39_reader',
                'code_39_vin_reader',
                'codabar_reader',
                'upc_reader',
                'upc_e_reader'
            ]
        }
    }, function(err) {
        if (err) {
            console.error('Ошибка инициализации сканера:', err);
            alert('Не удалось запустить камеру. Проверьте разрешения.');
            stopScanner();
            return;
        }
        console.log('Сканер запущен');
        Quagga.start();
        isScanning = true;
    });
    
    Quagga.onDetected(function(data) {
        const barcode = data.codeResult.code;
        console.log('Штрихкод обнаружен:', barcode);
        
        // Fill barcode input
        document.getElementById('barcode-input').value = barcode;
        
        // Trigger change event to load product info
        document.getElementById('barcode-input').dispatchEvent(new Event('change'));
        
        // Stop scanner after successful scan
        setTimeout(() => {
            stopScanner();
        }, 1000);
    });
}

function stopScanner() {
    if (scanner) {
        Quagga.stop();
        isScanning = false;
    }
    
    document.getElementById('scanner-placeholder').style.display = 'flex';
    document.getElementById('interactive').style.display = 'none';
    document.getElementById('start-scanner').style.display = 'inline-block';
    document.getElementById('stop-scanner').style.display = 'none';
}

// Load product info when barcode changes
document.getElementById('barcode-input').addEventListener('change', function() {
    const barcode = this.value.trim();
    if (barcode) {
        loadProductInfo(barcode);
    }
});

function loadProductInfo(barcode) {
    // Show loading state
    const productInfo = document.getElementById('product-info');
    const productDetails = document.getElementById('product-details');
    
    productInfo.style.display = 'block';
    productDetails.innerHTML = '<div class=\"text-center\"><div class=\"spinner-border text-primary\" role=\"status\"></div><p class=\"mt-2\">Загрузка информации о товаре...</p></div>';
    
    // Make AJAX request to get product info
    fetch('/index.php?r=product/get-by-barcode&barcode=' + encodeURIComponent(barcode))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                document.getElementById('quicksaleform-price_per_unit').value = product.price;
                
                productDetails.innerHTML = `
                    <div class=\"row\">
                        <div class=\"col-md-8\">
                            <h6 class=\"fw-bold text-primary\">\${product.name}</h6>
                            <p class=\"text-muted mb-2\">\${product.category_name || 'Без категории'}</p>
                            <div class=\"d-flex gap-3\">
                                <span class=\"badge bg-success\">\${Number(product.price).toLocaleString()} ₽</span>
                                <span class=\"badge bg-info\">\${product.stock} шт. в наличии</span>
                            </div>
                        </div>
                        <div class=\"col-md-4 text-end\">
                            <small class=\"text-muted\">Штрихкод:</small><br>
                            <code>\${product.barcode}</code>
                        </div>
                    </div>
                `;
            } else {
                productDetails.innerHTML = `
                    <div class=\"text-center text-danger\">
                        <div style=\"font-size: 2rem; margin-bottom: 1rem;\">❌</div>
                        <p>Товар с штрихкодом <code>\${barcode}</code> не найден</p>
                        <small class=\"text-muted\">Проверьте правильность штрихкода или добавьте товар в систему</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки информации о товаре:', error);
            productDetails.innerHTML = `
                <div class=\"text-center text-warning\">
                    <div style=\"font-size: 2rem; margin-bottom: 1rem;\">⚠️</div>
                    <p>Ошибка загрузки информации о товаре</p>
                    <small class=\"text-muted\">Попробуйте еще раз</small>
                </div>
            `;
        });
}

// Auto-focus barcode input
document.getElementById('barcode-input').focus();

// Form validation
document.getElementById('quick-sale-form').addEventListener('submit', function(e) {
    const barcode = document.getElementById('barcode-input').value.trim();
    const quantity = document.getElementById('quicksaleform-quantity').value;
    const price = document.getElementById('quicksaleform-price_per_unit').value;
    
    if (!barcode || !quantity || !price) {
        e.preventDefault();
        alert('Пожалуйста, заполните все поля');
        return false;
    }
    
    // Show loading state on submit button
    const submitBtn = document.getElementById('submit-sale');
    submitBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-2\"></span>Обработка...';
    submitBtn.disabled = true;
});
");
?>