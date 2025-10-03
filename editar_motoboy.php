<?php
$page_title = 'Editar Motoboy';
require_once 'includes/header.php';
requireLogin();

if (!isset($_GET['id'])) {
    redirect('motoboys.php');
}

$id = intval($_GET['id']);
$motoboy = $db->selectOne('motoboys', ['id' => $id]);

if (!$motoboy) {
    $_SESSION['message'] = 'Motoboy não encontrado!';
    $_SESSION['message_type'] = 'danger';
    redirect('motoboys.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $telefone = sanitize($_POST['telefone']);
    $placa_moto = sanitize($_POST['placa_moto']);
    $ativo = isset($_POST['ativo']) ? true : false;

    $result = $db->update('motoboys', $id, [
        'nome' => $nome,
        'telefone' => $telefone,
        'placa_moto' => $placa_moto,
        'ativo' => $ativo
    ]);

    if ($result !== false) {
        $_SESSION['message'] = 'Motoboy atualizado com sucesso!';
        $_SESSION['message_type'] = 'success';
        redirect('motoboys.php');
    } else {
        $_SESSION['message'] = 'Erro ao atualizar motoboy!';
        $_SESSION['message_type'] = 'danger';
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Editar Motoboy</h2>
        <a href="motoboys.php" class="btn btn-primary">Voltar</a>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?php echo sanitize($motoboy['nome']); ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-control" value="<?php echo sanitize($motoboy['telefone']); ?>" placeholder="(00) 00000-0000">
            </div>

            <div class="form-group">
                <label for="placa_moto">Placa da Moto</label>
                <input type="text" id="placa_moto" name="placa_moto" class="form-control" value="<?php echo sanitize($motoboy['placa_moto']); ?>" placeholder="ABC-1234">
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="ativo" <?php echo $motoboy['ativo'] ? 'checked' : ''; ?>> Motoboy Ativo
            </label>
        </div>

        <button type="submit" class="btn btn-success">Atualizar Motoboy</button>
        <a href="motoboys.php" class="btn btn-danger">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
