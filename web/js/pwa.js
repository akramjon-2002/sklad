// PWA functionality for Склад
class PWAManager {
  constructor() {
    this.deferredPrompt = null;
    this.isInstalled = false;
    this.init();
  }

  init() {
    this.registerServiceWorker();
    this.setupInstallPrompt();
    this.setupOfflineDetection();
    this.setupNotifications();
    this.addPWAStyles();
  }

  // Register service worker
  async registerServiceWorker() {
    if ('serviceWorker' in navigator) {
      try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        console.log('Service Worker registered:', registration);
        
        // Check for updates
        registration.addEventListener('updatefound', () => {
          const newWorker = registration.installing;
          newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
              this.showUpdateNotification();
            }
          });
        });
      } catch (error) {
        console.error('Service Worker registration failed:', error);
      }
    }
  }

  // Setup install prompt
  setupInstallPrompt() {
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      this.deferredPrompt = e;
      this.showInstallButton();
    });

    window.addEventListener('appinstalled', () => {
      console.log('PWA installed');
      this.isInstalled = true;
      this.hideInstallButton();
      this.showInstalledNotification();
    });

    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches || 
        window.navigator.standalone === true) {
      this.isInstalled = true;
    }
  }

  // Show install button
  showInstallButton() {
    if (this.isInstalled) return;

    let installButton = document.getElementById('pwa-install-btn');
    if (!installButton) {
      installButton = document.createElement('button');
      installButton.id = 'pwa-install-btn';
      installButton.className = 'pwa-install-button';
      installButton.innerHTML = `
        📱 Установить приложение
      `;
      installButton.addEventListener('click', () => this.installPWA());
      document.body.appendChild(installButton);
    }
    
    installButton.classList.add('show');
  }

  // Hide install button
  hideInstallButton() {
    const installButton = document.getElementById('pwa-install-btn');
    if (installButton) {
      installButton.classList.remove('show');
    }
  }

  // Install PWA
  async installPWA() {
    if (!this.deferredPrompt) return;

    this.deferredPrompt.prompt();
    const { outcome } = await this.deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
      console.log('User accepted the install prompt');
    } else {
      console.log('User dismissed the install prompt');
    }
    
    this.deferredPrompt = null;
    this.hideInstallButton();
  }

  // Setup offline detection
  setupOfflineDetection() {
    const updateOnlineStatus = () => {
      const isOnline = navigator.onLine;
      document.body.classList.toggle('offline', !isOnline);
      
      if (!isOnline) {
        this.showOfflineNotification();
      } else {
        this.hideOfflineNotification();
      }
    };

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();
  }

  // Setup notifications
  async setupNotifications() {
    if ('Notification' in window) {
      const permission = await Notification.requestPermission();
      console.log('Notification permission:', permission);
    }
  }

  // Show update notification
  showUpdateNotification() {
    this.showNotification('Обновление доступно', {
      body: 'Новая версия приложения готова к установке',
      actions: [
        { action: 'update', title: 'Обновить' },
        { action: 'dismiss', title: 'Позже' }
      ]
    });
  }

  // Show installed notification
  showInstalledNotification() {
    this.showNotification('Приложение установлено', {
      body: 'Склад успешно установлен на ваше устройство',
      icon: '/icon-192.png'
    });
  }

  // Show offline notification
  showOfflineNotification() {
    let offlineBar = document.getElementById('offline-bar');
    if (!offlineBar) {
      offlineBar = document.createElement('div');
      offlineBar.id = 'offline-bar';
      offlineBar.className = 'offline-notification';
      offlineBar.innerHTML = `
        📶 Нет подключения к интернету. Работаем в офлайн режиме.
      `;
      document.body.insertBefore(offlineBar, document.body.firstChild);
    }
    offlineBar.style.display = 'block';
  }

  // Hide offline notification
  hideOfflineNotification() {
    const offlineBar = document.getElementById('offline-bar');
    if (offlineBar) {
      offlineBar.style.display = 'none';
    }
  }

  // Generic notification helper
  showNotification(title, options = {}) {
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification(title, {
        icon: '/icon-192.png',
        badge: '/icon-192.png',
        ...options
      });
    } else {
      // Fallback to in-app notification
      this.showInAppNotification(title, options.body);
    }
  }

  // In-app notification
  showInAppNotification(title, message) {
    const notification = document.createElement('div');
    notification.className = 'in-app-notification fade-in';
    notification.innerHTML = `
      <div class="notification-content">
        <strong>${title}</strong>
        ${message ? `<p>${message}</p>` : ''}
        <button class="btn btn-sm btn-outline-primary" onclick="this.parentElement.parentElement.remove()">
          Закрыть
        </button>
      </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentElement) {
        notification.remove();
      }
    }, 5000);
  }

  // Add PWA specific styles
  addPWAStyles() {
    const style = document.createElement('style');
    style.textContent = `
      .offline-notification {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px;
        text-align: center;
        font-weight: 500;
        z-index: 9999;
        display: none;
        animation: slideDown 0.3s ease;
      }

      .in-app-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        padding: 20px;
        max-width: 300px;
        z-index: 9999;
        border-left: 4px solid var(--primary);
      }

      .notification-content strong {
        display: block;
        margin-bottom: 8px;
        color: var(--gray-800);
      }

      .notification-content p {
        margin: 0 0 12px 0;
        color: var(--gray-600);
        font-size: 14px;
      }

      @keyframes slideDown {
        from {
          transform: translateY(-100%);
        }
        to {
          transform: translateY(0);
        }
      }

      /* PWA display mode styles */
      @media (display-mode: standalone) {
        .navbar {
          padding-top: env(safe-area-inset-top, 20px);
        }
        
        body {
          padding-top: env(safe-area-inset-top);
          padding-bottom: env(safe-area-inset-bottom);
        }
      }

      /* iOS specific styles */
      @supports (-webkit-touch-callout: none) {
        .navbar {
          padding-top: max(env(safe-area-inset-top), 20px);
        }
      }
    `;
    document.head.appendChild(style);
  }

  // Add to home screen prompt for iOS
  showIOSInstallPrompt() {
    if (this.isIOS() && !this.isInstalled) {
      this.showInAppNotification(
        'Установить приложение',
        'Нажмите кнопку "Поделиться" и выберите "На экран Домой"'
      );
    }
  }

  // Check if iOS
  isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  }
}

// Initialize PWA when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  window.pwaManager = new PWAManager();
  
  // Show iOS install prompt after 3 seconds
  setTimeout(() => {
    window.pwaManager.showIOSInstallPrompt();
  }, 3000);
});

// Export for global access
window.PWAManager = PWAManager;