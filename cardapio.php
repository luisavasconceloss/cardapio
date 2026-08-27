<?php
session_start();
include "conexao.php";

$sql = "SELECT * FROM pratos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sushi Wabi-Sabi | Cardápio Digital</title>
    <link rel="shortcut icon" href="img/logo-sushi.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #0b0b0b;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(10px);
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar-brand,
        h1,
        h2 {
            font-family: 'Playfair Display', serif;
        }

        .navbar .container {
            position: relative;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            text-align: center;
        }

        .img-sushi {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .card-sushi {
            background: #1a1a1a;
            border: 1px solid #696969;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .card-sushi h5 {
            color: #fff;
        }

        .card-sushi:hover {
            transform: translateY(-10px);
            border-color: #e63946;
        }

        .card-sushi .ms-3 {
            margin-bottom: 0;
        }

        .btn-order {
            background-color: #e63946;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-order i {
            margin-right: 5px;
        }

        .btn-order:hover {
            background-color: #d4af37;
            transform: scale(1.05);
        }

        .price-tag {
            color: #e63946;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .category-title {
            border-left: 4px solid #e63946;
            padding-left: 15px;
            margin-bottom: 30px;
        }

        .nav-link {
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #d4af37 !important;
        }

        .nav-link:focus-visible,
        .btn-order:focus-visible {
            outline: 3px solid #d4af37;
            outline-offset: 2px;
        }

        .nav-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-left: 1rem;
        }

        .nav-icons .text-white {
            transition: color 0.3s ease;
        }

        .nav-icons .text-white:hover {
            color: #d4af37 !important;
        }

        .d-flex.align-items-center.gap-4 {
            position: absolute;
            right: 60px;
            top: 10px;
        }

        .text-secondary {
            color: #525050 !important;
        }

        .cart-count {
            position: absolute;
            top: -4px;
            right: -8px;
            background: #e63946;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .brand-text {
            letter-spacing: 1px;
        }

        .dropdown-menu {
            background-color: #1a1a1a;
            border: 1px solid #333;
        }

        .dropdown-item {
            color: #fff;
        }

        .dropdown-item:hover {
            background-color: #e63946;
            color: #fff;
        }

        .modal-title {
            color: #333;
        }

        .cart-modal .modal-content {
            background-color: #1a1a1a;
            border: 1px solid #333;
            color: #fff;
        }

        .cart-item {
            border-bottom: 1px solid #333;
            color: #e63946;
            padding: 10px 0;
        }

        .cart-item h6 {
            color: #1a1a1a;
        }

        .cart-item strong {
            color: #d4af37;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e63946;
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 15px;
        }

        .payment-method {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #e63946;
            background-color: rgba(230, 57, 70, 0.1);
        }

        .payment-method.selected {
            border-color: #e63946;
            background-color: rgba(230, 57, 70, 0.2);
        }

        .btn-clear-cart {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-checkout {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .modal-body {
            color: #1a1a1a;
        }

        .text-secondary.d-block {
            color: #7a4e4e !important;
        }

        .cart-empty {
            text-align: center;
            padding: 40px;
            color: #b0b0b0;
        }

        .modal-content .customize-modal {
            background-color: #1a1a1a;
            border: 1px solid #333;
            color: #fff;
        }

        .pieces-selector {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 10px;
        }

        .pieces-selector button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pieces-selector button.minus {
            background-color: #dc3545;
            color: white;
        }

        .pieces-selector button.plus {
            background-color: #28a745;
            color: white;
        }

        .pieces-selector span {
            font-size: 1.5rem;
            font-weight: bold;
            min-width: 50px;
            text-align: center;
            color: #1a1a1a;
        }

        .btn-customize-add {
            background-color: #e63946;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 20px;
            width: 100%;
        }

        .btn-customize-add:hover {
            background-color: #d4af37;
        }

        .observation-field {
            margin-top: 20px;
        }

        .observation-field textarea {
            width: 100%;
            padding: 10px;
            background: #fff;
            border: 1px solid #333;
            border-radius: 8px;
            resize: vertical;
        }

        .customization-option {
            margin: 15px 0;
            padding: 10px;
            background: #333;
            border-radius: 8px;
            color: white;
        }

        .customization-option label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-select,
        .form-check-input {
            background-color: #1a1a1a;
            color: white;
            border: 1px solid #333;
        }

        #observacaoPedido {
            background-color: #0b0b0b;
            border: 1px solid #333;
            color: white;
            border-radius: 8px;
            resize: vertical;
        }

        #observacaoPedido:focus {
            border-color: #e63946;
            outline: none;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                align-items: flex-start;
                padding: 1rem 0;
            }

            .nav-icons {
                margin-left: 0;
                padding: 0.5rem 0;
                border-top: 1px solid #333;
                width: 100%;
                justify-content: space-around;
            }
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }

            .navbar .container {
                display: flex;
                flex-wrap: wrap;
            }

            .card-sushi img {
                border-radius: 8px;
                width: 100% !important;
                height: 150px !important;
                margin-bottom: 15px;
                margin-left: 0 !important;
            }

            .card-sushi {
                flex-direction: column !important;
                text-align: center;
            }

            .d-flex.align-items-center.gap-4 {
                position: absolute;
                right: 60px;
                top: 10px;
            }
        }

        @media (max-width: 480px) {
            .d-flex.align-items-center.gap-4 {
                right: 50px;
            }

            .d-flex.align-items-center.gap-4 span {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top border-bottom border-secondary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#home">
                <img src="img/logo-sushi.png" alt="Logo" width="35" height="35"
                    class="d-inline-block align-middle me-2">
                <span class="brand-text">WABI-SABI</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-uppercase" style="font-size: 0.85rem; font-weight: 500;">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Entradas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#entradas">Entradas</a></li>
                            <li><a class="dropdown-item" href="#sushis">Sushis Tradicionais</a></li>
                            <li><a class="dropdown-item" href="#hossomaki">Hossomaki</a></li>
                            <li><a class="dropdown-item" href="#uramaki">Uramaki</a></li>
                            <li><a class="dropdown-item" href="#sashimis">Sashimis</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link px-3" href="#temakis">Temakis</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#hotrolls">Hot Rolls</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#renomados">Pratos Renomados</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#supremo">Supremos</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Bebidas</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#bebidas">Sem Álcool</a></li>
                            <li><a class="dropdown-item" href="#alcoolicas">Alcoólicas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link px-3" href="#combo">Combinados</a></li>
                </ul>

                <div class="nav-icons">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <div class="dropdown">
                            <a href="#"
                                class="text-white text-decoration-none d-flex flex-column align-items-center dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span style="font-size: 0.7rem;"><?= $_SESSION['usuario_nome'] ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#perfilModal"><i class="bi bi-person"></i> Meu Perfil</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#enderecoModal"><i class="bi bi-geo-alt"></i> Meu Endereço</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#pedidosModal"><i class="bi bi-clock-history"></i> Meus Pedidos</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="login_cardapio.php?logout=1"><i
                                            class="bi bi-box-arrow-right"></i> Sair</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login_cardapio.php"
                            class="text-white text-decoration-none d-flex flex-column align-items-center">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span style="font-size: 0.7rem;">Entrar</span>
                        </a>
                    <?php endif; ?>

                    <a href="#"
                        class="text-white text-decoration-none d-flex flex-column align-items-center position-relative"
                        onclick="displayCart(); new bootstrap.Modal(document.getElementById('cartModal')).show();">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="cart-count">0</span>
                        <span style="font-size: 0.7rem;">Carrinho</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section" id="home">
        <div class="container">
            <h1 class="display-2">A Arte da Culinária Japonesa</h1>
            <p class="lead">Ingredientes frescos, cortes precisos e sabor inesquecível.</p>
        </div>
    </header>

    <main class="container my-5">

        <section id="entradas" class="mb-5">
            <h2 class="category-title">Entradas</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 1): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Entradas">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="sushis" class="mb-5">
            <h2 class="category-title">Sushis Tradicionais</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 2): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Sushi Tradicional">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="hossomaki" class="mb-5">
            <h2 class="category-title">Hossomaki</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 3): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Hossomaki">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="uramaki" class="mb-5">
            <h2 class="category-title">Uramaki</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 4): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Uramaki">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="sashimis" class="mb-5">
            <h2 class="category-title">Sashimis</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 5): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Sashimi">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="temakis" class="mb-5">
            <h2 class="category-title">Temakis</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 6): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Temakis">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="hotrolls" class="mb-5">
            <h2 class="category-title">Hot Rolls</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 7): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Hot Roll">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="renomados" class="mb-5">
            <h2 class="category-title">Pratos Renomados</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 8): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Pratos Renomados">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="supremo" class="mb-5">
            <h2 class="category-title">Supremos</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 9): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Sobremesas">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="bebidas" class="mb-5">
            <h2 class="category-title">Bebidas</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 10): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Bebidas">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="alcoolicas" class="mb-5">
            <h2 class="category-title">Bebidas Alcoólicas</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 11): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="alcoolicas">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="combo" class="mb-5">
            <h2 class="category-title">Combinados</h2>
            <div class="row g-4">
                <?php foreach ($pratos as $prato): ?>
                    <?php if ($prato['id_categoria'] == 12): ?>
                        <div class="col-md-6">
                            <div class="card card-sushi flex-row align-items-center p-3"
                                onclick="openCustomizeModal(<?= htmlspecialchars(json_encode($prato)) ?>)">
                                <img src="img/<?= $prato['id_prato'] ?>.jpg"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="combos">
                                <div class="ms-3">
                                    <h5 class="mb-1"><?= $prato['nome'] ?></h5>
                                    <p class="small text-secondary mb-1"><?= $prato['descricao'] ?></p>
                                    <span class="price-tag">R$ <?= $prato['preco'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="bg-black text-center py-4 border-top border-secondary">
        <p class="mb-0 text-secondary">&copy; 2026 Sushi WABI-SABI - Qualidade e Tradição.</p>
    </footer>

    <div class="modal fade" id="customizeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content customize-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-sliders2"></i> Personalizar Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="customizeContent">
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="removerFoco()">Cancelar</button>
                    <button type="button" class="btn btn-customize-add" id="confirmCustomizeBtn">
                        <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content cart-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-cart3"></i> Seu Carrinho
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartContent">
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="removerFoco()">Fechar</button>
                    <button type="button" class="btn btn-success" onclick="removerFoco(); finalizarPedido()">
                        <i class="bi bi-check-circle"></i> Finalizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content cart-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-credit-card"></i> Formas de Pagamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="paymentContent">
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="removerFoco()">Voltar</button>
                    <button type="button" class="btn btn-primary" id="confirmPaymentBtn" onclick="confirmarPedido()">
                        Confirmar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Perfil -->
    <div class="modal fade" id="perfilModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content cart-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title"><i class="bi bi-person"></i> Meu Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <div class="text-center mb-3">
                            <i class="bi bi-person-circle" style="font-size: 80px; color: #e63946;"></i>
                        </div>
                        <p><strong>Nome:</strong> <?= $_SESSION['usuario_nome'] ?></p>
                        <p><strong>Email:</strong> <?= $_SESSION['usuario_email'] ?></p>
                        <button class="btn btn-primary w-100" onclick="alert('Funcionalidade em desenvolvimento')">Editar
                            Perfil</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Endereço -->
    <div class="modal fade" id="enderecoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content cart-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title"><i class="bi bi-geo-alt"></i> Meu Endereço</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEndereco">
                        <div class="mb-3">
                            <label>CEP</label>
                            <input type="text" class="form-control" id="cep" placeholder="00000-000">
                        </div>
                        <div class="mb-3">
                            <label>Rua</label>
                            <input type="text" class="form-control" id="rua">
                        </div>
                        <div class="mb-3">
                            <label>Número</label>
                            <input type="text" class="form-control" id="numero">
                        </div>
                        <div class="mb-3">
                            <label>Bairro</label>
                            <input type="text" class="form-control" id="bairro">
                        </div>
                        <div class="mb-3">
                            <label>Cidade</label>
                            <input type="text" class="form-control" id="cidade">
                        </div>
                        <div class="mb-3">
                            <label>Complemento</label>
                            <input type="text" class="form-control" id="complemento">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Salvar Endereço</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pedidos -->
    <div class="modal fade" id="pedidosModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content cart-modal">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title"><i class="bi bi-clock-history"></i> Meus Pedidos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="pedidosContent">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 50px;"></i>
                        <p class="mt-3">Nenhum pedido encontrado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Carregar carrinho do localStorage ou criar vazio
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        let currentCustomizeItem = null;
        let currentPieces = 1;
        let selectedPayment = '';
        let currentComboConfig = null;

        function removerFoco() {
            // Remove o foco de qualquer elemento que possa estar focado
            if (document.activeElement && document.activeElement.blur) {
                document.activeElement.blur();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Limpeza de backdrop e foco ao fechar qualquer modal
            const modais = ['cartModal', 'paymentModal', 'customizeModal'];
    
            modais.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    // Evento principal: depois que o modal termina de fechar
                    modal.addEventListener('hidden.bs.modal', function() {
                        limparBackdrop();
                    });
                    // Fallback: quando começa a fechar (caso o hidden não dispare)
                    modal.addEventListener('hide.bs.modal', function() {
                        removerFoco();
                        setTimeout(limparBackdrop, 350); // 350ms = duração da animação do Bootstrap
                    });
                }
            });
        });

        // Função centralizada para limpar backdrop e restaurar a página
        function limparBackdrop() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
        }

        // Função para salvar carrinho no localStorage
        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        // Função para atualizar o contador do carrinho
        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = totalItems;
            });
        }

        function openCustomizeModal(prato) {
            currentCustomizeItem = prato;
            currentPieces = 1;

            const isBebida = (prato.id_categoria == 10 || prato.id_categoria == 11);
            const pratosComAcucar = [40];
            const temOpcaoAcucar = pratosComAcucar.includes(prato.id_prato);
            const isRefrigerante = (prato.id_prato == 39);
            const isSuco = (prato.id_categoria == 10 && prato.id_prato != 39);

            if (isBebida) {
                const customizeContent = document.getElementById('customizeContent');
                let html = `
                    <div class="text-center mb-4">
                        <h4>${escapeHtml(prato.nome)}</h4>
                        <img src="img/${escapeHtml(prato.id_prato)}.jpg" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;" alt="${escapeHtml(prato.nome)}">
                        <p class="text-secondary mt-2">${escapeHtml(prato.descricao)}</p>
                        <div class="price-tag">R$ ${parseFloat(prato.preco).toFixed(2)}</div>
                    </div>
                    <div class="pieces-selector">
                        <button class="minus" onclick="changePieces(-1)">-</button>
                        <span id="piecesCount">${currentPieces}</span>
                        <button class="plus" onclick="changePieces(1)">+</button>
                        <span class="ms-3">unidade(s)</span>
                    </div>
                    <h6><i class="bi bi-sliders2"></i> Personalize sua bebida:</h6>
                `;

                if (isRefrigerante) {
                    html += `
                        <div class="customization-option">
                            <label><i class="bi bi-cup-straw"></i> Escolha o sabor:</label>
                            <select id="bebidaSabor" class="form-select">
                                <option value="Coca-Cola">Coca-Cola</option>
                                <option value="Coca-Cola Zero">Coca-Cola Zero</option>
                                <option value="Guaraná Antarctica">Guaraná Antarctica</option>
                                <option value="Fanta Laranja">Fanta Laranja</option>
                                <option value="Fanta Uva">Fanta Uva</option>
                                <option value="Schweppes">Schweppes</option>
                                <option value="Pepsi">Pepsi</option>
                            </select>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="customization-option">
                            <label><i class="bi bi-thermometer-half"></i> Temperatura:</label>
                            <select id="bebidaTemperatura" class="form-select">
                                <option value="normal" selected>Normal</option>
                                <option value="gelo">Com gelo (+ R$ 0,50)</option>
                                <option value="sem gelo">Sem gelo</option>
                                <option value="extra gelo">Extra gelo (+ R$ 1,00)</option>
                            </select>
                        </div>
                    `;
                    if (isSuco) {
                        html += `
                            <div class="customization-option">
                                <label><i class="bi bi-lemon"></i> Acompanhamentos:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="bebidaLimao">
                                    <label class="form-check-label">Rodelas de limão (R$ 0,50)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="bebidaHortela">
                                    <label class="form-check-label">Folhas de hortelã (R$ 0,50)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="bebidaCanela">
                                    <label class="form-check-label">Canela em pau (R$ 1,00)</label>
                                </div>
                            </div>
                        `;
                    }
                }

                if (temOpcaoAcucar && !isRefrigerante) {
                    html += `
                        <div class="customization-option">
                            <label><i class="bi bi-droplet"></i> Adoçar:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bebidaAcucar" id="bebidaAcucarNormal" value="normal" checked>
                                <label class="form-check-label">Normal (padrão)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bebidaAcucar" id="bebidaAcucarPouco" value="pouco">
                                <label class="form-check-label">Pouco açúcar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bebidaAcucar" id="bebidaAcucarSem" value="sem">
                                <label class="form-check-label">Sem açúcar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bebidaAcucar" id="bebidaAcucarAdocante" value="adocante">
                                <label class="form-check-label">Com adoçante (+ R$ 0,50)</label>
                            </div>
                        </div>
                    `;
                }

                html += `
                    <div class="observation-field">
                        <label><i class="bi bi-chat-text"></i> Observações (opcional):</label>
                        <textarea id="observationText" rows="3" placeholder="Ex: Pouco gelo, sem limão, etc..."></textarea>
                    </div>
                `;

                customizeContent.innerHTML = html;
                new bootstrap.Modal(document.getElementById('customizeModal')).show();
                return;
            }

            const customizeContent = document.getElementById('customizeContent');
            const isCombinado = (prato.id_categoria == 12);

            let html = ``;

            if (isCombinado) {
                const idPrato = prato.id_prato;
                let totalPecas = 0;
                let totalTemakis = 0;

                // Define as quantidades máximas com base no id do combinado
                switch (idPrato) {
                    case 49:
                        totalPecas = 20;
                        totalTemakis = 0;
                        break;
                    case 50:
                        totalPecas = 30;
                        totalTemakis = 0;
                        break;
                    case 51:
                        totalPecas = 40;
                        totalTemakis = 1;
                        break;
                    case 52:
                        totalPecas = 50;
                        totalTemakis = 2;
                        break;
                    default:
                        totalPecas = 0;
                }

                // Armazena no objeto global
                currentComboConfig = {
                    totalPecas,
                    totalTemakis,
                    idPrato
                };

                // Opções de sabores para as peças
                const sabores = ['Nigiri de Salmão', 'Nigiri de Atum', 'Joy Salmão', 'Gunkan Salmão', 'Hossomaki de Salmão', 'Hossomaki de Kani', 'Hossomaki de Pepino', 'Uramaki de Salmão', 'Uramaki Camarão', 'Uramaki Kani', 'Philadelphia', 'Skin', 'Sashimi de Salmão', 'Sashimi de Atum', 'Sashimi Misto', 'Hot roll Salmão', 'Hot roll Camarão', 'Hot roll Kani'];

                // Cria os controles de distribuição das peças
                let pecasHtml = '<div class="customization-option"><label>Distribuição das peças (total: ' + totalPecas + ' peças):</label>';
                sabores.forEach(sabor => {
                    // Usar um ID seguro (remover espaços e caracteres especiais)
                    let idSafe = sabor.replace(/[^a-zA-Z0-9]/g, '_');
                    pecasHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">${sabor}</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="alterarQuantidadePecas('${idSafe}', -1, ${totalPecas})">-</button>
                                <span id="pecas_${idSafe}" class="mx-2" style="min-width: 35px; text-align: center;">0</span>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="alterarQuantidadePecas('${idSafe}', 1, ${totalPecas})">+</button>
                            </div>
                        </div>
                    `;
                });
                pecasHtml += '<div id="avisoPecas" class="text-warning small mt-2"></div></div>';

                // Controles para adicionais (já existentes, mas reutilizamos)
                const adicionaisHtml = `
                    <div class="customization-option">
                        <label><i class="bi bi-plus-circle"></i> Extras:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraGergelim">
                            <label class="form-check-label">Gergelim (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraMolhoBranco">
                            <label class="form-check-label">Molho branco (R$ 2,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraCebolinha">
                            <label class="form-check-label">Cebolinha (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraCreamCheese">
                            <label class="form-check-label">Cream Cheese (R$ 3,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraShoyu">
                            <label class="form-check-label">Shoyu (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraTeriaki">
                            <label class="form-check-label">Teriyaki (R$ 2,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraWassabi">
                            <label class="form-check-label">Wasabi (R$ 1,00)</label>
                        </div>
                    </div>
                `;

                // Controles para escolha dos temakis (apenas para combos 51 e 52)
                let temakisHtml = '';
                if (totalTemakis > 0) {
                    const opcoesTemaki = [
                        'Temaki de salmão',
                        'Temaki de salmão cream cheese',
                        'Temaki de camarão',
                        'Temaki de kani',
                        'Temaki skin',
                        'Temaki califórnia'
                    ];
                    temakisHtml = '<div class="customization-option"><label><i class="bi bi-egg-fried"></i> Escolha os Temakis (máximo ' + totalTemakis + '):</label>';
                    for (let i = 1; i <= totalTemakis; i++) {
                        temakisHtml += `
                            <div class="mb-2">
                                <label class="form-label">Temaki ${i}</label>
                                <select id="temaki_${i}" class="form-select">
                                    <option value="">Selecione um sabor</option>
                                    ${opcoesTemaki.map(op => `<option value="${op}">${op}</option>`).join('')}
                                </select>
                            </div>
                        `;
                    }
                    temakisHtml += '</div>';
                }

                // Monta o HTML completo para o combinado
                html = `
                    <div class="text-center mb-4">
                        <h4>${escapeHtml(prato.nome)}</h4>
                        <img src="img/${escapeHtml(prato.id_prato)}.jpg" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;" alt="${escapeHtml(prato.nome)}">
                        <p class="text-secondary mt-2">${escapeHtml(prato.descricao)}</p>
                        <div class="price-tag">R$ ${parseFloat(prato.preco).toFixed(2)}</div>
                    </div>
                    <h6><i class="bi bi-sliders2"></i> Personalize seu Combinado:</h6>
                    ${pecasHtml}
                    ${adicionaisHtml}
                    ${temakisHtml}
                    <div class="observation-field">
                        <label><i class="bi bi-chat-text"></i> Observações (opcional):</label>
                        <textarea id="observationText" rows="3" placeholder="Ex: Sem cebolinha, pouco shoyu..."></textarea>
                    </div>
                `;
            } else {
                // Produtos comuns (categorias 1-9, etc.)
                html = `
                    <div class="text-center mb-4">
                        <h4>${escapeHtml(prato.nome)}</h4>
                        <img src="img/${escapeHtml(prato.id_prato)}.jpg" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;" alt="${escapeHtml(prato.nome)}">
                        <p class="text-secondary mt-2">${escapeHtml(prato.descricao)}</p>
                        <div class="price-tag">R$ ${parseFloat(prato.preco).toFixed(2)}</div>
                    </div>
                    <div class="pieces-selector">
                        <button class="minus" onclick="changePieces(-1)">-</button>
                        <span id="piecesCount">${currentPieces}</span>
                        <button class="plus" onclick="changePieces(1)">+</button>
                        <span class="ms-3">peça(s)</span>
                    </div>
                    <h6><i class="bi bi-sliders2"></i> Personalize seu pedido:</h6>
                    <div class="customization-option">
                        <label><i class="bi bi-plus-circle"></i> Extras:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraGergelim">
                            <label class="form-check-label">Gergelim (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraMolhoBranco">
                            <label class="form-check-label">Molho branco (R$ 2,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraCebolinha">
                            <label class="form-check-label">Cebolinha (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraCreamCheese">
                            <label class="form-check-label">Cream Cheese (R$ 3,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraShoyu">
                            <label class="form-check-label">Shoyu (R$ 1,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraTeriaki">
                            <label class="form-check-label">Teriyaki (R$ 2,00)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extraWassabi">
                            <label class="form-check-label">Wasabi (R$ 1,00)</label>
                        </div>
                    </div>
                    <div class="observation-field">
                        <label><i class="bi bi-chat-text"></i> Observações (opcional):</label>
                        <textarea id="observationText" rows="3" placeholder="Ex: Sem cebolinha, pouco shoyu, bem passado..."></textarea>
                    </div>
                `;
            }
            customizeContent.innerHTML = html;
            new bootstrap.Modal(document.getElementById('customizeModal')).show();
        }

        function addCustomizedBebidaToCart() {
            const observation = document.getElementById('observationText')?.value || '';
            const precoUnitario = parseFloat(currentCustomizeItem.preco);
            const isRefrigerante = (currentCustomizeItem.id_prato == 39);
            const isSuco = (currentCustomizeItem.id_categoria == 10 && currentCustomizeItem.id_prato != 39);

            let personalizationText = '';
            let adicional = 0;

            if (isRefrigerante) {
                const sabor = document.getElementById('bebidaSabor')?.value || 'Coca-Cola';
                personalizationText = ` - Sabor: ${ sabor } `;
            } else {
                const temperatura = document.getElementById('bebidaTemperatura')?.value || 'normal';
                if (temperatura === 'gelo') {
                    adicional += 0.50;
                    personalizationText += ', +Gelo';
                } else if (temperatura === 'extra gelo') {
                    adicional += 1.00;
                    personalizationText += ', +Extra gelo';
                } else if (temperatura === 'sem gelo') {
                    personalizationText += ', Sem gelo';
                }

                if (isSuco) {
                    const temLimao = document.getElementById('bebidaLimao')?.checked || false;
                    const temHortela = document.getElementById('bebidaHortela')?.checked || false;
                    const temCanela = document.getElementById('bebidaCanela')?.checked || false;
                    if (temLimao) {
                        adicional += 0.50;
                        personalizationText += ', +Limão';
                    }
                    if (temHortela) {
                        adicional += 0.50;
                        personalizationText += ', +Hortelã';
                    }
                    if (temCanela) {
                        adicional += 1.00;
                        personalizationText += ', +Canela';
                    }
                }

                const pratosComAcucar = [40];
                const temOpcaoAcucar = pratosComAcucar.includes(currentCustomizeItem.id_prato);
                if (temOpcaoAcucar) {
                    const acucar = document.querySelector('input[name="bebidaAcucar"]:checked')?.value || 'normal';
                    if (acucar === 'pouco') {
                        personalizationText += ', Pouco açúcar';
                    } else if (acucar === 'sem') {
                        personalizationText += ', Sem açúcar';
                    } else if (acucar === 'adocante') {
                        adicional += 0.50;
                        personalizationText += ', +Adoçante';
                    }
                }
            }

            if (observation) personalizationText += ` - Obs: ${ observation } `;
            const precoTotal = (precoUnitario * currentPieces) + adicional;

            const customItem = {
                id: currentCustomizeItem.id_prato + '_' + Date.now(),
                nome: currentCustomizeItem.nome + personalizationText,
                preco: precoTotal,
                quantity: 1,
                originalPreco: precoUnitario,
                pieces: currentPieces,
                tipo: 'bebida',
                categoria: currentCustomizeItem.id_categoria,
                id_prato: currentCustomizeItem.id_prato,
                ...(isRefrigerante && {
                    sabor: document.getElementById('bebidaSabor')?.value
                }),
                observation: observation,
                adicional: adicional
            };
            cart.push(customItem);
            saveCart();

            const btn = document.getElementById('confirmCustomizeBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Adicionado!';
            btn.style.backgroundColor = '#28a745';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.backgroundColor = '#e63946';
            }, 1500);
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('customizeModal')).hide();
            }, 500);
        }

        function changePieces(delta) {
            currentPieces += delta;
            if (currentPieces < 1) currentPieces = 1;
            if (currentPieces > 50) currentPieces = 50;
            document.getElementById('piecesCount').innerText = currentPieces;
        }

        // Controla a quantidade de peças de cada sabor
        function alterarQuantidadePecas(idSafe, delta, totalMaximo) {
            let span = document.getElementById(`pecas_${idSafe}`);
            if (!span) return;
            let valorAtual = parseInt(span.innerText) || 0;
            let novoValor = valorAtual + delta;
            if (novoValor < 0) novoValor = 0;
            // Calcula a soma atual de todos os sabores
            let somaAtual = 0;
            document.querySelectorAll('[id^="pecas_"]').forEach(el => {
                somaAtual += parseInt(el.innerText) || 0;
            });
            // Se for incremento, verifica se o total ultrapassa o limite
            if (delta > 0 && (somaAtual + delta) > totalMaximo) {
                document.getElementById('avisoPecas').innerText = `Total máximo de peças é ${totalMaximo}. Você já selecionou ${somaAtual} peças.`;
                return;
            }
            document.getElementById('avisoPecas').innerText = '';
            span.innerText = novoValor;
        }

        // Recupera a distribuição das peças
        function getDistribuicaoPecas() {
            let distribuicao = {};
            document.querySelectorAll('[id^="pecas_"]').forEach(el => {
                let saborId = el.id.replace('pecas_', '');
                let saborOriginal = saborId.replace(/_/g, ' '); // Reconstitui o nome (aproximado)
                // Ou, melhor: guarde os nomes em um array global, mas por enquanto basta o valor
                let qtd = parseInt(el.innerText) || 0;
                if (qtd > 0) {
                    distribuicao[saborOriginal] = qtd;
                }
            });
            return distribuicao;
        }

        // Recupera os temakis escolhidos
        function getTemakisEscolhidos(totalTemakis) {
            let temakis = [];
            for (let i = 1; i <= totalTemakis; i++) {
                let select = document.getElementById(`temaki_${i}`);
                if (select && select.value) temakis.push(select.value);
            }
            return temakis;
        }

        function addCustomizedToCart() {
            const observation = document.getElementById('observationText')?.value || '';
            const precoUnitario = parseFloat(currentCustomizeItem.preco);

            // Verifica se é um combinado (categoria 12)
            const isCombinado = (currentCustomizeItem.id_categoria == 12);
            let adicional = 0;
            let personalizationText = '';

            if (isCombinado) {
                // --- Lógica para COMBINADOS ---
                const config = currentComboConfig;
                if (!config) return;

                // 1. Distribuição das peças
                const distribuicao = getDistribuicaoPecas();
                let pecasTexto = '';
                let totalPecas = 0;
                for (let [sabor, qtd] of Object.entries(distribuicao)) {
                    if (qtd > 0) {
                        pecasTexto += `${qtd} ${sabor}, `;
                        totalPecas += qtd;
                    }
                }
                if (totalPecas !== config.totalPecas) {
                    alert(`A soma das peças deve ser exatamente ${config.totalPecas}. Você selecionou ${totalPecas}.`);
                    return;
                }
                if (pecasTexto) pecasTexto = pecasTexto.slice(0, -2);
                personalizationText += ` (${pecasTexto})`;

                // 2. Adicionais (usando os mesmos checkboxes)
                const extraGergelim = document.getElementById('extraGergelim')?.checked || false;
                const extraMolhoBranco = document.getElementById('extraMolhoBranco')?.checked || false;
                const extraCebolinha = document.getElementById('extraCebolinha')?.checked || false;
                const extraCreamCheese = document.getElementById('extraCreamCheese')?.checked || false;
                const extraShoyu = document.getElementById('extraShoyu')?.checked || false;
                const extraTeriaki = document.getElementById('extraTeriaki')?.checked || false;
                const extraWassabi = document.getElementById('extraWassabi')?.checked || false;

                let extrasTexto = '';
                if (extraGergelim) {
                    adicional += 1.00;
                    extrasTexto += ', +Gergelim';
                }
                if (extraMolhoBranco) {
                    adicional += 2.00;
                    extrasTexto += ', +MolhoBranco';
                }
                if (extraCebolinha) {
                    adicional += 1.00;
                    extrasTexto += ', +Cebolinha';
                }
                if (extraCreamCheese) {
                    adicional += 3.00;
                    extrasTexto += ', +CreamCheese';
                }
                if (extraShoyu) {
                    adicional += 1.00;
                    extrasTexto += ', +Shoyu';
                }
                if (extraTeriaki) {
                    adicional += 2.00;
                    extrasTexto += ', +Teriyaki';
                }
                if (extraWassabi) {
                    adicional += 1.00;
                    extrasTexto += ', +Wasabi';
                }
                if (extrasTexto) personalizationText += extrasTexto;

                // 3. Temakis (se houver)
                if (config.totalTemakis > 0) {
                    const temakis = getTemakisEscolhidos(config.totalTemakis);
                    if (temakis.length !== config.totalTemakis) {
                        alert(`Por favor, selecione os ${config.totalTemakis} temaki(s).`);
                        return;
                    }
                    personalizationText += ` - Temakis: ${temakis.join(', ')}`;
                }

                if (observation) personalizationText += ` - Obs: ${observation}`;
                const precoTotal = (precoUnitario * currentPieces) + adicional;

                const customItem = {
                    id: currentCustomizeItem.id_prato + '_' + Date.now(),
                    nome: currentCustomizeItem.nome + personalizationText,
                    preco: precoTotal,
                    quantity: 1,
                    originalPreco: precoUnitario,
                    id_prato: currentCustomizeItem.id_prato,
                    pieces: currentPieces,
                    tipo: 'combinado',
                    distribuicao: distribuicao,
                    extras: {
                        extraGergelim,
                        extraMolhoBranco,
                        extraCebolinha,
                        extraCreamCheese,
                        extraShoyu,
                        extraTeriaki,
                        extraWassabi
                    },
                    observation: observation,
                    adicional: adicional
                };
                cart.push(customItem);
                saveCart();

            } else {
                // --- Lógica original para outros produtos (comidas normais) ---
                const extraGergelim = document.getElementById('extraGergelim')?.checked || false;
                const extraMolhoBranco = document.getElementById('extraMolhoBranco')?.checked || false;
                const extraCebolinha = document.getElementById('extraCebolinha')?.checked || false;
                const extraCreamCheese = document.getElementById('extraCreamCheese')?.checked || false;
                const extraShoyu = document.getElementById('extraShoyu')?.checked || false;
                const extraTeriaki = document.getElementById('extraTeriaki')?.checked || false;
                const extraWassabi = document.getElementById('extraWassabi')?.checked || false;
                const substitute = document.getElementById('substituteProtein')?.value || '';

                let extrasTexto = '';
                if (extraGergelim) {
                    adicional += 1.00;
                    extrasTexto += ', +Gergelim';
                }
                if (extraMolhoBranco) {
                    adicional += 2.00;
                    extrasTexto += ', +MolhoBranco';
                }
                if (extraCebolinha) {
                    adicional += 1.00;
                    extrasTexto += ', +Cebolinha';
                }
                if (extraCreamCheese) {
                    adicional += 3.00;
                    extrasTexto += ', +CreamCheese';
                }
                if (extraShoyu) {
                    adicional += 1.00;
                    extrasTexto += ', +Shoyu';
                }
                if (extraTeriaki) {
                    adicional += 2.00;
                    extrasTexto += ', +Teriaki';
                }
                if (extraWassabi) {
                    adicional += 1.00;
                    extrasTexto += ', +Wassabi';
                }

                let substituicaoTexto = '';
                let substituicaoCusto = 0;
                if (substitute === 'atum') {
                    substituicaoCusto = 5.00;
                    substituicaoTexto = ', Troca: Atum';
                } else if (substitute === 'camarao') {
                    substituicaoCusto = 8.00;
                    substituicaoTexto = ', Troca: Camarão';
                } else if (substitute === 'salmao') {
                    substituicaoCusto = 9.00;
                    substituicaoTexto = ', Troca: Salmão';
                }
                adicional += substituicaoCusto;

                const precoTotal = (precoUnitario * currentPieces) + adicional;
                let personalizationText = '';
                if (extrasTexto) personalizationText += extrasTexto;
                if (substituicaoTexto) personalizationText += substituicaoTexto;
                if (observation) personalizationText += ` - Obs: ${observation}`;

                const customItem = {
                    id: currentCustomizeItem.id_prato + '_' + Date.now(),
                    nome: currentCustomizeItem.nome + personalizationText,
                    preco: precoTotal,
                    quantity: 1,
                    originalPreco: precoUnitario,
                    id_prato: currentCustomizeItem.id_prato,
                    pieces: currentPieces,
                    extraGergelim,
                    extraMolhoBranco,
                    extraCebolinha,
                    extraCreamCheese,
                    extraShoyu,
                    extraTeriaki,
                    extraWassabi,
                    substitute,
                    observation,
                    adicional
                };
                cart.push(customItem);
                saveCart();
            }

            // Feedback visual e fechamento do modal
            const btn = document.getElementById('confirmCustomizeBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Adicionado!';
            btn.style.backgroundColor = '#28a745';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.backgroundColor = '#e63946';
            }, 1500);
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('customizeModal')).hide();
            }, 500);
        }

        function addToCart(prato, evento = null) {
            const existingItem = cart.find(item => item.id === prato.id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: prato.id,
                    nome: prato.nome,
                    preco: prato.preco,
                    quantity: 1,
                    id_prato: prato.id_prato
                });
            }
            saveCart();

            if (evento && evento.target) {
                const btn = evento.target.closest('.btn-order');
                if (btn) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Adicionado';
                    btn.style.backgroundColor = '#28a745';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.backgroundColor = '#e63946';
                    }, 1500);
                }
            }
        }

        function removeFromCart(itemId) {
            cart = cart.filter(item => item.id !== itemId);
            saveCart();
            displayCart();
        }

        function updateQuantity(itemId, change) {
            try {
                const item = cart.find(item => item.id === itemId);
                if (item) {
                    item.quantity += change;
                    if (item.quantity <= 0) {
                        removeFromCart(itemId);
                    } else {
                        saveCart();
                        displayCart();
                    }
                }
            } catch (erro) {
                console.error('Erro no updateQuantity:', erro);
            }
        }

        function clearCart() {
            if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                cart = [];
                saveCart();
                displayCart();
            }
        }

        function calculateTotal() {
            return cart.reduce((sum, item) => sum + (item.preco * item.quantity), 0);
        }

        function displayCart() {
            try {
                const cartContent = document.getElementById('cartContent');
                if (cart.length === 0) {
                    cartContent.innerHTML = `
                        <div class="cart-empty">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <p class="mt-3">Seu carrinho está vazio!</p>
                            <button class="btn btn-primary" data-bs-dismiss="modal" onclick="removerFoco()">Continuar Comprando</button>
                        </div >
                    `;
                    return;
                }
                let html = '<div class="cart-items">';
                cart.forEach(item => {
                    const imgId = item.id_prato ? item.id_prato : 'placeholder';
                    const imgPath = `img/${imgId}.jpg`;
                    html += `
                        <div class="cart-item d-flex align-items-center">
                            <img src="${imgPath}" alt="${item.nome}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${item.nome}</h6>
                                        <small class="text-secondary">R$ ${item.preco.toFixed(2)}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-danger" onclick="updateQuantity('${item.id}', -1)">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="mx-2">${item.quantity}</span>
                                        <button class="btn btn-sm btn-outline-success" onclick="updateQuantity('${item.id}', 1)">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger ms-2" onclick="removeFromCart('${item.id}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += `
                    <div class="cart-total">
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>R$ ${calculateTotal().toFixed(2)}</strong>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-clear-cart" onclick="clearCart()">
                            <i class="bi bi-trash3"></i> Limpar Carrinho
                        </button>
                    </div>
                `;
                cartContent.innerHTML = html;
            } catch (erro) {
                console.error('Erro no displayCart:', erro);
                alert('Ocorreu um erro ao exibir o carrinho. Recarregue a página.');
            }
        }

        function finalizarPedido() {
            if (cart.length === 0) {
                alert('Seu carrinho está vazio!');
                return;
            }
            const total = calculateTotal();
            const paymentContent = document.getElementById('paymentContent');
            paymentContent.innerHTML = `
                <div class="mb-4">
                    <h6>Resumo do Pedido:</h6>
                    <p class="text-secondary">Total de itens: ${cart.reduce((sum, item) => sum + item.quantity, 0)}</p>
                    <h5 class="text-danger">Total: R$ ${total.toFixed(2)}</h5>
                </div >
                <h6 class="mb-3">Selecione a forma de pagamento:</h6>
                <div class="payment-method" onclick="selectPayment('Dinheiro', event)">
                    <i class="bi bi-cash-stack"></i> Dinheiro
                    <small class="text-secondary d-block">(10% de desconto)</small>
                </div>
                <div class="payment-method" onclick="selectPayment('Pix', event)">
                    <i class="bi bi-qr-code"></i> Pix
                    <small class="text-secondary d-block">(5% de desconto)</small>
                </div>
                <div class="payment-method" onclick="selectPayment('Cartão de Crédito', event)">
                    <i class="bi bi-credit-card"></i> Cartão de Crédito
                    <small class="text-secondary d-block">(Até 3x sem juros)</small>
                </div>
                <div class="payment-method" onclick="selectPayment('Cartão de Débito', event)">
                    <i class="bi bi-bank2"></i> Cartão de Débito
                </div>
                <input type="hidden" id="selectedPayment" value="">
            `;
            bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function selectPayment(payment, ev) {
            selectedPayment = payment;
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
            if (ev && ev.target) {
                ev.target.closest('.payment-method').classList.add('selected');
            }
            document.getElementById('selectedPayment').value = payment;
        }

        function salvarPedidoHistorico(pedido) {
            const usuarioId = <?= isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null' ?>;
            if (usuarioId) {
                let historico = JSON.parse(localStorage.getItem(`pedidos_${usuarioId}`)) || [];
                pedido.id = Date.now();
                pedido.data = new Date().toLocaleString();
                historico.push(pedido);
                localStorage.setItem(`pedidos_${usuarioId}`, JSON.stringify(historico));
            }
        }

        function carregarHistoricoPedidos() {
            const usuarioId = <?= isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null' ?>;
            if (usuarioId) {
                const historico = JSON.parse(localStorage.getItem(`pedidos_${usuarioId}`)) || [];
                const pedidosContent = document.getElementById('pedidosContent');
                if (historico.length === 0) {
                    pedidosContent.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 50px;"></i>
                            <p class="mt-3">Nenhum pedido encontrado.</p>
                        </div>
                    `;
                } else {
                    let html = '<div class="list-group">';
                    historico.reverse().forEach(pedido => {
                        html += `
                            <div class="list-group-item bg-dark text-white border-secondary mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>Pedido #${pedido.id}</strong>
                                    <small>${pedido.data}</small>
                                </div>
                                <div class="mt-2">
                                    <strong>Total:</strong> R$ ${pedido.total ? pedido.total.toFixed(2) : '0,00'}
                                </div>
                                <div class="mt-2">
                                    <strong>Forma de Pagamento:</strong> ${pedido.pagamento || 'Não informado'}
                                </div>
                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="alert('Repetir pedido #${pedido.id}')">
                                    <i class="bi bi-arrow-repeat"></i> Pedir Novamente
                                </button>
                            </div>
                        `;
                    });
                    html += '</div>';
                    pedidosContent.innerHTML = html;
                }
            }
        }

        // Função para escapar caracteres especiais HTML
        function escapeHtml(texto) {
            if (texto === undefined || texto === null) return '';
            texto = String(texto);
            return texto
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function confirmarPedido() {
            if (!selectedPayment) {
                alert('Por favor, selecione uma forma de pagamento!');
                return;
            }

            const total = calculateTotal();
            let finalTotal = total;
            let desconto = 0;

            if (selectedPayment === 'Dinheiro') {
                desconto = total * 0.1;
                finalTotal = total - desconto;
            } else if (selectedPayment === 'Pix') {
                desconto = total * 0.05;
                finalTotal = total - desconto;
            }

            // Capturar observações (se tiver campo)
            const observacoes = document.getElementById('observacaoPedido')?.value || '';

            // Preparar dados para enviar
            const pedidoData = {
                itens: cart.map(item => ({
                    nome: item.nome,
                    quantidade: item.quantity,
                    preco: item.preco * item.quantity
                })),
                subtotal: total,
                desconto: desconto,
                total: finalTotal,
                pagamento: selectedPayment,
                observacoes: observacoes
            };

            // Mostrar loading
            const btnConfirmar = document.getElementById('confirmPaymentBtn');
            if (btnConfirmar) {
                btnConfirmar.disabled = true;
                btnConfirmar.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            }

            // Enviar pedido para a API
            fetch('api_pedidos.php?action=salvar_pedido', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(pedidoData)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        let mensagem = `✅ PEDIDO ENVIADO PARA COZINHA!\n\n`;
                        mensagem += `Número do Pedido: ${result.numero_pedido}\n\n`;
                        mensagem += `Itens do pedido:\n`;
                        cart.forEach(item => {
                            mensagem += `• ${item.quantity}x ${item.nome} - R$ ${(item.preco * item.quantity).toFixed(2)}\n`;
                        });
                        mensagem += `\nSubtotal: R$ ${total.toFixed(2)}`;
                        if (desconto > 0) mensagem += `\nDesconto: -R$ ${desconto.toFixed(2)}`;
                        mensagem += `\nTotal: R$ ${finalTotal.toFixed(2)}`;
                        mensagem += `\nForma de Pagamento: ${selectedPayment}`;

                        alert(mensagem);

                        // Limpar carrinho
                        cart = [];
                        saveCart();

                        // Fechar modais
                        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                        limparBackdrop();
                        displayCart();
                    } else {
                        alert('Erro ao enviar pedido: ' + (result.error || 'Tente novamente'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor. Tente novamente.');
                })
                .finally(() => {
                    if (btnConfirmar) {
                        btnConfirmar.disabled = false;
                        btnConfirmar.innerHTML = 'Confirmar Pedido';
                    }
                });
        }

        // Eventos
        document.querySelector('.nav-icons .position-relative').addEventListener('click', function(e) {
            e.preventDefault();
            displayCart();
            new bootstrap.Modal(document.getElementById('cartModal')).show();
        });

        const confirmBtn = document.getElementById('confirmCustomizeBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const isBebida = (currentCustomizeItem && (currentCustomizeItem.id_categoria == 10 || currentCustomizeItem.id_categoria == 11));
                if (isBebida) {
                    addCustomizedBebidaToCart();
                } else {
                    addCustomizedToCart();
                }
            });
        }

        const pedidosModal = document.getElementById('pedidosModal');
        if (pedidosModal) {
            pedidosModal.addEventListener('show.bs.modal', function() {
                carregarHistoricoPedidos();
            });
        }

        updateCartCount();

        String.prototype.hashCode = function() {
            let hash = 0;
            for (let i = 0; i < this.length; i++) {
                hash = ((hash << 5) - hash) + this.charCodeAt(i);
                hash |= 0;
            }
            return hash;
        };

        window.changePieces = changePieces;
        window.openCustomizeModal = openCustomizeModal;
        window.addToCart = addToCart;
        window.removeFromCart = removeFromCart;
        window.updateQuantity = updateQuantity;
        window.clearCart = clearCart;
        window.finalizarPedido = finalizarPedido;
        window.selectPayment = selectPayment;
        window.confirmarPedido = confirmarPedido;
    </script>
</body>

</html>