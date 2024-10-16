<?php
$BASEDIR = explode('ajax', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/main/People.php';
require $BASEDIR . 'class/main/Medic.php';
require $BASEDIR . 'class/main/Patient.php';
require $BASEDIR . 'class/rai/ConnectRAI.php';
require $BASEDIR . 'class/rai/Consultation.php';
require $BASEDIR . 'class/rai/ConsultationEvolution.php';
require $BASEDIR . 'class/rai/ConsultationBlock.php';
require $BASEDIR . 'class/rai/BlockMedic.php';

if (extract($_POST)) {
  $_con = new ConnectMAIN();
  $_conrai = new ConnectRAI();
  $_ppl = new People();
  $_med = new Medic();
  $_pat = new Patient();
  $_cs = new Consultation();
  $_ce = new ConsultationEvolution();
  $_cob = new ConsultationBlock();
  $_bmd = new BlockMedic();

  try {
    $_conrai->autoCommit(FALSE);
    $code = generate_token(16);
    $iv = generate_token(16);
    $med = $_med->get($med_id);

    # Check for consultations in the same time slot
    $bm = $_bmd->getByMedic($med_id, $spec_id);
    $tmp = explode(':', $bm->blo_duration);
    $interval = ((int)$tmp[0] * 60) + (int)$tmp[1];

    $tmp = explode('_', $slot_data);
    $date = $tmp[0];
    $time = $tmp[1] . ':00';
    $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
    $datetime = DateTime::createFromFormat('H:i:s', $time);
    $datetime->modify('+' . $interval . ' minutes');
    $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $datetime->format('H:i:s'));
    $end_date->modify('-1 minute');

    $cons = $_cs->getBetweenDates($med_id, $start_date->format('Y-m-d H:i:s'), $end_date->format('Y-m-d H:i:s'));
    if (count($cons) > 0):
      throw new Exception('La hora seleccionada ya fue ocupada por otro paciente.');
    endif;

    # Check for consultation blocks in the same time slot
    $cons_b = $_cob->getBySlot($med_id, $start_date->format('Y-m-d H:i:s'));
    if ($cons_b->cob_id !== null):
      throw new Exception('La hora seleccionada no se encuentra disponible.');
    endif;

    # If patient not existing
    if (empty($pat_id)):
      $ins = $_ppl->set(null, null, null, $rut, 1, $pat_name, $pat_lastnamep, $pat_lastnamem, null, $pat_email, null, $pat_tel, $pat_fnac, null, $med->us_id, $med->us_id, $_con);

      if (!$ins['estado']):
        throw new Exception('Error al guardar los datos de la persona. ' . $ins['msg']);
      endif;

      $id = $ins['msg'];
    else:
      $ins = $_ppl->update($pat_name, $pat_lastnamep, $pat_lastnamem, $pat_email, $pat_fnac, $pat_tel, $pat_id, $_con);
      if (!$ins['estado']) throw new Exception('Error al actualizar los datos de la persona. ' . $ins['msg']);

      $id = $pat_id;
    endif;

    $chk = $_pat->getByPeopleMed($id, $med_id);

    if (is_null($chk->pat_id)):
      $ppl = $_ppl->get($id);
      $ins_pat = $_pat->set($id, $med_id, $code, $med->us_id, $med->us_id, $_con);
      if (!$ins_pat['estado']) throw new Exception('Error al guardar los datos del paciente. ' . $ins_pat['msg']);

      $ipatient = $ins_pat['msg'];
    else:
      $ins_pat = $_pat->update($med_id, $med->us_id, 1, $id, $_con);
      if (!$ins_pat['estado']) throw new Exception('Error al actualizar los datos del paciente. ' . $ins_pat['msg']);

      $ipatient = $chk->pat_id;
    endif;

    # Insert new consultation
    $ins = $_cs->set(2, $ipatient, $med_id, $prevision, null, $code, $iv, $tmp[0], $time, $datetime->format('H:i:s'), null, $med->us_id, $med->us_id, $_conrai);
    if (!$ins['estado']) throw new Exception('Error al guardar los datos del agendamiento. ' . $ins['msg']);

    # Insert new state
    $ins_s = $_ce->set($med->us_id, $ins['conid'], 1, null, $_conrai);
    if (!$ins_s['estado']) throw new Exception('Error al guardar los datos del estado del agendamiento. ' . $ins_s['msg']);

    # Insert consultation code
    $code = 'CM-' . $ins['conid'] . generate_token(8);
    $ins_code = $_cs->setCode($code, $ins['conid'], $_conrai);
    if (!$ins_code['estado']) throw new Exception('Error al guardar el código de la consulta. ' . $ins_code['msg']);

    $_conrai->Commit();
    $_conrai->autoCommit(TRUE);
    $response = array('type' => true, 'conid' => base64_encode($ins['conid']), 'specid' => base64_encode($spec_id));
    echo json_encode($response);
  } catch (Exception $e) {
    echo json_encode(['type' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
  }
}