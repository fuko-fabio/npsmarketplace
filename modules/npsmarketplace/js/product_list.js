$(document).ready(function(){
    $('.sale-event-btn').fancybox({
        'hideOnContentClick' : false
    });
});

function submitPriceReduction(id_product, reduction) {
    var selector = '#sale_error_' + id_product;
    $(selector).hide('slow');
    $(selector).html('');
    $.fancybox.showLoading();
    $.ajax({
        url: npsAjaxUrl,
        type: "POST",
        headers: {"cache-control": "no-cache"},
        dataType: "json",
        data: {
            action: 'specialPrice',
            id_product: id_product,
            reduction: reduction
        },
        success: function(json) {
            if (json.result) {
                location.reload();
            } else {
                $.fancybox.hideLoading();
                $(selector).append('<ul></ul>');
                $.each(json.errors, function(index, value) {
                    $(selector + ' ul').append('<li>' + value + '</li>');
                });
                $(selector).slideDown('slow');
            }
        }
    });  
};

function removePriceReduction(id_product) {
    $.fancybox.showLoading();
    $.ajax({
        url: npsAjaxUrl,
        type: "POST",
        headers: {"cache-control": "no-cache"},
        dataType: "json",
        data: {
            action: 'removeSpecialPrice',
            id_product: id_product,
        },
        success: function(json) {
            location.reload();
        }
    });  
};