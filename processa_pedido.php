<?php
require_once 'includes/header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('nova_entrega.php');
}

$cliente_nome = sanitize($_POST['cliente_nome']);
$cliente_telefone = sanitize($_POST['cliente_telefone']);
$endereco = sanitize($_POST['endereco']);
$motoboy_id = !empty($_POST['motoboy_id']) ? intval($_POST['motoboy_id']) : null;
$valor_entrega = floatval(str_replace(',', '.', $_POST['valor_entrega']));
$produtos = $_POST['produtos'] ?? [];

if (empty($produtos)) {
    $_SESSION['message'] = 'Adicione pelo menos um produto ao pedido!';
    $_SESSION['message_type'] = 'danger';
    redirect('nova_entrega.php');
}

$entrega_data = [
    'cliente_nome' => $cliente_nome,
    'cliente_telefone' => $cliente_telefone,
    'endereco' => $endereco,
    'motoboy_id' => $motoboy_id,
    'valor_entrega' => $valor_entrega,
    'status' => 'pendente',
    'data_entrega' => date('Y-m-d H:i:s')
];

$entrega = $db->insert('entregas', $entrega_data);

if ($entrega && isset($entrega[0]['id'])) {
    $entrega_id = $entrega[0]['id'];

    foreach ($produtos as $produto_id => $item) {
        $quantidade = intval($item['quantidade']);
        $preco = floatval($item['preco']);
        $subtotal = $quantidade * $preco;

        $db->insert('pedidos', [
            'entrega_id' => $entrega_id,
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $subtotal
        ]);
    }

    $caixa = $db->selectOne('caixa', ['status' => 'aberto']);
    if ($caixa) {
        $total_pedido = floatval($_POST['total']) + $valor_entrega;
        $db->insert('movimentacoes_caixa', [
            'caixa_id' => $caixa['id'],
            'tipo' => 'entrada',
            'descricao' => "Entrega #$entrega_id - $cliente_nome",
            'valor' => $total_pedido
        ]);
    }

    $_SESSION['message'] = 'Pedido criado com sucesso!';
    $_SESSION['message_type'] = 'success';
    redirect('dashboard.php');
} else {
    $_SESSION['message'] = 'Erro ao criar pedido!';
    $_SESSION['message_type'] = 'danger';
    redirect('nova_entrega.php');
}
