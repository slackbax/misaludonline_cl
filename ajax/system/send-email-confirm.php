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
  $mail->Subject = 'Notificaciones MiSaludOnline.cl | Confirmar consulta médica';
  $mail->isHTML();
  $html = null;
  $html .= '<p style="text-align:center"><img style="width:180px" alt="RAI health" src="https://www.rai.health/dist/img/rai.png"></p>';
  $html .= '<br><br><p style="text-align:center"><span style="font-size:20px;font-weight:bold">Estimado(a) ' . $pacie . ',</span></p>';
  $html .= '<br><br>Queremos recordarte tu próxima consulta médica.';
  $html .= '<br><br>Profesional: ' . $medic;
  $html .= '<br>Especialidad: ' . $espc;
  $html .= '<br>Fecha: ' . $date;
  $html .= '<br>Hora: ' . $time;
  $html .= '<br><br>Para confirmar o cancelar tu consulta médica, selecciona la opción correspondiente. Recuerda que si ya pagaste tu consulta, ésta se confirma automáticamente:';
  $html .= '<br><a href="https://rai.health/confirm-consultation/' . $code . '/1/" target="_blank" rel="noopener noreferrer">Confirmo mi asistencia</a> | <a href="https://rai.health/confirm-consultation/' . $code . '/0/" target="_blank" rel="noopener noreferrer">No podré asistir</a>';

  if ($ins1->cont_id == 2):
    $html .= '<br><br>Para iniciar tu teleconsulta, haz click en el siguiente enlace. Te recomendamos conectarte cinco minutos antes para que tu atención se lleve a cabo oportunamente.';
    $html .= '<br><a href="https://www.alercesoftware.cl/meeting/' . $code . '/" target="_blank" rel="noopener noreferrer">Conectarse a la teleconsulta</a>';
  endif;

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
