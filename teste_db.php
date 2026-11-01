<?php
require_once __DIR__ . '/app/Database/Conexao.php';
$db = Conexao::getConexao();

$tabelas = ['produtos', 'categorias', 'pedidos', 'pedido_itens', 'enderecos', 'bairros_delivery', 'horarios'];
foreach ($tabelas as $tab) {
    try {
        $cols = $db->query("DESCRIBE {$tab}")->fetchAll(PDO::FETCH_COLUMN);
        echo "<b>{$tab}:</b> " . implode(', ', $cols) . "<br><br>";
    } catch (Exception $e) {
        echo "<b>{$tab}:</b> Não existe ou erro: " . $e->getMessage() . "<br><br>";
    }
}