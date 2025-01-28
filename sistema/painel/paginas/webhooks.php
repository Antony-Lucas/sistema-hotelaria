<?php 
$pag = 'webhooks';

// Verificar permissão
if (@$banners_site == 'ocultar') {
    echo "<script>window.location='../index.php'</script>";
    exit();
}
?>

<div class="margin_mobile">
    <h2 style="margin-bottom: 20px;">Webhooks</h2>
</div>

<div class="bs-example widget-shadow" style="padding:15px">
    <div class="row">
        <!-- Campo 1: Endpoint Pagamento Efetuado -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <label for="endpoint_pagamento_efetuado">Endpoint Pagamento Efetuado</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" class="form-control" id="endpoint_pagamento_efetuado" placeholder="Insira o endpoint">
            </div>
        </div>

        <!-- Campo 2: Endpoint Pagamento Pendente -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <label for="endpoint_pagamento_pendente">Endpoint Pagamento Pendente</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" class="form-control" id="endpoint_pagamento_pendente" placeholder="Insira o endpoint">
            </div>
        </div>

        <!-- Campo 3: Endpoint Pagamento Falha -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <label for="endpoint_pagamento_falha">Endpoint Pagamento Falha</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" class="form-control" id="endpoint_pagamento_falha" placeholder="Insira o endpoint">
            </div>
        </div>
    </div>

    <!-- Botão único para salvar todos os campos -->
    <div class="text-right" style="margin-top: 15px;">
            <button type="button" class="btn btn-primary" onclick="salvarTodosEndpoints()">Salvar</button>
        </div>
</div>

<script type="text/javascript">
    // Função para salvar todos os endpoints
    function salvarTodosEndpoints() {
        const campos = ['pagamento_efetuado', 'pagamento_pendente', 'pagamento_falha'];
        const endpoints = {};

        campos.forEach(campo => {
            const input = document.getElementById(`endpoint_${campo}`);
            if (input) {
                endpoints[campo] = input.value || null;
            }
        });

        fetch('paginas/webhooks/salvar_endpoint.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(endpoints),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Endpoints salvos com sucesso!');
                listarEndpoints();
            } else {
                alert(data.message || 'Erro ao salvar os endpoints.');
            }
        })
        .catch(error => {
            console.error('Erro ao salvar os endpoints:', error);
        });
    }

    // Função para listar e preencher os endpoints nos campos
    function listarEndpoints() {
        fetch('paginas/webhooks/listar_endpoints.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const endpoints = data.endpoints;
                for (const [tipo, endpoint] of Object.entries(endpoints)) {
                    const input = document.getElementById(`endpoint_${tipo}`);
                    if (input) {
                        input.value = endpoint;
                    }
                }
            } else {
                alert(data.message || 'Erro ao listar os endpoints.');
            }
        })
        .catch(error => {
            console.error('Erro ao listar endpoints:', error);
        });
    }

    // Inicializar os campos ao carregar a página
    listarEndpoints();
</script>
