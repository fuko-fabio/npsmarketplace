function validate_isInteger(s) {
    var reg = /^\+?(0|[1-9]\d*)$/;
    return reg.test(s);
}

function validate_isDate(s) {
    var reg = /^\d{4}-\d{2}-\d{2}$/;
    return reg.test(s);
}

function validate_isTime(s) {
    var reg = /([01]?[0-9]|2[0-3]):[0-5][0-9]/;
    return reg.test(s);
}

function validate_isNip(s)
{
    var reg = /^[0-9]{10}$/;
    return reg.test(s);
}

function validate_isRegon(s)
{
    var reg = /^[0-9]{9}$/;
    return reg.test(s);
}

function validate_isPrice (s) {
  var reg = /^[0-9]{1,10}(\.[0-9]{1,9})?$/;
  return reg.test(s);
}

function validate_isIban (s) {
  return IBAN.isValid(s);
}

function validate_isNrb(nrb) {
    nrb = nrb.replace(/[^0-9]+/g, '');
    var w = new Array(1, 10, 3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51, 25, 56, 75, 71, 31, 19, 93, 57);

    if (nrb.length == 26) {
        nrb = nrb + "2521";
        nrb = nrb.substr(2) + nrb.substr(0, 2);
        var z = 0;
        for (var i = 0; i < 30; i++) {
            z += nrb[29 - i] * w[i];
        }
        if (z % 97 == 1) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function validate_isQuantity(s) {
    return validate_isInteger(s) && s > 0;
}

function validate_isPercent(s) {
    return validate_isPrice(s) && s >= 0 && s <= 100;
}