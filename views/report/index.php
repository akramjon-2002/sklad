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

    <!-- Статистика -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-number"><?= number_format($todaySales, 0, ',', ' ') ?> ₽</div>
                        <div class="stats-label">Продажи сегодня</div>
                        <small class="text-muted"><?= $todayCount ?> транзакций</small>
                    </div>
                    <div class="quick-action-icon" style="width: 48px; height: 48px; font-size: 1.2rem;">
                        💰
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-number"><?= number_format($monthSales, 0, ',', ' ') ?> ₽</div>
                        <div class="stats-label">Продажи за месяц</div>
                    </div>
                    <div class="quick-action-icon" style="width: 48px; height: 48px; font-size: 1.2rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        📈
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-number"><?= $totalProducts ?></div>
                        <div class="stats-label">Всего товаров</div>
                    </div>
                    <div class="quick-action-icon" style="width: 48px; height: 48px; font-size: 1.2rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        📦
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stats-number"><?= $outOfStockProducts ?></div>
                        <div class="stats-label">Нет в наличии</div>
                    </div>
                    <div class="quick-action-icon" style="width: 48px; height: 48px; font-size: 1.2rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        ⚠️
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="mb-5">
        <h3 class="h4 mb-4">⚡ Быстрые действия</h3>
        <div class="quick-actions">
            <a href="<?= Url::to(['sale/quick']) ?>" class="quick-action-card">
                <div class="quick-action-icon">📱</div>
                <div class="quick-action-title">Быстрая продажа</div>
                <div class="quick-action-desc">Сканирование штрихкода и продажа товара</div>
            </a>
            
            <a href="<?= Url::to(['product/create']) ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">➕</div>
                <div class="quick-action-title">Добавить товар</div>
                <div class="quick-action-desc">Создание нового товара в системе</div>
            </a>
            
            <a href="<?= Url::to(['income/create']) ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">📥</div>
                <div class="quick-action-title">Приход товара</div>
                <div class="quick-action-desc">Оформление поступления товара на склад</div>
            </a>
            
            <a href="<?= Url::to(['product/index']) ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">📋</div>
                <div class="quick-action-title">Все товары</div>
                <div class="quick-action-desc">Просмотр и управление товарами</div>
            </a>
        </div>
    </div>

    <!-- Последние операции и товары с низким остатком -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <span class="me-2">🛒</span>
                        Последние продажи
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentSales)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentSales as $sale): ?>
                                <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($sale->product->name) ?></div>
                                        <small class="text-muted">
                                            📅 <?= date('d.m.Y H:i', strtotime($sale->created_at)) ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-success fs-6 mb-1">
                                            <?= number_format($sale->total_amount, 0, '.', ' ') ?> ₽
                                        </div>
                                        <div>
                                            <small class="text-muted"><?= $sale->quantity ?> шт.</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <a href="<?= Url::to(['sale/index']) ?>" class="btn btn-outline-primary">
                                Все продажи →
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="text-muted mb-3" style="font-size: 3rem;">🛒</div>
                            <p class="text-muted">Продаж пока нет</p>
                            <a href="<?= Url::to(['sale/quick']) ?>" class="btn btn-primary">
                                Создать первую продажу
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <span class="me-2">⚠️</span>
                        Товары с низким остатком
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($lowStockProducts)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($lowStockProducts as $product): ?>
                                <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($product->name) ?></div>
                                        <small class="text-muted">
                                            Штрихкод: <?= Html::encode($product->barcode) ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-warning text-dark fs-6 mb-1">
                                            <?= $product->stock ?> шт.
                                        </div>
                                        <div>
                                            <small class="text-muted"><?= number_format($product->price, 0, '.', ' ') ?> ₽</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <a href="<?= Url::to(['product/index']) ?>" class="btn btn-outline-warning">
                                Все товары →
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="text-muted mb-3" style="font-size: 3rem;">✅</div>
                            <p class="text-muted">Все товары в наличии</p>
                            <a href="<?= Url::to(['product/index']) ?>" class="btn btn-primary">
                                Просмотреть товары
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>