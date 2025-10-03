<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Verifica dark mode via cookie
$dark_mode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled';
?>

<!DOCTYPE html>
<html lang="pt-BR" class="<?php echo $dark_mode ? 'dark' : ''; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/darkmode.js" defer></script>
</head>

<body>

    <?php if (isLoggedIn()): ?>
        <!-- Menu Lateral Esquerdo -->
        <nav class="sidebar left-sidebar">
            <div class="logo">
                <h3><?php echo SITE_NAME; ?></h3>
            </div>
            <ul>
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="entregas.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'entregas.php' ? 'active' : ''; ?>">Entregas</a></li>
                <li><a href="produtos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'produtos.php' ? 'active' : ''; ?>">Produtos</a></li>
                <li><a href="motoboys.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'motoboys.php' ? 'active' : ''; ?>">Motoboys</a></li>
            </ul>
        </nav>

        <!-- Menu Lateral Direito -->
        <nav class="sidebar right-sidebar">
            <div class="user-info">
                <span>Olá, Admin</span>
                <a href="logout.php" class="btn btn-sm btn-outline" onclick="return confirm('Deseja realmente sair?')">Sair</a>
            </div>

            <?php
            // Lógica do caixa para o menu direito
            $caixa_aberto = $db->selectOne('caixa', ['status' => 'aberto']);
            $total_caixa = 0;
            if ($caixa_aberto) {
                $movimentacoes = $db->select('movimentacoes_caixa', '*', ['caixa_id' => $caixa_aberto['id']]);
                $total_caixa = $caixa_aberto['valor_inicial'];
                foreach ($movimentacoes as $mov) {
                    if ($mov['tipo'] === 'entrada') {
                        $total_caixa += $mov['valor'];
                    } else {
                        $total_caixa -= $mov['valor'];
                    }
                }
            }
            ?>

            <?php if ($caixa_aberto): ?>
                <div class="caixa-status">
                    <span class="badge badge-success">Caixa Aberto</span>
                    <div class="caixa-total">R$ <?php echo number_format($total_caixa, 2, ',', '.'); ?></div>
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

            <div class="dark-mode-toggle">
                <button id="darkModeToggle" aria-label="Alternar modo escuro">
                    🌙 / ☀️
                </button>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <?php
            // Mensagens de sessão
            if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Para páginas não logadas (ex: login) -->
            <main class="container">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>