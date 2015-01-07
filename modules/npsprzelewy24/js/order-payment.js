
function payWithP24() {
    $.fancybox.showLoading();
    if (!$('input#cp24:checked').length) {
        $('.error-terms-of-p24').show('slow');
        $.fancybox.hideLoading();
    }
    else {
        $('.error-terms-of-service').hide('slow');
        window.location = p24PaymentUrl;
    }
}