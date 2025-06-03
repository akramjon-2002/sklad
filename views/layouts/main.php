<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, viewport-fit=cover']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? 'Современная PWA система управления складом с поддержкой штрихкодов']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? 'склад, инвентарь, штрихкод, PWA, мобильное приложение']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// PWA meta tags
$this->registerMetaTag(['name' => 'theme-color', 'content' => '#3b82f6']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-status-bar-style', 'content' => 'default']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-title', 'content' => 'Склад']);
$this->registerMetaTag(['name' => 'mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['name' => 'application-name', 'content' => 'Склад']);
$this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#3b82f6']);

// Icons
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '192x192', 'href' => Yii::getAlias('@web/icon-192.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '192x192', 'href' => Yii::getAlias('@web/icon-192.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '512x512', 'href' => Yii::getAlias('@web/icon-512.png')]);
$this->registerLinkTag(['rel' => 'manifest', 'href' => Yii::getAlias('@web/manifest.json')]);

// Register Tailwind CSS
$this->registerCssFile('https://cdn.tailwindcss.com', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/css/tailwind.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);

// Tailwind config
$this->registerJs("
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                }
            }
        }
    }
}", \yii\web\View::POS_HEAD);

// Register PWA JS
$this->registerJsFile('@web/js/pwa.js', ['position' => \yii\web\View::POS_END]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="min-h-screen bg-gray-50">
<?php $this->beginBody() ?>

<!-- Offline Indicator -->
<div id="offline-indicator" class="offline-indicator">
    <div class="flex items-center space-x-2">
        <div class="w-3 h-3 bg-red-300 rounded-full animate-pulse"></div>
        <span>Нет подключения к интернету</span>
    </div>
</div>

<!-- PWA Install Prompt -->
<div id="pwa-install-prompt" class="pwa-install-prompt">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="font-semibold">Установить приложение</h4>
            <p class="text-sm opacity-90">Добавьте на главный экран для быстрого доступа</p>
        </div>
        <div class="flex space-x-2">
            <button id="pwa-install-btn" class="bg-white text-blue-600 px-4 py-2 rounded font-semibold">Установить</button>
            <button id="pwa-dismiss-btn" class="text-white opacity-75 hover:opacity-100">✕</button>
        </div>
    </div>
</div>

<header class="navbar shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Brand -->
            <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand flex items-center space-x-2">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <span class="text-lg">📦</span>
                </div>
                <span>Склад</span>
            </a>
            
            <!-- Mobile menu button -->
            <button id="mobile-menu-btn" class="md:hidden text-white hover:text-blue-100 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex navbar-nav">
                <a href="<?= \yii\helpers\Url::to(['/report/index']) ?>" class="nav-link">📊 Панель</a>
                <a href="<?= \yii\helpers\Url::to(['/product/index']) ?>" class="nav-link">📦 Товары</a>
                <a href="<?= \yii\helpers\Url::to(['/category/index']) ?>" class="nav-link">🏷️ Категории</a>
                <a href="<?= \yii\helpers\Url::to(['/sale/index']) ?>" class="nav-link">💰 Продажи</a>
                <a href="<?= \yii\helpers\Url::to(['/income/index']) ?>" class="nav-link">📈 Приход</a>
                <a href="<?= \yii\helpers\Url::to(['/sale/quick']) ?>" class="nav-link bg-white bg-opacity-20">⚡ Быстрая продажа</a>
            </nav>
            
            <!-- User menu -->
            <div class="hidden md:flex items-center space-x-4">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="nav-link">🔐 Вход</a>
                <?php else: ?>
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'inline']) ?>
                    <?= Html::submitButton(
                        '👤 Выход',
                        ['class' => 'nav-link bg-transparent border-0 cursor-pointer']
                    ) ?>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <nav id="mobile-menu" class="md:hidden hidden bg-white bg-opacity-10 rounded-lg mt-2 p-4">
            <div class="flex flex-col space-y-2">
                <a href="<?= \yii\helpers\Url::to(['/report/index']) ?>" class="nav-link">📊 Панель</a>
                <a href="<?= \yii\helpers\Url::to(['/product/index']) ?>" class="nav-link">📦 Товары</a>
                <a href="<?= \yii\helpers\Url::to(['/category/index']) ?>" class="nav-link">🏷️ Категории</a>
                <a href="<?= \yii\helpers\Url::to(['/sale/index']) ?>" class="nav-link">💰 Продажи</a>
                <a href="<?= \yii\helpers\Url::to(['/income/index']) ?>" class="nav-link">📈 Приход</a>
                <a href="<?= \yii\helpers\Url::to(['/sale/quick']) ?>" class="nav-link bg-white bg-opacity-20">⚡ Быстрая продажа</a>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="nav-link">🔐 Вход</a>
                <?php else: ?>
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'inline']) ?>
                    <?= Html::submitButton(
                        '👤 Выход',
                        ['class' => 'nav-link bg-transparent border-0 cursor-pointer w-full text-left']
                    ) ?>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="flex-1 pt-4 pb-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <nav class="mb-6">
                <?= Breadcrumbs::widget([
                    'links' => $this->params['breadcrumbs'],
                    'options' => ['class' => 'flex items-center space-x-2 text-sm text-gray-600']
                ]) ?>
            </nav>
        <?php endif ?>
        
        <!-- Alerts -->
        <div class="mb-6">
            <?= Alert::widget() ?>
        </div>
        
        <!-- Content -->
        <div class="animate-fade-in-up">
            <?= $content ?>
        </div>
    </div>
</main>

<footer class="bg-white border-t border-gray-200 py-6 mt-auto">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
            <div class="mb-2 md:mb-0">
                &copy; Склад <?= date('Y') ?> - Система управления складом
            </div>
            <div class="flex items-center space-x-4">
                <span>Powered by <?= Yii::powered() ?></span>
                <div class="flex items-center space-x-1">
                    <div id="connection-status" class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span id="connection-text" class="text-xs">Онлайн</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Connection status
    const connectionStatus = document.getElementById('connection-status');
    const connectionText = document.getElementById('connection-text');
    const offlineIndicator = document.getElementById('offline-indicator');
    
    function updateConnectionStatus() {
        if (navigator.onLine) {
            connectionStatus.className = 'w-2 h-2 bg-green-500 rounded-full';
            connectionText.textContent = 'Онлайн';
            offlineIndicator.classList.remove('show');
        } else {
            connectionStatus.className = 'w-2 h-2 bg-red-500 rounded-full animate-pulse';
            connectionText.textContent = 'Офлайн';
            offlineIndicator.classList.add('show');
        }
    }
    
    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);
    updateConnectionStatus();
    
    // PWA Install prompt
    let deferredPrompt;
    const pwaInstallPrompt = document.getElementById('pwa-install-prompt');
    const pwaInstallBtn = document.getElementById('pwa-install-btn');
    const pwaDismissBtn = document.getElementById('pwa-dismiss-btn');
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        pwaInstallPrompt.classList.add('show');
    });
    
    if (pwaInstallBtn) {
        pwaInstallBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                deferredPrompt = null;
                pwaInstallPrompt.classList.remove('show');
            }
        });
    }
    
    if (pwaDismissBtn) {
        pwaDismissBtn.addEventListener('click', () => {
            pwaInstallPrompt.classList.remove('show');
        });
    }
});

// Register service worker for PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js')
      .then(function(registration) {
        console.log('SW registered: ', registration);
      })
      .catch(function(registrationError) {
        console.log('SW registration failed: ', registrationError);
      });
  });
}
</script>

</body>
</html>
<?php $this->endPage() ?>
