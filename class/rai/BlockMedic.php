<?php

class BlockMedic
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

    $stmt = $db->Prepare("SELECT * 
                                    FROM rai_block_medic bm
                                    JOIN rai_block rb on rb.blo_id = bm.blo_id
                                    WHERE bm_id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $id
   * @param $spec
   * @param $db
   * @return stdClass
   */
  public function getByMedic($id, $spec, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    $stmt = $db->Prepare("SELECT bm_id FROM rai_block_medic WHERE med_id = ? AND prs_id = ? AND bm_status IS TRUE");
    $stmt->bind_param("ii", $id, $spec);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    unset($db);
    return $this->get($row['bm_id']);
  }
}
