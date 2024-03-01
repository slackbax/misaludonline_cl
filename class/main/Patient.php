<?php

class Patient
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

    $stmt = $db->Prepare("SELECT * 
                                    FROM hm_patient
                                    WHERE pat_id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $id
   * @param $med
   * @param $db
   * @return stdClass
   */
  public function getByPeopleMed($id, $med, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT pat_id 
                                    FROM hm_patient
                                    WHERE pe_id = ? AND med_id = ?");

    $stmt->bind_param("ii", $id, $med);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (is_null($row)):
      $obj = new stdClass();
      $obj->pat_id = null;
    else:
      $obj = $this->get($row['pat_id']);
    endif;

    unset($db);
    return $obj;
  }

  /**
   * @param $pe
   * @param $med
   * @param $code
   * @param $us_ins
   * @param $us_upd
   * @param $db
   * @return array
   */
  public function set($pe, $med, $code, $us_ins, $us_upd, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("INSERT INTO hm_patient (pe_id, med_id, pat_code, pat_createat, us_create_id, pat_updateat, us_update_id, pat_status)
                                        VALUES (?, ?, UPPER(?), CURRENT_TIMESTAMP, ?, CURRENT_TIMESTAMP, ?, TRUE)");

      if (!$stmt):
        throw new Exception("La inserción del paciente falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind):
        throw new Exception("La inserción del paciente falló en su binding.");
      endif;

      if (!$stmt->execute()):
        throw new Exception("La inserción del paciente falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }

  /**
   * @param $id
   * @param $med
   * @param $us_upd
   * @param $status
   * @param $db
   * @return array
   */
  public function update($med, $us_upd, $status, $id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("UPDATE hm_patient SET med_id = ?, pat_updateat = CURRENT_TIMESTAMP, us_update_id = ?, pat_status = ? WHERE pat_id = ?");

      if (!$stmt):
        throw new Exception("La actualización del paciente falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind):
        throw new Exception("La actualización del paciente falló en su binding.");
      endif;

      if (!$stmt->execute()):
        throw new Exception("La actualización del paciente falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}
