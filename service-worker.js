// ============================================================
// SERVICE WORKER — Sushi Wabi-Sabi PWA
// ============================================================
// O Service Worker é um script que roda em background no navegador.
// Ele intercepta todas as requisições de rede e pode:
//   - Servir conteúdo do cache quando offline
//   - Atualizar o cache com conteúdo novo
//   - Mostrar uma página offline quando não há conexão
//
// FLUXO:
//   1. O navegador registra este arquivo via navigator.serviceWorker.register()
//   2. Ele fica "dormindo" até ser ativado
//   3. Quando ativo, intercepta cada requisição fetch()
//   4. Decide: servir do cache ou ir à rede
// ============================================================

const CACHE_NAME = 'wabisabi-v2';

// URLs para cache na instalação (assets estáticos essenciais)
const CACHE_INICIAL = [
    './',
    './cardapio.php',
    './img/icon-192.png',
    './img/icon-512.png',
    './img/logo-sushi.png',
    './img/home.jpeg',
    // Bootstrap CSS
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    // Bootstrap JS
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    // Bootstrap Icons
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    // Google Fonts
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&family=Playfair+Display:wght@700&display=swap'
];

// ============================================================
// EVENTO: install
// ============================================================
// Dispara quando o service worker é baixado pela primeira vez.
// Aqui fazemos o cache inicial dos assets essenciais.
self.addEventListener('install', (event) => {
    // waitUntil() faz o navegador esperar o cache terminar
    // antes de considerar o SW instalado
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Cache inicial criado');
                return cache.addAll(CACHE_INICIAL);
            })
            .then(() => {
                // skipWaiting() ativa o SW imediatamente
                // sem esperar que todas as abas sejam fechadas
                return self.skipWaiting();
            })
    );
});

// ============================================================
// EVENTO: activate
// ============================================================
// Dispara quando o SW assume o controle.
// Limpa caches antigos (versões diferentes do CACHE_NAME).
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    // Filtra todos os caches que NÃO são o atual
                    cacheNames
                        .filter((name) => name !== CACHE_NAME)
                        // Deleta cada cache antigo
                        .map((name) => caches.delete(name))
                );
            })
            .then(() => {
                // clients.claim() faz o SW assumir controle
                // de todas as abas abertas imediatamente
                return self.clients.claim();
            })
    );
});

// ============================================================
// EVENTO: fetch (interceptação de requisições)
// ============================================================
// Este é o coração do Service Worker. Toda vez que a página
// faz uma requisição (imagem, CSS, JS, API, etc.), este evento dispara.
//
// ESTRATÉGIAS DE CACHE:
//
// 1. Cache First (imagens, fontes, Bootstrap)
//    → Verifica o cache primeiro. Se não tem, vai à rede.
//    → Ideal para assets que não mudam frequentemente.
//
// 2. Network First (páginas PHP, API)
//    → Tenta ir à rede primeiro. Se falhar, usa o cache.
//    → Ideal para dados que precisam estar atualizados.
//
// 3. Network Only (API de pedidos)
//    → Sempre vai à rede. Nunca cacheia.
//    → Ideal para ações que precisam do servidor.
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // --- ESTRATÉGIA: Network Only ---
    // API de pedidos nunca deve ser cacheada
    if (url.pathname.includes('api_pedidos.php')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    // Se estiver offline, retorna erro JSON
                    return new Response(
                        JSON.stringify({ error: 'Você está offline' }),
                        {
                            headers: { 'Content-Type': 'application/json' },
                            status: 503
                        }
                    );
                })
        );
        return;
    }

    // --- ESTRATÉGIA: Network First ---
    // Páginas PHP (cardapio.php, login, etc.)
    // Tenta ir à rede. Se offline, serve do cache.
    if (event.request.destination === 'document' ||
        url.pathname.endsWith('.php')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Se obteve resposta da rede, salva no cache
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME)
                        .then((cache) => cache.put(event.request, responseClone));
                    return response;
                })
                .catch(() => {
                    // Se offline, tenta servir do cache
                    return caches.match(event.request)
                        .then((cached) => {
                            if (cached) return cached;
                            // Se não tem cache, mostra página offline
                            return new Response(
                                '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{background:#0b0b0b;color:#fff;font-family:Inter,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;text-align:center}.offline-box{max-width:400px;padding:40px}.offline-box i{font-size:80px;color:#4a6fa5;margin-bottom:20px}</style></head><body><div class="offline-box"><i class="bi bi-wifi-off"></i><h2>Você está offline</h2><p class="text-secondary mt-3">Conecte-se à internet para acessar o cardápio completo e fazer pedidos.</p><button class="btn mt-4" style="background:#4a6fa5;color:#fff" onclick="location.reload()">Tentar Novamente</button></div></body></html>',
                                {
                                    headers: { 'Content-Type': 'text/html' }
                                }
                            );
                        });
                })
        );
        return;
    }

    // --- ESTRATÉGIA: Cache First ---
    // Imagens, fontes, CSS, JS — verifica cache primeiro
    event.respondWith(
        caches.match(event.request)
            .then((cached) => {
                if (cached) return cached;

                return fetch(event.request)
                    .then((response) => {
                        // Só cacheia respostas válidas (status 200)
                        if (!response || response.status !== 200) {
                            return response;
                        }

                        const responseClone = response.clone();
                        caches.open(CACHE_NAME)
                            .then((cache) => cache.put(event.request, responseClone));

                        return response;
                    })
                    .catch(() => {
                        // Se for imagem e não tem cache, retorna 1px transparente
                        if (event.request.destination === 'image') {
                            return new Response(
                                'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                                {
                                    headers: { 'Content-Type': 'image/gif' }
                                }
                            );
                        }
                        return new Response('', { status: 408 });
                    });
            })
    );
});
