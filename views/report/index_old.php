<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var float $todaySales */
/** @var int $todayCount */
/** @var float $monthSales */
/** @var app\models\Product[] $lowStockProducts */
/** @var int $outOfStockProducts */
/** @var int $totalProducts */
/** @var app\models\Sale[] $recentSales */

$this->title = 'Панель управления';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-index fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 text-gradient mb-2"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Добро пожаловать в систему управления складом</p>
        </div>
        <div class="text-end">
            <div class="badge bg-primary fs-6 px-3 py-2">
                📅 <?= date('d.m.Y') ?>
            </div>
            <div class="text-muted mt-1">
                🕐 <?= date('H:i') ?>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($todaySales, 0, ',', ' ') ?> ₽</h4>
                            <p class="card-text">Продажи сегодня</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                    <small><?= $todayCount ?> транзакций</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($monthSales, 0, ',', ' ') ?> ₽</h4>
                            <p class="card-text">Продажи за месяц</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= count($lowStockProducts) ?></h4>
                            <p class="card-text">Мало на складе</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $outOfStockProducts ?></h4>
                            <p class="card-text">Нет в наличии</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-barcode fa-2x d-block mb-2"></i>Быстрая продажа', 
                                ['/sale/quick'], 
                                ['class' => 'btn btn-primary btn-lg w-100 text-center']) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-plus fa-2x d-block mb-2"></i>Добавить товар', 
                                ['/product/create'], 
                                ['class' => 'btn btn-success btn-lg w-100 text-center']) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-truck fa-2x d-block mb-2"></i>Приход товара', 
                                ['/income/create'], 
                                ['class' => 'btn btn-info btn-lg w-100 text-center']) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-list fa-2x d-block mb-2"></i>Все товары', 
                                ['/product/index'], 
                                ['class' => 'btn btn-secondary btn-lg w-100 text-center']) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-chart-bar fa-2x d-block mb-2"></i>Отчеты', 
                                ['/report/sales'], 
                                ['class' => 'btn btn-warning btn-lg w-100 text-center']) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <?= Html::a('<i class="fas fa-tags fa-2x d-block mb-2"></i>Категории', 
                                ['/category/index'], 
                                ['class' => 'btn btn-outline-primary btn-lg w-100 text-center']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Low Stock Products -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Товары с низким остатком</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Остаток</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td>
                                        <?= Html::encode($product->name) ?>
                                        <br><small class="text-muted"><?= Html::encode($product->barcode) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?= $product->current_stock ?> <?= $product->getUnitTypeLabel() ?></span>
                                    </td>
                                    <td>
                                        <?= Html::a('Пополнить', ['/income/create', 'product_id' => $product->id], 
                                            ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Sales -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Последние продажи</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentSales)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Количество</th>
                                    <th>Сумма</th>
                                    <th>Время</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td>
                                        <?= Html::encode($sale->product->name) ?>
                                    </td>
                                    <td><?= $sale->quantity ?> <?= $sale->product->getUnitTypeLabel() ?></td>
                                    <td><?= number_format($sale->total_amount, 0, ',', ' ') ?> ₽</td>
                                    <td>
                                        <small><?= Yii::$app->formatter->asRelativeTime($sale->created_at) ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Продаж пока нет</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.btn-lg i {
    opacity: 0.8;
}

@media (max-width: 768px) {
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.5rem;
    }
    
    .btn-lg i {
        font-size: 1.5rem !important;
    }
}
</style>