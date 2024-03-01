<?php

class Consultation
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

    $stmt = $db->Prepare("SELECT * FROM rai_consultation WHERE con_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $med
   * @param $dateini
   * @param $dateend
   * @param $db
   * @return array
   */
  public function getBetweenDates($med, $dateini, $dateend, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    $stmt = $db->Prepare("SELECT c.con_id FROM rai_consultation c 
                                    JOIN rai_consultation_evolution rce on c.con_id = rce.con_id
                                    WHERE med_id = ? AND CAST(CONCAT(con_date, ' ', con_hourstart) AS datetime) BETWEEN ? AND ? 
                                    AND cos_id <> 8 AND con_status IS TRUE AND coe_status IS TRUE");
    $stmt->bind_param("iss", $med, $dateini, $dateend);
    $stmt->execute();
    $result = $stmt->get_result();
    $lista = [];

    while ($row = $result->fetch_assoc()):
      $lista[] = $this->get($row['con_id']);
    endwhile;

    unset($db);
    return $lista;
  }

  /**
   * @param $tcon
   * @param $pat
   * @param $med
   * @param $pre
   * @param $pla
   * @param $code
   * @param $iv
   * @param $date
   * @param $hini
   * @param $hter
   * @param $reason
   * @param $us_ins
   * @param $us_upd
   * @param $db
   * @return array
   */
  public function set($tcon, $pat, $med, $pre, $pla, $code, $iv, $date, $hini, $hter, $reason, $us_ins, $us_upd, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    try {
      $stmt = $db->Prepare("INSERT INTO rai_consultation (cont_id, pat_id, med_id, pre_id, pla_id, con_enckey, con_iv, con_date, con_hourstart, con_hourend, con_reason, con_createat, us_create_id, con_updateat, us_update_id, con_status)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, CURRENT_TIMESTAMP, ?, TRUE)");

      if (!$stmt) throw new Exception("La inserción del agendamiento falló en su preparación.");

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) throw new Exception("La inserción del agendamiento falló en su binding.");
      if (!$stmt->execute()) throw new Exception("La inserción del agendamiento falló en su ejecución.");

      $result = array('estado' => true, 'conid' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }

  /**
   * @param $code
   * @param $id
   * @param $db
   * @return array
   */
  public function setCode($code, $id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectRAI();
    endif;

    try {
      $stmt = $db->Prepare("UPDATE rai_consultation SET con_code = UPPER(?) WHERE con_id = ?");
      if (!$stmt):
        throw new Exception("El registro del código falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);
      if (!$bind):
        throw new Exception("El registro del código falló en su binding.");
      endif;

      if (!$stmt->execute()):
        throw new Exception("El registro del código falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}
