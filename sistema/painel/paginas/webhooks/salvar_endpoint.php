<?php
require_once("../../../conexao.php");
$tabela = 'webhooks';

// Obter os dados enviados via POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Lista de tipos esperados
$tipos = ['pagamento_efetuado', 'pagamento_pendente', 'pagamento_falha'];

try {
    foreach ($tipos as $tipo) {
        $endpoint = $data[$tipo] ?? '';

        // Atualizar o campo endpoint para o tipo correspondente
        $stmt = $pdo->prepare("UPDATE $tabela SET endpoint = :endpoint WHERE tipo = :tipo");
        $stmt->execute(['endpoint' => $endpoint, 'tipo' => $tipo]);
    }

    echo json_encode(['success' => true, 'message' => 'Endpoints atualizados com sucesso!']);
} catch (Exception $e) {
    error_log("Erro ao salvar endpoints: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar os endpoints: ' . $e->getMessage()]);
}
?>
