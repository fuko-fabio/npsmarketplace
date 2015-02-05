$(document).ready(function() {
    $(".textarea-autosize").autosize();
    $('#max_events option').hide();
    $('#in_row').val(3);
    $('#max_events').val(3);
    $('#max_events option[value="3"], #max_events option[value="6"], #max_events option[value="9"], #max_events option[value="12"]').show();
    $('#in_row').change(function(event) {
        $('#max_events option').hide();
        var val = $('#in_row').val();
        if (val == 1) {
            $('#max_events option[value="1"], #max_events option[value="2"], #max_events option[value="3"], #max_events option[value="4"]').show();
            $('#max_events').val(1);
            $('#width').val(300);
        } else if (val == 2) {
            $('#max_events option[value="2"], #max_events option[value="4"], #max_events option[value="6"], #max_events option[value="8"]').show();
            $('#max_events').val(2);
            $('#width').val(400);
        } else if (val == 3) {
            $('#max_events option[value="3"], #max_events option[value="6"], #max_events option[value="9"], #max_events option[value="12"]').show();
            $('#max_events').val(3);
            $('#width').val(600);
        } else if (val == 4) {
            $('#max_events option[value="4"], #max_events option[value="8"], #max_events option[value="12"]').show();
            $('#max_events').val(4);
            $('#width').val(800);
        }
    });
});

function getTheCode() {
    $('.code-error').hide('slow');
    $.fancybox.showLoading();
    $.ajax({
        url : npsAjaxUrl,
        type : "POST",
        headers : {
            "cache-control" : "no-cache"
        },
        dataType : "json",
        data : $('.marketing-code').serialize() + '&action=getTheCode',
        success : function(result) {
            $('#iframe_code').text(result).focus().select();
            $('#iframe_code_preview').html('').append(result);
            $.fancybox.hideLoading();
        },
        error : function(result) {
            $.fancybox.hideLoading();
            $('.code-error').slideDown('slow');
        }
    });
}
