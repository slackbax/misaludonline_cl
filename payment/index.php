<?php
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/src/constants.php';
require $BASEDIR . '/src/functions.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/rai/ConnectRAI.php';
require $BASEDIR . 'class/rai/Consultation.php';
require $BASEDIR . 'class/rai/Schedule.php';
require $BASEDIR . 'class/main/People.php';
require $BASEDIR . 'class/main/Patient.php';
require $BASEDIR . 'class/main/Medic.php';

if (extract($_GET)) {
  $_con = new ConnectMAIN();
  $_conrai = new ConnectRAI();
  $_cns = new Consultation();
  $_ppl = new People();
  $_pat = new Patient();
  $_med = new Medic();
  $_sch = new Schedule();

  $id = base64_decode($id);
  $con = $_cns->get($id);
  $med = $_med->get($con->med_id);
  $sch = $_sch->getByMedic($con->med_id);
  $pat = $_pat->get($con->pat_id);
  $ppl = $_ppl->get($pat->pe_id);

  $optional = array(
    'ID' => base64_encode($id),
    'Especialista' => $med->pe_fullname . ' ' . $med->pe_fathername
  );
  $optional = json_encode($optional);

  $params = array(
    'commerceOrder' => rand(1100, 2000),
    'subject' => 'Pago de consulta',
    'currency' => 'CLP',
    'amount' => $sch->sch_amount,
    'email' => $ppl->pe_email,
    'urlConfirmation' => Config::get('BASEURL') . '/confirmation.php',
    'urlReturn' => Config::get('BASEURL') . '/return.php',
    'optional' => $optional
  );

  $serviceName = 'payment/create';

  try {
    $flowApi = new FlowApi;
    $response = $flowApi->send($serviceName, $params, 'POST');
    $redirect = $response['url'] . '?token=' . $response['token'];
    header("location:$redirect");
  } catch (Exception $e) {
    echo $e->getCode() . ' - ' . $e->getMessage();
  }
}