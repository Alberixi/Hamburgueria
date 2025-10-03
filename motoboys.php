<?php
$page_title = 'Motoboys';
require_once 'includes/header.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db->delete('motoboys', $id);
    $_SESSION['message'] = 'Motoboy excluído com sucesso!';
    $_SESSION['message_type'] = 'success';
    redirect('motoboys.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $telefone = sanitize($_POST['telefone']);
    $placa_moto = sanitize($_POST['placa_moto']);
    $ativo = isset($_POST['ativo']) ? true : false;

    $result = $db->insert('motoboys', [
        'nome' => $nome,
        'telefone' => $telefone,
        'placa_moto' => $placa_moto,
        'ativo' => $ativo
    ]);

    if ($result) {
        $_SESSION['message'] = 'Motoboy cadastrado com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('motoboys.php');
    } else {
        $_SESSION['message'] = 'Erro ao cadastrar motoboy!';
        $_SESSION['message_type'] = 'danger';
    }
}

$motoboys = $db->select('motoboys', '*');
?>

<div class="card">
    <div class="card-header">
        <h2>Motoboys</h2>
    </div>

    <form method="POST" style="background: #f8f9fa; padding: 1.5rem; border-radius: 4px; margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;">Novo Motoboy</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="placa_moto">Placa da Moto</label>
                <input type="text" id="placa_moto" name="placa_moto" class="form-control" placeholder="ABC-1234">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="ativo" checked> Motoboy Ativo
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Cadastrar Motoboy</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Placa</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($motoboys && count($motoboys) > 0): ?>
                <?php foreach ($motoboys as $motoboy): ?>
                <tr>
                    <td><?php echo $motoboy['id']; ?></td>
                    <td><?php echo sanitize($motoboy['nome']); ?></td>
                    <td><?php echo sanitize($motoboy['telefone']); ?></td>
                    <td><?php echo sanitize($motoboy['placa_moto']); ?></td>
                    <td>
                        <?php if ($motoboy['ativo']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-actions">
                        <a href="editar_motoboy.php?id=<?php echo $motoboy['id']; ?>" class="btn btn-primary btn-small">Editar</a>
                        <a href="motoboys.php?delete=<?php echo $motoboy['id']; ?>" class="btn btn-danger btn-small btn-delete">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum motoboy cadastrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
