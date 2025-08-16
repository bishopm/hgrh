var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/',
    '/blog',
    '/calendar',
    '/sermons',
    '/details',
    '/practices',
    '/devotionals',
    '/books',
    '/hgrh/css/bootstrap.min.css',
    '/hgrh/css/custom.css',
    '/hgrh/css/leaflet.css',
    '/hgrh/css/output.css',
    '/hgrh/css/images/marker-icon-2x.png',
    '/hgrh/css/images/marker-icon.png',
    '/hgrh/css/images/marker-shadow.png',
    '/hgrh/js/barcodescanner.js',
    '/hgrh/js/bootstrap-bundle.min.js',
    '/hgrh/js/custom.js',
    '/hgrh/js/zxing.min.js',
    '/offline',
    '/hgrh/images/icons/icon-72x72.png',
    '/hgrh/images/icons/icon-96x96.png',
    '/hgrh/images/icons/icon-128x128.png',
    '/hgrh/images/icons/icon-144x144.png',
    '/hgrh/images/icons/icon-152x152.png',
    '/hgrh/images/icons/icon-192x192.png',
    '/hgrh/images/icons/icon-384x384.png',
    '/hgrh/images/icons/icon-512x512.png',
    '/hgrh/images/aerial.png',
    '/hgrh/images/blacklogo.png',
    '/hgrh/images/blog.png',
    '/hgrh/images/bwidelogo.png',
    '/hgrh/images/calendar.png',
    '/hgrh/images/church.png',
    '/hgrh/images/circle.png',
    '/hgrh/images/growslide.png',
    '/hgrh/images/knowslide.png',
    '/hgrh/images/showslide.png',
    '/hgrh/images/welcomeslide.png'
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                cache.add('/').catch(error => {
                    console.error('Failed to cache root route:', error);
                });
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener('fetch', (event) => {
  event.respondWith(caches.open(staticCacheName).then((cache) => {
    return cache.match(event.request).then((cachedResponse) => {
        const fetchedResponse = fetch(event.request).then((networkResponse) => {
            cache.put(event.request, networkResponse.clone());
    
            return networkResponse;
        });
    
        return cachedResponse || fetchedResponse;
        });
    }));
});