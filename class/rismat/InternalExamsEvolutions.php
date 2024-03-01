<?php

class InternalExamsEvolutions
{
  public function get_data($id): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT * FROM ris_internal_exams_evolutions WHERE iev_id = ?";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }

  public function approval_evolution($exam): stdClass
  {
    $con = new ConnectRIS();
    $query = "SELECT iev_created_at FROM ris_internal_exams_evolutions JOIN ris_exam_states ON est_id = iev_exam_state WHERE iev_exam = ? AND iev_exam_state = 10 ORDER BY iev_id DESC LIMIT 1";
    $stmt = $con->prepare_query($query);
    $stmt->bind_param('i', $exam);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $con->set_to_object($data);
  }
}
