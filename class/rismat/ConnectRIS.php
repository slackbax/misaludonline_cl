<?php

class ConnectRIS
{
  public ?mysqli $mysqli = null;

  public function __construct()
  {
    $this->mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, RIS_DATABASE);
    $this->mysqli->set_charset(DB_CHARSET);
  }

  public function __destruct()
  {
    $this->mysqli->close();
  }

  public function prepare_query($query): bool|mysqli_stmt
  {
    return $this->mysqli->prepare($query);
  }

  public function set_to_object($args): stdClass
  {
    $obj = new stdClass();
    foreach ($args as $key => $value) {
      if (is_null($value)) {
        $obj->{$key} = '';
      } else {
        $obj->{$key} = $value;
      }
    }
    return $obj;
  }
}
