<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';
requireLogin();

$caixa_aberto = $db->selectOne('caixa', ['status' => 'aberto']);

$entregas_hoje = $db->select('entregas', '*', ['data_entrega' => 'gte.' . date('Y-m-d')]);
$total_entregas_hoje = count($entregas_hoje);

$entregas_pendentes = $db->select('entregas', '*', ['status' => 'pendente']);
$total_pendentes = count($entregas_pendentes);

$produtos = $db->select('produtos', '*', ['ativo' => 'true']);
$total_produtos = count($produtos);

$motoboys = $db->select('motoboys', '*', ['ativo' => 'true']);
$total_motoboys = count($motoboys);

$movimentacoes = [];
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

$entregas_recentes = array_slice($entregas_hoje, 0, 10);
?>

<div class="card">
    <div class="card-header">
        <h2>Dashboard</h2>
        <div>
            <?php if ($caixa_aberto): ?>
                <span class="badge badge-success">Caixa Aberto</span>
                <a href="fechar_caixa.php" class="btn btn-warning btn-small">Fechar Caixa</a>
            <?php else: ?>
                <span class="badge badge-danger">Caixa Fechado</span>
                <a href="dashboard.php?abrir_caixa=1" class="btn btn-success btn-small">Abrir Caixa</a>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if (isset($_GET['abrir_caixa']) && !$caixa_aberto) {
        $db->insert('caixa', [
            'data_abertura' => date('Y-m-d H:i:s'),
            'valor_inicial' => 0,
            'status' => 'aberto'
        ]);
        $_SESSION['message'] = 'Caixa aberto com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('dashboard.php');
    }
    ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Entregas Hoje</h3>
            <div class="stat-value"><?php echo $total_entregas_hoje; ?></div>
        </div>

        <div class="stat-card">
            <h3>Pendentes</h3>
            <div class="stat-value"><?php echo $total_pendentes; ?></div>
        </div>

        <div class="stat-card">
            <h3>Produtos Ativos</h3>
            <div class="stat-value"><?php echo $total_produtos; ?></div>
        </div>

        <div class="stat-card">
            <h3>Motoboys Ativos</h3>
            <div class="stat-value"><?php echo $total_motoboys; ?></div>
        </div>

        <?php if ($caixa_aberto): ?>
        <div class="stat-card">
            <h3>Total em Caixa</h3>
            <div class="stat-value"><?php echo formatMoney($total_caixa); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <h3 style="margin-top: 2rem;">Entregas Recentes</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Endereço</th>
                <th>Motoboy</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($entregas_recentes) > 0): ?>
                <?php foreach ($entregas_recentes as $entrega):
                    $motoboy = $entrega['motoboy_id'] ? $db->selectOne('motoboys', ['id' => $entrega['motoboy_id']]) : null;
                ?>
                <tr>
                    <td><?php echo $entrega['id']; ?></td>
                    <td><?php echo sanitize($entrega['cliente_nome']); ?></td>
                    <td><?php echo sanitize($entrega['endereco']); ?></td>
                    <td><?php echo $motoboy ? sanitize($motoboy['nome']) : '-'; ?></td>
                    <td><?php echo formatMoney($entrega['valor_entrega']); ?></td>
                    <td>
                        <?php
                        $badge_class = 'info';
                        if ($entrega['status'] === 'entregue') $badge_class = 'success';
                        if ($entrega['status'] === 'cancelado') $badge_class = 'danger';
                        if ($entrega['status'] === 'em_transito') $badge_class = 'warning';
                        ?>
                        <span class="badge badge-<?php echo $badge_class; ?>"><?php echo ucfirst($entrega['status']); ?></span>
                    </td>
                    <td class="table-actions">
                        <a href="editar_entrega.php?id=<?php echo $entrega['id']; ?>" class="btn btn-primary btn-small">Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhuma entrega encontrada</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 1.5rem;">
        <a href="nova_entrega.php" class="btn btn-success">Nova Entrega</a>
        <a href="produtos.php" class="btn btn-primary">Gerenciar Produtos</a>
        <a href="motoboys.php" class="btn btn-primary">Gerenciar Motoboys</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
