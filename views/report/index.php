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

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📊 Панель управления</h1>
            <p class="text-lg text-gray-600">Добро пожаловать в систему управления складом</p>
        </div>
        <div class="text-right">
            <div class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">
                📅 <?= date('d.m.Y') ?>
            </div>
            <div class="text-sm text-gray-500 mt-1">
                <?= date('H:i') ?>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today Sales -->
        <div class="stats-card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 mb-1">Продажи сегодня</p>
                    <p class="text-3xl font-bold text-blue-900"><?= number_format($todaySales, 0, ',', ' ') ?> ₽</p>
                    <p class="text-sm text-blue-600 mt-1"><?= $todayCount ?> транзакций</p>
                </div>
                <div class="text-4xl text-blue-500">💰</div>
            </div>
        </div>

        <!-- Month Sales -->
        <div class="stats-card bg-gradient-to-br from-green-50 to-green-100 border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 mb-1">Продажи за месяц</p>
                    <p class="text-3xl font-bold text-green-900"><?= number_format($monthSales, 0, ',', ' ') ?> ₽</p>
                    <p class="text-sm text-green-600 mt-1">Текущий месяц</p>
                </div>
                <div class="text-4xl text-green-500">📈</div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="stats-card bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 mb-1">Всего товаров</p>
                    <p class="text-3xl font-bold text-purple-900"><?= $totalProducts ?></p>
                    <p class="text-sm text-purple-600 mt-1">В каталоге</p>
                </div>
                <div class="text-4xl text-purple-500">📦</div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="stats-card bg-gradient-to-br from-red-50 to-red-100 border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 mb-1">Нет в наличии</p>
                    <p class="text-3xl font-bold text-red-900"><?= $outOfStockProducts ?></p>
                    <p class="text-sm text-red-600 mt-1">Товаров</p>
                </div>
                <div class="text-4xl text-red-500">⚠️</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-bold text-gray-900">⚡ Быстрые действия</h2>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="<?= Url::to(['/sale/quick']) ?>" class="btn-primary text-center block">
                    📱 Быстрая продажа
                </a>
                <a href="<?= Url::to(['/product/create']) ?>" class="btn-success text-center block">
                    ➕ Добавить товар
                </a>
                <a href="<?= Url::to(['/income/create']) ?>" class="btn-warning text-center block">
                    📦 Оформить приход
                </a>
                <a href="<?= Url::to(['/category/create']) ?>" class="btn-secondary text-center block">
                    🏷️ Новая категория
                </a>
                <a href="<?= Url::to(['/product/index']) ?>" class="btn-secondary text-center block">
                    📋 Все товары
                </a>
                <a href="<?= Url::to(['/sale/index']) ?>" class="btn-secondary text-center block">
                    💼 История продаж
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Sales -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-gray-900">🕒 Последние продажи</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($recentSales)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentSales as $sale): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold"><?= substr($sale->product->name, 0, 1) ?></span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?= Html::encode($sale->product->name) ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?= $sale->quantity ?> × <?= number_format($sale->price_per_unit, 0, ',', ' ') ?> ₽
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600"><?= number_format($sale->total_amount, 0, ',', ' ') ?> ₽</p>
                                    <p class="text-xs text-gray-500"><?= date('H:i', strtotime($sale->created_at)) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="<?= Url::to(['/sale/index']) ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                            Посмотреть все продажи →
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">📊</div>
                        <p class="text-gray-500">Пока нет продаж</p>
                        <a href="<?= Url::to(['/sale/quick']) ?>" class="btn-primary mt-4 inline-block">
                            Создать первую продажу
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-gray-900">⚠️ Низкий остаток</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($lowStockProducts)): ?>
                    <div class="space-y-4">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600 font-semibold"><?= substr($product->name, 0, 1) ?></span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?= Html::encode($product->name) ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?= number_format($product->price_per_unit, 0, ',', ' ') ?> ₽ за <?= Html::encode($product->unit_type) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-red-600"><?= $product->current_stock ?></p>
                                    <p class="text-xs text-gray-500">осталось</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="<?= Url::to(['/income/create']) ?>" class="btn-warning inline-block">
                            Пополнить склад
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">✅</div>
                        <p class="text-gray-500">Все товары в наличии</p>
                        <p class="text-sm text-gray-400 mt-2">Отличная работа!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>