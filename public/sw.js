const CACHE_NAME = "dressup-davao-v1";

// URLs that should NOT be cached (dynamic/session pages)
const NO_CACHE_URLS = [
    '/login',
    '/register',
    '/logout',
    '/admin/*',
    '/api/*'
];

// Install event
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Fetch event - FIXED FOR SESSION/CACHE ISSUES
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);
    
    // Skip caching for session-related requests
    if (shouldSkipCache(event.request)) {
        event.respondWith(fetch(event.request));
        return;
    }
    
    // For HTML pages, use network-first strategy
    if (event.request.headers.get('Accept').includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Only cache successful responses
                    if (response.ok) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME)
                            .then(cache => cache.put(event.request, responseClone));
                    }
                    return response;
                })
                .catch(() => {
                    // If network fails, try cache
                    return caches.match(event.request);
                })
        );
        return;
    }
    
    // For static assets, use cache-first strategy
    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request);
            })
    );
});

function shouldSkipCache(request) {
    const url = new URL(request.url);
    
    // Don't cache POST, PUT, DELETE requests
    if (['POST', 'PUT', 'DELETE'].includes(request.method)) {
        return true;
    }
    
    // Don't cache session/csrf related URLs
    if (NO_CACHE_URLS.some(pattern => {
        if (pattern.includes('*')) {
            const basePattern = pattern.replace('*', '');
            return url.pathname.startsWith(basePattern);
        }
        return url.pathname === pattern;
    })) {
        return true;
    }
    
    // Don't cache if it's a session-related request
    if (request.headers.has('X-CSRF-TOKEN') || 
        request.url.includes('_token=') ||
        url.pathname.includes('session')) {
        return true;
    }
    
    return false;
}