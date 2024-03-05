window.mobileCheck = function () {
  let check = false
  if (window.matchMedia("(max-width: 767px)").matches) check = true
  return check
}

$(document).ready(function () {
  $.fn.modal.Constructor.prototype._enforceFocus = function () {
  }

  $('input[type="text"]').prop('autocomplete', 'off')
  $('input[type="email"]').prop('autocomplete', 'off')

  $('body').tooltip({
    html: true,
    selector: '[rel=tooltip]',
    trigger: 'hover'
  })

  $('[form-keydown-false]').keydown(function (e) {
    if (e.keyCode === 13) {
      e.preventDefault()
      return false
    }
  })

  $('[mask-phone]').mask('0 0000 0000')

  $('[val-phone]').change(function () {
    if ($(this).val().length < 11) $(this).removeClass('is-valid').addClass('is-invalid')
  })

  bsCustomFileInput.init()
  $('.custom-file-input').change(function () {
    if ($(this).val() === '') {
      $(this).closest('.custom-file').find('.custom-file-label').text('Ningún archivo seleccionado')
    }
  })

  Noty.overrideDefaults({
    theme: 'sunset',
    killer: true,
    timeout: 1000
  })

  moment.locale('es')

  $.fn.daterangepicker.defaultOptions = {
    startDate: moment(),
    locale: {
      applyLabel: 'Aplicar',
      cancelLabel: 'Cancelar',
    },
    applyClass: 'btn btn-info',
    cancelClass: 'btn btn-danger'
  }

  $('#digital-date').html(moment().format('dddd') + ', ' + moment().format('DD') + ' de ' + moment().format('MMMM') + ' de ' + moment().format('YYYY'))
  $('#digital-time').html(moment().format('HH:mm:ss'))

  window.setInterval(function () {
    $('#digital-date').html(moment().format('dddd') + ', ' + moment().format('DD') + ' de ' + moment().format('MMMM') + ' de ' + moment().format('YYYY'))
    $('#digital-time').html(moment().format('HH:mm:ss'))
  }, 1000)

  $('.mask-date').mask('00/00/0000')
  $('.mask-time').mask('00:00')
  $('.mask-rut').mask('00.000.000-D', {translation: {D: {pattern: /[k0-9]/}}, reverse: true})
  $('.mask-phone').mask('0 0000 0000')
  $('.mask-number').mask('0#')
  $('.mask-amount').mask('0#').on('input', function () {
    if ($(this).val() !== '') {
      $(this).val(parseInt($(this).val()))
    }
  })

  $.fn.select2.defaults.set('theme', 'bootstrap4')
  $.fn.select2.defaults.set('language', 'es')
  $.fn.select2.defaults.set('allowClear', true)
  $.fn.select2.defaults.set('minimumResultsForSearch', 11)

  $.extend(true, $.fn.dataTable.defaults, {
    autoWidth: false,
    language: {url: '/plugins/datatables/es.json'},
    dom: '<\'row\'<\'col-md-4\'B><\'col-md-4 text-center\'l><\'col-md-4\'f>>' + '<\'row\'<\'col-md-12\'t>>' + '<\'row\'<\'col-md-6\'i><\'col-md-6\'p>>',
    buttons: ['excel'],
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todo']],
    pageLength: 10,
    ordering: false
  })

  $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
    widgetPositioning: {
      horizontal: 'right',
      vertical: 'bottom'
    },
    locale: 'es'
  })

  $('.dropdown-item-checkbox').on('click', function (e) {
    e.stopPropagation()
    let input = $(this).find('.checkbox-dropdown')
    input.prop('checked', !input.prop('checked'))
  })

  $('.dropdown-item-radio').on('click', function () {
    let input = $(this).find('.radio-dropdown')
    input.prop('checked', true)
  })

  $.fn.fadeSlideRight = function (speed, fn) {
    let mRight = '-25%';
    if (mobileCheck()) mRight = '-100%'
    return $(this).animate({
      'margin-right': mRight
    }, speed || 500, function () {
      $.isFunction(fn) && fn.call(this);
    });
  }

  $.fn.fadeSlideLeft = function (speed, fn) {
    return $(this).animate({
      'margin-right': 0
    }, speed || 500, function () {
      $.isFunction(fn) && fn.call(this);
    });
  }
})