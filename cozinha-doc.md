# Documentação — Módulo de Cozinha (`cozinha.php`)

> Guia de estudo e explicação do código-fonte do painel da cozinha do restaurante Sushi Wabi-Sabi.

---

## Sumário

1. [Visão Geral](#1-visão-geral)
2. [Fluxo de Autenticação](#2-fluxo-de-autenticação)
3. [Estrutura HTML](#3-estrutura-html)
4. [Sistema de Design (CSS)](#4-sistema-de-design-css)
5. [Lógica JavaScript](#5-lógica-javascript)
6. [Comunicação com a API](#6-comunicação-com-a-api)
7. [Fluxo de Status dos Pedidos](#7-fluxo-de-status-dos-pedidos)
8. [Melhorias de Design Aplicadas](#8-melhorias-de-design-aplicadas)
9. [Dicionário de Classes CSS](#9-dicionário-de-classes-css)
10. [Referências Rápidas](#10-referências-rápidas)

---

## 1. Visão Geral

O `cozinha.php` é o painel interno onde funcionários da cozinha (chef e cozinheiros) gerenciam os pedidos dos clientes. Ele se comunica com o banco de dados através de `api_pedidos.php`.

### Fluxo de dados

```
┌─────────────┐     fetch()      ┌──────────────┐     PDO      ┌─────────────┐
│ cozinha.php │ ──────────────►  │ api_pedidos  │ ──────────►  │  MariaDB    │
│  (front-end │ ◄──────────────  │    .php      │ ◄──────────  │ (banco de   │
│  JavaScript)│     JSON         │  (back-end)  │    dados     │  dados)     │
└─────────────┘                  └──────────────┘              └─────────────┘
```

### Arquivos envolvidos

| Arquivo | Função |
|---|---|
| `cozinha.php` | Interface do usuário (HTML + CSS + JavaScript) |
| `api_pedidos.php` | API REST que acessa o banco de dados |
| `conexao.php` | Configuração de conexão PDO com o MySQL |
| `login_cozinha.php` | Autenticação de funcionários |
| `cadastrar_funcionario.php` | Cadastro de novos funcionários (apenas chef) |

---

## 2. Fluxo de Autenticação

### Verificação no início do arquivo (PHP)

```php
session_start();  // Inicia ou retoma a sessão PHP

// 1. Verifica se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: login_cozinha.php");  // Redireciona para login
    exit();  // SEMPRE usar exit() após header()!
}

// 2. Verifica se o cargo é permitido
$cargosPermitidos = ['chef', 'cozinheiro'];
if (!in_array($_SESSION['funcionario_cargo'], $cargosPermitidos)) {
    session_destroy();  // Destrói sessão por segurança
    header("Location: login_cozinha.php?erro=acesso_negado");
    exit();
}
```

### Sessões do funcionário (definidas em login_cozinha.php)

| Chave da Sessão | Tipo | Descrição |
|---|---|---|
| `funcionario_id` | int | ID do funcionário no banco |
| `funcionario_nome` | string | Nome completo |
| `funcionario_cargo` | string | 'chef' ou 'cozinheiro' |
| `funcionario_email` | string | Email do funcionário |

### Diferença entre Chef e Cozinheiro

| Funcionalidade | Chef | Cozinheiro |
|---|---|---|
| Visualizar pedidos | ✅ | ✅ |
| Alterar status | ✅ | ✅ |
| Cadastrar funcionários | ✅ | ❌ |
| Ver badge "Chef" | ✅ | ❌ |

---

## 3. Estrutura HTML

### Hierarquia dos elementos

```
<body>
├── <nav>                    ← Barra de navegação (logo, nome, botões)
├── <div.container>          ← Conteúdo principal
│   ├── <ul.nav-tabs>       ← Abas: "Em Andamento" e "Histórico"
│   └── <div.tab-content>    ← Conteúdo das abas
│       ├── <div#ativos>     ← Container dos pedidos ativos
│       └── <div#historico>  ← Container do histórico
├── <div#detalhesModal>      ← Modal de detalhes do pedido
└── <script>                 ← Todo o JavaScript
```

### Navbar (Barra de Navegação)

O navbar contém:
- **Logo + nome** do restaurante
- **Nome do funcionário** + badge do cargo (Chef/Cozinheiro)
- **Botão "Novo Funcionário"** — visível apenas para chefs
- **Botão "Sair"** — redireciona para `login_cozinha.php?logout=1`

```php
// PHP condicional — só mostra o botão se o cargo for 'chef'
<?php if ($_SESSION['funcionario_cargo'] == 'chef'): ?>
    <a href="cadastrar_funcionario.php" class="btn btn-outline-warning btn-sm">
        <i class="bi bi-person-plus"></i> Novo Funcionário
    </a>
<?php endif; ?>
```

### Abas (Tabs do Bootstrap)

As abas usam o componente **Nav Tabs** do Bootstrap 5:

```html
<!-- Cada aba é um <button> com data-bs-toggle="tab" -->
<button class="nav-link active" data-bs-target="#ativos" data-bs-toggle="tab">
    <i class="bi bi-clock-history"></i> Em Andamento
    <span id="ativosCount" class="badge-count">0</span>
</button>
```

- `data-bs-target="#ativos"` — define qual aba mostra ao clicar
- `data-bs-toggle="tab"` — ativa o comportamento de aba do Bootstrap
- `class="active"` — a primeira aba começa ativa

### Cards de Pedido

Cada pedido é renderizado como um card HTML dinâmico:

```html
<div class="col-sm-12 col-md-6 col-lg-4">  <!-- Grid responsivo -->
    <div class="pedido-card status-border-pendente">  <!-- Borda lateral colorida -->
        <div class="pedido-header">  <!-- Cabeçalho vermelho -->
            <!-- Número do pedido + Hora + Badge de status -->
        </div>
        <div class="pedido-body">  <!-- Corpo do card -->
            <!-- Cliente, Itens, Total, Botões de ação -->
        </div>
    </div>
</div>
```

---

## 4. Sistema de Design (CSS)

### Paleta de Cores do Projeto

| Cor | Código | Uso |
|---|---|---|
| Preto profundo | `#0b0b0b` | Fundo do body |
| Cinza escuro | `#1a1a1a` | Fundo dos cards e navbar |
| Cinza médio | `#333` | Bordas e separadores |
| Cinza claro | `#696969` | Bordas secundárias |
| Vermelho | `#e63946` | Cor primária (botões, destaques, header) |
| Vermelho escuro | `#c1121f` | Gradiente do header |
| Amarelo | `#ffc107` | Status "pendente" |
| Azul | `#17a2b8` | Status "preparando" |
| Verde | `#28a745` | Status "concluido" |
| Vermelho (erro) | `#dc3545` | Status "cancelado", urgência |
| Dourado | `#d4af37` | Acento (usado no cardápio) |

### Tipografia

| Fonte | Uso | Arquivo |
|---|---|---|
| Playfair Display | Títulos, navbar-brand | Google Fonts |
| Inter | Texto do corpo, botões | Google Fonts |

### Bordas Laterais por Status

```css
/* Cada status tem uma borda esquerda de 4px com sua cor */
.pedido-card.status-border-pendente   { border-left: 4px solid #ffc107; }
.pedido-card.status-border-preparando { border-left: 4px solid #17a2b8; }
.pedido-card.status-border-concluido  { border-left: 4px solid #28a745; }
.pedido-card.status-border-cancelado  { border-left: 4px solid #dc3545; }
```

**Por que bordas laterais?** Em ambiente de cozinha, o funcionário precisa identificar rapidamente o status de cada pedido. A cor lateral dá uma indicação visual imediata sem precisar ler o badge.

### Badge de Urgência

```css
/* Badge pulsante para pedidos pendentes há mais de 30 minutos */
.badge-urgencia {
    background-color: #dc3545;
    animation: pulse-urgencia 1.5s ease-in-out infinite;
}

@keyframes pulse-urgencia {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
```

A animação de pulso chama a atenção para pedidos que estão aguardando há muito tempo.

---

## 5. Lógica JavaScript

### Variáveis Globais

```javascript
let audioContext = null;      // Contexto de áudio (Web Audio API)
let ultimoPedidoId = null;    // ID do último pedido carregado
```

### Funções Principais

#### `playNotificationSound()`

Toca um som curto de 880Hz quando um novo pedido chega.

**Como funciona:**
1. Cria um `AudioContext` (sob demanda, por restrição dos navegadores)
2. Cria um oscilador (gerador de onda sonora)
3. Conecta: oscilador → ganho → altifalantes
4. Toca por 1 segundo e faz fade out

**Por que não usa um arquivo de áudio?**
Porque não queremos depender de arquivos externos. O oscilador gera um tom puro programaticamente.

#### `tempoRelativo(dataPedido)`

Calcula o tempo decorrido desde o pedido.

```
Entrada: "2026-05-27 03:19:04"
Saída:   "há 15 min" (se forem 15 minutos desde então)
```

**Lógica:**
- Menos de 1 minuto → "agora"
- Menos de 1 hora → "há X min"
- Menos de 1 dia → "há Xh Ymin"
- Mais de 1 dia → "há Xd Yh"

#### `getStatusBadge(status)`

Retorna HTML de um badge colorido para o status.

| Status | Badge | Cor |
|---|---|---|
| `pendente` | ⏳ Pendente | Amarelo |
| `preparando` | 🍳 Preparando | Azul |
| `concluido` | ✅ Concluído | Verde |
| `cancelado` | ❌ Cancelado | Vermelho |

#### `renderPedidoCard(pedido, isHistorico)`

Gera o HTML completo de um card de pedido.

**Parâmetros:**
- `pedido` — objeto com os dados do pedido
- `isHistorico` — se `true`, não mostra botões de ação

**Estrutura do card gerado:**
```
┌──────────────────────────────────────┐
│ PED-6A1656F963724     ⏳ Pendente    │  ← Header (gradiente vermelho)
│ 27/05/2026 02:29 · há 45min ⚠️ 45min │  ← Hora + urgência
├──────────────────────────────────────┤
│ Cliente: Nivea                       │
│ Itens:                               │
│   Monte o seu - 30 peças      1x    │
│   Yuzu Spritz drink            1x    │
├──────────────────────────────────────┤
│ Total: R$ 82,50                      │
│ Subtotal: R$ 82,50                   │
│ Pagamento: Dinheiro                  │
│                                      │
│ [ Detalhes ] [ Iniciar Preparo ]     │  ← Botões de ação
└──────────────────────────────────────┘
```

#### `carregarPedidosAtivos()`

Busca pedidos pendentes + preparando via API.

**Fluxo:**
1. `fetch('api_pedidos.php?action=pedidos_pendentes')`
2. Converte resposta para JSON
3. Se vazio → mostra empty state
4. Se não vazio → renderiza cards + atualiza badge
5. Verifica se algum pedido sumiu (para tocar som)

#### `carregarHistorico()`

Busca pedidos concluídos + cancelados via API.

#### `verDetalhes(idPedido)`

Abre modal com informações detalhadas do pedido.

**Dados exibidos:**
- Número do pedido
- Nome, telefone e endereço do cliente
- Data e hora
- Status atual
- Tabela de itens (nome, qtd, preço unitário, subtotal)
- Totais (subtotal, desconto, total)
- Forma de pagamento
- Observações do cliente

#### `atualizarStatus(idPedido, novoStatus, btnElement)`

Altera o status de um pedido.

**Feedback visual:**
1. Botão desabilitado + texto "Processando..." + spinner
2. Requisição POST para a API
3. Se sucesso → recarrega listas
4. Se erro → restaura botão + alerta

---

## 6. Comunicação com a API

### Endpoints utilizados

| Requisição | URL | Método |
|---|---|---|
| Pedidos ativos | `api_pedidos.php?action=pedidos_pendentes` | GET |
| Histórico | `api_pedidos.php?action=historico_pedidos` | GET |
| Detalhes | `api_pedidos.php?action=buscar_pedido&id=X` | GET |
| Atualizar status | `api_pedidos.php?action=atualizar_status` | POST |

### Formato das respostas

**Pedidos ativos:**
```json
[
    {
        "id_pedido": 7,
        "numero_pedido": "PED-6A1656F963724",
        "id_usuario": 5,
        "itens": [
            {"nome": "Monte o seu - 30 peças", "quantidade": 1, "preco": 54},
            {"nome": "Yuzu Spritz drink", "quantidade": 1, "preco": 28.5}
        ],
        "subtotal": 82.50,
        "desconto": 8.25,
        "total": 74.25,
        "forma_pagamento": "Dinheiro",
        "status": "pendente",
        "data_pedido": "2026-05-27 02:29:13",
        "cliente_nome": "Nivea"
    }
]
```

**Atualizar status (requisição):**
```json
POST api_pedidos.php?action=atualizar_status
{
    "id_pedido": 7,
    "status": "preparando"
}
```

**Atualizar status (resposta):**
```json
{"success": true}
```

---

## 7. Fluxo de Status dos Pedidos

```
                    ┌──────────────┐
                    │   PENDENTE   │ ← Pedido recém-criado pelo cliente
                    │   (amarelo)  │
                    └──────┬───────┘
                           │
                    Botão "Iniciar Preparo"
                    (status: 'preparando')
                           │
                           ▼
                    ┌──────────────┐
                    │  PREPARANDO  │ ← Cozinha está preparando
                    │    (azul)    │
                    └──────┬───────┘
                           │
                    Botão "Concluir Pedido"
                    (status: 'concluido')
                           │
                           ▼
                    ┌──────────────┐
                    │  CONCLUÍDO   │ ← Pedido finalizado
                    │   (verde)    │
                    └──────────────┘
```

### Validações no back-end

A `api_pedidos.php` valida:
- Apenas funcionários podem alterar status
- O status anterior é registrado no log (`pedido_status_log`)
- O nome do funcionário que alterou é salvo (`alterado_por`)

---

## 8. Melhorias de Design Aplicadas

### Antes vs Depois

| Aspecto | Antes | Depois |
|---|---|---|
| Fonte dos títulos | Inter (genérica) | Playfair Display (elegante) |
| Distinção visual por status | Apenas badge de cor | Badge + borda lateral colorida |
| Indicador de tempo | Apenas data/hora absoluta | Data/hora + "há X min" |
| Urgência | Nenhuma | Badge pulsante para >30min |
| Botões de ação | Pequenos (btn-sm outline) | Grandes, sólidos, fáceis de clicar |
| Feedback ao clicar | Nenhum | Spinner + "Processando..." |
| Contagem no histórico | Não tinha | Badge atualizado |
| Empty states | Ícone + texto mínimo | Ícone + texto + sugestão |
| Navbar | Sólida | Backdrop blur (glassmorphism) |
| Responsividade | 2-3 colunas | 1 coluna em mobile |

### Por que essas mudanças?

1. **Bordas laterais** → Identificação visual instantânea do status
2. **Tempo relativo** → O cozinheiro sabe imediatamente há quanto tempo o pedido está aguardando
3. **Urgência** → Pedidos antigos ganham destaque pulsante para não serem esquecidos
4. **Botões maiores** → Facilita clique em ambiente de cozinha (luvas, mãos ocupadas)
5. **Feedback visual** → Confirma que a ação foi registrada, evita cliques duplos
6. **Playfair Display** → Consistência visual com o cardápio do cliente

---

## 9. Dicionário de Classes CSS

### Layout

| Classe | Função |
|---|---|
| `pedido-card` | Card de pedido (fundo #1a1a1a, borda, cantos arredondados) |
| `status-border-{status}` | Borda lateral colorida por status |
| `pedido-header` | Cabeçalho do card (gradiente vermelho) |
| `pedido-body` | Corpo do card |
| `empty-state` | Estado vazio (quando não há pedidos) |

### Badges

| Classe | Função |
|---|---|
| `status-badge` | Badge genérico de status |
| `status-pendente` | Badge amarelo |
| `status-preparando` | Badge azul |
| `status-concluido` | Badge verde |
| `status-cancelado` | Badge vermelho |
| `badge-count` | Badge de contagem nas abas |
| `badge-urgencia` | Badge pulsante de urgência |

### Botões

| Classe | Função |
|---|---|
| `btn-acao` | Botão de ação (Iniciar/Concluir) — maior e sólido |
| `btn-warning` | Botão amarelo — "Iniciar Preparo" |
| `btn-success` | Botão verde — "Concluir Pedido" |
| `btn-outline-info` | Botão outline azul — "Detalhes" |

### Animações

| Classe/Keyframe | Função |
|---|---|
| `pulse` | Animação de pulso para novos pedidos |
| `pulse-urgencia` | Animação de pulso para badge de urgência |
| `novo-pedido` | Classe aplicada a cards de pedidos novos |

---

## 10. Referências Rápidas

### Como adicionar uma nova funcionalidade

1. **Novo endpoint na API:** Adicionar bloco `if ($action == '...')` em `api_pedidos.php`
2. **Nova função JS:** Adicionar no `<script>` de `cozinha.php`
3. **Novo estilo CSS:** Adicionar no `<style>` de `cozinha.php`
4. **Novo modal:** Adicionar HTML no `<body>` e preencher via JS

### Como testar

1. Iniciar servidor PHP: `php -S localhost:8000`
2. Acessar `http://localhost:8000/login_cozinha.php`
3. Login com: `chef@wabisabi.com` (senha do banco)
4. Criar um pedido pelo cardápio do cliente em outra aba
5. Voltar à aba da cozinha — o pedido deve aparecer automaticamente (auto-refresh 10s)

### Comandos úteis

```bash
# Importar banco de dados
mysql -u root -P 3306 cardapio < cardapio_sql.sql

# Iniciar servidor PHP
php -S localhost:8000

# Ver logs de status (banco)
SELECT * FROM pedido_status_log ORDER BY data_alteracao DESC;
```
