<?php

class Previsions
{
  /**
   * @param $id
   * @return stdClass
   */
  public function get($id): stdClass
  {
    $con = new ConnectMAIN();
    $query = "SELECT * FROM hm_prevision WHERE pre_id = ?";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->setToObject($data);
  }

  /**
   * @return array
   */
  public function select(): array
  {
    $con = new ConnectMAIN();
    $query = "SELECT pre_id, pre_name FROM hm_prevision";
    $stmt = $con->Prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['pre_id'];
      $obj->text = ucwords(mb_strtolower($data['pre_name'], 'utf-8'));
      $array[] = $obj;
    }
    return $array;
  }
}
