const CACHE_NAME = "laravel-pwa-cache-v1";
const assetsToCache = ["/", "/offline", "/css/app.css", "/js/app.js"];

// Tahap Instalasi
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(assetsToCache);
        }),
    );
});

// Strategi Fetch (Mengambil dari cache jika offline)
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches
            .match(event.request)
            .then((response) => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match("/offline");
            }),
    );
});

// Aktivasi dan Pembersihan Cache Lama
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name)),
            );
        }),
    );
});
