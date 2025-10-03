<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="caixa_' . date('Y-m-d_H-i-s') . '.csv"');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['RELATÓRIO DE CAIXA'], ';');
fputcsv($output, ['Data Abertura', formatDate($caixa['data_abertura'])], ';');
fputcsv($output, ['Valor Inicial', formatMoney($caixa['valor_inicial'])], ';');
fputcsv($output, [''], ';');

fputcsv($output, ['Data/Hora', 'Tipo', 'Descrição', 'Valor'], ';');

foreach ($movimentacoes as $mov) {
    fputcsv($output, [
        formatDate($mov['created_at']),
        $mov['tipo'] === 'entrada' ? 'Entrada' : 'Saída',
        $mov['descricao'],
        formatMoney($mov['valor'])
    ], ';');
}

fputcsv($output, [''], ';');
fputcsv($output, ['RESUMO'], ';');
fputcsv($output, ['Total Entradas', formatMoney($total_entradas)], ';');
fputcsv($output, ['Total Saídas', formatMoney($total_saidas)], ';');
fputcsv($output, ['Valor Final', formatMoney($valor_final)], ';');

fclose($output);
exit;
