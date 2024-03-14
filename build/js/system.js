window.mobileCheck = function () {
  let check = false
  if (window.matchMedia("(max-width: 767px)").matches) check = true
  return check
}

$(document).ready(function () {
  $('input[type="text"]').prop('autocomplete', 'off')
  $('input[type="email"]').prop('autocomplete', 'off')

  $('body').tooltip({
    html: true,
    selector: '[rel=tooltip]',
    trigger: 'hover'
  })

  $.fn.select2.defaults.set('theme', 'bootstrap4')
  $.fn.select2.defaults.set('language', 'es')
  $.fn.select2.defaults.set('allowClear', true)
  $.fn.select2.defaults.set('minimumResultsForSearch', 11)

  $.fn.fadeSlideRight = function (speed, fn) {
    let mRight = '-30%';
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