<?php
$page_title = 'Nova Entrega';
require_once 'includes/header.php';
requireLogin();

$produtos = $db->select('produtos', '*', ['ativo' => 'true']);
$motoboys = $db->select('motoboys', '*', ['ativo' => 'true']);
?>

<div class="card">
    <div class="card-header">
        <h2>Nova Entrega</h2>
        <a href="dashboard.php" class="btn btn-primary">Voltar</a>
    </div>

    <form method="POST" action="processa_pedido.php">
        <h3>Dados do Cliente</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="cliente_nome">Nome do Cliente *</label>
                <input type="text" id="cliente_nome" name="cliente_nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cliente_telefone">Telefone</label>
                <input type="text" id="cliente_telefone" name="cliente_telefone" class="form-control" placeholder="(00) 00000-0000">
            </div>
        </div>

        <div class="form-group">
            <label for="endereco">Endereço de Entrega *</label>
            <textarea id="endereco" name="endereco" class="form-control" required placeholder="Rua, número, bairro, complemento"></textarea>
        </div>

        <h3 style="margin-top: 2rem;">Dados da Entrega</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="motoboy_id">Motoboy</label>
                <select id="motoboy_id" name="motoboy_id" class="form-control">
                    <option value="">Selecione um motoboy...</option>
                    <?php foreach ($motoboys as $motoboy): ?>
                        <option value="<?php echo $motoboy['id']; ?>"><?php echo sanitize($motoboy['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="valor_entrega">Taxa de Entrega *</label>
                <input type="text" id="valor_entrega" name="valor_entrega" class="form-control currency-input" required placeholder="0,00">
            </div>
        </div>

        <h3 style="margin-top: 2rem;">Produtos</h3>
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 4px; margin-bottom: 1rem;">
            <label>Selecione os produtos:</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <?php foreach ($produtos as $produto): ?>
                    <button type="button" class="btn btn-primary" onclick="adicionarProduto(<?php echo $produto['id']; ?>, '<?php echo addslashes($produto['nome']); ?>', <?php echo $produto['preco']; ?>)">
                        <?php echo sanitize($produto['nome']); ?><br>
                        <small><?php echo formatMoney($produto['preco']); ?></small>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unit.</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="produtos-pedido">
            </tbody>
        </table>

        <div style="text-align: right; font-size: 1.5rem; font-weight: bold; margin-top: 1rem;">
            Total: <span id="total-pedido">R$ 0,00</span>
            <input type="hidden" id="total-hidden" name="total" value="0">
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Criar Entrega</button>
            <a href="dashboard.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
