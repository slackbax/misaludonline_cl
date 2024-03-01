<?php
$BASEDIR = explode('ajax', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/main/Previsions.php';

try {
    $_prv = new Previsions();
    $select = $_prv->select();
    echo json_encode(['error' => false, 'select' => $select]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
}
