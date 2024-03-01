<?php

class People
{
  /**
   * @param $id
   * @return stdClass
   */
  public function get($id): stdClass
  {
    $con = new ConnectMAIN();
    $query = "SELECT * FROM hm_people WHERE pe_id = ?";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->setToObject($data);
  }

  /**
   * @param $rut
   * @param $db
   * @return stdClass
   */
  public function getByRut($rut, $db = null): stdClass
  {
    if (is_null($db)) :
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT pe_id 
                                    FROM hm_people
                                    WHERE pe_rut = ?");
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (is_null($row)):
      $obj = new stdClass();
      $obj->pe_id = null;
    else:
      $obj = $this->get($row['pe_id']);
    endif;

    unset($db);
    return $obj;
  }

  /**
   * @param $gen
   * @param $com
   * @param $cis
   * @param $rut
   * @param $isrut
   * @param $name
   * @param $fname
   * @param $mname
   * @param $sname
   * @param $email
   * @param $email_alt
   * @param $phone
   * @param $bdate
   * @param $addr
   * @param $us_ins
   * @param $us_upd
   * @param $db
   * @return array
   */
  public function set($gen, $com, $cis, $rut, $isrut, $name, $fname, $mname, $sname, $email, $email_alt, $phone, $bdate, $addr, $us_ins, $us_upd, $db = null): array
  {
    if (is_null($db)) :
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("INSERT INTO hm_people (gen_id, com_id, civs_id, pe_rut, pe_isrut, pe_fullname, pe_fathername, pe_mothername, pe_socialname, pe_email, pe_email_alt, pe_phone, 
                                        pe_birthdate, pe_address, pe_createat, us_create_id, pe_updateat, us_update_id, pe_status) 
                                        VALUES (?, ?, ?, UPPER(?), ?, UPPER(?), UPPER(?), UPPER(?), UPPER(?), ?, ?, UPPER(?), ?, UPPER(?), CURRENT_TIMESTAMP, ?, CURRENT_TIMESTAMP, ?, TRUE)");

      if (!$stmt) :
        throw new Exception("La inserción de la persona falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);
      if (!$bind) :
        throw new Exception("La inserción de la persona falló en su binding.");
      endif;

      if (!$stmt->execute()) :
        throw new Exception("La inserción de la persona falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}
