<?php

class Machines
{
  public function get_data($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT * FROM ris_machines WHERE mac_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }
}
