<?php
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/src/constants.php';
require $BASEDIR . '/src/functions.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';
require $BASEDIR . '/class/rai/ConnectRAI.php';
require $BASEDIR . '/class/rai/ConsultationPayment.php';
require $BASEDIR . '/class/rai/ConsultationEvolution.php';

$_con = new ConnectRAI();
$_cpa = new ConsultationPayment();
$_ce = new ConsultationEvolution();

try {
  if (!isset($_POST["token"])) {
    throw new Exception("No se recibio el token", 1);
  }
  $token = filter_input(INPUT_POST, 'token');
  $params = array(
    "token" => $token
  );
  $serviceName = "payment/getStatus";
  $flowApi = new FlowApi();
  $response = $flowApi->send($serviceName, $params);

  $ins = $_cpa->set(base64_decode($response['optional']['ID']), $response['paymentData']['date'], $response['paymentData']['amount'], $response['paymentData']['fee'], $response['paymentData']['balance']);
  if (!$ins['estado']) throw new Exception('No pudo registrarse la consulta.');
  $ins1 = $_ce->setInactive(base64_decode($response['optional']['ID']));
  if (!$ins1['estado']) throw new Exception("No pudo actualizarse el estado de la consulta.");
  $ins2 = $_ce->set(1, 3, base64_decode($response['optional']['ID']), 'PAGO EN LINEA');
  if (!$ins2['estado']) throw new Exception('No pudo registrarse el estado de la consulta.');
} catch (Exception $e) {
  echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}