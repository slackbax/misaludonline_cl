<?php

class ConsultationBlock
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

    $stmt = $db->Prepare("SELECT * FROM rai_consultation_block WHERE cob_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $id
   * @param $fulldate
   * @param $db
   * @return stdClass
   */
  public function getBySlot($id, $fulldate, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    $stmt = $db->Prepare("SELECT cob_id 
                                    FROM rai_consultation_block 
                                    WHERE med_id = ? AND ? BETWEEN cob_datestart AND cob_dateend AND cob_status IS TRUE");
    $stmt->bind_param("is", $id, $fulldate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (is_null($row)):
      $obj = new stdClass();
      $obj->cob_id = null;
    else:
      $obj = $this->get($row['cob_id']);
    endif;

    unset($db);
    return $obj;
  }
}
