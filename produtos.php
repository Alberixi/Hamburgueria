<?php
$page_title = 'Produtos';
require_once 'includes/header.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db->delete('produtos', $id);
    $_SESSION['message'] = 'Produto excluído com sucesso!';
    $_SESSION['message_type'] = 'success';
    redirect('produtos.php');
}

$produtos = $db->select('produtos', '*');
?>

<div class="card">
    <div class="card-header">
        <h2>Produtos</h2>
        <a href="novo_produto.php" class="btn btn-success">Novo Produto</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($produtos && count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['id']; ?></td>
                    <td><?php echo sanitize($produto['nome']); ?></td>
                    <td><?php echo sanitize($produto['categoria']); ?></td>
                    <td><?php echo formatMoney($produto['preco']); ?></td>
                    <td>
                        <?php if ($produto['ativo']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-actions">
                        <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-primary btn-small">Editar</a>
                        <a href="produtos.php?delete=<?php echo $produto['id']; ?>" class="btn btn-danger btn-small btn-delete">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum produto cadastrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
