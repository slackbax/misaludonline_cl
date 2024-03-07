<?php
$subd = ($_SERVER["HTTP_HOST"] == 'www.misaludonline.cl' or $_SERVER["HTTP_HOST"] == 'misaludonline.cl') ? '..' : '';
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/src/constants.php';
require $BASEDIR . '/src/functions.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';
require $BASEDIR . 'class/main/ConnectMAIN.php';
require $BASEDIR . 'class/rai/ConnectRAI.php';
require $BASEDIR . 'class/rai/Consultation.php';
require $BASEDIR . 'class/rai/Schedule.php';
require $BASEDIR . 'class/main/People.php';
require $BASEDIR . 'class/main/Patient.php';
require $BASEDIR . 'class/main/Medic.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proceso de pago</title>
  <?php include 'favicon.php' ?>
  <?php include 'styles.php' ?>
</head>

<body class="hold-transition layout-top-nav layout-footer-fixed">
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <a href="https://www.misaludonline.cl">
              <img src="<?php echo $subd ?>/dist/img/misaludonline.png" alt="MiSaludOnline.cl" style="width: 200px">
            </a>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container">
        <?php
        if (extract($_GET)) {
          $_con = new ConnectMAIN();
          $_conrai = new ConnectRAI();
          $_cns = new Consultation();
          $_ppl = new People();
          $_pat = new Patient();
          $_med = new Medic();
          $_sch = new Schedule();

          $id = base64_decode($id);
          $con = $_cns->get($id);
          $med = $_med->get($con->med_id);
          $sch = $_sch->getByMedic($con->med_id);
          $pat = $_pat->get($con->pat_id);
          $ppl = $_ppl->get($pat->pe_id);

          $optional = array(
            'ID' => base64_encode($id),
            'Especialista' => $med->pe_fullname . ' ' . $med->pe_fathername
          );
          $optional = json_encode($optional);

          $params = array(
            'commerceOrder' => $id,
            'subject' => 'Pago de consulta',
            'currency' => 'CLP',
            'amount' => $sch->sch_amount,
            'email' => $ppl->pe_email,
            'urlConfirmation' => Config::get('BASEURL') . '/confirmation.php',
            'urlReturn' => Config::get('BASEURL') . '/return.php',
            'optional' => $optional
          );

          $serviceName = 'payment/create';

          try {
            $flowApi = new FlowApi;
            $response = $flowApi->send($serviceName, $params, 'POST');

            if (isset($response['token'])) {
              $redirect = $response['url'] . '?token=' . $response['token'];
              header("location:$redirect");
            } else {
              ?>
              <div class="row">
                <div class="col-md-8 offset-md-2">
                  <div class="alert alert-warning">
                    <h5><i class="fa fa-exclamation-triangle mr-2"></i>Atención!</h5>
                    <p class="mb-4">No es posible iniciar el proceso de pago. Es posible que la consulta ya haya sido pagada.</p>
                    <p class="mb-0 font-italic">Cod <?php echo $response['code'] ?></p>
                    <p class="font-italic"><?php echo $response['message'] ?></p>
                  </div>
                </div>
              </div>
              <?php
            }
          } catch (Exception $e) {
            echo $e->getCode() . ' - ' . $e->getMessage();
          }
        }
        ?>
      </div>
    </section>
  </div>
</div>
<?php include 'scripts.php' ?>
</body>
</html>
