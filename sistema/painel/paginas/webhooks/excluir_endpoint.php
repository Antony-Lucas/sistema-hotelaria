<?php
require_once("../../../conexao.php");
$tabela = 'webhooks';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM $tabela WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
