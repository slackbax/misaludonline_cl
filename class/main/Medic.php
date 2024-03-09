<?php

class Medic
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

    $query = "SELECT * FROM hm_medic m JOIN hm_people hp on hp.pe_id = m.pe_id WHERE med_id = ?";
    $stmt = $db->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $db->setToObject($data);
  }

  /**
   * @param $db
   * @return array
   */
  public function select($db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $query = "SELECT med_id, CONCAT(pe_fullname, ' ', pe_fathername, ' ', pe_mothername) AS med_name 
                FROM hm_medic m 
                JOIN hm_people hp on hp.pe_id = m.pe_id
                WHERE med_status IS TRUE AND med_telemedicine IS TRUE";
    $stmt = $db->Prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['med_id'];
      $obj->text = ucwords(mb_strtolower($data['med_name'], 'utf-8'));
      $array[] = $obj;
    }
    return $array;
  }

  public function getById($id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $query = "SELECT m.med_id, CONCAT(pe_fullname, ' ', pe_fathername, ' ', pe_mothername) AS med_name, prs_name AS specialty, pss_name AS subspecialty
                FROM hm_medic m 
                JOIN hm_people hp ON hp.pe_id = m.pe_id
                JOIN hm_medic_specialty hms ON m.med_id = hms.med_id
                LEFT JOIN hm_medic_subspecialty mss ON m.med_id = mss.med_id 
                LEFT JOIN hm_prof_subespecialty pss ON mss.pss_id = pss.pss_id
                JOIN hm_prof_specialty ps ON hms.prs_id = ps.prs_id
                WHERE m.med_id = ?";
    $stmt = $db->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['med_id'];
      $obj->name = ucwords(mb_strtolower($data['med_name'], 'utf-8'));
      if ($data['specialty'] == 'CIRUJANO')
        $obj->specialty = 'Medicina General';
      else
        $obj->specialty = ucwords(mb_strtolower($data['specialty'], 'utf-8'));
      $obj->subspecialty = !empty($data['subspecialty']) ? ucwords(mb_strtolower($data['subspecialty'], 'utf-8')) : '';
      $array[] = $obj;
    }
    return $array;
  }

  /**
   * @param $id
   * @param $subid
   * @param $db
   * @return array
   */
  public function getBySpecialty($id, $subid = null, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $data = [$id];
    $subq = '';
    if (!is_null($subid)) {
      $data[] = $subid;
      $subq = ' AND mss.pss_id = ?';
    }

    $query = "SELECT m.med_id, CONCAT(pe_fullname, ' ', pe_fathername, ' ', pe_mothername) AS med_name, prs_name AS specialty, pss_name AS subspecialty
                FROM hm_medic m 
                JOIN hm_people hp ON hp.pe_id = m.pe_id
                JOIN hm_medic_specialty hms ON m.med_id = hms.med_id
                LEFT JOIN hm_medic_subspecialty mss ON m.med_id = mss.med_id 
                LEFT JOIN hm_prof_subespecialty pss ON mss.pss_id = pss.pss_id
                JOIN hm_prof_specialty ps ON hms.prs_id = ps.prs_id
                WHERE hms.prs_id = ? $subq";
    $stmt = $db->Prepare($query);
    $stmt->bind_param(str_repeat('i', count($data)), ...$data);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['med_id'];
      $obj->name = ucwords(mb_strtolower($data['med_name'], 'utf-8'));
      if ($data['specialty'] == 'CIRUJANO')
        $obj->specialty = 'Medicina General';
      else
        $obj->specialty = ucwords(mb_strtolower($data['specialty'], 'utf-8'));
      $obj->subspecialty = !empty($data['subspecialty']) ? ucwords(mb_strtolower($data['subspecialty'], 'utf-8')) : '';
      $array[] = $obj;
    }
    return $array;
  }
}