<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title . TIT ?></title>
  <?php include $BASEDIR . 'html/system/includes/favicon.php' ?>
  <?php include $BASEDIR . 'html/system/includes/styles.php' ?>
</head>

<body class="hold-transition layout-top-nav layout-footer-fixed">
<div class="wrapper">
  <?php include $BASEDIR . 'html/system/includes/preloader.php' ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <a href="https://centromenteverde.com">
              <img src="dist/img/misaludonline.png" alt="MiSaludOnline.cl" style="width: 200px">
              <img src="dist/img/menteverde.png" alt="CentroMenteVerde.cl" style="width:200px;margin-left:20px">
            </a>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-md-8 offset-md-2">
            <div class="bs-stepper">
              <div class="bs-stepper-header d-none d-md-flex" role="tablist">
                <div class="step" data-target="#patient-data">
                  <button type="button" class="step-trigger" role="tab" aria-controls="patient-data" id="patient-data-trigger">
                    <span class="bs-stepper-circle">1</span>
                    <span class="bs-stepper-label">Tus datos de atención</span>
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#search-hour">
                  <button type="button" class="step-trigger" role="tab" aria-controls="search-hour" id="search-hour-trigger">
                    <span class="bs-stepper-circle">2</span>
                    <span class="bs-stepper-label">Busca tu profesional</span>
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#select-hour">
                  <button type="button" class="step-trigger" role="tab" aria-controls="select-hour" id="select-hour-trigger">
                    <span class="bs-stepper-circle">3</span>
                    <span class="bs-stepper-label">Selecciona tu hora</span>
                  </button>
                </div>
              </div>

              <form id="fReserve" role="form">
                <div class="bs-stepper-content pr-0 pl-0 pb-0">
                  <div id="patient-data" class="content" role="tabpanel" aria-labelledby="patient-data-trigger">
                    <div class="card card-primary">
                      <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-user mr-2"></i>Tus datos de atención</h3>
                      </div>

                      <div class="card-body">
                        <h5 class="text-center mb-5">Ingresa los datos del paciente para reservar la hora</h5>
                        <div class="form-group col-md-10 offset-md-1 mb-5">
                          <label for="rut-pac">RUT</label>
                          <input id="rut-pac" name="rut" class="form-control form-control-border border-width-2" type="text" placeholder="Ingresa RUT del paciente">
                        </div>

                        <div class="form-group col-md-10 offset-md-1" id="select-prevision">
                          <label for="prevision-pac">Previsión</label>
                          <select id="prevision-pac" name="prevision" class="form-control form-control-border border-width-2" data-placeholder="Selecciona tu previsión"></select>
                        </div>
                      </div>

                      <div class="card-footer">
                        <div class="row">
                          <div class="col-12 text-right">
                            <button id="data-next" disabled type="button" class="btn btn-lg btn-outline-success" onclick="stepper.next()">Ingresar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div id="search-hour" class="content" role="tabpanel" aria-labelledby="search-hour-trigger">
                    <div class="card card-primary">
                      <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-search mr-2"></i>Busca tu profesional</h3>
                      </div>

                      <div class="card-body">
                        <h5 class="text-center mb-4">¿Qué tipo de atención buscas?</h5>
                        <div class="btn-group col-md-8 offset-md-2 mb-5">
                          <button type="button" id="show-spec" class="btn btn-dark">Especialidad</button>
                          <button type="button" id="show-prof" class="btn btn-outline-dark">Profesional</button>
                        </div>

                        <div style="min-height: 120px">
                          <div class="mb-5" id="div-spec">
                            <div class="form-group col-md-10 offset-md-1" id="select-specialty">
                              <label for="name-spec">Especialidad</label>
                              <select id="name-spec" name="specialty" class="form-control form-control-border border-width-2" data-placeholder="Selecciona la especialidad"></select>
                            </div>

                            <div id="div-subspec">
                              <div class="form-group col-md-10 offset-md-1" id="select-subspecialty">
                                <label for="name-subspec">Sub-especialidad</label>
                                <select id="name-subspec" name="subspecialty" class="form-control form-control-border border-width-2" data-placeholder="Selecciona la subespecialidad"></select>
                              </div>
                            </div>
                          </div>

                          <div id="div-prof">
                            <div class="form-group col-md-10 offset-md-1" id="select-professional">
                              <label for="name-prof">Profesional</label>
                              <select id="name-prof" name="professional" class="form-control form-control-border border-width-2" data-placeholder="Selecciona el profesional"></select>
                            </div>

                            <div id="div-prspec">
                              <div class="form-group col-md-10 offset-md-1" id="select-prspecialty">
                                <label for="name-prspec">Especialidad</label>
                                <select id="name-prspec" name="specialty" class="form-control form-control-border border-width-2" data-placeholder="Selecciona la especialidad"></select>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group clearfix col-md-10 offset-md-1">
                          <div class="icheck-primary d-inline">
                            <input type="checkbox" id="check-days" checked>
                            <label for="check-days">Buscar hora para los próximos 7 días
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="card-footer">
                        <div class="row">
                          <div class="col-6">
                            <button type="button" class="btn btn-lg btn-light" onclick="stepper.previous()">
                              <i class="fa fa-chevron-left mr-2"></i>Volver
                            </button>
                          </div>
                          <div class="col-6 text-right">
                            <button id="search-next" disabled type="button" class="btn btn-lg btn-outline-success" onclick="stepper.next()">Buscar hora</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div id="select-hour" class="content" role="tabpanel" aria-labelledby="select-hour-trigger">
                    <div class="card card-primary">
                      <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-calendar-alt mr-2"></i>Selecciona tu hora</h3>
                      </div>

                      <div class="card-body mb-3">
                        <div class="rescalendar" id="days-calendar"></div>
                        <div class="form-group col-md-10 offset-md-1" id="week-calendar-container">
                          <div id="week-calendar"></div>
                          <div class="col-md-6 offset-md-3 mt-3">
                            <button id="search-date" type="button" class="btn btn-block btn-outline-dark">Buscar hora</button>
                          </div>
                        </div>
                      </div>

                      <div class="card-body" id="search-results">
                        <input type="hidden" id="pat_id" name="pat_id">
                        <input type="hidden" id="med_id" name="med_id">
                        <input type="hidden" id="spec_id" name="spec_id">
                        <input type="hidden" id="med_amount" name="med_amount">
                        <input type="hidden" id="slot_data" name="slot_data">
                        <input type="hidden" id="pat_name" name="pat_name">
                        <input type="hidden" id="pat_lastnamep" name="pat_lastnamep">
                        <input type="hidden" id="pat_lastnamem" name="pat_lastnamem">
                        <input type="hidden" id="pat_email" name="pat_email">
                        <input type="hidden" id="pat_fnac" name="pat_fnac">
                        <input type="hidden" id="pat_tel" name="pat_tel">
                        <h5 id="search-result-date" class="mb-4"></h5>
                        <div id="search-objects"></div>
                      </div>

                      <div class="card-footer">
                        <div class="row">
                          <div class="col-6">
                            <button id="back-date" type="button" class="btn btn-lg btn-light" onclick="stepper.previous()">
                              <i class="fa fa-chevron-left mr-2"></i>Volver
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-4 offset-md-4">
            <a class="btn btn-block btn-outline-success" href="https://centromenteverde.com"><i class="fa fa-home mr-2"></i>Volver a CENTRO MENTE VERDE</a>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<div id="confirmation-window" class="card card-primary">
  <div class="card-header">
    <h5><i class="fa fa-check mr-2"></i>Resumen de tu hora</h5>
    <button type="button" id="close-confirmation-window" class="close">×</button>
  </div>
  <div class="card-body bg-primary">
    <div id="cons-resume"></div>
    <div class="mt-5">
      <h5>Tus datos</h5>
      <div class="row">
        <div class="form-group col-md-6">
          <label for="name-pac">Nombres *</label>
          <input id="name-pac" class="form-control form-control-border border-width-2" type="text" placeholder="Tu nombre">
        </div>
        <div class="form-group col-md-6">
          <label for="fname-pac">Primer apellido *</label>
          <input id="fname-pac" class="form-control form-control-border border-width-2" type="text" placeholder="Tu primer apellido">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label for="sname-pac">Segundo apellido</label>
          <input id="sname-pac" class="form-control form-control-border border-width-2" type="text" placeholder="Tu segundo apellido">
        </div>
        <div class="form-group col-md-6">
          <label for="fnac-pac">Fecha nacimiento *</label>
          <input id="fnac-pac" class="form-control form-control-border border-width-2" mask-date type="text" placeholder="dd/mm/yyyy">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label for="tel-pac">Teléfono *</label>
          <input id="tel-pac" class="form-control form-control-border border-width-2" mask-phone val-phone type="text" placeholder="9 9999 9999" autocomplete="off" maxlength="11">
        </div>
        <div class="form-group col-md-6">
          <label for="email-pac">E-mail *</label>
          <input id="email-pac" class="form-control form-control-border border-width-2" type="text" placeholder="Tu e-mail">
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="col-md-10 offset-md-1 mb-2">
      <button type="button" id="accept-data" class="btn btn-success btn-block btn-lg">Agendar mi hora</button>
    </div>
    <div class="col-md-10 offset-md-1 mb-2">
      <button type="button" id="keep-searching" class="btn btn-outline-secondary btn-block">Elegir otro horario</button>
    </div>
  </div>
</div>

<?php include $BASEDIR . 'html/system/includes/footer.php' ?>
<?php include $BASEDIR . 'html/system/includes/scripts.php' ?>
<script src="<?php echo $dmn ?>build/js/system/home.js?v=<?php echo time() ?>"></script>
</body>

</html>