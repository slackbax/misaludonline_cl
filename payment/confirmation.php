<?php
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/src/constants.php';
require $BASEDIR . '/src/functions.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';
require $BASEDIR . '/class/rai/ConnectRAI.php';
require $BASEDIR . '/class/main/ConnectMAIN.php';
require $BASEDIR . '/class/rai/Consultation.php';
require $BASEDIR . '/class/rai/ConsultationPayment.php';
require $BASEDIR . '/class/rai/ConsultationEvolution.php';
require $BASEDIR . '/class/main/Patient.php';
require $BASEDIR . '/class/main/People.php';
require $BASEDIR . '/class/main/Medic.php';
require $BASEDIR . '/class/main/MedicSpecialty.php';
require $BASEDIR . '/class/main/ProfessionSpecialty.php';
require $BASEDIR . '/vendor/autoload.php';

$_con = new ConnectRAI();
$_cns = new Consultation();
$_cpa = new ConsultationPayment();
$_ce = new ConsultationEvolution();
$_pat = new Patient();
$_ppl = new People();
$_med = new Medic();
$_msp = new MedicSpecialty();
$_psp = new ProfessionSpecialty();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->setLanguage('es', $BASEDIR . '/vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php');

try {
  if (!isset($_POST["token"])) {
    throw new Exception("No se recibio el token", 1);
  }
  $token = filter_input(INPUT_POST, 'token');
  $params = array(
    "token" => $token
  );
  $serviceName = "payment/getStatus";
  $flowApi = new FlowApi();
  $response = $flowApi->send($serviceName, $params);
  $conid = base64_decode($response['optional']['ID']);

  $ins = $_cpa->set($conid, $response['flowOrder'], $response['paymentData']['date'], $response['paymentData']['amount'], $response['paymentData']['fee'], $response['paymentData']['balance']);
  if (!$ins['estado']) throw new Exception('No pudo registrarse la consulta.');
  $ins1 = $_ce->setInactive($conid);
  if (!$ins1['estado']) throw new Exception("No pudo actualizarse el estado de la consulta.");
  $ins2 = $_ce->set(1, $conid, 3, 'PAGO EN LINEA');
  if (!$ins2['estado']) throw new Exception('No pudo registrarse el estado de la consulta.');

  $cons = $_cns->get($conid);
  $date = formatter_date($cons->con_date);
  $time = formatter_time($cons->con_hourstart) . ' - ' . formatter_time($cons->con_hourend);
  $pat = $_pat->get($cons->pat_id);
  $ppl = $_ppl->get($pat->pe_id);
  $pacie = $ppl->pe_fullname . ' ' . $ppl->pe_fathername . ' ' . $ppl->pe_mothername;
  $email = $ppl->pe_email;

  $med = $_med->get($cons->med_id);
  $ppl = $_ppl->get($med->pe_id);
  $medic = $ppl->pe_fullname . ' ' . $ppl->pe_fathername . ' ' . $ppl->pe_mothername;

  $msp = $_msp->getByMedic($cons->med_id);
  $psp = $_psp->get($msp->prs_id);
  $espc = $psp->prs_name;

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
  $mail->Subject = 'Notificaciones MiSaludOnline.cl | Enlace de consulta médica en línea';
  $mail->isHTML();

  $html = null;
  $html .= '<p><img style="width:180px" alt="misaludonline.cl" src="https://www.misaludonline.cl/scheduling/dist/img/misaludonline.png"></p>';
  $html .= '<br><br><p><span style="font-size:18px;font-weight:bold">Estimado(a) ' . $pacie . ',</span></p>';
  $html .= '<br><br>Queremos recordarte tu próxima consulta médica.';
  $html .= '<br><br>Profesional: ' . $medic;
  $html .= '<br>Especialidad: ' . $espc;
  $html .= '<br>Fecha: ' . $date;
  $html .= '<br>Hora: ' . $time;

  if ($cons->cont_id == 2):
    $html .= '<br><br>Para iniciar tu teleconsulta, haz click en el siguiente enlace. Te recomendamos conectarte cinco minutos antes para que tu atención se lleve a cabo oportunamente.';
    $html .= '<br><a href="https://www.alercesoftware.cl/meeting/' . $response['optional']['ID'] . '/" target="_blank" rel="noopener noreferrer">Conectarse a la teleconsulta</a>';
  endif;

  $html .= "<br><br>Muchas gracias por atenderse con nosotros.";
  $html .= "<br><br><strong>MiSaludOnline.cl - Atención médica digital</strong>";
  $mail->Body = $html;
  $mail->AltBody = '¡Para visualizar correctamente el mensaje, por favor utilice un visor de correos compatible con HTML!';

  $send = $mail->send();
  if (!$send) throw new Exception('No se pudo enviar el correo electrónico. {' . $mail->ErrorInfo . '}');

} catch (Exception $e) {
  echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}