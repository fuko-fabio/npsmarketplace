/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

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
    populateCombinations();
    updateCombinationsDropdown();
    initVariantReductions('#ticket_combination_form');
    initVariantReductions('#carnet_combination_form');
});

function initVariantReductions(scope) {
    $(scope + ' [name="reduction"]').change(function() {
        if ($(this).is(':checked')) {
            $(scope + ' .reduction').show();
            $(scope + ' .reduction input').addClass('is_required');
        } else {
            $(scope + ' .reduction').hide();
            $(scope + ' .reduction input').removeClass('is_required');
        }
        $.fancybox.update();
    });
    $(scope + ' .reduction').hide();
}

function updateCombinationsDropdown() {
    if (productCombinations.length > 0) {
        var types = [];
        $.each(productCombinations, function(index, combination) {
            types.push(combination['type']);
        });
        if (types.indexOf('0') != -1 || types.indexOf('1') != -1) {
            $('.variants-dropdown').show();
            $('.add-ticket, .add-carnet').show();
            $('.add-ad, .add-ext-ad').hide();
        } else if (types.indexOf('2') != -1 || types.indexOf('3') != -1) {
            $('.variants-dropdown').hide();
        }
    } else {
        $('.variants-dropdown').show();
        $('.add-ticket, .add-carnet, .add-ad, .add-ext-ad').show();
    }
}

function populateCombinations() {
    if (productCombinations.length > 0) {
        $('.no-variants').hide();
        $.each(productCombinations, function(index, combination) {
            combination['index'] = index;
            renderCombination(combination);
        });
    } else {
        $('.no-variants').show();
    }
}

function renderCombination(combination) {
    $('.variants-container').append(tmpl("ticket-tmpl", combination));
}

function clearCombinations() {
    $('.variants-container').empty();
}

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
        data['reduction'] = $(id_form + ' [name=reduction]').is(':checked');
        console.log(data);
        productCombinations.push(data);
        clearCombinations();
        populateCombinations();
        updateCombinationsDropdown();
        $.fancybox.close();
        clearFancyboxForm(id_form);
        setTimeout(function() {
            $('body').scrollTo('.variants-box');
            $("input[type='checkbox']:not(.comparator)").uniform();
        }, 500);
    }
}

function clearFancyboxForm(id_form) {
    $(id_form + ' input.validate').each( function(index) {
        $(this).val('');
        $(this).parent().removeClass('form-error').removeClass('form-ok');
    });
}

function removeVariant(index) {
    productCombinations.splice(index, 1);
    clearCombinations();
    populateCombinations();
    updateCombinationsDropdown();
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