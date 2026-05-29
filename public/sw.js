const filesToCache = [
    '/',];


const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        // caching index and important routes
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", function (event) {
    event.waitUntil(preLoad());
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
    if (request.url.startsWith('chrome-extension:') || request.url.startsWith('data:')) {
        console.warn(`Skipping caching of unsupported URL scheme: ${request.url}`);
        return Promise.resolve();
    }

    return caches.open("offline").then(function (cache) {
        return fetch(request.clone()).then(function (response) {
            return cache.put(request.clone(), response.clone());
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
    event.respondWith(checkResponse(event.request).catch(function () {
        return returnFromCache(event.request);
    }));
    if (event.request.url.startsWith('http')) {
        event.waitUntil(addToCache(event.request));
    }
});
