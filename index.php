<?php
$page_title = 'Login';
require_once 'includes/header.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = sanitize($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($usuario === 'admin' && $senha === 'admin') {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['usuario'] = $usuario;
        redirect('dashboard.php');
    } else {
        $error = 'Usuário ou senha incorretos!';
    }
}
?>

<div class="login-container">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login - <?php echo SITE_NAME; ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>

        <p style="text-align: center; margin-top: 1rem; color: #7f8c8d; font-size: 0.875rem;">
            Usuário padrão: admin / Senha: admin
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
