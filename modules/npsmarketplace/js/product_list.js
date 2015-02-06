$(document).ready(function(){
    $('.sale-event-btn, .quantity-btn').fancybox({
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

function submitCombinationQuantity(id_product_attribute, quantity) {
    var selector = '#quantity_validation_error_' + id_product_attribute;
    var errorSelector = '#quantity_error_' + id_product_attribute;
    $(selector).hide('slow');
    $(errorSelector).hide('slow');
    if (!validate_isQuantity(quantity)) {
        $(selector).slideDown('slow');
        return;
    }
    $.fancybox.showLoading();
    $.ajax({
        url: npsAjaxUrl,
        type: "POST",
        headers: {"cache-control": "no-cache"},
        dataType: "json",
        data: {
            action: 'submitCombinationQuantity',
            id_product_attribute: id_product_attribute,
            quantity: quantity
        },
        success: function(result) {
            if (result) {
                location.reload();
            } else {
                $.fancybox.hideLoading();
                $(errorSelector).slideDown('slow');
            }
        },
        error: function() {
            $.fancybox.hideLoading();
            $(errorSelector).slideDown('slow');
        }
    });  
}
