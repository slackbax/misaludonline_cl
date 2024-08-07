$(document).ready(function () {
  let today = new Date(), checkRut = false, checkPrevision = false, selectedHour = '', selectedProf = ''

  const dd = String(today.getDate()).padStart(2, '0'), mm = String(today.getMonth() + 1).padStart(2, '0'),
    yyyy = today.getFullYear(), $dataNext = $('#data-next'), $searchNext = $('#search-next'), $rut = $('#rut-pac'),
    $prevision = $('#prevision-pac'), $specialty = $('#name-spec'), $div_subs = $('#div-subspec'),
    $subspecialty = $('#name-subspec'), $professional = $('#name-prof'), $div_specs = $('#div-prspec'), $prspecialty = $('#name-prspec'),
    $checkDays = $('#check-days'), $daysCalendar = $('#days-calendar'), $weekCalendar = $('#week-calendar'),
    $weekCalCont = $('#week-calendar-container'), $searchDate = $('#search-date'),
    $searchResDate = $('#search-result-date'), $backDate = $('#back-date'), $confirmWindow = $('#confirmation-window'),
    $acceptData = $('#accept-data'), $form = $('#fReserve'), $patientPhone = $('#tel-pac'),
    $patientEmail = $('#email-pac')

  today = dd + '/' + mm + '/' + yyyy

  $weekCalendar.datetimepicker({
    inline: true, sideBySide: false, format: 'L', minDate: moment()
  });

  $('#div-subspec, #div-prof, #div-prspec, #week-calendar-container, #search-results').hide()

  $.ajax({
    url: 'ajax/system/select-previsions.php', dataType: 'json', success: function (res) {
      if (res.error) {
        $prevision.empty().select2().val(null).trigger('change')
        console.error(res.message)
      } else {
        $prevision.empty().select2({
          data: res.select, dropdownParent: $('#select-prevision')
        }).val(null).trigger('change')
      }
    }
  })

  $.ajax({
    url: 'ajax/system/select-specialty.php', dataType: 'json', success: function (res) {
      if (res.error) {
        $specialty.empty().select2().val(null).trigger('change')
        console.error(res.message)
      } else {
        $specialty.empty().select2({
          data: res.select, dropdownParent: $('#select-specialty')
        }).val(null).trigger('change')
      }
    }
  })

  $.ajax({
    url: 'ajax/system/select-professional.php', dataType: 'json', success: function (res) {
      if (res.error) {
        $professional.empty().select2().val(null).trigger('change')
        console.error(res.message)
      } else {
        $professional.empty().select2({
          data: res.select, dropdownParent: $('#select-professional')
        }).val(null).trigger('change')
      }
    }
  })

  $rut.change(function () {
    $dataNext.prop('disabled', true)
  }).Rut({
    on_error: function () {
      Swal.fire({
        title: 'Error!', html: 'El RUT ingresado no es válido.', icon: 'error'
      })
      $rut.val('')
      checkRut = false
    }, on_success: function () {
      checkRut = true
      if (checkRut && checkPrevision) $dataNext.prop('disabled', false)

      $.ajax({
        url: 'ajax/system/get-people-data.php', type: 'post', dataType: 'json', data: {rut: $rut.val()}
      }).done(function (d) {
        if (d.pe_id !== null) {
          $('#pat_id').val(d.pe_id)
          $('#pat_name, #name-pac').val(d.pe_fullname).prop('readonly', true)
          $('#pat_lastnamep, #fname-pac').val(d.pe_fathername).prop('readonly', true)
          $('#pat_lastnamem, #sname-pac').val(d.pe_mothername).prop('readonly', true)
          $('#pat_email, #email-pac').val(d.pe_email)
          $('#pat_tel, #tel-pac').val(d.pe_phone)
          $('#pat_fnac').val(d.pe_birthdate)
          $('#fnac-pac').val(getDate(d.pe_birthdate)).prop('readonly', true)
        }
      })
    }, format_on: 'keyup'
  })

  $prevision.change(function () {
    if ($(this).val() === null) {
      checkPrevision = false
      $dataNext.prop('disabled', true)
    } else {
      checkPrevision = true
      if (checkRut && checkPrevision) $dataNext.prop('disabled', false)
    }
  })

  $specialty.change(function () {
    $div_subs.hide()
    if ($(this).val() !== null) {
      $.ajax({
        url: 'ajax/system/select-subspecialty.php',
        method: 'post',
        dataType: 'json',
        data: {id: $specialty.val()},
        success: function (res) {
          if (res.error) {
            $div_subs.hide()
            $subspecialty.empty().select2().val(null).trigger('change')
            console.error(res.message)
          } else {
            if (res.select.length > 0) {
              $div_subs.show()
              $subspecialty.empty().select2({
                data: res.select, dropdownParent: $('#select-subspecialty')
              }).val(null).trigger('change')
            } else {
              $div_subs.hide()
              $subspecialty.empty().select2().val(null).trigger('change')
            }
          }
        }
      })
    } else {
      $subspecialty.empty().select2().val(null).trigger('change')
    }
  })

  $professional.change(function () {
    $div_specs.hide()
    if ($(this).val() !== null) {
      $.ajax({
        url: 'ajax/system/select-prof-specialty.php',
        method: 'post',
        dataType: 'json',
        data: {id: $professional.val()},
        success: function (res) {
          if (res.error) {
            $div_specs.hide()
            $prspecialty.empty().select2().val(null).trigger('change')
            console.error(res.message)
          } else {
            if (res.select.length > 1) {
              $div_specs.show()
              $prspecialty.empty().select2({
                data: res.select, dropdownParent: $('#select-prspecialty')
              }).val(null).trigger('change')
            } else if (res.select.length === 1) {
              $div_specs.hide()
              $prspecialty.empty().select2({
                data: res.select, dropdownParent: $('#select-prspecialty')
              }).val(res.select[0].id).trigger('change')
            } else {
              $div_specs.hide()
              $prspecialty.empty().select2().val(null).trigger('change')
            }
          }
        }
      })
    } else {
      $prspecialty.empty().select2().val(null).trigger('change')
    }
  })

  $searchNext.click(function () {
    $('#search-results').hide()
    today = moment().format('DD/MM/YYYY')
    $daysCalendar.rescalendar({
      id: 'days-calendar', format: 'DD/MM/YYYY', calSize: 7, jumpSize: 8, locale: 'es', refDate: today, lang: {
        'today': 'Hoy'
      }
    })
    $weekCalendar.datetimepicker('date', moment())

    if ($checkDays.is(':checked')) {
      getData()
    }
  })

  $('#show-prof, #show-spec').click(function () {
    handleAttention(this)
  })

  $('#name-spec, #name-prof').change(function () {
    enableNext(this)
  })

  $checkDays.click(function () {
    if ($(this).is(':checked')) {
      $searchNext.html('Buscar hora')
      $daysCalendar.show()
      $weekCalCont.hide()
    } else {
      $searchNext.html('Buscar en calendario')
      $daysCalendar.hide()
      $weekCalCont.show()
    }
  })

  $weekCalendar.on('change.datetimepicker', ({date}) => {
    today = date.format('DD/MM/YYYY')
  })

  $searchDate.click(function () {
    $daysCalendar.rescalendar({
      format: 'DD/MM/YYYY', calSize: 7, jumpSize: 8, locale: 'es', refDate: today, lang: {
        'today': 'Hoy'
      }
    })
    $weekCalCont.hide()
    $daysCalendar.show()
    getData()
  })

  $backDate.click(function () {
    if (!$checkDays.is(':checked')) {
      $checkDays.trigger('click')
    }
  })

  $('#search-results').on('click', '.hour-slot', function () {
    let resHtml = ''
    const id = $(this).attr('id')
    $('.hour-slot').each(function () {
      $(this).removeClass('hour-selected')
    })
    let tmp = id.split('_')
    let hourFull = tmp[2] + ':' + tmp[3], selectedProf = '', selectedSpec = ''
    if (selectedHour !== hourFull) {
      selectedHour = hourFull
      selectedProf = tmp[0]
      selectedSpec = tmp[1]
      let consValue = $('#medamount_' + selectedProf).val()
      $('#med_id').val(selectedProf)
      $('#spec_id').val(selectedSpec)
      $('#med_amount').val(consValue)
      $('#slot_data').val($(this).data('hour'))
      $('#' + id).addClass('hour-selected')
      resHtml += '<p><i class="fa fa-user-md mr-2"></i>' + $('#medname_' + selectedProf).html() + '</p>'
      resHtml += '<p><i class="fa fa-calendar-alt mr-2"></i>' + $searchResDate.html() + ', ' + hourFull + ' hrs.</p>'
      resHtml += '<p><i class="fa fa-video mr-2"></i>' + $('#consname_' + selectedProf).html() + '</p>'
      resHtml += '<p><i class="fa fa-usd-circle mr-2"></i>Valor consulta <strong>$' + number_format(consValue, 0, '', '.') + '</strong></p>'
      $('#cons-resume').html(resHtml)
      $confirmWindow.fadeSlideLeft()
      $('.wrapper').addClass('noVisibility')
    } else {
      selectedHour = ''
    }
  })

  $('#close-confirmation-window, #keep-searching').click(function () {
    $('.middleDay').click()
    $confirmWindow.fadeSlideRight()
    $('.wrapper').removeClass('noVisibility')
    $('.hour-slot').each(function () {
      $(this).removeClass('hour-selected')
    })
    selectedHour = ''
    selectedProf = ''
  })

  $patientPhone.change(function () {
    $('#pat_tel').val($(this).val())
  })

  $patientEmail.change(function () {
    $('#pat_email').val($(this).val())
  })

  $acceptData.click(function () {
    $form.submit()
  })

  function handleAttention(obj) {
    const id = $(obj).attr('id').split('-').pop(), otherId = id === 'spec' ? 'prof' : 'spec'
    $('#div-' + otherId).hide()
    $('#div-' + id).show(500)
    $(obj).removeClass('btn-outline-dark').addClass('btn-dark')
    $('#show-' + otherId).removeClass('btn-dark').addClass('btn-outline-dark')
    $('#name-' + otherId).val(null).trigger('change')
  }

  function enableNext(obj) {
    $searchNext.prop('disabled', true)
    if ($(obj).val() !== null) {
      $searchNext.prop('disabled', false)
    }
  }

  function getData() {
    $('.rescalendar_controls, .rescalendar_table').addClass('noVisibility')
    $searchResDate.html('Buscando horas disponibles...').css('text-align', 'center')
    setWaitingTemplate()

    $.ajax({
      url: 'ajax/system/get-data.php', type: 'post', dataType: 'json', data: {
        date: moment(today, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        spec: $specialty.val(),
        subspec: $subspecialty.val(),
        prof: $professional.val(),
        prspec: $prspecialty.val()
      }
    }).done(function (d) {
      if (d.res) {
        $('.rescalendar_controls, .rescalendar_table').removeClass('noVisibility')
        $('#search-results').show()
        let formattedDate = moment(setDate(today)).format('dddd D [de] MMMM')
        $searchResDate.animate({'opacity': 0}, 100, function () {
          $(this).html(formatCaseDate(formattedDate)).css('text-align', 'left').animate({'opacity': 1}, 200)
        })
        setTemplate(d.results)
      }
    })
  }

  function setWaitingTemplate() {
    let html
    html = '<div class="card card-widget widget-user-2 prof-info mb-4" style="box-shadow:none;border:1px solid #eaeaea">' + '<div class="widget-user-header bg-light">' + '<div class="widget-user-image">' + '<img class="img-circle" src="dist/img/avatar.png" alt="User Avatar" style="opacity:.3">' + '</div>' + '<h3 class="widget-user-username"></h3>' + '<h5 class="widget-user-desc"></h5>' + '</div>' + '<div class="card-body">' + '<ul class="nav flex-column">' + '<li class="nav-item">' + '<span class="nav-link" style="padding:1.5rem 0"></span>' + '</li>' + '<li class="nav-item hour-container" style="padding:1.5rem 0">' + '</li>' + '</ul>' + '</div>' + '</div>'
    html += '<div class="card card-widget widget-user-2 prof-info mb-4" style="box-shadow:none;border:1px solid #eaeaea">' + '<div class="widget-user-header bg-light">' + '<div class="widget-user-image">' + '<img class="img-circle" src="dist/img/avatar.png" alt="User Avatar" style="opacity:.5">' + '</div>' + '<h3 class="widget-user-username"></h3>' + '<h5 class="widget-user-desc"></h5>' + '</div>' + '<div class="card-body">' + '<ul class="nav flex-column">' + '<li class="nav-item">' + '<span class="nav-link" style="padding:1.5rem 0"></span>' + '</li>' + '<li class="nav-item hour-container" style="padding:1.5rem 0">' + '</li>' + '</ul>' + '</div>' + '</div>'
    $('#search-objects').animate({'opacity': 0}, 100, function () {
      $(this).html(html).animate({'opacity': 1}, 200)
    })
  }

  function setTemplate(obj) {
    let html = ''
    $.each(obj, function (i, v) {
      html += '<div class="card card-widget widget-user-2 prof-info mb-4">' + '<div class="widget-user-header bg-secondary">' + '<div class="widget-user-image">' + '<img class="img-circle elevation-2" src="dist/img/avatar.png" alt="User Avatar">' + '</div>' + '<h3 class="widget-user-username" id="medname_' + v.id + '">' + v.name + '</h3>' + '<h5 class="widget-user-desc" id="specname_' + v.id + '">' + v.specialty + '</h5>' + '<input type="hidden" id="medamount_' + v.id + '" value="' + v.amount + '">' + '</div>' + '<div class="card-body">' + '<ul class="nav flex-column">' + '<li class="nav-item">' + '<span class="nav-link" id="consname_' + v.id + '">Videoconsulta ' + v.specialty + ' </span>' + '</li>' + '<li class="nav-item hour-container">' + '<span class="nav-link">'
      $.each(v.hours, function (hi, h) {
        let tmp = h.split(':')
        html += '<button type="button" class="btn hour-slot" id="' + v.id + '_' + v.spec_id + '_' + tmp[0] + '_' + tmp[1] + '" data-hour="' + v.date + '_' + h + '">' + h + '</button>'
      })
      html += '</span>' + '</li>' + '</ul>' + '</div>' + '</div>'
    })
    if (html === '') html += 'No se han encontrado horas disponibles en la fecha especificada.'
    $('#search-objects').animate({'opacity': 0}, 100, function () {
      $(this).html(html).animate({'opacity': 1}, 200)
    })
  }

  function validateForm() {
    $acceptData.html('<span class="spinner-border spinner-border-sm ml-2" id="spnSubmit" role="status"></span>')
    $confirmWindow.addClass('noVisibility')
    return true;
  }

  function showResponse(response) {
    if (response.type) {
      $.ajax({
        url: 'ajax/system/send-email-confirm.php', type: 'post', data: {conid: response.conid}, dataType: 'json'
      }).done(function (r) {
        $acceptData.html('Agendar mi hora')
        if (r.res) {
          Swal.fire({
            icon: 'success',
            title: '¡Tu consulta ha sido agendada con éxito!',
            text: 'Un e-mail de verificación ha sido enviado a tu casilla de correo. Recuerda confirmar tu hora y prepararte con tiempo para tu cita.',
            confirmButtonText: '<i class="fas fa-check mr-2"></i>Pagar mi consulta',
            confirmButtonColor: '#3498db',
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
          }).then((result) => {
            if (result.isConfirmed) {
              let win = window.open('payment/index.php?id=' + response.conid)
              if (win) {
                win.focus()
              }
            }
          });

          $confirmWindow.removeClass('noVisibility')
          $('#keep-searching').click()
          $form.clearForm()
          $specialty.val(null).trigger('change')
          $prevision.val(null).trigger('change')
          if (!$checkDays.is(':checked')) {
            $checkDays.trigger('click')
          }
          stepper.to(1)
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Hubo un error al agendar tu hora',
            text: r.msg + ' Por favor, inténtalo nuevamente.',
            confirmButtonText: '<i class="fas fa-check mr-2"></i>Aceptar',
            confirmButtonColor: '#3498db',
            showCancelButton: false
          })
        }
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Hubo un error al agendar tu hora',
        text: response.message + ' Por favor, inténtalo nuevamente.',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Aceptar',
        confirmButtonColor: '#3498db',
        showCancelButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
      }).then((result) => {
        if (result.isConfirmed) {
          $acceptData.html('Agendar mi hora')
          $confirmWindow.removeClass('noVisibility')
          $('#keep-searching').click()
        }
      })
    }
  }

  const options = {
    url: 'ajax/system/set-reservation.php',
    type: 'post',
    dataType: 'json',
    beforeSubmit: validateForm,
    success: showResponse
  }

  $form.submit(function () {
    $(this).ajaxSubmit(options)
    return false
  })
})

document.addEventListener('DOMContentLoaded', function () {
  window.stepper = new Stepper(document.querySelector('.bs-stepper'), ({
    animation: true
  }))
})
