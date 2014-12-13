$(function() {
    $('input.seller-star').rating();

    $('#new_comment_tab_btn').fancybox({
        'hideOnContentClick' : false
    });

    $('#submitSellerNewMessage').click(function(e) {
        // Kill default behaviour
        e.preventDefault();
		$.fancybox.showLoading();
        // Form element
        $.ajax({
            url : npscomments_controller_url + '&' + 'action=add_comment' + '&secure_key=' + npscomments_secure_key + '&rand=' + new Date().getTime(),
            data : $('#id_new_seller_comment_form').serialize(),
            type : 'POST',
            headers : {
                "cache-control" : "no-cache"
            },
            dataType : "json",
            success : function(data) {
            	$.fancybox.hideLoading();
                if (data.result) {
                    $.fancybox.close();
                    var buttons = {};
                    buttons[npscomments_ok] = "npscommentsRefreshPage";
                    fancyChooseBox( npscomments_moderation_active ? npscomments_added_moderation : npscomments_added, npscomments_title, buttons);
                } else {
                    $('#new_seller_comment_form_error ul').html('');
                    $.each(data.errors, function(index, value) {
                        $('#new_seller_comment_form_error ul').append('<li>' + npscomments_general_error + '</li>');
                    });
                    $('#new_seller_comment_form_error').slideDown('slow');
                }
            },
            error: function(data) {
                $.fancybox.hideLoading();
                $('#new_seller_comment_form_error ul').html('');
                $('#new_seller_comment_form_error ul').append('<li>' + value + '</li>');
                $('#new_seller_comment_form_error').slideDown('slow');
            }
        });
        return false;
    });
});

function npscommentsRefreshPage() {
    window.location.reload();
}