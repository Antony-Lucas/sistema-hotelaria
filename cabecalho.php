<?php require_once("sistema/conexao.php"); 

$query = $pdo->query("SELECT * FROM dados_site order by id asc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$logo_site = @$res[0]['logo_site'];
$titulo_sobre = @$res[0]['titulo_sobre'];
$descricao_sobre1 = @$res[0]['descricao_sobre1'];
$descricao_sobre2 = @$res[0]['descricao_sobre2'];
$descricao_sobre3 = @$res[0]['descricao_sobre3'];
$foto_sobre_index = @$res[0]['foto_sobre_index'];
$foto_sobre_pagina = @$res[0]['foto_sobre_pagina'];
$video_sobre_index = @$res[0]['video_sobre_index'];
$foto_video_sobre = @$res[0]['foto_video_sobre'];
$foto_banner_mobile = @$res[0]['foto_banner_mobile'];
$mapa = @$res[0]['mapa'];

$ativa_home = '';
$ativa_sobre = '';
$ativa_quartos = '';
$ativa_contatos = '';
$ativa_sistema = '';

@session_start();
if($_SESSION['pagina'] == 'home'){
    $ativa_home = 'active';
}

if($_SESSION['pagina'] == 'sobre'){
    $ativa_sobre = 'active';
}

if($_SESSION['pagina'] == 'quartos'){
    $ativa_quartos = 'active';
}

if($_SESSION['pagina'] == 'contatos'){
    $ativa_contatos = 'active';
}


?>


<!doctype html>
<html lang="pt-br">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="sistema/img/icone.png" type="image/x-icon">
        <title><?php echo $nome_sistema ?></title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="vendors/linericon/style.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
        <link rel="stylesheet" href="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="vendors/nice-select/css/nice-select.css">
        <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
        <!-- main css -->
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/responsive.css">
    </head>
    <body>
        <!--================Header Area =================-->
        <header class="header_area" >
            <div class="container" style="width: 100%; max-width: 100%; padding: 0" >
                <nav class="navbar navbar-expand-lg navbar-light">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo_h" href="index.php"><img class="logo_mobile" src="sistema/img/logo_site.png" alt="" ></a>
                    <button style="padding-left: 15px" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent" >
                        <ul class="nav navbar-nav menu_nav ml-auto" style="padding-left: 15px">
                            <li class="nav-item <?php echo $ativa_home ?>"><a class="nav-link" href="index.php">Home</a></li> 
                            <li class="nav-item <?php echo $ativa_sobre ?>"><a class="nav-link" href="sobre.php">Sobre</a></li>
                            <li class="nav-item <?php echo $ativa_quartos ?>"><a class="nav-link" href="quartos.php">Quartos</a></li>
                            
                            <li class="nav-item <?php echo $ativa_contatos ?>"><a class="nav-link" href="contatos.php">Contatos</a></li>
                        </ul>
                    </div> 
                </nav>
            </div>
        </header>
        <!--================Header Area =================-->