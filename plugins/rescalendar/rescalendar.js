/*!
Rescalendar.js - https://cesarchas.es/rescalendar
Licensed under the MIT license - http://opensource.org/licenses/MIT

Copyright (c) 2019 César Chas
*/

(function ($) {
  $.fn.rescalendar = function (options) {
    // INITIALIZATION
    const settings = $.extend({
      id: 'rescalendar',
      format: 'YYYY-MM-DD',
      refDate: moment().format('YYYY-MM-DD'),
      jumpSize: 15,
      calSize: 30,
      locale: 'en',
      disabledDays: [],
      disabledWeekDays: [],
      dataKeyField: 'name',
      dataKeyValues: [],
      data: {},
      lang: {
        'init_error': 'Error when initializing',
        'no_data_error': 'No data found',
        'no_ref_date': 'No refDate found',
        'today': 'Today'
      },
      template_html: function (targetObj, settings) {
        const id = targetObj.attr('id'),
          refDate = settings.refDate;
        let disabledYesterday = ''
        if (refDate === moment().format('DD/MM/YYYY'))
          disabledYesterday = ' disabled'

        return [
          '<div class="rescalendar ', id, '_wrapper">',

          '<div class="rescalendar_controls">',

          '<div class="input-group mb-1">',
          '<div class="input-group-prepend">',
          '<button type="button"' + disabledYesterday + ' class="btn btn-sm move move_to_yesterday"><i class="fa fa-chevron-left"></i></button>',
          '</div>',

          '<input class="form-control-plaintext input-sm refDate" readonly type="text" value="' + refDate + '">',

          '<div class="input-group-append">',
          '<button type="button" class="btn btn-sm move move_to_tomorrow"><i class="fa fa-chevron-right"></i></button>',
          '</div>',
          '</div>',

          '<button type="button" class="btn btn-outline-secondary btn-xs move_to_today"> ' + settings.lang.today + ' </button>',

          '</div>',

          '<table class="rescalendar_table">',
          '<thead>',
          '<tr class="rescalendar_day_cells"></tr>',
          '</thead>',
          '<tbody class="rescalendar_data_rows">',
          '</tbody>',
          '</table>',

          '</div>',
        ].join('')
      }
    }, options)

    function alert_error(error_message) {
      return [
        '<div class="error_wrapper">',
        '<div class="thumbnail_image vertical-center">',
        '<p>',
        '<span class="error">',
        error_message,
        '</span>',
        '</p>',
        '</div>',
        '</div>'
      ].join('')
    }

    function set_template(targetObj, settings) {
      let template = ''

      if (settings.refDate.length !== 10) {
        targetObj.html(alert_error(settings.lang.no_ref_date))
        return false
      }

      template = settings.template_html(targetObj, settings)
      targetObj.html(template)
      return true
    }

    function dateInRange(date, startDate, endDate) {
      if (date === startDate || date === endDate) {
        return true
      }

      const date1 = moment(startDate, settings.format),
        date2 = moment(endDate, settings.format),
        date_compare = moment(date, settings.format)

      return date_compare.isBetween(date1, date2, null, '[]')
    }

    function dataInSet(data, name, date) {
      let obj_data = {};

      for (let i = 0; i < data.length; i++) {
        obj_data = data[i]

        if (
          name === obj_data.name &&
          dateInRange(date, obj_data.startDate, obj_data.endDate)
        ) {
          return obj_data
        }
      }
      return false
    }

    function setData(targetObj) {
      let html = '',
        dataKeyValues = settings.dataKeyValues,
        data = settings.data,
        arr_dates = [],
        name = '',
        content = '',
        hasEventClass = '',
        customClass = '',
        obj_data = {};

      targetObj.find('td.day_cell').each(function () {
        arr_dates.push($(this).attr('data-cellDate'))
      })

      for (let i = 0; i < dataKeyValues.length; i++) {
        content = ''
        let date = ''
        name = dataKeyValues[i]
        html += '<tr class="dataRow">'

        for (let j = 0; j < arr_dates.length; j++) {
          let title = ''
          date = arr_dates[j]
          obj_data = dataInSet(data, name, date)

          if (typeof obj_data === 'object') {
            if (obj_data.title) {
              title = ' title="' + obj_data.title + '" '
            }
            content = '<a href="#" ' + title + '>&nbsp;</a>'
            hasEventClass = 'hasEvent'
            customClass = obj_data.customClass
          } else {
            content = ' '
            hasEventClass = ''
            customClass = ''
          }
          html += '<td data-date="' + date + '" data-name="' + name + '" class="data_cell ' + hasEventClass + ' ' + customClass + '">' + content + '</td>'
        }
        html += '</tr>'
      }
      targetObj.find('.rescalendar_data_rows').html(html)
    }

    function setDayCells(targetObj, refDate) {
      const format = settings.format,
        f_inicio = moment(refDate, format),
        today = moment().startOf('day');
      let html = '',
        f_aux = '',
        f_aux_format = '',
        dia = '',
        dia_semana = '',
        num_dia_semana = 0,
        mes = '',
        clase_today = '',
        clase_middleDay = '',
        clase_disabled = '';
      const middleDay = targetObj.find('input.refDate').val();

      for (let i = 0; i < (settings.calSize + 1); i++) {
        clase_disabled = ''
        f_aux = moment(f_inicio).add(i, 'days')
        f_aux_format = f_aux.format(format)
        dia = f_aux.format('DD')
        mes = f_aux.locale(settings.locale).format('MMM').replace('.', '')
        dia_semana = f_aux.locale(settings.locale).format('dd')
        num_dia_semana = f_aux.day()

        f_aux_format === today.format(format) ? clase_today = 'today' : clase_today = ''
        f_aux_format === middleDay ? clase_middleDay = 'middleDay' : clase_middleDay = ''

        if (
          settings.disabledDays.indexOf(f_aux_format) > -1 ||
          settings.disabledWeekDays.indexOf(num_dia_semana) > -1
        ) {
          clase_disabled = 'disabledDay'
        }

        html += [
          '<td class="day_cell ' + clase_today + ' ' + clase_middleDay + ' ' + clase_disabled + '" data-cellDate="' + f_aux_format + '">',
          '<span class="day_week">' + dia_semana + '</span>',
          '<span class="day">' + dia + '</span>',
          '</td>'
        ].join('')
      }

      targetObj.find('.rescalendar_day_cells').html(html)
      addTdClickEvent(targetObj)
      setData(targetObj)
    }

    function addTdClickEvent(targetObj) {
      const day_cell = targetObj.find('td.day_cell');

      day_cell.on('click', function (e) {
        const cellDate = e.currentTarget.attributes['data-cellDate'].value;
        targetObj.find('input.refDate').val(cellDate)
        setDayCells(targetObj, moment(cellDate, settings.format))
        getData(moment(cellDate, settings.format))
        if (cellDate === moment().format('DD/MM/YYYY')) {
          $('.move_to_yesterday').prop('disabled', true)
        } else {
          $('.move_to_yesterday').prop('disabled', false)
        }
      })
    }

    function change_day(targetObj, action, num_days) {
      const refDate = targetObj.find('input.refDate').val();
      let f_ref;
      if (action === 'subtract') {
        f_ref = moment(refDate, settings.format).subtract(num_days, 'days');
      } else {
        f_ref = moment(refDate, settings.format).add(num_days, 'days');
      }
      targetObj.find('input.refDate').val(f_ref.format(settings.format))
      setDayCells(targetObj, f_ref)
      getData(f_ref)
      if (f_ref.format('DD/MM/YYYY') === moment().format('DD/MM/YYYY')) {
        $('.move_to_yesterday').prop('disabled', true)
      } else {
        $('.move_to_yesterday').prop('disabled', false)
      }
    }

    function getData(targetObj) {
      $('.rescalendar_controls, .rescalendar_table').addClass('noVisibility')
      $('#search-result-date').html('Buscando horas disponibles...').css('text-align', 'center')
      setWaitingTemplate()

      $.ajax({
        url: 'ajax/system/get-data.php',
        type: 'post',
        dataType: 'json',
        data: {
          date: moment(targetObj).format('YYYY-MM-DD'),
          spec: $('#name-spec').val(),
          subspec: $('#name-subspec').val(),
          prof: $('#name-prof').val(),
          prspec: $('#name-prspec').val()
        }
      }).done(function (d) {
        if (d.res) {
          $('.rescalendar_controls, .rescalendar_table').removeClass('noVisibility')
          $('#search-results').show()
          $('#search-result-date').animate({'opacity': 0}, 100, function () {
            $(this).html(formatCaseDate(moment(targetObj).format('dddd D [de] MMMM'))).css('text-align', 'left').animate({'opacity': 1}, 200)
          })
          setTemplate(d.results)
        }
      })
    }

    function setWaitingTemplate() {
      let html
      html = '<div class="card card-widget widget-user-2 prof-info mb-4" style="box-shadow:none;border:1px solid #eaeaea">' +
        '<div class="widget-user-header bg-light">' +
        '<div class="widget-user-image">' +
        '<img class="img-circle" src="dist/img/avatar.png" alt="User Avatar" style="opacity:.3">' +
        '</div>' +
        '<h3 class="widget-user-username"></h3>' +
        '<h5 class="widget-user-desc"></h5>' +
        '</div>' +
        '<div class="card-body">' +
        '<ul class="nav flex-column">' +
        '<li class="nav-item">' +
        '<span class="nav-link" style="padding:1.5rem 0"></span>' +
        '</li>' +
        '<li class="nav-item hour-container" style="padding:1.5rem 0">' +
        '</li>' +
        '</ul>' +
        '</div>' +
        '</div>'
      html += '<div class="card card-widget widget-user-2 prof-info mb-4" style="box-shadow:none;border:1px solid #eaeaea">' +
        '<div class="widget-user-header bg-light">' +
        '<div class="widget-user-image">' +
        '<img class="img-circle" src="dist/img/avatar.png" alt="User Avatar" style="opacity:.5">' +
        '</div>' +
        '<h3 class="widget-user-username"></h3>' +
        '<h5 class="widget-user-desc"></h5>' +
        '</div>' +
        '<div class="card-body">' +
        '<ul class="nav flex-column">' +
        '<li class="nav-item">' +
        '<span class="nav-link" style="padding:1.5rem 0"></span>' +
        '</li>' +
        '<li class="nav-item hour-container" style="padding:1.5rem 0">' +
        '</li>' +
        '</ul>' +
        '</div>' +
        '</div>'
      $('#search-objects').animate({'opacity': 0}, 100, function () {
        $(this).html(html).animate({'opacity': 1}, 200)
      })
    }

    function setTemplate(obj) {
      let html = ''
      $.each(obj, function (i, v) {
        html += '<div class="card card-widget widget-user-2 prof-info mb-4">' +
          '<div class="widget-user-header bg-secondary">' +
          '<div class="widget-user-image">' +
          '<img class="img-circle elevation-2" src="dist/img/avatar.png" alt="User Avatar">' +
          '</div>' +
          '<h3 class="widget-user-username" id="medname_' + v.id + '">' + v.name + '</h3>' +
          '<h5 class="widget-user-desc" id="specname_' + v.id + '">' + v.specialty + '</h5>' +
          '<input type="hidden" id="medamount_' + v.id + '" value="' + v.amount + '">' +
          '</div>' +
          '<div class="card-body">' +
          '<ul class="nav flex-column">' +
          '<li class="nav-item">' +
          '<span class="nav-link" id="consname_' + v.id + '">Videoconsulta ' + v.specialty + ' </span>' +
          '</li>' +
          '<li class="nav-item hour-container">' +
          '<span class="nav-link">'
        $.each(v.hours, function (hi, h) {
          let tmp = h.split(':')
          html += '<button type="button" class="btn hour-slot" id="' + v.id + '_' + v.spec_id + '_' + tmp[0] + '_' + tmp[1] + '" data-hour="' + v.date + '_' + h + '">' + h + '</button>'
        })
        html += '</span>' +
          '</li>' +
          '</ul>' +
          '</div>' +
          '</div>'
      })
      if (html === '')
        html += 'No se han encontrado horas disponibles en la fecha especificada.'
      $('#search-objects').animate({'opacity': 0}, 100, function () {
        $(this).html(html).animate({'opacity': 1}, 200)
      })
    }

    return this.each(function () {
      const targetObj = $(this);
      set_template(targetObj, settings)
      setDayCells(targetObj, settings.refDate)

      // Events
      const move_to_last_month = targetObj.find('.move_to_last_month'),
        move_to_yesterday = targetObj.find('.move_to_yesterday'),
        move_to_tomorrow = targetObj.find('.move_to_tomorrow'),
        move_to_next_month = targetObj.find('.move_to_next_month'),
        move_to_today = targetObj.find('.move_to_today'),
        refDate = targetObj.find('.refDate');

      move_to_last_month.on('click', function () {
        change_day(targetObj, 'subtract', settings.jumpSize)
      })

      move_to_yesterday.on('click', function () {
        change_day(targetObj, 'subtract', 1)
      })

      move_to_tomorrow.on('click', function () {
        change_day(targetObj, 'add', 1)
      })

      move_to_next_month.on('click', function () {
        change_day(targetObj, 'add', settings.jumpSize)
      })

      refDate.on('blur', function () {
        const refDate = targetObj.find('input.refDate').val();
        setDayCells(targetObj, refDate)
      })

      move_to_today.on('click', function () {
        const today = moment().startOf('day').format(settings.format);
        targetObj.find('input.refDate').val(today)
        setDayCells(targetObj, today)
        getData(moment(today, settings.format))
      })

      return this
    })
  }
}(jQuery))