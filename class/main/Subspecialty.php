<?php

class Subspecialty
{
  /**
   * @param $id
   * @return stdClass
   */
  public function get($id): stdClass
  {
    $con = new ConnectMAIN();
    $query = "SELECT * FROM hm_medic_subspecialty WHERE pss_id = ?";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->setToObject($data);
  }

  /**
   * @param $id
   * @return array
   */
  public function select($id): array
  {
    $con = new ConnectMAIN();
    $query = "SELECT DISTINCT s.pss_id, s.pss_name 
                FROM hm_prof_subespecialty s
                JOIN hm_medic_subspecialty hms on s.pss_id = hms.pss_id
                JOIN hm_medic hm on hms.med_id = hm.med_id
                WHERE s.prs_id = ? AND med_status IS TRUE AND med_telemedicine IS TRUE";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['pss_id'];
      $obj->text = str_replace(' Y ', ' y ', ucwords(mb_strtolower($data['pss_name'], 'utf-8')));
      $array[] = $obj;
    }
    return $array;
  }
}