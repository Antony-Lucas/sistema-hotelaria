<?php
require_once("../../../conexao.php");
$tabela = 'webhooks';

try {
    $stmt = $pdo->query("SELECT tipo, endpoint FROM $tabela");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estruturar os dados para retorno
    $endpoints = [];
    foreach ($result as $row) {
        $endpoints[$row['tipo']] = $row['endpoint'];
    }

    echo json_encode(['success' => true, 'endpoints' => $endpoints]);
} catch (Exception $e) {
    error_log("Erro ao listar endpoints: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao listar os endpoints: ' . $e->getMessage()]);
}
?>
