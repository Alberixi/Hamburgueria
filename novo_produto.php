<?php
$page_title = 'Novo Produto';
require_once 'includes/header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $descricao = sanitize($_POST['descricao']);
    $preco = floatval(str_replace(',', '.', $_POST['preco']));
    $categoria = sanitize($_POST['categoria']);
    $ativo = isset($_POST['ativo']) ? true : false;

    $result = $db->insert('produtos', [
        'nome' => $nome,
        'descricao' => $descricao,
        'preco' => $preco,
        'categoria' => $categoria,
        'ativo' => $ativo
    ]);

    if ($result) {
        $_SESSION['message'] = 'Produto cadastrado com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('produtos.php');
    } else {
        $_SESSION['message'] = 'Erro ao cadastrar produto!';
        $_SESSION['message_type'] = 'danger';
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Novo Produto</h2>
        <a href="produtos.php" class="btn btn-primary">Voltar</a>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="preco">Preço *</label>
                <input type="text" id="preco" name="preco" class="form-control currency-input" required placeholder="0,00">
            </div>

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="form-control">
                    <option value="">Selecione...</option>
                    <option value="Lanches">Lanches</option>
                    <option value="Bebidas">Bebidas</option>
                    <option value="Porções">Porções</option>
                    <option value="Sobremesas">Sobremesas</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="ativo" checked> Produto Ativo
            </label>
        </div>

        <button type="submit" class="btn btn-success">Cadastrar Produto</button>
        <a href="produtos.php" class="btn btn-danger">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
