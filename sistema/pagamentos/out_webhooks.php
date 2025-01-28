<?php
include("./config.php");
include("../conexao.php");

header('Content-Type: application/json');

try {
    $tipo = $_GET['tipo'] ?? null;

    if ($tipo) {
        $stmt = $pdo->prepare("SELECT endpoint FROM webhooks WHERE tipo = :tipo");
        $stmt->execute(['tipo' => $tipo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['endpoint' => $result['endpoint']]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo é necessário']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor', 'message' => $e->getMessage()]);
}
?>
