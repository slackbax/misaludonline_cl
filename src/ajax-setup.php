<?php

$BASEDIR = explode('src', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/rismat/ConnectRIS.php';
require $BASEDIR . 'class/controlsalud/ConnectCSA.php';

session_start();
date_default_timezone_set('America/Santiago');
