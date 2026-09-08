<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
$baseUrl = $isLocal ? '/cardapio/' : '/';

echo '{
    "name": "Sushi Wabi-Sabi - Cardápio Digital",
    "short_name": "Wabi-Sabi",
    "description": "Cardápio digital do restaurante Sushi Wabi-Sabi.",
    "start_url": "' . $baseUrl . 'cardapio.php",
    "scope": "' . $baseUrl . '",
    "display": "fullscreen",
    "orientation": "portrait",
    "background_color": "#0b0b0b",
    "theme_color": "#0b0b0b",
    "lang": "pt-BR",
    "icons": [
        {
            "src": "' . $baseUrl . 'img/icon-192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "' . $baseUrl . 'img/icon-512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "' . $baseUrl . 'img/icon-192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "maskable"
        },
        {
            "src": "' . $baseUrl . 'img/icon-512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "maskable"
        }
    ],
    "categories": ["food", "restaurant"]
}';
