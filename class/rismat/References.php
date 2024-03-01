<?php

class References
{
  public function get_data($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT * FROM ris_references WHERE ref_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function data_reference($person): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT ref_id FROM ris_references WHERE ref_person = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $person);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $this->get_data($data['ref_id']);
  }
}