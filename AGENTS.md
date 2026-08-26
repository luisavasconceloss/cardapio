# AGENTS.md

## Project Overview

Sushi Wabi-Sabi: a digital menu & kitchen order system for a Japanese restaurant. Vanilla PHP + MySQL (MariaDB) + Bootstrap 5. No framework, no build system, no package manager.

## Architecture

- **Client side:** `home.html` → `login_cardapio.php` → `cardapio.php` (menu + ordering)
- **Kitchen side:** `login_cozinha.php` → `cozinha.php` (order management)
- **API:** `api_pedidos.php` (JSON API, session-based auth)
- **Shared DB:** `conexao.php` (PDO, included by all PHP files)
- **Schema:** `cardapio_sql.sql` (full dump with seed data)

## Database

- **Engine:** MariaDB 10.4+ (or MySQL)
- **Connection:** `127.0.0.1`, user `root`, no password, database `cardapio`
- **Schema dump port:** The SQL file specifies port 3307 (phpMyAdmin default), but `conexao.php` uses port 3306. Match your local MySQL port.

### Key tables

| Table | Purpose |
|---|---|
| `pratos` | Menu items (id_prato, nome, descricao, id_categoria, quantidade, preco) |
| `categoria` | Menu categories (1-12, hardcoded in cardapio.php sections) |
| `usuarios` | Customer accounts (bcrypt passwords) |
| `funcionarios` | Staff accounts (chef/cozinheiro roles, bcrypt passwords) |
| `pedidos` | Orders (items stored as JSON, status: pendente→preparando→concluido/cancelado) |
| `pedido_status_log` | Audit trail for status changes |

## Auth Model

Two separate login systems — do not mix them up:

| Flow | Session keys | Login file | Landing page |
|---|---|---|---|
| Customer | `usuario_id`, `usuario_nome`, `usuario_email` | `login_cardapio.php` | `cardapio.php` |
| Staff | `funcionario_id`, `funcionario_nome`, `funcionario_cargo`, `funcionario_email` | `login_cozinha.php` | `cozinha.php` |

- **Roles:** `chef` (admin — can register employees), `cozinheiro` (kitchen staff)
- Passwords: `password_hash()` / `password_verify()` with `PASSWORD_DEFAULT`
- Logout: `login_cardapio.php?logout=1` (customer), `login_cozinha.php?logout=1` (staff)

## API (`api_pedidos.php`)

All endpoints require session auth (`usuario_id` OR `funcionario_id`). Actions via `?action=`:

| Action | Method | Access | Purpose |
|---|---|---|---|
| `salvar_pedido` | POST | Customer only | Create new order |
| `pedidos_pendentes` | GET | Staff only | List pendente + preparando orders |
| `historico_pedidos` | GET | Staff only | List concluido + cancelado orders |
| `atualizar_status` | POST | Staff only | Change order status |
| `buscar_pedido&id=X` | GET | Staff only | Fetch single order details |

POST body is raw JSON (`php://input`).

## Category IDs (hardcoded in cardapio.php)

1=Entradas, 2=Sushis Tradicionais, 3=Hossomaki, 4=Uramaki, 5=Sashimis, 6=Temakis, 7=Hot Rolls, 8=Pratos Renomados, 9=Supremos, 10=Sem álcool, 11=Alcoólicas, 12=Combinados

Category sections are rendered with `id_categoria == N` filters — adding a new category requires adding a new `<section>` block in `cardapio.php`.

## Image convention

Dish images: `img/{id_prato}.jpg` (numbered by primary key). Logo: `img/logo-sushi.png`. Home background: `img/home.jpeg`.

## Combo system (category 12)

Combinados (IDs 49-52) have a custom picker UI in `cardapio.php` JS. Piece counts are hardcoded in a `switch(idPrato)` block: 20/30/40/50 pieces with 0-2 temakis. Customization logic is complex — check the `openCustomizeModal()` JS function.

## Running locally

Requires a local PHP server with MySQL/MariaDB:

1. Import `cardapio_sql.sql` into a `cardapio` database
2. Ensure MySQL runs on port 3306 (or update `conexao.php`)
3. Start PHP: `php -S localhost:8000` from project root
4. Access: `http://localhost:8000/home.html`

## Documentation

- `cozinha-doc.md` — detailed study guide for the kitchen module (`cozinha.php`), covering auth flow, CSS design system, JS logic, API communication, and status workflow.

## Gotchas

- `conexao.php` echoes "Conexão realizada com sucesso!" on every include — this can break JSON responses if included in API files (currently works because `api_pedidos.php` sets `Content-Type: application/json` before include, but the echo is included in the response body).
- No CSRF protection on any form.
- No input sanitization on many queries (uses PDO prepared statements but some outputs are unescaped).
- Cart is client-side only (localStorage), not synced with the database.
- Category IDs are hardcoded in PHP sections and JS — changing the schema requires updating multiple files.
- `login_cozinha.php` logout redirects to `/cardapio/login_cozinha.php` (assumes `/cardapio` is the document root).
