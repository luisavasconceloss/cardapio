<?php

try {
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        $pdo = new PDO(
            "mysql:host=127.0.0.1;port=3306;dbname=cardapio;charset=utf8mb4",
            "root",
            ""
        );
    } else {
        $pdo = new PDO(
            "mysql:host=sql201.infinityfree.com;dbname=if0_42833961_cardapio;charset=utf8mb4",
            "if0_42833961",
            "cardapio123456"
        );
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexao com o banco de dados']);
    exit();
}
