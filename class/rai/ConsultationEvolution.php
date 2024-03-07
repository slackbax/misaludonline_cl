<?php

class ConsultationEvolution
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

    $stmt = $db->Prepare("SELECT * FROM rai_consultation_evolution WHERE coe_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $us
   * @param $state
   * @param $cons
   * @param $reason
   * @param $db
   * @return array
   */
  public function set($us, $cons, $state, $reason, $db = null): array
  {
    if (is_null($db)) $db = new ConnectRAI();

    try {
      $stmt = $db->Prepare("INSERT INTO rai_consultation_evolution (us_id, con_id, cos_id, coe_reason, coe_status) VALUES (?, ?, ?, UPPER(?), 1)");

      if (!$stmt) :
        throw new Exception("La inserción del estado falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) :
        throw new Exception("La inserción del estado falló en su binding.");
      endif;

      if (!$stmt->execute()) :
        throw new Exception("La inserción del estado falló en su ejecución.");
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
   * @param $db
   * @return array
   */
  public function setInactive($id, $db = null): array
  {
    if (is_null($db)) $db = new ConnectRAI();

    try {
      $stmt = $db->Prepare("UPDATE rai_consultation_evolution SET coe_status = FALSE WHERE con_id = ?");

      if (!$stmt) :
        throw new Exception("La actualización de los estados falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) :
        throw new Exception("La actualización de los estados falló en su binding.");
      endif;

      if (!$stmt->execute()) :
        throw new Exception("La actualización de los estados falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => 'OK');
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}
