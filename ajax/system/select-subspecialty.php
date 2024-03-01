<?php
$BASEDIR = explode('ajax', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/main/Subspecialty.php';

if (extract($_POST)) {
  try {
    $_spe = new Subspecialty();
    $select = $_spe->select($id);
    echo json_encode(['error' => false, 'select' => $select]);
  } catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
  }
}
