const CACHE_NAME = 'sklad-v2';
const STATIC_CACHE = 'sklad-static-v2';
const DYNAMIC_CACHE = 'sklad-dynamic-v2';

const STATIC_FILES = [
  '/',
  '/index.php',
  '/css/bootstrap.min.css',
  '/css/modern.css',
  '/js/bootstrap.bundle.min.js',
  '/manifest.json',
  '/icon-192.png',
  '/icon-512.png',
  'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js'
];

const CACHE_STRATEGIES = {
  // Cache first for static assets
  static: [
    '/css/',
    '/js/',
    '/icon-',
    'bootstrap',
    'quagga'
  ],
  // Network first for dynamic content
  dynamic: [
    '/index.php',
    '?r='
  ]
};

// Install event
self.addEventListener('install', event => {
  console.log('Service Worker installing...');
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('Caching static files');
        return cache.addAll(STATIC_FILES);
      })
      .then(() => {
        console.log('Static files cached');
        return self.skipWaiting();
      })
  );
});

// Activate event
self.addEventListener('activate', event => {
  console.log('Service Worker activating...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('Service Worker activated');
      return self.clients.claim();
    })
  );
});

// Fetch event with different strategies
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Skip chrome-extension and other protocols
  if (!url.protocol.startsWith('http')) {
    return;
  }
  
  // Determine cache strategy
  const isStatic = CACHE_STRATEGIES.static.some(pattern => 
    request.url.includes(pattern)
  );
  
  const isDynamic = CACHE_STRATEGIES.dynamic.some(pattern => 
    request.url.includes(pattern)
  );
  
  if (isStatic) {
    // Cache first strategy for static assets
    event.respondWith(cacheFirst(request));
  } else if (isDynamic) {
    // Network first strategy for dynamic content
    event.respondWith(networkFirst(request));
  } else {
    // Default to network first
    event.respondWith(networkFirst(request));
  }
});

// Cache first strategy
async function cacheFirst(request) {
  try {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('Cache first failed:', error);
    return new Response('Offline', { status: 503 });
  }
}

// Network first strategy
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('Network first failed, trying cache:', error);
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // Return offline page for navigation requests
    if (request.mode === 'navigate') {
      return caches.match('/') || new Response('Offline', { 
        status: 503,
        headers: { 'Content-Type': 'text/html' }
      });
    }
    
    return new Response('Offline', { status: 503 });
  }
}

// Background sync for offline actions
self.addEventListener('sync', event => {
  console.log('Background sync:', event.tag);
  
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  // Handle offline actions when back online
  console.log('Performing background sync...');
}

// Push notifications
self.addEventListener('push', event => {
  console.log('Push received:', event);
  
  const options = {
    body: event.data ? event.data.text() : 'Новое уведомление',
    icon: '/icon-192.png',
    badge: '/icon-192.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Открыть',
        icon: '/icon-192.png'
      },
      {
        action: 'close',
        title: 'Закрыть',
        icon: '/icon-192.png'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification('Склад', options)
  );
});

// Notification click
self.addEventListener('notificationclick', event => {
  console.log('Notification click:', event);
  
  event.notification.close();
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});