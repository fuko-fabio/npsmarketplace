/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
var vIndex = 0;

$(document).ready(function(){
    $(".textarea-autosize").autosize();

    CollapsibleLists.apply();
    $('.collapsibleListClosed').click();

    $("#product_province").change(function() {
        populateTowns(this.value);
    });

    $('.add-variant-btn').fancybox({
        hideOnContentClick : false,
        width       : '60%',
        height      : 'auto',
        autoSize    : false,
    });
});

function populateTowns(id_feature_value) {
    $.each(provincesMap, function(index, province) {
        if (province.id_feature_value == id_feature_value) {
            $('#product_town').html('');
            $.each(province.towns, function(index, town) {
                $('#product_town').append(new Option(town.name, town.id_feature_value));
            });
            $('#product_town').append(new Option(dictTownsOther, 0));
            $('#product_town').trigger('change');
            return false;
        }
    });
}

function addVariant(id_form) {
    if (validateVariant(id_form)) {
        var data = {};
        $.each($(id_form + ' .validate').serializeArray(), function(index, item) {
          if (data.hasOwnProperty(item.name)) {
            data[item.name] = $.makeArray(data[item.name]);
            data[item.name].push(item.value);
          }
          else {
            data[item.name] = item.value;
          }
        });
        data['type'] = $(id_form + ' [name=type]').val();
        data['index'] = vIndex;
        console.log(data);
        $('.variants-container').append(tmpl("ticket-tmpl", data));
        $.fancybox.close();
        vIndex = vIndex + 1;
        clearFancyboxForm(id_form);
        $('body').scrollTo('.variants-box');
        $("input[type='checkbox']:not(.comparator)").uniform();
    }
}

function clearFancyboxForm(id_form) {
    $(id_form + ' input.validate').each( function(index) {
        $(this).val('');
        $(this).parent().removeClass('form-error').removeClass('form-ok');
    });
}

function removeVariant(index) {
    $('.variant-index-' + index).remove();
}

function validateVariant(id_form) {
    var valid = true;
    $(id_form + ' .validate').each( function( index ) {
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