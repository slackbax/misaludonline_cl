<?php

if (!isset($prm1)) {
  $title = 'Inicio • ';
  include 'html/system/layouts/home.php';
} else {
    header('Location: /');
}
