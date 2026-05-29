const CACHE_NAME = 'diario-obras-v7';
const OFFLINE_URL = 'offline';
const SYNC_TAG = 'sync-diario-posts';

// URLs relativas ao diretório do SW (compatível com deploy em subdiretório)
const apiUrl = (path) => new URL(path, self.location).href;
const appRootUrl = () => new URL('./', self.location.href).href;
const isWithinAppScope = (url) => url.startsWith(appRootUrl());

// ==========================================
// INSTALL: Cache static assets + offline page
// ==========================================
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                OFFLINE_URL,
                appRootUrl(),
            ]);
        })
    );
    self.skipWaiting();
});

// ==========================================
// ACTIVATE: Clean up old caches immediately
// ==========================================
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('SW: Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// ==========================================
// MESSAGE: Handle skipWaiting from client
// ==========================================
self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// ==========================================
// FETCH: Serve from cache or network
// ==========================================
self.addEventListener('fetch', (event) => {
    if (event.request.mode !== 'navigate') {
        return;
    }

    if (!isWithinAppScope(event.request.url)) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => caches.match(OFFLINE_URL))
    );
});

// ==========================================
// BACKGROUND SYNC: Process queued posts
// ==========================================
self.addEventListener('sync', (event) => {
    if (event.tag === SYNC_TAG) {
        event.waitUntil(syncPendingPosts());
    }
});

async function syncPendingPosts() {
    const db = await openDB();
    const posts = await getAllPendingPosts(db);

    if (posts.length === 0) return;

    // 1. Fetch a fresh CSRF token using the existing session cookie
    let freshToken = null;
    try {
        const tokenResponse = await fetch(apiUrl('csrf-token'), { credentials: 'include' });
        if (tokenResponse.ok) {
            const json = await tokenResponse.json();
            freshToken = json.token;
        }
    } catch (e) {
        console.warn('Could not refresh CSRF token, using stored token as fallback');
    }

    for (const post of posts) {
        try {
            if (!post.fotoBase64) {
                await deletePendingPost(db, post.id);
                const clients = await self.clients.matchAll();
                clients.forEach(client => {
                    client.postMessage({
                        type: 'sync-error',
                        message: 'Publicação inválida removida da fila: foto obrigatória.',
                    });
                });
                continue;
            }

            const formData = new FormData();
            formData.append('texto', post.texto || '');
            formData.append('_token', freshToken || post.token);

            const blob = base64ToBlob(post.fotoBase64, post.fotoMime);
            formData.append('foto', blob, post.fotoName);

            const response = await fetch(apiUrl('diario-posts'), {
                method: 'POST',
                body: formData,
                credentials: 'include',
                redirect: 'follow',
            });

            if (response.ok || response.redirected || response.status === 302) {
                await deletePendingPost(db, post.id);
                const clients = await self.clients.matchAll();
                clients.forEach(client => {
                    client.postMessage({ type: 'sync-success', postId: post.id });
                });
            } else if (response.status === 422) {
                await deletePendingPost(db, post.id);
                const clients = await self.clients.matchAll();
                clients.forEach(client => {
                    client.postMessage({
                        type: 'sync-error',
                        message: 'Publicação inválida removida da fila: foto obrigatória.',
                    });
                });
            } else {
                console.error('Sync failed with status:', response.status);
            }
        } catch (err) {
            console.error('Falha ao sincronizar post:', post.id, err);
        }
    }
}

// ==========================================
// INDEXEDDB HELPERS
// ==========================================
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('diario-obras-offline', 1);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('pending-posts')) {
                db.createObjectStore('pending-posts', { keyPath: 'id', autoIncrement: true });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

function getAllPendingPosts(db) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction('pending-posts', 'readonly');
        const store = tx.objectStore('pending-posts');
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

function deletePendingPost(db, id) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction('pending-posts', 'readwrite');
        const store = tx.objectStore('pending-posts');
        const request = store.delete(id);
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

function base64ToBlob(base64, mime) {
    const binary = atob(base64);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
        bytes[i] = binary.charCodeAt(i);
    }
    return new Blob([bytes], { type: mime });
}
