<?php 
require_once("../sistema/conexao.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$documento = $_POST['documento'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$diarias = $_POST['diarias'];
$hospedes = $_POST['hospedes'];

$check_in = $_POST['checkin'];
$check_out = $_POST['checkout'];

$tempo_reserva = 20; // Definição do tempo de reserva em minutos

$hora_atual = date('H:i:s');

// Somar 20 minutos à hora atual
$horaNova = strtotime("+$tempo_reserva minutes", strtotime($hora_atual));
$hora_excluir = date("H:i:s", $horaNova);

$id_hospede = "";



$id_hospede = "";

$query = $pdo->query("SELECT * from quartos where tipo = '$id' order by id asc");	
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){
	$id_quarto = $res[$i]['id'];

			//verificar se o quarto tem checkin no dia
				$query3 = $pdo->query("SELECT * from reservas where quarto = '$id_quarto' and check_in = '$check_in' and hora_checkout is null order by id desc");
				$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
				if(@count($res3) == 0){
				
				//verificar se nesta data já possui reserva para o quarto
				$query2 = $pdo->query("SELECT * from reservas where (quarto = '$id_quarto' and check_in <= '$check_in' and check_out > '$check_in') or (quarto = '$id_quarto' and check_in < '$check_out' and check_out > '$check_out') order by id desc");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
				if(@count($res2) == 0){

					//ACRESCENTAR ESSA CONSUTA PARA PODER VERIFICAR RESERVAS QUE ESTIVEREM ANTES DO CHECKIN E DEPOIS DO CHECKOUT DE UMA EXISTENTE, OU SEJA, QUE PEGA TODO O PERIODO DELA
					$query2 = $pdo->query("SELECT * from reservas where (quarto = '$id_quarto' and check_in < '$check_out' and check_out >= '$check_out') or (quarto = '$id_quarto' and check_in > '$check_in' and check_out <= '$check_out') order by id desc");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);		
				if(@count($res2) == 0){
					break;
				}

			}

		}

	}
}

$query_ct = $pdo->query("SELECT * from categorias_quartos where id = '$id'");
$res_ct = $query_ct->fetchAll(PDO::FETCH_ASSOC);
$nome_quarto = $res_ct[0]['nome'];
$valor = $res_ct[0]['valor'];
$valorF = number_format($valor, 2, ',', '.');

$valor_reserva = $valor * $diarias;

$query = $pdo->query("SELECT * from hospedes where telefone = '$telefone'");	
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	$id_hospede = $res[0]['id'];
	$query = $pdo->prepare("UPDATE hospedes SET nome = :nome, cpf = :documento, responsavel = 'Sim' WHERE telefone = '$telefone'");

	$query->bindValue(":nome", "$nome");
	$query->bindValue(":documento", "$documento");
	$query->execute();
	
	
}


$query = $pdo->query("SELECT * from hospedes where cpf = '$documento'");	
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	$id_hospede = $res[0]['id'];
	$query = $pdo->prepare("UPDATE hospedes SET nome = :nome, telefone = :telefone, responsavel = 'Sim' WHERE cpf = '$documento'");

	$query->bindValue(":nome", "$nome");
	$query->bindValue(":telefone", "$telefone");
	$query->execute();
	
	
}

if($id_hospede == ""){
	$query = $pdo->prepare("INSERT INTO hospedes SET nome = :nome, cpf = :documento, telefone = '$telefone', data = curDate(), responsavel = 'Não'");

	$query->bindValue(":nome", "$nome");
	$query->bindValue(":documento", "$documento");
	$query->execute();
	$id_hospede = $pdo->lastInsertId();
}


//excluir a reserva anterior caso o mesmo hóspede faça uma nova
$pdo->query("DELETE FROM reservas WHERE hospede = '$id_hospede' and reserva_site = 'Sim' and no_show <= 0");

//salvar a reserva
$pdo->query("INSERT INTO reservas SET hospede = '$id_hospede', tipo_quarto = '$id', quarto = '$id_quarto', funcionario = '0', check_in = '$checkin', check_out = '$checkout', valor = '$valor_reserva', no_show = '0', hospedes = '$hospedes', valor_diaria = '$valor', data = curDate(), desconto = '0', forma_pgto = '0', reserva_site = 'Sim', hora_excluir = '$hora_excluir'");
$id_reserva = $pdo->lastInsertId();

echo 'Salvo com Sucesso*'.$id_reserva;
?>