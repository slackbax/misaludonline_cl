<?php
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';

try {
  //Recibe el token enviado por Flow
  if (!isset($_POST["token"])) {
    throw new Exception("No se recibio el token", 1);
  }
  $token = filter_input(INPUT_POST, 'token');
  $params = array(
    "token" => $token
  );
  //Indica el servicio a utilizar
  $serviceName = "payment/getStatus";
  $flowApi = new FlowApi();
  $response = $flowApi->send($serviceName, $params);

  print_r($response);
} catch (Exception $e) {
  echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}