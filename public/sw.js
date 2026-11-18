const filesToCache = [
    '/',
];

const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", function (event) {
    event.waitUntil(preLoad());
    self.skipWaiting(); // Naye service worker ko turant active karne ke liye
});

self.addEventListener("activate", function (event) {
    event.waitUntil(self.clients.claim());
});

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

const addToCache = function (request) {
    // 1. IMPORTANT: Sirf GET requests ko allow karein, POST/Livewire/AJAX requests ko seedha skip karein
    if (!request.url.startsWith('http') || request.method !== 'GET') {
        return Promise.resolve();
    }

    if (request.url.startsWith('chrome-extension:') || request.url.startsWith('data:')) {
        return Promise.resolve();
    }

    return caches.open("offline").then(function (cache) {
        return fetch(request).then(function (response) {
            if (response.status < 400) {
                return cache.put(request, response.clone());
            }
        }).catch(function (error) {
            console.debug("Skipping cache update due to network failure:", error);
        });
    });
};

const returnFromCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", function (event) {
    // Sirf GET requests ke liye offline fallback handle karein
    if (event.request.method === 'GET') {
        event.respondWith(
            checkResponse(event.request).catch(function () {
                return returnFromCache(event.request);
            })
        );

        event.waitUntil(addToCache(event.request));
    }
});
