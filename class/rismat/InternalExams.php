<?php

class InternalExams
{
  public function get_data($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT * FROM ris_internal_exams WHERE iex_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function associated_exams($person): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT COUNT(*) AS num FROM ris_internal_exams WHERE iex_person = ? AND iex_study IS NOT NULL";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $person);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function exams_table($person): array
  {
    $con = new ConnectRIS();
    $query = "SELECT iex_id, iex_date, iex_start, iex_created_at, ins_name, est_name, iev_exam_state,
                CONCAT(rad.per_names, ' ', rad.per_paternal_name, ' ', rad.per_maternal_name) AS radiologist 
              FROM ris_internal_exams 
                JOIN ris_machines ON mac_id = iex_machine 
                JOIN " . MAIN_DATABASE . ".alc_institutions ON mac_institution = ins_id 
                JOIN ris_modalities ON mod_id = mac_modality 
                JOIN ris_internal_exams_evolutions ON iev_exam = iex_id 
                JOIN ris_exam_states ON est_id = iev_exam_state 
                JOIN ris_radiologists ON rad_id = iex_radiologist 
                JOIN ris_users ON use_id = rad_user 
                JOIN " . MAIN_DATABASE . ".alc_people rad ON rad.per_id = use_person 
                WHERE iex_status IS TRUE AND iex_person = ? AND iev_status IS TRUE AND iex_study IS NOT NULL";
    $bind_types = 'i';
    $bind_values = [$person];
    $query .= " ORDER BY iex_date DESC, iex_start DESC";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param($bind_types, ...$bind_values);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $array[] = $con->set_to_object($data);
    }
    return $array;
  }

  public function benefits_exam($exam): array
  {
    $con = new ConnectRIS();
    $query = "SELECT ben_id, ben_code, ben_description FROM ris_internal_exams_benefits JOIN ris_benefits ON ben_id = ieb_benefit WHERE ieb_exam = ? AND ieb_status IS TRUE";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $exam);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = [];
    while ($data = $result->fetch_assoc()) {
      $obj = new stdClass();
      $obj->id = $data['ben_id'];
      $obj->code = $data['ben_code'];
      $obj->description = $data['ben_description'];
      $array[] = $obj;
    }
    return $array;
  }

  public function data_pdf($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT iex_study, iex_referring_str, iex_history, iex_created_at, ier_id, ier_title, ier_technique, ier_findings, ier_impression, per_document, CONCAT(per_names, ' ', per_paternal_name, ' ', per_maternal_name) AS fullname, per_birth, sex_name FROM ris_internal_exams JOIN " . MAIN_DATABASE . ".alc_people ON per_id = iex_person JOIN " . MAIN_DATABASE . ".alc_sexes ON sex_id = per_sex JOIN ris_internal_exams_reports ON ier_exam = iex_id WHERE iex_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function data_email($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT iev_exam_state, ier_notification, rad_rnpi, rad_specialty, rad_digital_signature, use_username, per_sex, per_document, per_is_rut, CONCAT(per_names, ' ', per_paternal_name, ' ', per_maternal_name) AS fullname FROM ris_internal_exams JOIN ris_internal_exams_evolutions ON iev_exam = iex_id JOIN ris_internal_exams_reports ON ier_exam = iex_id JOIN ris_radiologists ON rad_id = iex_radiologist JOIN ris_users ON use_id = rad_user JOIN " . MAIN_DATABASE . ".alc_people ON per_id = use_person WHERE iex_id = ? AND iev_status IS TRUE AND ier_status IS TRUE";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }
}
