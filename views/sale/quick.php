<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\QuickSaleForm $model */

$this->title = 'Быстрая продажа';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">⚡ Быстрая продажа</h1>
        <p class="text-lg text-gray-600">Сканируйте штрихкод или введите его вручную</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Scanner Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-gray-900">📷 Сканер штрихкодов</h2>
            </div>
            <div class="card-body">
                <!-- Camera Scanner -->
                <div id="scanner-container" class="scanner-container mb-6">
                    <div id="scanner" class="w-full h-64 bg-gray-900 rounded-lg flex items-center justify-center">
                        <div class="text-center text-white">
                            <div class="text-4xl mb-4">📷</div>
                            <p class="mb-4">Нажмите кнопку ниже для включения камеры</p>
                            <button id="start-scanner" class="btn-primary">
                                Включить камеру
                            </button>
                        </div>
                    </div>
                    <div id="scanner-overlay" class="scanner-overlay hidden"></div>
                </div>

                <!-- Manual Input -->
                <div class="space-y-4">
                    <label class="form-label">Или введите штрихкод вручную:</label>
                    <div class="flex space-x-2">
                        <input type="text" id="manual-barcode" class="form-input flex-1" 
                               placeholder="Введите штрихкод..." autocomplete="off">
                        <button id="search-product" class="btn-secondary">
                            🔍 Найти
                        </button>
                    </div>
                </div>

                <!-- Scanner Status -->
                <div id="scanner-status" class="mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200 hidden">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="text-blue-800 font-medium">Сканер активен - наведите на штрихкод</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Form Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-gray-900">🛒 Оформление продажи</h2>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'quick-sale-form',
                    'options' => ['class' => 'space-y-6']
                ]); ?>

                <!-- Product Info Display -->
                <div id="product-info" class="product-info-card hidden">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">📦</span>
                        </div>
                        <div class="flex-1">
                            <h3 id="product-name" class="text-lg font-bold text-green-800 mb-1"></h3>
                            <p id="product-details" class="text-sm text-green-600 mb-2"></p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="bg-green-100 px-2 py-1 rounded">
                                    💰 <span id="product-price"></span>
                                </span>
                                <span class="bg-green-100 px-2 py-1 rounded">
                                    📦 <span id="product-stock"></span> в наличии
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                    <?= $form->field($model, 'barcode', [
                        'template' => '<label class="form-label">{label}</label>{input}{error}',
                        'inputOptions' => [
                            'class' => 'form-input',
                            'placeholder' => 'Штрихкод будет заполнен автоматически'
                        ]
                    ]) ?>

                    <?= $form->field($model, 'quantity', [
                        'template' => '<label class="form-label">{label}</label>{input}{error}',
                        'inputOptions' => [
                            'class' => 'form-input',
                            'type' => 'number',
                            'step' => '0.001',
                            'min' => '0.001',
                            'placeholder' => 'Введите количество'
                        ]
                    ]) ?>

                    <?= $form->field($model, 'price_per_unit', [
                        'template' => '<label class="form-label">{label}</label>{input}{error}',
                        'inputOptions' => [
                            'class' => 'form-input',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'placeholder' => 'Цена будет заполнена автоматически'
                        ]
                    ]) ?>

                    <!-- Total Amount Display -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-700">Итого к оплате:</span>
                            <span id="total-amount" class="text-2xl font-bold text-green-600">0 ₽</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex space-x-4">
                    <?= Html::submitButton('💳 Оформить продажу', [
                        'class' => 'btn-success flex-1',
                        'id' => 'submit-sale'
                    ]) ?>
                    <button type="button" id="clear-form" class="btn-secondary">
                        🗑️ Очистить
                    </button>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="sale-messages" class="space-y-4"></div>
</div>

