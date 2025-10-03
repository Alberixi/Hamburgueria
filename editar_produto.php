<?php
$page_title = 'Editar Produto';
require_once 'includes/header.php';
requireLogin();

if (!isset($_GET['id'])) {
    redirect('produtos.php');
}

$id = intval($_GET['id']);
$produto = $db->selectOne('produtos', ['id' => $id]);

if (!$produto) {
    $_SESSION['message'] = 'Produto não encontrado!';
    $_SESSION['message_type'] = 'danger';
    redirect('produtos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $descricao = sanitize($_POST['descricao']);
    $preco = floatval(str_replace(',', '.', $_POST['preco']));
    $categoria = sanitize($_POST['categoria']);
    $ativo = isset($_POST['ativo']) ? true : false;

    $result = $db->update('produtos', $id, [
        'nome' => $nome,
        'descricao' => $descricao,
        'preco' => $preco,
        'categoria' => $categoria,
        'ativo' => $ativo
    ]);

    if ($result !== false) {
        $_SESSION['message'] = 'Produto atualizado com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('produtos.php');
    } else {
        $_SESSION['message'] = 'Erro ao atualizar produto!';
        $_SESSION['message_type'] = 'danger';
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Editar Produto</h2>
        <a href="produtos.php" class="btn btn-primary">Voltar</a>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?php echo sanitize($produto['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control"><?php echo sanitize($produto['descricao']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="preco">Preço *</label>
                <input type="text" id="preco" name="preco" class="form-control currency-input" value="<?php echo number_format($produto['preco'], 2, ',', ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="form-control">
                    <option value="">Selecione...</option>
                    <option value="Lanches" <?php echo $produto['categoria'] === 'Lanches' ? 'selected' : ''; ?>>Lanches</option>
                    <option value="Bebidas" <?php echo $produto['categoria'] === 'Bebidas' ? 'selected' : ''; ?>>Bebidas</option>
                    <option value="Porções" <?php echo $produto['categoria'] === 'Porções' ? 'selected' : ''; ?>>Porções</option>
                    <option value="Sobremesas" <?php echo $produto['categoria'] === 'Sobremesas' ? 'selected' : ''; ?>>Sobremesas</option>
                    <option value="Outros" <?php echo $produto['categoria'] === 'Outros' ? 'selected' : ''; ?>>Outros</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="ativo" <?php echo $produto['ativo'] ? 'checked' : ''; ?>> Produto Ativo
            </label>
        </div>

        <button type="submit" class="btn btn-success">Atualizar Produto</button>
        <a href="produtos.php" class="btn btn-danger">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
