<?php
// ============================================================
// COZINHA.PHP — Painel de Gerenciamento de Pedidos da Cozinha
// ============================================================
// Este arquivo é o painel interno da cozinha do restaurante.
// Nele, os funcionários (chef e cozinheiro) podem visualizar
// pedidos pendentes, alterar seu status e acompanhar o histórico.
//
// FLUXO DE STATUS DOS PEDIDOS:
//   pendente → preparando → concluido
//                                  ↘ cancelado
//
// SESSÃO:
//   Este arquivo verifica se o usuário está logado como funcionário.
//   Se não estiver, redireciona para login_cozinha.php.
// ============================================================

session_start();

// --- VERIFICAÇÃO DE AUTENTICAÇÃO ---
// Verifica se existe uma sessão de funcionário ativa.
// A variável 'funcionario_id' é definida no login_cozinha.php após autenticação bem-sucedida.
if (!isset($_SESSION['funcionario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login_cozinha.php");
    exit(); // Sempre usar exit() após header() para impedir continuação do script
}

// --- VERIFICAÇÃO DE PERMISSÃO ---
// Apenas cargos 'chef' e 'cozinheiro' podem acessar o painel da cozinha.
// Isso garante que, mesmo que alguém acesse diretamente a URL, sem o cargo correto será bloqueado.
$cargosPermitidos = ['chef', 'cozinheiro'];
if (!isset($_SESSION['funcionario_cargo']) || !in_array($_SESSION['funcionario_cargo'], $cargosPermitidos)) {
    session_destroy(); // Destrói a sessão por segurança
    header("Location: login_cozinha.php?erro=acesso_negado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Cozinha - Sushi Wabi-Sabi</title>

    <!-- Favicon — ícone que aparece na aba do navegador -->
    <link rel="shortcut icon" href="img/logo-sushi.png" type="image/x-icon">

    <!-- Bootstrap 5 — framework CSS/JS para componentes como modais, abas, grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons — biblioteca de ícones (bi bi-*) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts — importação das fontes usadas no projeto -->
    <!-- Inter: fonte principal para textos do corpo -->
    <!-- Playfair Display: fonte elegante para títulos (mesma do cardapio.php) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Bootstrap JS — necessário para funcionamento de abas, modais, dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* ============================================================
           RESET E ESTILOS BASE
           ============================================================
           Define o estilo global da página: fundo escuro, texto branco,
           fonte Inter. O fundo #0b0b0b é o preto profundo usado em todo
           o projeto (cardapio.php, home.html). */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #0b0b0b;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        /* ============================================================
           NAVBAR (Barra de Navegação)
           ============================================================
           Estilo escuro com borda vermelha inferior.
           O backdrop-filter cria um efeito de vidro fosco (glassmorphism)
           quando o usuário rola a página, consistente com cardapio.php. */
        .navbar {
            background: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e63946;
        }

        /* Playfair Display para o brand — mesma fonte do cardápio */
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-brand span {
            color: #e63946;
        }

        /* ============================================================
           BADGES DE STATUS
           ============================================================
           Cada status do pedido tem uma cor diferente para identificação
           visual rápida:
             - pendente:  amarelo (#ffc107) — aguardando atenção
             - preparando: azul (#17a2b8) — em andamento
             - concluido: verde (#28a745) — finalizado com sucesso
             - cancelado: vermelho (#dc3545) — cancelado */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            white-space: nowrap; /* Impede que o texto quebre linha */
        }

        .status-pendente {
            background-color: #ffc107;
            color: #1a1a1a;
        }

        .status-preparando {
            background-color: #17a2b8;
            color: white;
        }

        .status-concluido {
            background-color: #28a745;
            color: white;
        }

        .status-cancelado {
            background-color: #dc3545;
            color: white;
        }

        /* ============================================================
           CARDS DE PEDIDO
           ============================================================
           Cada pedido é exibido como um card com:
             - Fundo escuro (#1a1a1a)
             - Borda lateral colorida que indica o status visualmente
             - Efeito hover que levanta o card levemente
             - Cantos arredondados (12px) — padrão do projeto */
        .pedido-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden; /* Garante que o conteúdo não vaze dos cantos arredondados */
        }

        .pedido-card:hover {
            transform: translateY(-5px);
            border-color: #e63946;
            box-shadow: 0 5px 20px rgba(230, 57, 70, 0.2);
        }

        /* --- Bordas laterais por status ---
           A borda esquerda de 4px dá uma indicação visual imediata
           do status do pedido, sem precisar ler o badge. */
        .pedido-card.status-border-pendente {
            border-left: 4px solid #ffc107;
        }

        .pedido-card.status-border-preparando {
            border-left: 4px solid #17a2b8;
        }

        .pedido-card.status-border-concluido {
            border-left: 4px solid #28a745;
        }

        .pedido-card.status-border-cancelado {
            border-left: 4px solid #dc3545;
        }

        /* --- Header do card ---
           Gradiente vermelho (#e63946 → #c1121f) — consistente
           com o estilo do cardápio. Contém o número do pedido,
           horário e badge de status. */
        .pedido-header {
            background: linear-gradient(135deg, #e63946, #c1121f);
            border-radius: 12px 12px 0 0;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pedido-numero {
            font-family: 'Playfair Display', serif; /* Fonte de destaque */
            font-size: 1.2rem;
            font-weight: bold;
        }

        .pedido-hora {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        /* Corpo do card — contém lista de itens e informações */
        .pedido-body {
            padding: 15px;
        }

        /* ============================================================
           LISTA DE ITENS DO PEDIDO
           ============================================================
           Cada item é separado por uma borda sutil (#333).
           O último item não tem borda inferior (para não criar
           uma borda dupla com a seção de totais). */
        .item-lista {
            border-bottom: 1px solid #333;
            padding: 8px 0;
        }

        .item-lista:last-child {
            border-bottom: none;
        }

        .item-nome {
            font-weight: 500;
        }

        /* Quantidade em vermelho — mesma cor primária do projeto */
        .item-quantidade {
            color: #e63946;
            font-weight: bold;
        }

        /* ============================================================
           BADGE DE URGÊNCIA (tempo relativo)
           ============================================================
           Quando um pedido está pendente há mais de 30 minutos,
           este badge aparece pulsante para chamar atenção.
           Usa animação CSS @keyframes. */
        .badge-urgencia {
            background-color: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: bold;
            animation: pulse-urgencia 1.5s ease-in-out infinite;
        }

        @keyframes pulse-urgencia {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* ============================================================
           ABAS (Tabs)
           ============================================================
           Estilo das abas "Em Andamento" e "Histórico".
           A aba ativa tem uma borda inferior vermelha (#e63946). */
        .nav-tabs {
            border-bottom: 1px solid #333;
        }

        .nav-tabs .nav-link {
            color: #b0b0b0;
            background: transparent;
            border: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #e63946;
        }

        .nav-tabs .nav-link.active {
            color: #e63946;
            background: transparent;
            border-bottom: 2px solid #e63946;
        }

        /* ============================================================
           ANIMAÇÃO DE PULSO (para novos pedidos)
           ============================================================
           Quando um pedido novo aparece, ele tem uma animação
           de pulso para chamar a atenção do cozinheiro. */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .novo-pedido {
            animation: pulse 0.5s ease-in-out;
        }

        /* ============================================================
           SCROLLBAR PERSONALIZADA
           ============================================================
           A barra de rolagem segue o tema escuro do projeto,
           com thumb vermelho (#e63946). */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #e63946;
            border-radius: 4px;
        }

        /* ============================================================
           BADGE DE CONTAGEM ( nas abas)
           ============================================================
           Mostra o número de pedidos em cada aba.
           Círculo vermelho pequeno e discreto. */
        .badge-count {
            background-color: #e63946;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 5px;
        }

        /* ============================================================
           MODAL (janela pop-up de detalhes)
           ============================================================
           Fundo escuro consistente com o resto da interface.
           Bordas sutis em #333. */
        .modal-content {
            background-color: #1a1a1a;
            border: 1px solid #333;
            color: white;
        }

        .modal-header {
            border-bottom-color: #333;
        }

        .modal-footer {
            border-top-color: #333;
        }

        /* ============================================================
           BOTÕES DE AÇÃO (Iniciar / Concluir)
           ============================================================
           Botões maiores e mais visíveis para ambiente de cozinha.
           Usuários podem estar com luvas ou com as mãos ocupadas,
           então os botões precisam ser grandes e fáceis de clicar. */
        .btn-acao {
            padding: 10px 16px;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%; /* Largura total para facilitar toque */
        }

        .btn-acao:hover {
            transform: scale(1.02); /* Leve aumento ao passar o mouse */
        }

        .btn-acao:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ============================================================
           EMPTY STATE (estados vazios)
           ============================================================
           Quando não há pedidos para mostrar, exibe uma mensagem
           amigável com ícone grande e texto descritivo. */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #444;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #666;
            font-size: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .empty-state .text-muted {
            color: #555 !important;
            font-size: 0.85rem;
            margin-top: 8px;
        }

        /* ============================================================
           RESPONSIVIDADE
           ============================================================
           Em telas muito pequenas (< 576px), os cards ocupam
           largura total para melhor legibilidade. */
        @media (max-width: 576px) {
            .pedido-header {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .pedido-numero {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- ============================================================
         NAVBAR — Barra de Navegação Superior
         ============================================================
         Contém:
           - Logo + nome do restaurante
           - Nome do funcionário logado + badge do cargo
           - Botão "Novo Funcionário" (apenas para chef)
           - Botão "Sair" (logout) -->
    <nav class="navbar navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logo-sushi.png" alt="Logo" width="35" height="35">
                WABI-SABI <span> | Cozinha </span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Exibe o nome do funcionário logado -->
                <span class="text-light">
                    <i class="bi bi-person-badge"></i>
                    <?= $_SESSION['funcionario_nome'] ?>

                    <!-- Badge do cargo: Chef (amarelo) ou Cozinheiro (azul) -->
                    <?php if ($_SESSION['funcionario_cargo'] == 'chef'): ?>
                        <span class="badge bg-warning text-dark ms-1">
                            <i class="bi bi-star-fill"></i> Chef
                        </span>
                    <?php else: ?>
                        <span class="badge bg-info ms-1">
                            <i class="bi bi-egg-fried"></i> Cozinheiro
                        </span>
                    <?php endif; ?>
                </span>

                <!-- Botão para cadastrar novo funcionário (apenas chef) -->
                <?php if ($_SESSION['funcionario_cargo'] == 'chef'): ?>
                    <a href="cadastrar_funcionario.php" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-person-plus"></i> Novo Funcionário
                    </a>
                <?php endif; ?>

                <!-- Botão de logout — destrói a sessão e redireciona -->
                <a href="login_cozinha.php?logout=1" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- ============================================================
         CONTEÚDO PRINCIPAL
         ============================================================ -->
    <div class="container mt-4">

        <!-- ============================================================
             ABAS DE NAVEGAÇÃO
             ============================================================
             Duas abas:
               1. "Em Andamento" — pedidos com status pendente ou preparando
               2. "Histórico" — pedidos concluídos ou cancelados

             O JavaScript controla qual aba está ativa e carrega
             os dados correspondentes. -->
        <ul class="nav nav-tabs mb-4" id="pedidosTab" role="tablist">
            <!-- Aba 1: Pedidos em andamento -->
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ativos-tab" data-bs-toggle="tab" data-bs-target="#ativos"
                    type="button" role="tab">
                    <i class="bi bi-clock-history"></i> Em Andamento
                    <!-- Badge com contagem de pedidos ativos (atualizado via JS) -->
                    <span id="ativosCount" class="badge-count">0</span>
                </button>
            </li>
            <!-- Aba 2: Histórico de pedidos -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico"
                    type="button" role="tab">
                    <i class="bi bi-archive"></i> Histórico
                    <!-- Badge com contagem de pedidos no histórico -->
                    <span id="historicoCount" class="badge-count">0</span>
                </button>
            </li>
        </ul>

        <!-- Conteúdo das abas -->
        <div class="tab-content" id="pedidosTabContent">

            <!-- === ABA: EM ANDAMENTO === -->
            <!-- Container onde os cards de pedidos ativos serão inseridos via JavaScript -->
            <div class="tab-pane fade show active" id="ativos" role="tabpanel">
                <div class="row g-4" id="pedidosAtivosContainer">
                    <!-- Loading spinner — exibido enquanto os dados são carregados -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === ABA: HISTÓRICO === -->
            <!-- Container onde os cards de pedidos finalizados serão inseridos via JavaScript -->
            <div class="tab-pane fade" id="historico" role="tabpanel">
                <div class="row g-4" id="historicoContainer">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         MODAL DE DETALHES DO PEDIDO
         ============================================================
         Janela pop-up que mostra informações completas de um pedido
         quando o usuário clica no botão "Detalhes".
         O conteúdo é carregado dinamicamente via JavaScript. -->
    <div class="modal fade" id="detalhesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt"></i> Detalhes do Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalhesContent">
                    <!-- Conteúdo preenchido dinamicamente pelo JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         JAVASCRIPT — Lógica do Painel da Cozinha
         ============================================================
         Este bloco contém toda a lógica de:
           - Carregar pedidos da API
           - Renderizar cards na tela
           - Atualizar status dos pedidos
           - Tocar sons de notificação
           - Calcular tempo relativo
           - Auto-refresh a cada 10 segundos -->
    <script>
        // ============================================================
        // VARIÁVEIS GLOBAIS
        // ============================================================

        // AudioContext — necessário para tocar sons de notificação.
        // É criado sob demanda (lazy) porque navegadores exigem
        // interação do usuário antes de permitir áudio.
        let audioContext = null;

        // Armazena o ID do último pedido carregado.
        // Usado para detectar quando um pedido antigo some da lista
        // (foi concluído/cancelado) e tocar o som de notificação.
        let ultimoPedidoId = null;

        // ============================================================
        // FUNÇÃO: playNotificationSound()
        // ============================================================
        // Toca um som curto de notificação quando um novo pedido chega.
        // Usa a Web Audio API para gerar um tom sinusoidal de 880Hz
        // que dura 1 segundo e diminui gradualmente (fade out).
        //
        // POR QUE USA AudioContext?
        //   Porque não queremos depender de um arquivo de áudio externo.
        //   O oscillator gera um tom puro programaticamente.
        function playNotificationSound() {
            try {
                // Cria o AudioContext sob demanda (requisito dos navegadores)
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }

                // Cria um oscilador (gerador de onda sonora)
                const oscillator = audioContext.createOscillator();
                // Cria um nó de ganho para controlar o volume
                const gainNode = audioContext.createGain();

                // Conecta: oscilador → ganho → saída de áudio (altifalantes)
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                // Configura o tom:
                oscillator.frequency.value = 880; // 880Hz = nota Lá4
                gainNode.gain.value = 0.3;        // Volume baixo (30%)

                // Inicia o som e agenda o fim após 1 segundo
                oscillator.start();
                gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 1);
                oscillator.stop(audioContext.currentTime + 1);
            } catch (e) {
                // Se o navegador não suportar áudio, apenas ignora silenciosamente
                console.log('Som não suportado:', e);
            }
        }

        // ============================================================
        // FUNÇÃO: tempoRelativo(dataPedido)
        // ============================================================
        // Calcula e retorna a diferença de tempo entre agora e o
        // momento em que o pedido foi feito, em formato legível.
        //
        // EXEMPLOS DE RETORNO:
        //   - "há 5 min"     (menos de 1 hora)
        //   - "há 2h 15min"  (mais de 1 hora)
        //   - "há 1d 3h"     (mais de 1 dia)
        //
        // PARÂMETROS:
        //   dataPedido — string de data/hora vinda do banco de dados
        //                (formato MySQL: "2026-05-27 03:19:04")
        function tempoRelativo(dataPedido) {
            // Cria um objeto Date a partir da string do banco
            // Nota: new Date("2026-05-27 03:19:04") funciona em todos os navegadores modernos
            const agora = new Date();
            const pedido = new Date(dataPedido);

            // Calcula a diferença em milissegundos e converte para minutos
            const diffMs = agora - pedido;
            const diffMin = Math.floor(diffMs / 60000); // 60000ms = 1 minuto

            // Retorna string formatada dependendo da magnitude
            if (diffMin < 1) {
                return 'agora';
            } else if (diffMin < 60) {
                return `há ${diffMin} min`;
            } else {
                const horas = Math.floor(diffMin / 60);
                const minutos = diffMin % 60;
                if (horas < 24) {
                    return `há ${horas}h ${minutos}min`;
                } else {
                    const dias = Math.floor(horas / 24);
                    const horasRest = horas % 24;
                    return `há ${dias}d ${horasRest}h`;
                }
            }
        }

        // ============================================================
        // FUNÇÃO: getStatusBadge(status)
        // ============================================================
        // Retorna o HTML de um badge colorido para o status do pedido.
        //
        // MAPEAMENTO DE STATUS:
        //   pendente  → badge amarelo  "⏳ Pendente"
        //   preparando → badge azul    "🍳 Preparando"
        //   concluido → badge verde    "✅ Concluído"
        //   cancelado → badge vermelho "❌ Cancelado"
        //
        // O badge usa a classe CSS correspondente (status-pendente, etc.)
        // que define a cor de fundo e texto.
        function getStatusBadge(status) {
            const statusMap = {
                'pendente': 'status-pendente',
                'preparando': 'status-preparando',
                'concluido': 'status-concluido',
                'cancelado': 'status-cancelado'
            };
            const textMap = {
                'pendente': '⏳ Pendente',
                'preparando': '🍳 Preparando',
                'concluido': '✅ Concluído',
                'cancelado': '❌ Cancelado'
            };
            return `<span class="status-badge ${statusMap[status] || 'status-pendente'}">${textMap[status] || status}</span>`;
        }

        // ============================================================
        // FUNÇÃO: renderPedidoCard(pedido, isHistorico)
        // ============================================================
        // Gera o HTML de um card de pedido completo.
        //
        // PARÂMETROS:
        //   pedido      — objeto com os dados do pedido (vindo da API)
        //   isHistorico — booleano. Se true, NÃO exibe botões de ação
        //                 (porque pedidos do histórico já foram finalizados)
        //
        // ESTRUTURA DO CARD:
        //   ┌─────────────────────────────────┐
        //   │ [Header] Número + Hora + Status │
        //   ├─────────────────────────────────┤
        //   │ Cliente: Fulano                  │
        //   │ Itens:                           │
        //   │   sushi x2                       │
        //   │   temaki x1                      │
        //   ├─────────────────────────────────┤
        //   │ Total: R$ 54,00                  │
        //   │ [Detalhes] [Iniciar/Concluir]    │
        //   └─────────────────────────────────┘
        function renderPedidoCard(pedido, isHistorico = false) {
            const itens = pedido.itens || [];
            const hora = formatarData(pedido.data_pedido);
            const tempo = tempoRelativo(pedido.data_pedido);

            // Badge de urgência: aparece quando um pedido pendente
            // está aguardando há mais de 30 minutos
            let urgenciaHtml = '';
            if (pedido.status === 'pendente') {
                const diffMin = Math.floor((new Date() - new Date(pedido.data_pedido)) / 60000);
                if (diffMin >= 30) {
                    urgenciaHtml = `<span class="badge-urgencia ms-2"><i class="bi bi-exclamation-triangle"></i> ${diffMin}min aguardando</span>`;
                }
            }

            // Início do HTML do card
            // A classe status-border-{status} adiciona a borda lateral colorida
            let html = `
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="pedido-card status-border-${pedido.status}" data-pedido-id="${pedido.id_pedido}">
                    <!-- HEADER DO CARD -->
                    <div class="pedido-header">
                        <div>
                            <div class="pedido-numero">${pedido.numero_pedido}</div>
                            <div class="pedido-hora">
                                <i class="bi bi-clock"></i> ${hora} · ${tempo}
                                ${urgenciaHtml}
                            </div>
                        </div>
                        ${getStatusBadge(pedido.status)}
                    </div>
                    <!-- CORPO DO CARD -->
                    <div class="pedido-body">
                        <!-- Informações do cliente -->
                        <div class="mb-2">
                            <small class="text-secondary"><i class="bi bi-person"></i> Cliente:</small>
                            <div>${pedido.cliente_nome || 'Cliente não identificado'}</div>
                        </div>
                        <!-- Lista de itens do pedido -->
                        <div class="mb-2">
                            <small class="text-secondary"><i class="bi bi-cup-straw"></i> Itens:</small>
            `;

            // Renderiza cada item do pedido
            itens.forEach(item => {
                html += `
                <div class="item-lista d-flex justify-content-between align-items-center">
                    <span class="item-nome">${item.nome}</span>
                    <span class="item-quantidade">${item.quantidade}x</span>
                </div>
            `;
            });

            // Seção de totais e informações de pagamento
            html += `
                        </div>
                        <div class="mt-3 pt-2 border-top border-secondary">
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-danger">R$ ${parseFloat(pedido.total).toFixed(2)}</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small>Subtotal:</small>
                                <small>R$ ${parseFloat(pedido.subtotal).toFixed(2)}</small>
                            </div>
                            ${pedido.desconto > 0 ? `<div class="d-flex justify-content-between mt-1"><small>Desconto:</small><small>- R$ ${parseFloat(pedido.desconto).toFixed(2)}</small></div>` : ''}
                            <div class="d-flex justify-content-between mt-1">
                                <small>Pagamento:</small>
                                <small>${pedido.forma_pagamento || 'Não informado'}</small>
                            </div>
                        </div>
            `;

            // Botões de ação
            // isHistorico = true → apenas botão "Detalhes" (sem ações de status)
            // isHistorico = false → botão "Detalhes" + botão de status (Iniciar/Concluir)
            html += `
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-sm btn-outline-info flex-grow-1" onclick="verDetalhes(${pedido.id_pedido})">
                                <i class="bi bi-eye"></i> Detalhes
                            </button>
            `;

            if (!isHistorico) {
                // Botão "Iniciar" — aparece apenas para pedidos PENDENTES
                // Muda o status de pendente → preparando
                if (pedido.status === 'pendente') {
                    html += `<button class="btn btn-acao btn-warning flex-grow-1" onclick="atualizarStatus(${pedido.id_pedido}, 'preparando', this)">
                            <i class="bi bi-play-fill"></i> Iniciar Preparo
                        </button>`;
                // Botão "Concluir" — aparece apenas para pedidos EM PREPARO
                // Muda o status de preparando → concluido
                } else if (pedido.status === 'preparando') {
                    html += `<button class="btn btn-acao btn-success flex-grow-1" onclick="atualizarStatus(${pedido.id_pedido}, 'concluido', this)">
                            <i class="bi bi-check-lg"></i> Concluir Pedido
                        </button>`;
                }
            }

            // Fecha as divs do card
            html += `
                        </div>
                    </div>
                </div>
            </div>
        `;

            return html;
        }

        // ============================================================
        // FUNÇÃO: formatarData(data)
        // ============================================================
        // Converte uma string de data do banco MySQL para formato
        // brasileiro legível.
        //
        // EXEMPLO:
        //   Entrada: "2026-05-27 03:19:04"
        //   Saída:   "27/05/2026 03:19"
        function formatarData(data) {
            const d = new Date(data);
            return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }

        // ============================================================
        // FUNÇÃO: carregarPedidosAtivos()
        // ============================================================
        // Busca na API todos os pedidos com status "pendente" ou
        // "preparando" e os renderiza na aba "Em Andamento".
        //
        // FLUXO:
        //   1. Faz requisição GET para api_pedidos.php?action=pedidos_pendentes
        //   2. Recebe JSON com array de pedidos
        //   3. Verifica se há pedidos novos (para tocar som)
        //   4. Renderiza os cards no container
        //   5. Atualiza o badge de contagem
        //
        // A API retorna pedidos ordenados por data_pedido ASC
        // (mais antigos primeiro), para que os pedidos urgentes
        // apareçam no topo.
        async function carregarPedidosAtivos() {
            try {
                const response = await fetch('api_pedidos.php?action=pedidos_pendentes');
                const pedidos = await response.json();

                const container = document.getElementById('pedidosAtivosContainer');
                const ativosCount = document.getElementById('ativosCount');

                // Estado vazio — nenhum pedido pendente
                if (pedidos.length === 0) {
                    container.innerHTML = `
                    <div class="col-12 empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Nenhum pedido pendente no momento</p>
                        <p class="text-muted">Os pedidos aparecerão aqui quando os clientes fizerem pedidos</p>
                    </div>
                `;
                    ativosCount.textContent = '0';
                    return;
                }

                // Verifica se algum pedido antigo desapareceu da lista
                // (foi concluído ou cancelado por outro funcionário)
                // Se sim, toca o som de notificação
                const novosIds = pedidos.map(p => p.id_pedido);
                if (ultimoPedidoId && !novosIds.includes(ultimoPedidoId)) {
                    playNotificationSound();
                }
                ultimoPedidoId = novosIds[0]; // O primeiro é o mais antigo

                // Renderiza todos os cards de pedidos ativos
                let html = '';
                pedidos.forEach(pedido => {
                    html += renderPedidoCard(pedido, false);
                });
                container.innerHTML = html;

                // Atualiza o badge de contagem na aba
                ativosCount.textContent = pedidos.length;

            } catch (error) {
                console.error('Erro ao carregar pedidos:', error);
            }
        }

        // ============================================================
        // FUNÇÃO: carregarHistorico()
        // ============================================================
        // Busca na API todos os pedidos com status "concluido" ou
        // "cancelado" e os renderiza na aba "Histórico".
        //
        // A API retorna no máximo 50 pedidos, ordenados por data
        // decrescente (mais recentes primeiro).
        async function carregarHistorico() {
            try {
                const response = await fetch('api_pedidos.php?action=historico_pedidos');
                const pedidos = await response.json();

                const container = document.getElementById('historicoContainer');
                const historicoCount = document.getElementById('historicoCount');

                // Estado vazio — nenhum pedido no histórico
                if (pedidos.length === 0) {
                    container.innerHTML = `
                    <div class="col-12 empty-state">
                        <i class="bi bi-archive"></i>
                        <p>Nenhum pedido no histórico</p>
                        <p class="text-muted">Pedidos concluídos ou cancelados aparecerão aqui</p>
                    </div>
                `;
                    historicoCount.textContent = '0';
                    return;
                }

                // Renderiza os cards do histórico
                // isHistorico = true → sem botões de ação
                let html = '';
                pedidos.forEach(pedido => {
                    html += renderPedidoCard(pedido, true);
                });
                container.innerHTML = html;

                // Atualiza o badge de contagem na aba
                historicoCount.textContent = pedidos.length;

            } catch (error) {
                console.error('Erro ao carregar histórico:', error);
            }
        }

        // ============================================================
        // FUNÇÃO: verDetalhes(idPedido)
        // ============================================================
        // Abre um modal com informações detalhadas de um pedido.
        //
        // FLUXO:
        //   1. Busca os dados completos do pedido via API
        //   2. Monta uma tabela HTML com todos os itens
        //   3. Exibe informações de cliente, telefone, endereço
        //   4. Mostra totais e forma de pagamento
        //   5. Exibe observações do cliente (se houver)
        //
        // A API buscar_pedido retorna:
        //   - Dados do pedido (itens, total, etc.)
        //   - Dados do cliente (nome, telefone, endereço)
        async function verDetalhes(idPedido) {
            try {
                const response = await fetch(`api_pedidos.php?action=buscar_pedido&id=${idPedido}`);
                const pedido = await response.json();

                if (pedido.error) {
                    alert(pedido.error);
                    return;
                }

                const itens = pedido.itens || [];

                // Monta a tabela de itens
                // Cada linha mostra: nome, quantidade, preço unitário e subtotal
                let itensHtml = '<table class="table table-dark table-sm">';
                itensHtml += '<thead><tr><th>Item</th><th>Quantidade</th><th>Preço Unit.</th><th>Subtotal</th></tr></thead><tbody>';

                itens.forEach(item => {
                    // Calcula o preço unitário dividindo o total pela quantidade
                    const precoUnit = item.preco / item.quantidade;
                    itensHtml += `
                    <tr>
                        <td>${item.nome}</td>
                        <td>${item.quantidade}x</td>
                        <td>R$ ${precoUnit.toFixed(2)}</td>
                        <td>R$ ${item.preco.toFixed(2)}</td>
                    </tr>
                `;
                });

                itensHtml += '</tbody></table>';

                // Monta o HTML completo do modal
                const html = `
                <div class="mb-3">
                    <h6><i class="bi bi-receipt"></i> Pedido: ${pedido.numero_pedido}</h6>
                    <p><strong>Cliente:</strong> ${pedido.cliente_nome || 'Não identificado'}</p>
                    <p><strong>Telefone:</strong> ${pedido.telefone || 'Não informado'}</p>
                    <p><strong>Endereço:</strong> ${pedido.endereco || 'Não informado'}</p>
                    <p><strong>Data:</strong> ${formatarData(pedido.data_pedido)}</p>
                    <p><strong>Status:</strong> ${getStatusBadge(pedido.status)}</p>
                </div>
                <h6>Itens do pedido:</h6>
                ${itensHtml}
                <div class="mt-3 pt-2 border-top border-secondary">
                    <div class="d-flex justify-content-between">
                        <strong>Subtotal:</strong>
                        <span>R$ ${parseFloat(pedido.subtotal).toFixed(2)}</span>
                    </div>
                    ${pedido.desconto > 0 ? `
                        <div class="d-flex justify-content-between">
                            <strong>Desconto:</strong>
                            <span>- R$ ${parseFloat(pedido.desconto).toFixed(2)}</span>
                        </div>
                    ` : ''}
                    <div class="d-flex justify-content-between mt-2">
                        <strong class="text-danger">Total:</strong>
                        <strong class="text-danger">R$ ${parseFloat(pedido.total).toFixed(2)}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small>Forma de pagamento:</small>
                        <small>${pedido.forma_pagamento || 'Não informado'}</small>
                    </div>
                </div>
                ${pedido.observacoes ? `
                    <div class="mt-3 p-2 bg-dark rounded">
                        <small><i class="bi bi-chat-text"></i> Observações do cliente:</small>
                        <p class="mb-0">${pedido.observacoes}</p>
                    </div>
                ` : ''}
            `;

                // Insere o HTML no modal e o exibe
                document.getElementById('detalhesContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('detalhesModal')).show();

            } catch (error) {
                console.error('Erro ao carregar detalhes:', error);
                alert('Erro ao carregar detalhes do pedido');
            }
        }

        // ============================================================
        // FUNÇÃO: atualizarStatus(idPedido, novoStatus, btnElement)
        // ============================================================
        // Atualiza o status de um pedido via API.
        //
        // PARÂMETROS:
        //   idPedido  — ID do pedido no banco de dados
        //   novoStatus — novo status ('preparando' ou 'concluido')
        //   btnElement — referência ao botão clicado (para feedback visual)
        //
        // FLUXO:
        //   1. Desabilita o botão e mostra spinner (feedback visual)
        //   2. Envia POST para a API com o novo status
        //   3. Se sucesso: recarrega a lista de pedidos ativos
        //   4. Se for "concluido": também recarrega o histórico
        //   5. Toca som de notificação
        //   6. Se erro: reabilita o botão e mostra alerta
        //
        // FEEDBACK VISUAL:
        //   O botão mostra "Processando..." com spinner enquanto
        //   a requisição está em andamento. Isso é importante em
        //   ambiente de cozinha onde o usuário pode clicar múltiplas vezes.
        async function atualizarStatus(idPedido, novoStatus, btnElement) {
            // Salva o texto original do botão para restaurar em caso de erro
            const textoOriginal = btnElement.innerHTML;

            // Feedback visual: desabilita botão e mostra spinner
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processando...';

            try {
                const response = await fetch('api_pedidos.php?action=atualizar_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_pedido: idPedido, status: novoStatus })
                });
                const result = await response.json();

                if (result.success) {
                    // Recarrega a lista de pedidos ativos para refletir a mudança
                    carregarPedidosAtivos();

                    // Se o pedido foi concluído, também atualiza o histórico
                    if (novoStatus === 'concluido') {
                        carregarHistorico();
                    }

                    // Toca som de notificação para alertar a equipe
                    playNotificationSound();
                } else {
                    // Em caso de erro da API, reabilita o botão
                    btnElement.disabled = false;
                    btnElement.innerHTML = textoOriginal;
                    alert('Erro ao atualizar status');
                }
            } catch (error) {
                // Em caso de erro de rede, reabilita o botão
                console.error('Erro:', error);
                btnElement.disabled = false;
                btnElement.innerHTML = textoOriginal;
                alert('Erro ao atualizar status');
            }
        }

        // ============================================================
        // AUTO-REFRESH — Atualização Automática
        // ============================================================
        // A cada 10 segundos, recarrega os pedidos ativos.
        // Isso garante que o painel sempre mostre os pedidos mais
        // recentes, mesmo sem que o usuário recarregue a página.
        //
        // SÓ recarrega a aba ativa para evitar requisições desnecessárias.
        // Se o histórico estiver ativo, não recarrega os ativos.
        setInterval(() => {
            const activeTab = document.querySelector('#pedidosTab .nav-link.active');
            if (activeTab && activeTab.id === 'ativos-tab') {
                carregarPedidosAtivos();
            }
        }, 10000); // 10000ms = 10 segundos

        // ============================================================
        // INICIALIZAÇÃO
        // ============================================================
        // Quando a página termina de carregar (DOMContentLoaded),
        // carrega os pedidos ativos e o histórico pela primeira vez.
        document.addEventListener('DOMContentLoaded', () => {
            carregarPedidosAtivos();
            carregarHistorico();
        });
    </script>
</body>

</html>
