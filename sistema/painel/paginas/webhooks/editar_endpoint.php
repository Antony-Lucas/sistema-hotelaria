<?php
require_once("../../../conexao.php");
$tabela = 'webhooks';

$id = $_POST['id'] ?? null;
$endpoint = $_POST['endpoint'] ?? '';

if (!$id || empty($endpoint)) {
    echo json_encode(['success' => false, 'message' => 'ID ou endpoint inválido.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE $tabela SET endpoint = ? WHERE id = ?");
    $stmt->execute([$endpoint, $id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
