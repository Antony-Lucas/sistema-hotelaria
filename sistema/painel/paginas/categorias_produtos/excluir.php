<?php 
$tabela = 'categorias_produtos';
require_once("../../../conexao.php");

$id = $_POST['id'];


//verificar se possui registros relacionados a ela
$query = $pdo->query("SELECT * FROM produtos where categoria = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Você não pode excluir esta categoria, existem produtos relacionados a ela!';
	exit();
}


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$foto = $res[0]['foto'];

if($foto != "sem-foto.jpg" and $foto != "sem-foto.png"){
	@unlink('../../images/produtos/'.$foto);
}

$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>