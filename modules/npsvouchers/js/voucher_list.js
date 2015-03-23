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
    $('#send_vouchers_email_error_' + voucherId).hide('slow');
    if (!validate(voucherId)) {
        return;
    }
    var emails = $('#vouchers_emails_' + voucherId).val();
    var newsletterUsers = $('#send_vouchers_input_' + voucherId).is(':checked') ? 1 : 0;
    if (emails.length == 0 && !newsletterUsers) {
        $('#send_vouchers_email_error_' + voucherId).slideDown('slow');
        return;
    }
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
            newsletter_users: newsletterUsers,
            emails: emails,
            id_voucher: voucherId,
            message: $('#vouchers_message_' + voucherId).val()
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

function validate(voucherId) {
    var valid = true;
    $('#vouchers_message_' + voucherId + '.validate').each( function( index ) {
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