<?php 

$tabela = 'receber';

require_once("../../../conexao.php");

@session_start();

$id_usuario = $_SESSION['id'];



$id = $_POST['id'];



$query = $pdo->query("SELECT * from $tabela where id = '$id' and pago = 'Sim'");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

$total_reg = @count($res);

if($total_reg > 0){

	exit();

}

//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}
//  

$pdo->query("UPDATE $tabela SET pago = 'Sim', usuario_pgto = '$id_usuario', data_pgto = curDate(), hora = curTime(), caixa = '$id_caixa' where id = '$id'");



echo 'Baixado com Sucesso';



?>



