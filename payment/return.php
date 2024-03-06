<?php
$BASEDIR = explode('payment', dirname(__FILE__))[0];
require $BASEDIR . '/src/settings.php';
require $BASEDIR . '/src/constants.php';
require $BASEDIR . '/src/functions.php';
require $BASEDIR . '/class/flow_cl/FlowApi.class.php';

try {
  //Recibe el token enviado por Flow
  if (!isset($_POST["token"])) {
    throw new Exception("No se recibio el token", 1);
  }
  $token = filter_input(INPUT_POST, 'token');
  $params = array(
    "token" => $token
  );
  //Indica el servicio a utilizar
  $serviceName = "payment/getStatus";
  $flowApi = new FlowApi();
  $response = $flowApi->send($serviceName, $params);
  ?>
  <!DOCTYPE html>
  <html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resumen de tu pago</title>
  <?php include 'favicon.php' ?>
  <?php include 'styles.php' ?>
</head>

<body class="hold-transition layout-top-nav layout-footer-fixed">
<div class="wrapper">
  <?php include $BASEDIR . '/html/system/includes/preloader.php' ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <a href="https://www.misaludonline.cl">
              <img src="/dist/img/misaludonline.png" alt="MiSaludOnline.cl" style="width: 200px">
            </a>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fa fa-user mr-2"></i>Resumen de tu transacción</h3>
              </div>

              <div class="card-body">
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="payment_id">ID Consulta</label>
                    <input id="payment_id" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo $response['optional']['ID'] ?>">
                  </div>
                  <div class="form-group col-12">
                    <label for="payment_spec">Especialista</label>
                    <input id="payment_spec" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo $response['optional']['Especialista'] ?>">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="payment_order">Número de orden</label>
                    <input id="payment_order" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo $response['commerceOrder'] ?>">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="payment_amount">Monto</label>
                    <input id="payment_amount" class="form-control form-control-border border-width-2" type="text" readonly value="$<?php echo number_format($response['amount'], 0, '', '.') ?>">
                  </div>
                  <div class="form-group col-12">
                    <label for="payment_subject">Por concepto de</label>
                    <input id="payment_subject" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo $response['subject'] ?>">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="payment_media">Medio de pago</label>
                    <input id="payment_media" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo $response['paymentData']['media'] ?>">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="payment_date">Fecha y hora de pago</label>
                    <input id="payment_date" class="form-control form-control-border border-width-2" type="text" readonly value="<?php echo formatter_date_hour($response['paymentData']['date']) ?>">
                  </div>
                </div>
                <p class="mt-5"><a href="https://www.misaludonline.cl">MiSaludOnline.cl</a> agradece tu preferencia!<br>Cualquier duda o consulta, dirígete al correo <a href="mailto:contacto@alercesoftware.cl">contacto@alercesoftware.cl</a></p>
                <p class="mt-5 text-gray font-italic"><span class="fw-bolder">MiSaludOnline.cl</span> &copy; <?php echo date('Y') ?> <a href="https://www.alercesoftware.cl/" target="_blank" class="text-primary fw-bolder">AlerceSoftware</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include 'scripts.php' ?>
</body>
  <?php
} catch (Exception $e) {
  echo "Error: " . $e->getCode() . " - " . $e->getMessage();
}
