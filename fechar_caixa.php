<?php
$page_title = 'Fechar Caixa';
require_once 'includes/header.php';
requireLogin();

$caixa = $db->selectOne('caixa', ['status' => 'aberto']);

if (!$caixa) {
    $_SESSION['message'] = 'Não há caixa aberto!';
    $_SESSION['message_type'] = 'warning';
    redirect('dashboard.php');
}

$movimentacoes = $db->select('movimentacoes_caixa', '*', ['caixa_id' => $caixa['id']]);

$total_entradas = $caixa['valor_inicial'];
$total_saidas = 0;

foreach ($movimentacoes as $mov) {
    if ($mov['tipo'] === 'entrada') {
        $total_entradas += $mov['valor'];
    } else {
        $total_saidas += $mov['valor'];
    }
}

$valor_final = $total_entradas - $total_saidas;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->update('caixa', $caixa['id'], [
        'data_fechamento' => date('Y-m-d H:i:s'),
        'valor_final' => $valor_final,
        'status' => 'fechado'
    ]);

    $_SESSION['message'] = 'Caixa fechado com sucesso!';
    $_SESSION['message_type'] = 'success';
    redirect('dashboard.php');
}
?>

<div class="card">
    <div class="card-header">
        <h2>Fechar Caixa</h2>
        <a href="dashboard.php" class="btn btn-primary">Voltar</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Valor Inicial</h3>
            <div class="stat-value"><?php echo formatMoney($caixa['valor_inicial']); ?></div>
        </div>

        <div class="stat-card">
            <h3>Total Entradas</h3>
            <div class="stat-value" style="color: #27ae60;"><?php echo formatMoney($total_entradas); ?></div>
        </div>

        <div class="stat-card">
            <h3>Total Saídas</h3>
            <div class="stat-value" style="color: #e74c3c;"><?php echo formatMoney($total_saidas); ?></div>
        </div>

        <div class="stat-card">
            <h3>Valor Final</h3>
            <div class="stat-value" style="color: #3498db;"><?php echo formatMoney($valor_final); ?></div>
        </div>
    </div>

    <h3>Movimentações</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($movimentacoes) > 0): ?>
                <?php foreach ($movimentacoes as $mov): ?>
                <tr>
                    <td><?php echo formatDate($mov['created_at']); ?></td>
                    <td>
                        <?php if ($mov['tipo'] === 'entrada'): ?>
                            <span class="badge badge-success">Entrada</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Saída</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo sanitize($mov['descricao']); ?></td>
                    <td style="color: <?php echo $mov['tipo'] === 'entrada' ? '#27ae60' : '#e74c3c'; ?>;">
                        <?php echo formatMoney($mov['valor']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhuma movimentação registrada</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form method="POST" style="margin-top: 2rem;">
        <div style="text-align: center;">
            <p style="font-size: 1.25rem; margin-bottom: 1rem;">
                Deseja fechar o caixa com valor final de <strong><?php echo formatMoney($valor_final); ?></strong>?
            </p>
            <button type="submit" class="btn btn-warning" onclick="return confirm('Tem certeza que deseja fechar o caixa?')">
                Confirmar Fechamento
            </button>
            <a href="export_caixa.php" class="btn btn-primary">Exportar Relatório</a>
            <a href="dashboard.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
