<?php
$page_title = 'Editar Entrega';
require_once 'includes/header.php';
requireLogin();

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$id = intval($_GET['id']);
$entrega = $db->selectOne('entregas', ['id' => $id]);

if (!$entrega) {
    $_SESSION['message'] = 'Entrega não encontrada!';
    $_SESSION['message_type'] = 'danger';
    redirect('dashboard.php');
}

$motoboys = $db->select('motoboys', '*', ['ativo' => 'true']);
$pedidos = $db->select('pedidos', '*', ['entrega_id' => $id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motoboy_id = !empty($_POST['motoboy_id']) ? intval($_POST['motoboy_id']) : null;
    $status = sanitize($_POST['status']);
    $valor_entrega = floatval(str_replace(',', '.', $_POST['valor_entrega']));

    $result = $db->update('entregas', $id, [
        'motoboy_id' => $motoboy_id,
        'status' => $status,
        'valor_entrega' => $valor_entrega
    ]);

    if ($result !== false) {
        $_SESSION['message'] = 'Entrega atualizada com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('dashboard.php');
    } else {
        $_SESSION['message'] = 'Erro ao atualizar entrega!';
        $_SESSION['message_type'] = 'danger';
    }
}

$total_produtos = 0;
foreach ($pedidos as $pedido) {
    $total_produtos += $pedido['subtotal'];
}
$total_entrega = $total_produtos + $entrega['valor_entrega'];
?>

<div class="card">
    <div class="card-header">
        <h2>Editar Entrega #<?php echo $id; ?></h2>
        <a href="dashboard.php" class="btn btn-primary">Voltar</a>
    </div>

    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 4px; margin-bottom: 2rem;">
        <h3>Dados do Cliente</h3>
        <p><strong>Nome:</strong> <?php echo sanitize($entrega['cliente_nome']); ?></p>
        <p><strong>Telefone:</strong> <?php echo sanitize($entrega['cliente_telefone']); ?></p>
        <p><strong>Endereço:</strong> <?php echo sanitize($entrega['endereco']); ?></p>
        <p><strong>Data:</strong> <?php echo formatDate($entrega['data_entrega']); ?></p>
    </div>

    <h3>Produtos do Pedido</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unit.</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido):
                $produto = $db->selectOne('produtos', ['id' => $pedido['produto_id']]);
            ?>
            <tr>
                <td><?php echo $produto ? sanitize($produto['nome']) : 'Produto removido'; ?></td>
                <td><?php echo formatMoney($pedido['preco_unitario']); ?></td>
                <td><?php echo $pedido['quantidade']; ?></td>
                <td><?php echo formatMoney($pedido['subtotal']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 1rem;">
        <p><strong>Subtotal Produtos:</strong> <?php echo formatMoney($total_produtos); ?></p>
        <p><strong>Taxa de Entrega:</strong> <?php echo formatMoney($entrega['valor_entrega']); ?></p>
        <p style="font-size: 1.25rem;"><strong>Total:</strong> <?php echo formatMoney($total_entrega); ?></p>
    </div>

    <form method="POST" style="margin-top: 2rem;">
        <h3>Atualizar Entrega</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="motoboy_id">Motoboy</label>
                <select id="motoboy_id" name="motoboy_id" class="form-control">
                    <option value="">Selecione um motoboy...</option>
                    <?php foreach ($motoboys as $motoboy): ?>
                        <option value="<?php echo $motoboy['id']; ?>" <?php echo $entrega['motoboy_id'] == $motoboy['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($motoboy['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="pendente" <?php echo $entrega['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="em_transito" <?php echo $entrega['status'] === 'em_transito' ? 'selected' : ''; ?>>Em Trânsito</option>
                    <option value="entregue" <?php echo $entrega['status'] === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                    <option value="cancelado" <?php echo $entrega['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="valor_entrega">Taxa de Entrega *</label>
            <input type="text" id="valor_entrega" name="valor_entrega" class="form-control currency-input" value="<?php echo number_format($entrega['valor_entrega'], 2, ',', ''); ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Atualizar Entrega</button>
        <a href="dashboard.php" class="btn btn-danger">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
