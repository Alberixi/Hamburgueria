# 🚚 Sistema de Gestão de Entregas

Sistema simples para gerenciar entregas, produtos, motoboys e caixa, usando **PHP puro + Supabase** como backend.

---

## 🌟 Recursos

- Dashboard com estatísticas em tempo real
- Cadastro e gerenciamento de:
  - Produtos
  - Motoboys
  - Entregas
- Controle de caixa (abrir/fechar, movimentações)
- Modo escuro (Dark Mode)
- Layout com menus laterais (esquerda e direita)

---

## 🛠️ Requisitos

- PHP 7.4+ (com extensão `cURL`)
- Servidor local (XAMPP, WAMP, Laragon, etc.)
- Conta no [Supabase](https://supabase.com/)
- Tabelas configuradas no Supabase (veja `supabase_schema.sql`)

---

## ⚙️ Configuração

### 1. Clone ou baixe o projeto
```bash
git clone https://github.com/seu-usuario/seu-repo.git





---

## ✅ PARTE 3: **Adicionar Dark Mode + Menus Laterais**

Vamos modificar seu layout. Precisaremos:

1. Atualizar `includes/header.php`
2. Criar `js/darkmode.js`
3. Atualizar `css/style.css`

---

### 📁 1. Atualize `includes/header.php`

Substitua pelo seguinte (mantendo seu `requireLogin()` e lógica existente):

```php
<?php
// Verifica se o dark mode está ativado
$dark_mode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled';
?>

<!DOCTYPE html>
<html lang="pt-BR" class="<?php echo $dark_mode ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Sistema de Entregas'; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/darkmode.js" defer></script>
</head>
<body>

<!-- Menu Lateral Esquerdo (Navegação Principal) -->
<nav class="sidebar left-sidebar">
    <div class="logo">
        <h3><?php echo SITE_NAME; ?></h3>
    </div>
    <ul>
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="entregas.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'entregas.php' ? 'active' : ''; ?>">Entregas</a></li>
        <li><a href="produtos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'produtos.php' ? 'active' : ''; ?>">Produtos</a></li>
        <li><a href="motoboys.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'motoboys.php' ? 'active' : ''; ?>">Motoboys</a></li>
        <li><a href="relatorios.php">Relatórios</a></li>
    </ul>
</nav>

<!-- Menu Lateral Direito (Ações Rápidas + Caixa) -->
<nav class="sidebar right-sidebar">
    <div class="user-info">
        <span>Olá, Admin</span>
        <a href="logout.php" class="btn btn-sm btn-outline">Sair</a>
    </div>

    <?php if (isset($caixa_aberto)): ?>
    <div class="caixa-status">
        <span class="badge badge-success">Caixa Aberto</span>
        <div class="caixa-total">R$ <?php echo number_format($total_caixa ?? 0, 2, ',', '.'); ?></div>
        <a href="fechar_caixa.php" class="btn btn-sm btn-warning">Fechar Caixa</a>
    </div>
    <?php else: ?>
    <div class="caixa-status">
        <span class="badge badge-danger">Caixa Fechado</span>
        <a href="dashboard.php?abrir_caixa=1" class="btn btn-sm btn-success">Abrir Caixa</a>
    </div>
    <?php endif; ?>

    <div class="quick-actions">
        <h4>Ações Rápidas</h4>
        <a href="nova_entrega.php" class="btn btn-sm btn-primary">Nova Entrega</a>
        <a href="novo_produto.php" class="btn btn-sm btn-primary">Novo Produto</a>
    </div>

    <!-- Botão Dark Mode -->
    <div class="dark-mode-toggle">
        <button id="darkModeToggle" aria-label="Alternar modo escuro">
            🌙 / ☀️
        </button>
    </div>
</nav>

<!-- Conteúdo Principal -->
<main class="main-content">