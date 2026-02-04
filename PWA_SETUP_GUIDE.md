# Progressive Web App (PWA) Setup Guide for Dress Up Davao

This guide will help you convert your Laravel application into a Progressive Web App (PWA) that can be installed on mobile devices and desktops without publishing to app stores.

## What You'll Get

-   ✅ "Add to Home Screen" prompt on mobile devices
-   ✅ App icon on home screen
-   ✅ Standalone app experience (no browser UI)
-   ✅ Offline capability for cached pages
-   ✅ Works on Android, iOS, Windows, and macOS

---

## Step 1: Create App Icons

You need app icons in multiple sizes. Create these files in `public/images/icons/`:

### Required Icon Sizes:

-   `icon-72x72.png`
-   `icon-96x96.png`
-   `icon-128x128.png`
-   `icon-144x144.png`
-   `icon-152x152.png`
-   `icon-192x192.png`
-   `icon-384x384.png`
-   `icon-512x512.png`

**Quick Way:** Use your logo `public/images/DressUp-Davao-Logo.png` and resize it using:

-   Online tool: [PWA Asset Generator](https://www.pwabuilder.com/imageGenerator)
-   Or use an image editor to create each size

Create the icons folder:

```bash
mkdir public/images/icons
```

---

## Step 2: Create the Web App Manifest

Create a new file `public/manifest.json`:

```json
{
    "name": "Dress Up Davao",
    "short_name": "DressUp",
    "description": "Rent beautiful gowns and suits in Davao City",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#1f2937",
    "orientation": "portrait-primary",
    "icons": [
        {
            "src": "/images/icons/icon-72x72.png",
            "sizes": "72x72",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-96x96.png",
            "sizes": "96x96",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-128x128.png",
            "sizes": "128x128",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-144x144.png",
            "sizes": "144x144",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-152x152.png",
            "sizes": "152x152",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-384x384.png",
            "sizes": "384x384",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/images/icons/icon-512x512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "maskable any"
        }
    ],
    "categories": ["shopping", "lifestyle"],
    "screenshots": [
        {
            "src": "/images/icons/screenshot-wide.png",
            "sizes": "1280x720",
            "type": "image/png",
            "form_factor": "wide"
        },
        {
            "src": "/images/icons/screenshot-mobile.png",
            "sizes": "720x1280",
            "type": "image/png",
            "form_factor": "narrow"
        }
    ]
}
```

> **Note:** Screenshots are optional but recommended for a better install experience.

---

## Step 3: Create the Service Worker

Create a new file `public/sw.js`:

```javascript
const CACHE_NAME = "dressup-davao-v1";
const urlsToCache = [
    "/",
    "/css/app.css",
    "/js/app.js",
    "/images/DressUp-Davao-Logo.png",
    "/images/icons/icon-192x192.png",
    "/images/icons/icon-512x512.png",
    "/frontend-images/optimized-images/hero-mobile.webp",
    "/frontend-images/optimized-images/hero-desktop.webp",
];

// Install event - cache essential files
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("Opened cache");
                return cache.addAll(urlsToCache);
            })
            .catch((error) => {
                console.log("Cache install failed:", error);
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log("Deleting old cache:", cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener("fetch", (event) => {
    // Skip non-GET requests
    if (event.request.method !== "GET") return;

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        caches
            .match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                if (response) {
                    return response;
                }
                return fetch(event.request).then((response) => {
                    // Don't cache if not a valid response
                    if (
                        !response ||
                        response.status !== 200 ||
                        response.type !== "basic"
                    ) {
                        return response;
                    }
                    // Clone the response
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    return response;
                });
            })
            .catch(() => {
                // Return offline page if available
                return caches.match("/");
            })
    );
});
```

---

## Step 4: Add Meta Tags to Blade Templates

Add these tags in the `<head>` section of your main blade files. Since your project doesn't use a shared layout, you'll need to add these to each page.

### Add to `resources/views/home.blade.php` (and other blade files):

Add these lines after the existing `<meta>` tags:

```html
<!-- PWA Meta Tags -->
<link rel="manifest" href="/manifest.json" />
<meta name="theme-color" content="#1f2937" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta
    name="apple-mobile-web-app-status-bar-style"
    content="black-translucent"
/>
<meta name="apple-mobile-web-app-title" content="DressUp" />
<link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />
```

### Files to update:

-   `resources/views/home.blade.php`
-   `resources/views/login.blade.php`
-   `resources/views/register.blade.php`
-   `resources/views/productlist.blade.php`
-   `resources/views/productoverview.blade.php`
-   `resources/views/shops.blade.php`
-   `resources/views/shopoverview.blade.php`
-   `resources/views/shopcenter.blade.php`
-   `resources/views/accountpage.blade.php`
-   `resources/views/contact.blade.php`
-   `resources/views/aboutus.blade.php`
-   `resources/views/faq.blade.php`

---

## Step 5: Register the Service Worker

Add this script at the end of your blade files (before `</body>`), or add it to `resources/js/app.js`:

### Option A: Add to `resources/js/app.js` (Recommended)

```javascript
// Register Service Worker for PWA
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker
            .register("/sw.js")
            .then((registration) => {
                console.log("ServiceWorker registered:", registration.scope);
            })
            .catch((error) => {
                console.log("ServiceWorker registration failed:", error);
            });
    });
}
```

### Option B: Add inline script to blade files

Add before the closing `</body>` tag:

```html
<script>
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => {
            navigator.serviceWorker
                .register("/sw.js")
                .then((reg) => console.log("SW registered"))
                .catch((err) => console.log("SW failed:", err));
        });
    }
</script>
```

---

## Step 6: Testing Your PWA

### On Desktop (Chrome):

1. Run your Laravel app: `php artisan serve`
2. Open Chrome and go to `http://localhost:8000`
3. Open DevTools (F12) → **Application** tab
4. Check **Manifest** section - should show your app info
5. Check **Service Workers** section - should show registered
6. Look for the install icon in the address bar (⊕)

### On Mobile:

1. Deploy to a server with HTTPS (required for PWA)
2. Open the site in Chrome (Android) or Safari (iOS)
3. **Android:** You'll see "Add to Home Screen" banner or use menu → "Install app"
4. **iOS:** Tap Share button → "Add to Home Screen"

### Lighthouse Audit:

1. Open Chrome DevTools → **Lighthouse** tab
2. Select "Progressive Web App" category
3. Click "Generate report"
4. Fix any issues reported

---

## Step 7: Deployment Checklist

### Requirements for PWA to work:

-   [ ] **HTTPS required** - PWA only works on HTTPS (except localhost)
-   [ ] All icons exist in `/public/images/icons/`
-   [ ] `manifest.json` is accessible at `/manifest.json`
-   [ ] `sw.js` is accessible at `/sw.js`
-   [ ] Meta tags added to all blade files
-   [ ] Service worker registration code added

### Update Cache Version:

When you deploy updates, change the cache version in `sw.js`:

```javascript
const CACHE_NAME = "dressup-davao-v2"; // Increment version
```

---

## Quick Reference: File Locations

| File            | Location                   |
| --------------- | -------------------------- |
| Manifest        | `public/manifest.json`     |
| Service Worker  | `public/sw.js`             |
| Icons           | `public/images/icons/`     |
| Meta Tags       | Each blade file's `<head>` |
| SW Registration | `resources/js/app.js`      |

---

## Troubleshooting

### "Add to Home Screen" not appearing?

-   Ensure HTTPS is enabled (or use localhost)
-   Check manifest.json is valid (use DevTools → Application → Manifest)
-   Verify all icons exist and are accessible

### Service Worker not registering?

-   Check browser console for errors
-   Ensure sw.js is in the public root folder
-   Verify the path in registration code

### App not updating after changes?

-   Increment CACHE_NAME version in sw.js
-   Clear browser cache and service worker
-   In DevTools → Application → Service Workers → "Update on reload"

---

## Done! 🎉

After completing these steps, your app will be installable on any device. Users can:

-   Add it to their home screen
-   Use it like a native app
-   Access cached content offline

No app store submission required!
