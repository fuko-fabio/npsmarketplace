function selectAutoWidth() {
    setTimeout(function() {
        $('.combinations-list select').uniform({selectAutoWidth : true});
    }, 300);
}

function addCombinationToCart(id_product) {
    if (id_product) {
        $('[name="qty[]"]').each(function() {
            var qty = $(this).val();
            var id_comb = $(this).attr('data-target');
            if (qty && id_comb) {
                ajaxCart.add(id_product, id_comb, true, null, qty);
            }
        
        });
    }
}
