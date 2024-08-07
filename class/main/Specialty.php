<?php

class Specialty
{
  /**
   * @param $id
   * @return stdClass
   */
  public function get($id): stdClass
  {
    $con = new ConnectMAIN();
    $query = "SELECT * FROM hm_medic_specialty WHERE prs_id = ?";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->setToObject($data);
  }

  /**
   * @return array
   */
  public function select(): array
  {
    $con = new ConnectMAIN();
    $query = "SELECT DISTINCT hms.prs_id, prs_name 
                FROM hm_prof_specialty s
                JOIN rai_health.rai_block_medic hms on s.prs_id = hms.prs_id
                JOIN hm_medic hm on hms.med_id = hm.med_id
                WHERE med_status IS TRUE AND med_telemedicine IS TRUE";
    $stmt = $con->Prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['prs_id'];
      if ($data['prs_name'] == 'CIRUJANO'):
        $obj->text = 'Medicina General';
      else:
        $obj->text = str_replace(' Y ', ' y ', ucwords(mb_strtolower($data['prs_name'], 'utf-8')));
      endif;
      $array[] = $obj;
    }
    return $array;
  }

  /**
   * @param $id
   * @return array
   */
  public function prof_select($id): array
  {
    $con = new ConnectMAIN();
    $query = "SELECT DISTINCT hms.prs_id, prs_name 
                FROM hm_prof_specialty s
                JOIN rai_health.rai_block_medic hms on s.prs_id = hms.prs_id
                JOIN hm_medic hm on hms.med_id = hm.med_id
                WHERE hms.med_id = ? AND med_status IS TRUE AND med_telemedicine IS TRUE";
    $stmt = $con->Prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['prs_id'];
      if ($data['prs_name'] == 'CIRUJANO'):
        $obj->text = 'Medicina General';
      else:
        $obj->text = str_replace(' Y ', ' y ', ucwords(mb_strtolower($data['prs_name'], 'utf-8')));
      endif;
      $array[] = $obj;
    }
    return $array;
  }
}