<!-- Include QuaggaJS for barcode scanning -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isScanning = false;
    
    // Elements
    const startScannerBtn = document.getElementById('start-scanner');
    const scannerContainer = document.getElementById('scanner');
    const scannerOverlay = document.getElementById('scanner-overlay');
    const scannerStatus = document.getElementById('scanner-status');
    const manualBarcodeInput = document.getElementById('manual-barcode');
    const searchProductBtn = document.getElementById('search-product');
    const productInfo = document.getElementById('product-info');
    const clearFormBtn = document.getElementById('clear-form');
    const submitSaleBtn = document.getElementById('submit-sale');
    const saleMessages = document.getElementById('sale-messages');
    
    // Form fields
    const barcodeField = document.getElementById('quicksaleform-barcode');
    const quantityField = document.getElementById('quicksaleform-quantity');
    const priceField = document.getElementById('quicksaleform-price_per_unit');
    const totalAmount = document.getElementById('total-amount');
    
    // Start scanner
    startScannerBtn.addEventListener('click', function() {
        if (!isScanning) {
            startScanner();
        } else {
            stopScanner();
        }
    });
    
    // Manual barcode search
    searchProductBtn.addEventListener('click', function() {
        const barcode = manualBarcodeInput.value.trim();
        if (barcode) {
            searchProduct(barcode);
        }
    });
    
    // Enter key for manual input
    manualBarcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProductBtn.click();
        }
    });
    
    // Calculate total on quantity/price change
    quantityField.addEventListener('input', calculateTotal);
    priceField.addEventListener('input', calculateTotal);
    
    // Clear form
    clearFormBtn.addEventListener('click', function() {
        clearForm();
    });
    
    function startScanner() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: scannerContainer,
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                }
            },
            decoder: {
                readers: [
                    "code_128_reader",
                    "ean_reader",
                    "ean_8_reader",
                    "code_39_reader"
                ]
            }
        }, function(err) {
            if (err) {
                console.log(err);
                showMessage('Ошибка доступа к камере: ' + err.message, 'error');
                return;
            }
            console.log("Initialization finished. Ready to start");
            Quagga.start();
            isScanning = true;
            startScannerBtn.textContent = 'Остановить камеру';
            startScannerBtn.className = 'btn-danger';
            scannerOverlay.classList.remove('hidden');
            scannerStatus.classList.remove('hidden');
        });
        
        Quagga.onDetected(function(data) {
            const barcode = data.codeResult.code;
            console.log('Barcode detected:', barcode);
            searchProduct(barcode);
            stopScanner();
        });
    }
    
    function stopScanner() {
        if (isScanning) {
            Quagga.stop();
            isScanning = false;
            startScannerBtn.textContent = 'Включить камеру';
            startScannerBtn.className = 'btn-primary';
            scannerOverlay.classList.add('hidden');
            scannerStatus.classList.add('hidden');
            
            // Reset scanner container
            scannerContainer.innerHTML = `
                <div class="text-center text-white">
                    <div class="text-4xl mb-4">📷</div>
                    <p class="mb-4">Нажмите кнопку ниже для включения камеры</p>
                    <button id="start-scanner" class="btn-primary">
                        Включить камеру
                    </button>
                </div>
            `;
            
            // Re-attach event listener
            document.getElementById('start-scanner').addEventListener('click', function() {
                startScanner();
            });
        }
    }
    
    function searchProduct(barcode) {
        showMessage('Поиск товара...', 'info');
        
        fetch('/index.php?r=product/get-by-barcode&barcode=' + encodeURIComponent(barcode))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayProduct(data.product, barcode);
                    manualBarcodeInput.value = '';
                } else {
                    showMessage('Товар с таким штрихкодом не найден', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Ошибка при поиске товара', 'error');
            });
    }
    
    function displayProduct(product, barcode) {
        // Fill form fields
        barcodeField.value = barcode;
        quantityField.value = '1';
        priceField.value = product.price;
        
        // Display product info
        document.getElementById('product-name').textContent = product.name;
        document.getElementById('product-details').textContent = 
            `Категория: ${product.category_name} | Штрихкод: ${barcode}`;
        document.getElementById('product-price').textContent = 
            `${parseFloat(product.price).toLocaleString()} ₽`;
        document.getElementById('product-stock').textContent = 
            `${parseFloat(product.stock)} шт`;
        
        productInfo.classList.remove('hidden');
        calculateTotal();
        
        showMessage('Товар найден и добавлен в форму', 'success');
        
        // Focus on quantity field
        quantityField.focus();
        quantityField.select();
    }
    
    function calculateTotal() {
        const quantity = parseFloat(quantityField.value) || 0;
        const price = parseFloat(priceField.value) || 0;
        const total = quantity * price;
        
        totalAmount.textContent = total.toLocaleString() + ' ₽';
    }
    
    function clearForm() {
        barcodeField.value = '';
        quantityField.value = '';
        priceField.value = '';
        manualBarcodeInput.value = '';
        productInfo.classList.add('hidden');
        totalAmount.textContent = '0 ₽';
        clearMessages();
    }
    
    function showMessage(message, type) {
        clearMessages();
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-error',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = alertClass;
        messageDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-75 hover:opacity-100">✕</button>
            </div>
        `;
        
        saleMessages.appendChild(messageDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }
    
    function clearMessages() {
        saleMessages.innerHTML = '';
    }
    
    // Handle form submission
    document.getElementById('quick-sale-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        submitSaleBtn.disabled = true;
        submitSaleBtn.innerHTML = '<div class="spinner mr-2"></div>Обработка...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Check if response contains success message
            if (data.includes('Продажа успешно оформлена') || data.includes('alert-success')) {
                showMessage('Продажа успешно оформлена!', 'success');
                clearForm();
            } else {
                showMessage('Ошибка при оформлении продажи', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Ошибка при отправке данных', 'error');
        })
        .finally(() => {
            submitSaleBtn.disabled = false;
            submitSaleBtn.innerHTML = '💳 Оформить продажу';
        });
    });
});
</script>