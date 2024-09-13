<?php
extract($_POST);

$BASEDIR = explode('ajax', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/rai/ConnectRAI.php';
require $BASEDIR . 'class/rai/Consultation.php';
require $BASEDIR . 'class/main/Patient.php';
require $BASEDIR . 'class/main/People.php';
require $BASEDIR . 'class/main/Medic.php';
require $BASEDIR . 'class/main/MedicSpecialty.php';
require $BASEDIR . 'class/main/ProfessionSpecialty.php';
require $BASEDIR . 'vendor/autoload.php';

$_cl1 = new Consultation();
$_cl2 = new Patient();
$_cl3 = new People();
$_cl4 = new Medic();
$_cl5 = new MedicSpecialty();
$_cl6 = new ProfessionSpecialty();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->setLanguage('es', $BASEDIR . 'vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php');

try {
  $conid = base64_decode($conid);
  $specid = base64_decode($specid);

  $ins1 = $_cl1->get($conid);
  $date = formatter_date($ins1->con_date);
  $time = formatter_time($ins1->con_hourstart) . ' - ' . formatter_time($ins1->con_hourend);

  $ins2 = $_cl2->get($ins1->pat_id);
  $ins3 = $_cl3->get($ins2->pe_id);
  $pacie = $ins3->pe_fullname . ' ' . $ins3->pe_fathername . ' ' . $ins3->pe_mothername;
  $email = $ins3->pe_email;

  $ins4 = $_cl4->get($ins1->med_id);
  $ins5 = $_cl3->get($ins4->pe_id);
  $medic = $ins5->pe_fullname . ' ' . $ins5->pe_fathername . ' ' . $ins5->pe_mothername;

  $ins6 = $_cl5->getByMedic($ins1->med_id);
  $int7 = $_cl6->get($ins6->prs_id);
  $espc = $int7->prs_name;

  $code = base64_encode($conid);
  $specode = base64_encode($specid);

  // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = MAIL_USER;
  $mail->Password = MAIL_PASSWORD;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  $mail->setFrom(MAIL_USER, 'MiSaludOnline.cl');
  $mail->addAddress($email, $pacie);
  $mail->CharSet = 'utf-8';
  $mail->Subject = 'Notificaciones MiSaludOnline.cl | Confirmación de consulta médica';
  $mail->isHTML();
  $html = null;
  $html .= '<p><img style="width:180px" alt="misaludonline.cl" src="https://www.misaludonline.cl/scheduling/dist/img/misaludonline.png"></p>';
  $html .= '<br><br><p><span style="font-size:18px;font-weight:bold">Estimado(a) ' . $pacie . ',</span></p>';
  $html .= '<br><br>Queremos recordarte tu próxima consulta médica.';
  $html .= '<br><br>Profesional: ' . $medic;
  $html .= '<br>Especialidad: ' . $espc;
  $html .= '<br>Fecha: ' . $date;
  $html .= '<br>Hora: ' . $time;
  $html .= '<br><br>Para pagar o cancelar tu consulta médica, selecciona la opción correspondiente. Recuerda que si ya pagaste tu consulta en línea, ésta se confirma automáticamente:';
  $html .= '<br><a href="https://www.misaludonline.cl/scheduling/payment/index.php?id=' . $code . '&spec=' . $specode . '" target="_blank" rel="noopener noreferrer">Pagar mi consulta</a> | <a href="https://rai.health/confirm-consultation/' . $code . '/0/" target="_blank" rel="noopener noreferrer">No podré asistir</a>';

  $html .= "<br><br>Muchas gracias por atenderse con nosotros.";
  $html .= "<br><br><strong>MiSaludOnline.cl - Atención médica digital</strong>";
  $mail->Body = $html;
  $mail->AltBody = '¡Para visualizar correctamente el mensaje, por favor utilice un visor de correos compatible con HTML!';

  $send = $mail->send();
  if (!$send) throw new Exception('No se pudo enviar el correo electrónico. {' . $mail->ErrorInfo . '}');

  $msg = 'Se reenvió el correo electrónico de confirmación correctamente.';
  $response = ['res' => true, 'msg' => $msg];
  echo json_encode($response);
} catch (Exception $e) {
  $response = ['res' => false, 'msg' => $e->getMessage()];
  echo json_encode($response);
}
