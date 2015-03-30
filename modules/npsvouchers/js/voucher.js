/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    $('.datetimepicker-from, .datetimepicker-to').datetimepicker({
        pickTime: false
    });

    $('.datetimepicker-from').on('changeDate', function(e) {
        $('.datetimepicker-from').datetimepicker('hide');
    });

    $('.datetimepicker-to').on('changeDate', function(e) {
        $('.datetimepicker-to').datetimepicker('hide');
    });

    $('input[name="from"]').on('click', function() {
        $('.datetimepicker-from').datetimepicker('show');
    });

    $('input[name="to"]').on('click', function() {
        $('.datetimepicker-to').datetimepicker('show');
    });
    
    if ($('input[name="from"]').val().length == 0) {
        $('.datetimepicker-from').data("datetimepicker").setValue(getDateTime());
    }

    $('input[type=radio][name=type]').change(function() {
        if (this.value == 'percent')
            setPercentageInput(true);
        else if (this.value == 'price')
            setPriceInput(true);
    });
    if (!$('input[name="code"]').val().length)
        gencode();
    var type = $('input[type=radio][name=type]:checked').val();
    if (type == 'percent')
        setPercentageInput(false);
    else if (type == 'price')
        setPriceInput(false);

    $('select[name=id_product]').change(function() {
        onProductSelected(true);
    });
    $('input[name=discount]').on('keyup', function() {
        updateHint();
    });

    $(document).on('submit', '#edit-voucher-form', function(){
        return validate();
    });
    
    onProductSelected(false);
    
    if (!localStorage.getItem("voucherAddTour")) {
        startVoucherTour();
    }
});

function onProductSelected(reset) {
    var el = $('select[name="id_product"] option:selected');
    if (el.length) {
        if ($('input[name=to]').val() == "" || reset)
            $('.datetimepicker-to').data("datetimepicker").setValue(el.attr('data-date-to'));
        if ($('input[name=quantity]').val() == "" || reset)
            $('input[name=quantity]').val(el.attr('data-quantity'));
        if ($('input[name=name]').val() == "" || reset)
            $('input[name=name]').val(voucherPrefix + el.text());
        updateHint();
    }
}

function setPercentageInput(reset) {
    $('.percent').show();
    $('.price').hide();
    if (reset)
        $('input[name="discount"]').val('').attr('data-validate', 'isPercent');
}

function setPriceInput(reset) {
    $('.price').show();
    $('.percent').hide();
    if (reset)
        $('input[name="discount"]').val('').attr('data-validate', 'isPrice');
}

function gencode() {
    var result = '';
    /* There are no O/0 in the codes in order to avoid confusion */
    var chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
    for (var i = 1; i <= 8; ++i)
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    $('input[name="code"]').val(result);
}

function validate() {
    var valid = true;
    $('input.validate, textarea.validate').each( function( index ) {
        if ($(this).hasClass('is_required') || $(this).val().length) {
            if ($(this).attr('name') == 'postcode' && typeof(countriesNeedZipCode[$('#id_country option:selected').val()]) != 'undefined')
                var result = window['validate_'+$(this).attr('data-validate')]($(this).val(), countriesNeedZipCode[$('#id_country option:selected').val()]);
            else
                var result = window['validate_'+$(this).attr('data-validate')]($(this).val());
            if (!result) {
                valid = false;
            }
            if (result) {
                $(this).parent().removeClass('form-error').addClass('form-ok');
            } else {
                $(this).parent().addClass('form-error').removeClass('form-ok');
                valid = false;
            }
        }
    });
    return valid;
};

function updateHint() {
    var price = $('select[name="id_product"] option:selected').attr('data-price');
    if (price) {
        $('.product-price').text($('select[name="id_product"] option:selected').attr('data-display-price'));
        var discount = $('input[name=discount]').val();
        if (discount != null && discount != "") {
            var type = $('input[type=radio][name=type]:checked').val();
            if (type == 'percent')
                var newPrice = price - (price * (discount/100));
            else if (type == 'price')
                var newPrice = price - discount;
            $('.discount-price').text(Math.round(newPrice * 100) / 100);
        }
    }
}

function getDateTime() {
    var now = new Date(); 
    now.setMinutes(0);
    now.setSeconds(0);
    return now;
}

function startVoucherTour() {
    var tour = introJs();
    tour.setOption('tooltipPosition', 'auto');
    tour.setOption('positionPrecedence', ['top', 'left', 'right']);
    tour.setOption('showProgress', true);
    tour.setOption('exitOnOverlayClick', false);
    tour.setOption('showBullets', false);
    tour.setOption('scrollToElement', true);
    tour.setOption('disableInteraction', false);
    tour.setOption('showStepNumbers', false);
    tour.setOption('nextLabel', npsTourNext);
    tour.setOption('prevLabel', npsTourPrev);
    tour.setOption('skipLabel', npsTourSkip);
    tour.setOption('doneLabel', npsTourDone);
    tour.oncomplete(endVoucherTour);
    tour.onexit(endVoucherTour);
    tour.start();
}

function endVoucherTour() {
    localStorage.setItem("voucherAddTour", true);
}
