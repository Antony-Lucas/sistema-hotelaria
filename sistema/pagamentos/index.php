<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

include("./config.php");
include("../conexao.php");

$id_reserva = @$_POST['id_reserva'];
if($id_reserva == ""){
   $id_reserva = @$_GET['id_reserva']; 
}

//excluir reservas pendentes de finalização
$pdo->query("DELETE FROM reservas WHERE reserva_site = 'Sim' and no_show <= 0 and hora_excluir < curTime()");  

$query = $pdo->query("SELECT * from reservas where id = '$id_reserva'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas == 0){
    echo 'Tempo para fechar compra finalizado, faça novamente !';

    echo '<script>window.location="../../index.php"</script>';  
    exit();
}else{
    $hospede = $res[0]['hospede'];
    $tipo_quarto = $res[0]['tipo_quarto'];
    $quarto = $res[0]['quarto'];
    $funcionario = $res[0]['funcionario'];
    $check_in = $res[0]['check_in'];
    $check_out = $res[0]['check_out'];
    $valor = $res[0]['valor'];
    $no_show = $res[0]['no_show'];
    $hospedes = $res[0]['hospedes'];
    $obs = $res[0]['obs'];
    $valor_diaria = $res[0]['valor_diaria'];
    $data = $res[0]['data'];
    $desconto = $res[0]['desconto'];
    $forma_pgto = $res[0]['forma_pgto'];
    $ref_pgto = $res[0]['ref_pgto'];

    $query2 = $pdo->query("SELECT * from categorias_quartos where id = '$tipo_quarto'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $nome_tipo = @$res2[0]['nome'];


}

if($ref_pgto != ""){
     require('consultar_pagamento.php');
     if($status_api == 'approved'){
         echo 'Essa reserva Já foi Paga e está aprovada!';  
         exit();  
        }
}

$valorF = number_format($valor, 2, ',', '.');

$query2 = $pdo->query("SELECT * from hospedes where id = '$hospede'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $nome_cliente = @$res2[0]['nome'];
    $cpf_cliente = @$res2[0]['cpf'];
    $telefone_hospede = @$res2[0]['telefone'];
    $email_cliente = @$res2[0]['email'];

$token_valor = ($valor!="")? sha1($valor) : "";
$doc = $cpf_cliente;
$doc =  str_replace(array(",", ".", "-", "/", " "), "", $doc);
$ref = $_REQUEST["ref"];
$email = $email_cliente;
$gerarDireto = $_REQUEST["gerarDireto"];
$descricao = $descricao;
$nome = $nome_cliente;
$sobrenome = $_REQUEST["sobrenome"];

?>
<html lang="pt-br">
<head>
    <title>Pagamento</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/signin.css" rel="stylesheet">
    <script src="./assets/jquery-3.6.4.min.js"></script>
</head>
<body  class="text-center">


<form action="../painel/rel/reserva_class.php" method="get" style="display:none">
    <input type="hidden" name="id" value="<?=$id_reserva;?>">
     <input type="hidden" name="enviar" value="sim">
    <button id="btn_form" type="submit"></button>
</form>



<div style="max-width: 500px; max-height: 800px; margin: 0 auto;  text-align: center; margin-bottom: 20px; word-break: break-all;" >


<div id="info_pagamento" style="text-align: center;"> 
        <h4 class="h3 mb-3 font-weight-normal" style=" font-size: 18px; border-radius: 4px;"><span>(Quarto <?=$nome_tipo;?>)</span> <span style="color:green">R$ <?=$valorF;?></span></h4>  

</div>    

<div  id="paymentBrick_container">
        </div>
        <div id="statusScreenBrick_container">
        </div>
        <div class="form-signin" id="form-pago" style="display:none;text-align: center;">
            <h1 class="h3 mb-3 font-weight-normal">Obrigado!</h1>
            <img class="mb-4"  src="<?=$url_sistema;?>pagamentos/assets/check_ok.png" alt="" width="120" height="120">
            <br>
            <h5><?=$MSG_APOS_PAGAMENTO;?></h5>
            <br>
            Código do pagamento: <?php echo $_GET["id"]; ?>
        </div>
        <div class="form-signin" id="form-erro" style="display:none;text-align: center;">
            <h1 class="h3 mb-3 font-weight-normal">Ocorreu um erro ao processar seu pagamento</h1>
            <img class="mb-4"  src="<?=$url_sistema;?>pagamentos/assets/check_error.png" alt="" width="120" height="120">
            <br>
            <h5><?=$MSG_APOS_PAGAMENTO;?></h5>
            <br>
            Código do pagamento: <?php echo $_GET["id"]; ?>
        </div>
    </div>
    <style>
        body{font-family:arial}
    </style>
    <script>
        var payment_check;
        const mp = new MercadoPago('<?=$TOKEN_MERCADO_PAGO_PUBLICO;?>', {
            locale: 'pt-BR'
        });
        const bricksBuilder = mp.bricks();
        const renderPaymentBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    amount: '<?=$valor;?>',
                    payer: {
                        firstName: "<?=$nome;?>",
                        lastName: "<?=$sobrenome;?>",
                        email: "<?=$email;?>",
                        identification: {
                            type: '<?=(strlen($doc)>11? "CNPJ" : "CPF");?>',
                            number: '<?=$doc;?>',
                        },
                        address: {
                            zipCode: '',
                            federalUnit: '',
                            city: '',
                            neighborhood: '',
                            streetName: '',
                            streetNumber: '',
                            complement: '',
                        }
                    },
                },
                customization: {
                    visual: {
                        style: {
                            theme: "dark",
                        },
                    },
                    paymentMethods: {
                        <?php if($ATIVAR_CARTAO_CREDITO=="1"){?>creditCard: "all",<?php } ?>
                        <?php if($ATIVAR_CARTAO_DEBIDO=="1"){?>debitCard: "all",<?php } ?>
                        <?php if($ATIVAR_BOLETO=="1"){?>ticket: "all",<?php } ?>
                        <?php if($ATIVAR_PIX=="1"){?>bankTransfer: "all",<?php } ?>
                        maxInstallments: 12
                    },
                },
                callbacks: {
                    onReady: () => {
                    },
                    onSubmit: ({ selectedPaymentMethod, formData }) => {

                        formData.external_reference = '<?=$ref;?>';
                        formData.description = '<?=$descricao;?>';
                        var id_reserva = '<?=$id_reserva;?>';
                        var telefone_hospede = '<?=$telefone_hospede;?>'
                        var nome_cliente = '<?=$nome_cliente;?>';
                        var cpf_cliente = '<?=$cpf_cliente;?>';
                        var quarto = '<?=$quarto;?>';
                        var tipo_quarto = '<?=$nome_tipo;?>';
                        var funcionario = '<?=$funcionario;?>';
                        var check_in = '<?=$check_in;?>';
                        var check_out = '<?=$check_out;?>';
                        var valor = '<?=$valor;?>';
                        var no_show = '<?=$no_show;?>';
                        var hospedes = '<?=$hospedes;?>';
                        var obs = '<?=$obs;?>';
                        var valor_diaria = '<?=$valor_diaria;?>';
                        var data = '<?=$data;?>';
                        var desconto = '<?=$desconto;?>';
                        var forma_pgto = '<?=$forma_pgto;?>';
                        var ref_pgto = '<?=$ref_pgto;?>';

                        return new Promise((resolve, reject) => {
                            fetch("<?=$url_sistema;?>pagamentos/process_payment.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify(formData),
                            })
                            .then((response) => {
                                return response.json();
                            })
                            .then((response) => {
                                console.log("Resposta completa do servidor:", response);
                                
                                if(response.status!=true){
                                    alert(response.message);
                                }

                                fetch("<?=$url_sistema;?>pagamentos/out_webhooks.php?tipo=pagamento_pendente")
                                .then((webhookResponse) => {
                                    console.log("Resposta bruta do servidor:", webhookResponse);
                                    return webhookResponse.json();
                                })
                                .then((webhookData) => {
                                    console.log(webhookData)
                                    if (webhookData.endpoint) {
                                        fetch(webhookData.endpoint, {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                            },
                                            body: JSON.stringify({
                                                statusPagamento: 'pendente',
                                                tipoPagamento: response.tipo,
                                                paymentId: response.id,
                                                idReserva: id_reserva,
                                                nomeCliente: nome_cliente,
                                                cpfCliente: cpf_cliente,
                                                tipoQuarto: tipo_quarto,
                                                numeroQuarto: quarto,
                                                valorDiaria: valor_diaria,
                                                checkIn: check_in,
                                                checkOut: check_out,
                                                valorTotal: valor,
                                                hospedes: hospedes,
                                                telefoneHospede: telefone_hospede,
                                                description: formData.description,
                                                externalReference: formData.external_reference
                                            }),
                                        })
                                        .then((finalResponse) => {
                                            if(response.status==true){
                                                window.location.href = "<?=$url_sistema;?>pagamentos/index.php?id="+response.id+'&id_reserva='+id_reserva;
                                            }

                                            return finalResponse.json()
                                        } )
                                        .catch((error) => {
                                            console.error("Erro ao chamar o endpoint pagamento_pendente:", error);
                                        });
                                    } else {
                                        console.error("Endpoint não encontrado para pagamento_pendente");
                                    }
                                })
                                .catch((error) => {
                                    console.error("Erro ao buscar endpoint para pagamento_pendente:", error);
                                });

                                resolve();
                            })
                            .catch((error) => {
                                reject();
                            });
                        });
                    },
                    onError: (error) => {
                        console.error(error);
                    },
                },
            };
            window.paymentBrickController = await bricksBuilder.create(
                "payment",
                "paymentBrick_container",
                settings
                );
        };

        const renderStatusScreenBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    paymentId: '<?=$_GET["id"];?>',
                },
                customization: {
                    visual: {
                        hideStatusDetails: false,
                        hideTransactionDate: false,
                        style: {
            theme: 'dark', // 'default' | 'dark' | 'bootstrap' | 'flat'
        }
    },
    backUrls: {
        //'error': '<http://<your domain>/error>',
        //'return': '<http://<your domain>/homepage>'
    }
},
callbacks: {
    onReady: () => {
        check("<?=$_GET["id"];?>", "<?=$_GET["id_reserva"];?>");
    },
    onError: (error) => {
    },
},
};
window.statusScreenBrickController = await bricksBuilder.create('statusScreen', 'statusScreenBrick_container', settings);
};

