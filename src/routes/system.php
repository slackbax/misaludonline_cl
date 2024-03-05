<?php

if (!isset($prm1)) {
  $title = 'Agendamiento • ';
  include 'html/system/layouts/home.php';
} else {
    header('Location: /');
}
