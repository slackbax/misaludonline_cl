<?php

class InternalExamsReports
{
  public function get_data($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT * FROM ris_internal_exams_reports WHERE ier_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function data_report($exam): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT ier_id FROM ris_internal_exams_reports WHERE ier_exam = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $exam);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $this->get_data($data['ier_id']);
  }
}
