$(function() {
    $('input.seller-star').rating();

    $('.open-seller-comment-form').fancybox({
        'hideOnContentClick' : false
    });

    $('button.nps_usefulness_btn').click(function() {
        var id_seller_comment = $(this).data('id-seller-comment');
        var is_usefull = $(this).data('is-usefull');
        var parent = $(this).parent();

        $.ajax({
            url : sellercomments_controller_url + '?rand=' + new Date().getTime(),
            data : {
                id_seller_comment : id_seller_comment,
                action : 'comment_is_usefull',
                value : is_usefull
            },
            type : 'POST',
            headers : {
                "cache-control" : "no-cache"
            },
            success : function(result) {
                parent.fadeOut('slow', function() {
                    parent.remove();
                });
            }
        });
    });

    $('span.nps_report_btn').click(function() {
        if (confirm(sellercomments_confirm_report_message)) {
            var idSellerComment = $(this).data('id-seller-comment');
            var parent = $(this).parent();

            $.ajax({
                url : sellercomments_controller_url + '?rand=' + new Date().getTime(),
                data : {
                    id_seller_comment : idSellerComment,
                    action : 'report_abuse'
                },
                type : 'POST',
                headers : {
                    "cache-control" : "no-cache"
                },
                success : function(result) {
                    parent.fadeOut('slow', function() {
                        parent.remove();
                    });
                }
            });
        }
    });

    $('#submitSellerNewMessage').click(function(e) {
        // Kill default behaviour
        e.preventDefault();

        // Form element
        $.ajax({
            url : sellercomments_controller_url + '&' + 'action=add_comment&secure_key=' + sellercomments_secure_key + '&rand=' + new Date().getTime(),
            data : $('#id_new_seller_comment_form').serialize(),
            type : 'POST',
            headers : {
                "cache-control" : "no-cache"
            },
            dataType : "json",
            success : function(data) {
                if (data.result) {
                    $.fancybox.close();
                    var buttons = {};
                    buttons[sellercomment_ok] = "sellercommentRefreshPage";
                    fancyChooseBox( sellercomments_moderation_active ? sellercomment_added_moderation : sellercomment_added, sellercomment_title, buttons);
                } else {
                    $('#new_seller_comment_form_error ul').html('');
                    $.each(data.errors, function(index, value) {
                        $('#new_seller_comment_form_error ul').append('<li>' + value + '</li>');
                    });
                    $('#new_seller_comment_form_error').slideDown('slow');
                }
            }
        });
        return false;
    });
});

function sellercommentRefreshPage() {
    window.location.reload();
}