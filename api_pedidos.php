<?php
session_start();
header('Content-Type: application/json');
include "conexao.php";

$isCliente = isset($_SESSION['usuario_id']);
$isFuncionario = isset($_SESSION['funcionario_id']);

if (!$isCliente && !$isFuncionario) {
    echo json_encode(['error' => 'Nao autorizado']);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'salvar_pedido' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$isCliente) {
        echo json_encode(['error' => 'Apenas clientes podem fazer pedidos']);
        exit();
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id_usuario = $_SESSION['usuario_id'];
    $numero_pedido = 'PED-' . strtoupper(uniqid());
    $itens = json_encode($data['itens']);
    $subtotal = $data['subtotal'];
    $desconto = $data['desconto'];
    $total = $data['total'];
    $forma_pagamento = $data['pagamento'];
    $observacoes = $data['observacoes'] ?? '';
    
    $sql = "INSERT INTO pedidos (id_usuario, numero_pedido, itens, subtotal, desconto, total, forma_pagamento, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_usuario, $numero_pedido, $itens, $subtotal, $desconto, $total, $forma_pagamento, $observacoes])) {
        $id_pedido = $pdo->lastInsertId();
        $logSql = "INSERT INTO pedido_status_log (id_pedido, status_anterior, status_novo, alterado_por) VALUES (?, NULL, 'pendente', ?)";
        $logStmt = $pdo->prepare($logSql);
        $logStmt->execute([$id_pedido, $_SESSION['usuario_nome']]);
        
        echo json_encode(['success' => true, 'numero_pedido' => $numero_pedido]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar pedido']);
    }
    exit();
}

if ($action == 'meus_pedidos') {
    if (!$isCliente) {
        echo json_encode(['error' => 'Apenas clientes podem ver seus pedidos']);
        exit();
    }

    $id_usuario = $_SESSION['usuario_id'];

    try {
        $sql = "SELECT p.id_pedido, p.numero_pedido, p.itens, p.subtotal, p.desconto, p.total, 
                       p.forma_pagamento, p.observacoes, p.status, p.data_pedido
                FROM pedidos p 
                WHERE p.id_usuario = ? 
                ORDER BY p.data_pedido DESC 
                LIMIT 30";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pedidos as &$pedido) {
            $pedido['itens'] = json_decode($pedido['itens'], true);
        }

        echo json_encode($pedidos);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao buscar pedidos', 'details' => $e->getMessage()]);
    }
    exit();
}

if ($action == 'pedidos_pendentes') {
    if (!$isFuncionario) {
        echo json_encode(['error' => 'Acesso restrito a cozinha']);
        exit();
    }
    
    $sql = "SELECT p.*, u.nome as cliente_nome 
            FROM pedidos p 
            LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
            WHERE p.status IN ('pendente', 'preparando') 
            ORDER BY p.data_pedido ASC";
    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($pedidos as &$pedido) {
        $pedido['itens'] = json_decode($pedido['itens'], true);
    }
    
    echo json_encode($pedidos);
    exit();
}

if ($action == 'historico_pedidos') {
    if (!$isFuncionario) {
        echo json_encode(['error' => 'Acesso restrito a cozinha']);
        exit();
    }
    
    $sql = "SELECT p.*, u.nome as cliente_nome 
            FROM pedidos p 
            LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
            WHERE p.status IN ('concluido', 'cancelado') 
            ORDER BY p.data_pedido DESC LIMIT 50";
    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($pedidos as &$pedido) {
        $pedido['itens'] = json_decode($pedido['itens'], true);
    }
    
    echo json_encode($pedidos);
    exit();
}

if ($action == 'atualizar_status' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$isFuncionario) {
        echo json_encode(['error' => 'Acesso restrito a cozinha']);
        exit();
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id_pedido = $data['id_pedido'];
    $novo_status = $data['status'];
    
    $sql = "SELECT status FROM pedidos WHERE id_pedido = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pedido]);
    $status_anterior = $stmt->fetchColumn();
    
    $sql = "UPDATE pedidos SET status = ? WHERE id_pedido = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$novo_status, $id_pedido])) {
        $logSql = "INSERT INTO pedido_status_log (id_pedido, status_anterior, status_novo, alterado_por) VALUES (?, ?, ?, ?)";
        $logStmt = $pdo->prepare($logSql);
        $logStmt->execute([$id_pedido, $status_anterior, $novo_status, $_SESSION['funcionario_nome']]);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

if ($action == 'buscar_pedido' && isset($_GET['id'])) {
    if (!$isFuncionario) {
        echo json_encode(['error' => 'Acesso restrito a cozinha']);
        exit();
    }
    
    $id_pedido = $_GET['id'];
    $sql = "SELECT p.*, u.nome as cliente_nome, u.telefone, u.endereco 
            FROM pedidos p 
            LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
            WHERE p.id_pedido = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pedido) {
        $pedido['itens'] = json_decode($pedido['itens'], true);
        echo json_encode($pedido);
    } else {
        echo json_encode(['error' => 'Pedido nao encontrado']);
    }
    exit();
}

echo json_encode(['error' => 'Acao invalida']);
?>