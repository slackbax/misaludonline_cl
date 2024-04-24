<?php
$BASEDIR = explode('ajax', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/rai/ConnectRAI.php';
require $BASEDIR . 'class/main/People.php';
require $BASEDIR . 'class/main/Medic.php';
require $BASEDIR . 'class/main/Specialty.php';
require $BASEDIR . 'class/main/Subspecialty.php';
require $BASEDIR . 'class/rai/Schedule.php';
require $BASEDIR . 'class/rai/BlockMedic.php';
require $BASEDIR . 'class/rai/Consultation.php';
require $BASEDIR . 'class/rai/ConsultationBlock.php';

if (extract($_POST)) {
  $_con = new ConnectMAIN();
  $_conrai = new ConnectRAI();
  $_ppl = new People();
  $_med = new Medic();
  $_sp = new Specialty();
  $_ssp = new Subspecialty();
  $_sch = new Schedule();
  $_bmd = new BlockMedic();
  $_cos = new Consultation();
  $_cob = new ConsultationBlock();

  try {
    if (!empty($spec)) {
      $subspec = !empty($subspec) ? $subspec : null;
      $medic_data = $_med->getBySpecialty($spec, $subspec);
    }

    if (!empty($prof)) {
      $medic_data = $_med->getById($prof);
    }

    $data = [];
    foreach ($medic_data as $i => $med) {
      $schedule = $_sch->getByMedic($med->id);
      $date_time = strtotime($date);
      $week_day = date("w", $date_time);
      $sch_day = explode(',', str_replace(['[', ']'], '', $schedule->sch_day));
      if (!in_array($week_day, $sch_day))
        break;

      $hours = [];
      $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $schedule->sch_hourstart);
      $moving_date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $schedule->sch_hourstart);
      $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $schedule->sch_hourend);
      $today_date = new DateTime('now');
      $bm = $_bmd->getByMedic($med->id);
      $tmp = explode(':', $bm->blo_duration);
      $interval = ((int)$tmp[0] * 60) + (int)$tmp[1];
      if ($start_date > $today_date)
        $hours[] = $start_date->format('Y-m-d H:i:s');
      while ($moving_date < $end_date) {
        $moving_date->modify('+' . $interval . ' minutes');
        if ($moving_date > $today_date)
          $hours[] = $moving_date->format('Y-m-d H:i:s');
      }

      $del_last = array_pop($hours);
      $cons = $_cos->getBetweenDates($med->id, $start_date->format('Y-m-d H:i:s'), $end_date->format('Y-m-d H:i:s'));
      $arr_cons = [];
      foreach ($cons as $ci => $val) {
        $arr_cons[] = $val->con_date . ' ' . $val->con_hourstart;
      }

      $hours = array_diff($hours, $arr_cons);
      if (count($hours) == 0)
        break;

      foreach ($hours as $ih => $hour) {
        $cons_b = $_cob->getBySlot($med->id, $hour);
        if ($cons_b->cob_id !== null)
          unset($hours[$ih]);
      }
      if (count($hours) == 0)
        break;

      $item = new stdClass();
      $item->id = $med->id;
      $item->name = $med->name;
      $item->specialty = $med->specialty;
      if (!empty($med->subspecialty))
        $item->specialty .= ' - ' . $med->subspecialty;
      $item->date = $date;
      $item->amount = $schedule->sch_amount;
      $item->hours = formatDate($hours);
      $data[] = $item;
    }

    $response = ['res' => true, 'results' => $data, 'msg' => ''];
    echo json_encode($response);
  } catch (Exception $e) {
    $response = ['res' => false, 'title' => '<strong>¡Error!</strong><br>', 'msg' => $e->getMessage(), 'code' => $e->getCode()];
    echo json_encode($response);
  }
}

function formatDate($arr): array
{
  $result = [];
  foreach ($arr as $item) {
    $tmp = explode(':', explode(' ', $item)[1]);
    $result[] = $tmp[0] . ':' . $tmp[1];
  }
  return $result;
}
