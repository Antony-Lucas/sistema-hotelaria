<?php 
require_once("../sistema/conexao.php");

$check_in = @$_POST['checkin'];
$check_out = @$_POST['checkout'];
$diarias = @$_POST['diarias'];


$id_quarto_post = '';
$id = '';

$total_quartos = 0;

//percorrer as categorias dos quartos
$query_ct = $pdo->query("SELECT * from categorias_quartos");
$res_ct = $query_ct->fetchAll(PDO::FETCH_ASSOC);
$linhas_ct = @count($res_ct);
if($linhas_ct > 0){
	for($ic=0; $ic<$linhas_ct; $ic++){
$valor = $res_ct[$ic]['valor'];
$tipo = $res_ct[$ic]['id'];
$nome_quarto = $res_ct[$ic]['nome'];
$descricao = $res_ct[$ic]['descricao'];
$especificacoes = $res_ct[$ic]['especificacoes'];
$especificacoesF = str_replace('**', ', ', $especificacoes);
$valor = $res_ct[$ic]['valor'];
$foto = $res_ct[$ic]['foto'];

$total_valor = $valor * $diarias;
$total_valorF = number_format($total_valor, 2, ',', '.');

//excluir reservas pendentes de finalização
$pdo->query("DELETE FROM reservas WHERE reserva_site = 'Sim' and no_show <= 0 and hora_excluir < curTime()");  

$query = $pdo->query("SELECT * from quartos where ativo = 'Sim' and tipo = '$tipo' order by numero asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){
		$id_quarto = $res[$i]['id'];

		//verificar se o quarto tem checkin no dia
		$query3 = $pdo->prepare("SELECT * from reservas where quarto = '$id_quarto' and check_in = :check_in and hora_checkout is null order by id desc");
		$query3->bindValue(":check_in", "$check_in");
		$query3->execute();
		$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
		if(@count($res3) == 0){
		
		//verificar se nesta data já possui reserva para o quarto
		$query2 = $pdo->prepare("SELECT * from reservas where (quarto = '$id_quarto' and check_in <= :check_in and check_out > :check_in) or (quarto = '$id_quarto' and check_in < :check_out and check_out > :check_out) order by id desc");
		$query2->bindValue(":check_in", "$check_in");
		$query2->bindValue(":check_out", "$check_out");
		$query2->execute();
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if(@count($res2) == 0){

			//ACRESCENTAR ESSA CONSUTA PARA PODER VERIFICAR RESERVAS QUE ESTIVEREM ANTES DO CHECKIN E DEPOIS DO CHECKOUT DE UMA EXISTENTE, OU SEJA, QUE PEGA TODO O PERIODO DELA
			$query2 = $pdo->prepare("SELECT * from reservas where (quarto = '$id_quarto' and check_in < :check_out and check_out >= :check_out) or (quarto = '$id_quarto' and check_in > :check_in and check_out <= :check_out) order by id desc");
			$query2->bindValue(":check_in", "$check_in");
		$query2->bindValue(":check_out", "$check_out");
		$query2->execute();
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);		
		if(@count($res2) == 0){

			$total_quartos += 1;

			echo '<div class="section-top-border">

			<h3 class="mb-30 title_color">'.$nome_quarto.'<span style="color:green"> R$ '.$total_valorF.'</span> <a style="margin-left:10px" href="#" onclick="comprar('.$tipo.')" title="Reservar Quarto" class="btn theme_btn button_hover">Reservar</a> </h3>
			<div class="row">
				<div class="col-lg-10">
					<blockquote class="generic-blockquote">
					<strong><span style="color:blue;">Descrição:</span> </strong><span style="font-size:13px">'.$descricao.'</span> <br>';
							
					$query20 = $pdo->query("SELECT * FROM especificacoes_quartos where cat_quartos = '$tipo' order by id asc");
$res20 = $query20->fetchAll(PDO::FETCH_ASSOC);
$total_reg20 = @count($res20);
if($total_reg20 > 0){
for($i20=0; $i20 < $total_reg20; $i20++){
$nome20 = $res20[$i20]['texto'];

					echo '<div style="border-bottom: 1px solid #959595; padding:2px; font-size:12px"><i class="fa fa-check text-verde"></i>'.$nome20.'</div>';
					} }

					echo '</blockquote>
				</div>
				<div class="col-lg-2" align="center">
				<img src="sistema/painel/images/quartos/'.$foto.'" width="250px">
				</div>
			</div>
		</div>';	

		break;	

			}

			
		}	

		}

	} }else{
		echo '';
	 } 

	}

}else{
	echo '';
}

if($total_quartos == 0){
	echo 'Nenhum quarto disponível nessa data!';
}
?>

