<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $errors = [];

        $produtos_exemplo = [
            ['nome' => 'X-Burger', 'descricao' => 'Pão, carne, queijo, alface, tomate', 'preco' => 15.00, 'categoria' => 'Lanches', 'ativo' => true],
            ['nome' => 'X-Bacon', 'descricao' => 'Pão, carne, queijo, bacon, alface, tomate', 'preco' => 18.00, 'categoria' => 'Lanches', 'ativo' => true],
            ['nome' => 'X-Salada', 'descricao' => 'Pão, carne, queijo, alface, tomate, milho', 'preco' => 16.00, 'categoria' => 'Lanches', 'ativo' => true],
            ['nome' => 'Coca-Cola 350ml', 'descricao' => 'Refrigerante lata', 'preco' => 5.00, 'categoria' => 'Bebidas', 'ativo' => true],
            ['nome' => 'Guaraná 2L', 'descricao' => 'Refrigerante garrafa', 'preco' => 8.00, 'categoria' => 'Bebidas', 'ativo' => true],
            ['nome' => 'Batata Frita', 'descricao' => 'Porção de batata frita', 'preco' => 12.00, 'categoria' => 'Porções', 'ativo' => true]
        ];

        foreach ($produtos_exemplo as $produto) {
            $result = $db->insert('produtos', $produto);
            if (empty($result)) {
                $errors[] = "Falha ao inserir produto: {$produto['nome']}";
            }
        }

        $motoboys_exemplo = [
            ['nome' => 'João Silva', 'telefone' => '(11) 98765-4321', 'placa_moto' => 'ABC-1234', 'ativo' => true],
            ['nome' => 'Maria Santos', 'telefone' => '(11) 97654-3210', 'placa_moto' => 'DEF-5678', 'ativo' => true]
        ];

        foreach ($motoboys_exemplo as $motoboy) {
            $result = $db->insert('motoboys', $motoboy);
            if (empty($result)) {
                $errors[] = "Falha ao inserir motoboy: {$motoboy['nome']}";
            }
        }

        if (!empty($errors)) {
            $error = "Alguns dados não foram inseridos:<br>" . implode('<br>', $errors);
        } else {
            $message = 'Sistema instalado com sucesso! Dados de exemplo criados.';
        }
    } catch (Exception $e) {
        $error = 'Erro inesperado ao instalar: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container" style="padding-top: 3rem;">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Instalação do Sistema</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
                <p style="text-align: center;">
                    <a href="index.php" class="btn btn-primary">Ir para o Login</a>
                </p>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <p style="text-align: center; margin-top: 1rem;">
                    <a href="install.php" class="btn btn-secondary">Tentar Novamente</a>
                </p>
            <?php else: ?>
                <p>Este assistente irá instalar o sistema e criar dados de exemplo.</p>

                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 4px; margin: 1.5rem 0;">
                    <h3>O que será criado:</h3>
                    <ul>
                        <li>6 produtos de exemplo (lanches, bebidas, porções)</li>
                        <li>2 motoboys de exemplo</li>
                    </ul>
                </div>

                <div style="background: #fff3cd; padding: 1rem; border-radius: 4px; margin: 1.5rem 0; border: 1px solid #ffeeba;">
                    <strong>Credenciais de acesso:</strong>
                    <p style="margin: 0.5rem 0 0 0;">Usuário: <code>admin</code> | Senha: <code>admin</code></p>
                </div>

                <form method="POST" style="text-align: center;">
                    <button type="submit" class="btn btn-success">Instalar Sistema</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>