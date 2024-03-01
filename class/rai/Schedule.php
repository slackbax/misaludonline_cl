<?php

class Schedule
{
  /**
   * @param $id
   * @param $db
   * @return stdClass
   */
  public function get($id, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    $stmt = $db->Prepare("SELECT * FROM rai_schedule s WHERE sch_id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $id
   * @param $db
   * @return stdClass
   */
  public function getByMedic($id, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    $stmt = $db->Prepare("SELECT sch_id FROM rai_schedule WHERE med_id = ? AND sch_status IS TRUE");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $obj = new stdClass();

    unset($db);
    if (is_null($row)):
      $obj->sch_id = null;
    else:
      $obj = $this->get($row['sch_id']);
    endif;

    return $obj;
  }
}
