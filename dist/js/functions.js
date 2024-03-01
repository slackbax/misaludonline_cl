function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^\d+\-Ee.]/g, '')
  let n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? ',' : dec_point
  let s = ''
  let toFixedFix = function (n, prec) {
    let k = Math.pow(10, prec)
    return '' + (Math.round(n * k) / k).toFixed(prec)
  }
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }
  return s.join(dec)
}

function toSentenceCase(text) {
  return text.replace(/ {2,}/g, ' ').trim()
}

function toSentenceUpperCaseIp(text) {
  return text.replace(/ {2,}/g, ' ').trim().toUpperCase()
}

function toSentenceLowerCaseIp(text) {
  return text.replace(/ {2,}/g, ' ').trim().toLowerCase()
}

function toSentenceCaseTx(text) {
  text = text.replace(/ {2,}/g, ' ').replace(/ +\n/g, '\n').replace(/\n +/g, '\n').trim()
  text = text.split('\n').map(function (line) {
    return line.charAt(0).toUpperCase() + line.slice(1)
  }).join('\n')
  return text
}

function validate_rut(rut) {
  rut = rut.replace(/[\.-]/g, '')
  let body = rut.slice(0, -1)
  let checkDigit = rut.slice(-1).toUpperCase()
  if (!/^[0-9]+[kK]?$/i.test(rut)) {
    return false
  }
  let sum = 0
  let factor = 2
  for (let i = body.length - 1; i >= 0; i--) {
    sum += factor * body[i]
    factor = factor === 7 ? 2 : factor + 1
  }
  let calculatedCheckDigit = 11 - (sum % 11)
  calculatedCheckDigit = calculatedCheckDigit === 11 ? 0 : calculatedCheckDigit === 10 ? 'K' : calculatedCheckDigit
  return checkDigit === calculatedCheckDigit
}

function validate_email(email) {
  const input = $('<input>').attr('type', 'email').val(email)
  return input[0].checkValidity()
}

function setDate(date) {
  const tmp = date.split('/')
  return tmp[2] + '-' + tmp[1] + '-' + tmp[0]
}

function getDate(date) {
  const tmp = date.split('-')
  return tmp[2] + '/' + tmp[1] + '/' + tmp[0]
}

function formatCaseDate(str) {
  const firstChar = str.charAt(0)
  str = str.replace(firstChar, firstChar.toUpperCase())

  let tmp = str.split(' de ')
  const firstMonthChar = tmp[1].charAt(0)
  tmp[1] = tmp[1].replace(firstMonthChar, firstMonthChar.toUpperCase())

  str = tmp[0] + ' de ' + tmp[1]
  return str
}
