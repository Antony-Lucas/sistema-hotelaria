<?php 
require_once("cabecalho.php");

$checkin = filter_var(@$_POST['checkin'], @FILTER_SANITIZE_STRING);
$checkout = filter_var(@$_POST['checkout'], @FILTER_SANITIZE_STRING);
$adultos = filter_var(@$_POST['adultos'], @FILTER_SANITIZE_STRING);
$criancas = filter_var(@$_POST['criancas'], @FILTER_SANITIZE_STRING);

$hospedes = intval($adultos) + intval($criancas);

$checkinF = implode('/', array_reverse(explode('-', $checkin)));
$checkoutF = implode('/', array_reverse(explode('-', $checkout)));

//calcular diferença de dias entre as datas
$diferenca = strtotime($checkout) - strtotime($checkin);
$dias = floor($diferenca / (60 * 60 * 24));

if($dias <= 0){
	$dias = 1;
}

?>  


<div class="whole-wrap" style="margin-top: 50px;">



	<div class="container">
		<br>
		<div style="border:1px solid #000; margin-top: 20px; padding: 10px; font-size:15px">
			<div class="row" >
				<div class="col-md-3 col-6">
					<b>CheckIn: </b> <strong><?php echo $checkinF ?></strong>
				</div>

				<div class="col-md-3 col-6">
					<b>CheckOut: </b> <strong><?php echo $checkoutF ?></strong>
				</div>

				<div class="col-md-3 col-6">
					<b>Hóspedes: </b> <strong><?php echo $hospedes ?></strong>
				</div>

				<div class="col-md-3 col-6">
					<b>Diárias: </b> <strong><?php echo $dias ?></strong>
				</div>
			</div>
		</div>

		<div style="margin-top: 20px;">
			
			 <form class="row contact_form" method="post" id="contactForm" novalidate="novalidate">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu Nome">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Seu Telefone">
                                </div>
                            </div>

                             <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="documento" name="documento" placeholder="Seu CPF ou RG" onkeyup="verMasc()">
                                </div>
                            </div>
                        </form>
		</div>

		<div id="blocos">

		

		</div>



	</div>
</div>

<form method="post" action="sistema/pagamentos/index.php" style="display:none">
<input type="text" name="id_reserva" id="id_reserva">
<button id="form_comprar" type="submit"></button>
</form>

<?php require_once("rodape.php") ?>    


<script type="text/javascript">

		$(document).ready( function () {
						verMasc()
						listar_quartos();
					});
	
		function listar_quartos(){

		var checkin = "<?=$checkin?>";
		var checkout = "<?=$checkout?>";
		var diarias = "<?=$dias?>";
		$.ajax({
			url: 'ajax/listar_quartos.php',
			method: 'POST',
			data: {checkin, checkout, diarias},
			dataType: "html",

			success:function(result){
				
				$("#blocos").html(result);           
			}
		});
	}



	function comprar(id){
		var nome = $("#nome").val();
		var telefone = $("#telefone").val();
		var documento = $("#documento").val();

		var checkin = "<?=$checkin?>";
		var checkout = "<?=$checkout?>";
		var diarias = "<?=$dias?>";
		var hospedes = "<?=$hospedes?>";


		if(nome == ""){
			alert('Preencha o Nome!');
			return;
		}

		if(telefone == ""){
			alert('Preencha o Telefone!');
			return;
		}

		if(documento == ""){
			alert('Preencha o Documento!');
			return;
		}

		$("#tipo_q").val(id);

		//salvar a reserva
		 $.ajax({
		        url: 'ajax/salvar_prereserva.php',
		        method: 'POST',
		        data: {id, nome, telefone, documento, checkin, checkout, diarias, hospedes},
		        dataType: "html",

		        success:function(result){		        	
		        	var split = result.split("*");
		            if(split[0].trim() == 'Salvo com Sucesso'){
		            	var id_reserva = split[1];		            	
		            	$("#id_reserva").val(id_reserva);
		            	$("#form_comprar").click();
		            }
		        }
		    });

		
	}

</script>


	<!-- Mascaras JS -->
<script type="text/javascript" src="sistema/painel/js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script> 


<script type="text/javascript">
	function verMasc(){
		var cpf = $('#documento').val();
		
		if(cpf.length >= 15){
			$('#documento').mask('AA 00 000 000-0');
		}else{
			$('#documento').mask('AA0.000.000-000');
		}
	}
</script>