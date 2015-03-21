/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    $('.send-voucher-btn').fancybox({
        hideOnContentClick : false
    });
    $('.emails').tagify( {
        showButton: true,
        buttonText: tagifyBtnText,
        placeholder: tagifyPlaceholderText,
        inputValidation: unicode_hack(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false),
    });
    $('.voucher-error').hide();
});

function sendVouchers(voucherId) {
    $('#send_vouchers_error_' + voucherId).hide('slow');
    $.fancybox.showLoading();
    $.ajax({
        url : npsVouchersAjaxUrl,
        type : "POST",
        headers : {
            "cache-control" : "no-cache"
        },
        dataType : "json",
        data : {
            action: 'sendVouchers',
            newsletter_users: $('#send_vouchers_input_' + voucherId).is(':checked') ? 1 : 0,
            emails: $('#vouchers_emails_' + voucherId).val(),
            id_voucher: voucherId
        },
        success : function(result) {
            $.fancybox.hideLoading();
            if (result.error) {
                $('#send_vouchers_error_' + voucherId).slideDown('slow');
            } else {
                $.fancybox.close();
                fancyMsgBox(npsVouchersMsgSuccess + result.success + ' ' + npsVouchersMsgFail + result.fail, npsVouchersTitle);
            }
        },
        error : function(result) {
            $.fancybox.hideLoading();
            $('#send_vouchers_error_' + voucherId).slideDown('slow');
        }
    });
}