<?php if($_GET["id"]!=""){ ?>
    renderStatusScreenBrick(bricksBuilder);
<?php } else { ?>
    <?php if($valor==""){?>
        alert("O valor do pagamento está vazio.");
    <?php } ?>
    renderPaymentBrick(bricksBuilder);
<?php } ?>
var redi = "<?=$URL_REDIRECIONAR;?>";
function check(id, id_reserva) {
    var settings = {
        "url": "<?=$url_sistema;?>pagamentos/process_payment.php?acc=check&id=" + id + "&id_reserva=" + id_reserva,
        "method": "GET",
        "timeout": 0
    };
    $.ajax(settings).done(function(response) {
        console.log(response.status)
        var id_reserva = '<?=$id_reserva;?>';
        var telefone_hospede = '<?=$telefone_hospede;?>'
        var nome_cliente = '<?=$nome_cliente;?>';
        var cpf_cliente = '<?=$cpf_cliente;?>';
        var quarto = '<?=$quarto;?>';
        var tipo_quarto = '<?=$nome_tipo;?>';
        var funcionario = '<?=$funcionario;?>';
        var check_in = '<?=$check_in;?>';
        var check_out = '<?=$check_out;?>';
        var valor = '<?=$valor;?>';
        var hospedes = '<?=$hospedes;?>';
        var valor_diaria = '<?=$valor_diaria;?>';
        var data = '<?=$data;?>';
        var desconto = '<?=$desconto;?>';
        var forma_pgto = '<?=$forma_pgto;?>';
        var ref_pgto = '<?=$ref_pgto;?>';
        try {
            if (response.status == "pago") {
                fetch("<?=$url_sistema;?>pagamentos/out_webhooks.php?tipo=pagamento_efetuado")
                    .then((webhookResponse) => {
                        console.log("Resposta bruta do servidor:", webhookResponse);
                        return webhookResponse.json();
                    })
                    .then((webhookData) => {
                        if (webhookData.endpoint) {
                        fetch(webhookData.endpoint, {
                            method: "POST",
                            headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            statusPagamento: response.status,
                            tipoPagamento: response.tipo,
                            paymentId: response.id,
                            idReserva: id_reserva,
                            nomeCliente: nome_cliente,
                            cpfCliente: cpf_cliente,
                            tipoQuarto: tipo_quarto,
                            numeroQuarto: quarto,
                            valorDiaria: valor_diaria,
                            checkIn: check_in,
                            checkOut: check_out,
                            valorTotal: valor,
                            hospedes: hospedes,
                            telefoneHospede: telefone_hospede,
                        }),
                    })
                    .then((finalResponse) => {
                        if(response.status==true){
                            window.location.href = "<?=$url_sistema;?>pagamentos/index.php?id="+response.id+'&id_reserva='+id_reserva;
                        }

                        return finalResponse.json()
                    } )
                    .catch((error) => {
                        console.error("Erro ao chamar o endpoint pagamento_pendente:", error);
                    });
                    } else {
                        console.error("Endpoint não encontrado para pagamento_pendente");
                    }})
                    .catch((error) => {
                        console.error("Erro ao buscar endpoint para pagamento_pendente:", error);
                    });         
                      
                $("#statusScreenBrick_container").slideUp("fast");
                $("#form-pago").slideDown("fast");
                if (redi.trim() == "Sim") {
                    setTimeout(() => {
                        //window.location = redi;
                        $("#btn_form").click();
                    }, 6000);
                }
            } 
            if (response.status == "rejected") {
                fetch("<?=$url_sistema;?>pagamentos/out_webhooks.php?tipo=pagamento_falha")
                    .then((webhookResponse) => {
                        console.log("Resposta bruta do servidor:", webhookResponse);
                        return webhookResponse.json();
                    })
                    .then((webhookData) => {
                        if (webhookData.endpoint) {
                        fetch(webhookData.endpoint, {
                            method: "POST",
                            headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            statusPagamento: response.status,
                            tipoPagamento: response.tipo,
                            paymentId: response.id,
                            idReserva: id_reserva,
                            nomeCliente: nome_cliente,
                            cpfCliente: cpf_cliente,
                            tipoQuarto: tipo_quarto,
                            numeroQuarto: quarto,
                            valorDiaria: valor_diaria,
                            checkIn: check_in,
                            checkOut: check_out,
                            valorTotal: valor,
                            hospedes: hospedes,
                            telefoneHospede: telefone_hospede,
                        }),
                    })
                    .then((finalResponse) => {
                        if(response.status==true){
                            window.location.href = "<?=$url_sistema;?>pagamentos/index.php?id="+response.id+'&id_reserva='+id_reserva;
                        }

                        return finalResponse.json()
                    } )
                    .catch((error) => {
                        console.error("Erro ao chamar o endpoint pagamento_pendente:", error);
                    });
                    } else {
                        console.error("Endpoint não encontrado para pagamento_pendente");
                    }})
                    .catch((error) => {
                        console.error("Erro ao buscar endpoint para pagamento_pendente:", error);
                    });         
            }
            else {
                setTimeout(() => {
                    check(id)
                }, 3000);
            }
        } catch (error) {
            alert("Erro ao localizar o pagamento, contacte com o suporte");
        }
    });
}
</script>
</body>
</html>