<?php

class MedicSpecialty
{
  /**
   * @param $id
   * @param $db
   * @return stdClass
   */
  public function get($id, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT * FROM hm_medic_specialty WHERE msp_id = ?");

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
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT msp_id FROM hm_medic_specialty
                                    WHERE med_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $this->get($row['msp_id']);
  }
}
