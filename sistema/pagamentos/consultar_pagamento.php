<?php
include('tokens.php');
//$ref_pgto = '59947648042';
$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/'.$ref_pgto,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'Authorization: Bearer '.$access_token
    ),
    ));
    $response = curl_exec($curl);
    $resultado = json_decode($response);
curl_close($curl);
//echo $resultado->status;
$status_api = $resultado->status;

if($status_api == 'approved'){  
    require_once("aprovar_pgto.php");
}
//var_dump($resultado);
?>