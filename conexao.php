<?php

try {
    // Detectar ambiente automaticamente
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);

    if ($isLocal) {
        // XAMPP local
        $pdo = new PDO(
            "mysql:host=127.0.0.1;port=3306;dbname=cardapio;charset=utf8mb4",
            "root",
            ""
        );
    } else {
        // InfinityFree (producao)
        $pdo = new PDO(
            "mysql:sql201.infinityfree.com;dbname=if0_42833961_cardapio;charset=utf8mb4",
            "if0_42833961",
            "cardapio123456"
        );
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com o banco de dados']);
    exit();
}
