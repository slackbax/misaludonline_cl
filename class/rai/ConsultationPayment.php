<?php

class ConsultationPayment
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

    $stmt = $db->Prepare("SELECT * FROM rai_consultation_payment WHERE cpa_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $cons
   * @param $order
   * @param $date
   * @param $amount
   * @param $fee
   * @param $balance
   * @param $db
   * @return array
   */
  public function set($cons, $order, $date, $amount, $fee, $balance, $db = null): array
  {
    if (is_null($db)) $db = new ConnectRAI();

    try {
      $stmt = $db->Prepare("INSERT INTO rai_consultation_payment (con_id, cpa_order, cpa_date, cpa_amount, cpa_fee, cpa_balance) VALUES (?, ?, ?, ?, ?, ?)");

      if (!$stmt) :
        throw new Exception("La inserción del pago falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) :
        throw new Exception("La inserción del pago falló en su binding.");
      endif;

      if (!$stmt->execute()) :
        throw new Exception("La inserción del pago falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}