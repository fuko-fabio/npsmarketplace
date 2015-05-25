/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    CollapsibleLists.apply();
    $('.collapsibleListClosed').click();

    $("#product_province").change(function() {
        populateTowns(this.value);
    });

    $('.add-variant-btn, .add-question-btn').fancybox({
        hideOnContentClick : false,
        width       : '60%',
        height      : 'auto',
        autoSize    : false,
        helpers: {
        overlay: {
          locked: false
        }
      }
    });

    initDatetimePickers();
    populateCombinations();
    updateCombinationsDropdown();
    initVariantReductions('#ticket_combination_form');
    initVariantReductions('#carnet_combination_form');
});

function initDatetimePickers() {
    // Ticket
    initDatetimePicker('#ticket_date', function(selectedDate) {
        $( "#ticket_expiry_date" ).datepicker( "option", "maxDate", selectedDate );
        $( "#ticket_expiry_date" ).datepicker( "setDate", selectedDate );
        $( "#ticket_to" ).datepicker( "option", "maxDate", selectedDate );
        $( "#ticket_from" ).datepicker( "option", "maxDate", selectedDate );
    });
    initDatetimePicker('#ticket_expiry_date', function(selectedDate) {
        $( "#ticket_to" ).datepicker( "option", "maxDate", selectedDate );
        $( "#ticket_from" ).datepicker( "option", "maxDate", selectedDate );
    });
    initDatetimePicker('#ticket_from', function(selectedDate) {
        $( "#ticket_to" ).datepicker( "option", "minDate", selectedDate );
    });
    initDatetimePicker('#ticket_to', function(selectedDate) {
        $( "#ticket_from" ).datepicker( "option", "maxDate", selectedDate );
    });

    // Carnet
    initDatetimePicker('#carnet_expiry_date', function(selectedDate) {
        $( "#carnet_to" ).datepicker( "option", "maxDate", selectedDate );
        $( "#carnet_from" ).datepicker( "option", "maxDate", selectedDate );
    });
    initDatetimePicker('#carnet_from', function(selectedDate) {
        $( "#carnet_to" ).datepicker( "option", "minDate", selectedDate );
    });
    initDatetimePicker('#carnet_to', function(selectedDate) {
        $( "#carnet_from" ).datepicker( "option", "maxDate", selectedDate );
    });

    // Ad
    initDatetimePicker('#ad_date', function(selectedDate) {
        $( "#ad_expiry_date" ).datepicker( "option", "maxDate", selectedDate );
        $( "#ad_expiry_date" ).datepicker( "setDate", selectedDate );
    });
    initDatetimePicker('#ad_expiry_date', function(selectedDate) {});

    // External Ad
    initDatetimePicker('#ead_date', function(selectedDate) {
        $( "#ead_expiry_date" ).datepicker( "option", "maxDate", selectedDate );
        $( "#ead_expiry_date" ).datepicker( "setDate", selectedDate );
    });
    initDatetimePicker('#ead_expiry_date', function(selectedDate) {});
}

function initDatetimePicker(selector, onCloseCallback) {
    var coeff = 1000 * 60 * 5;
    var date = new Date();
    var rounded = new Date(Math.round(date.getTime() / coeff) * coeff);

    $(selector).datetimepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        minDateTime: rounded,
        dateFormat: 'yy-mm-dd',
        stepMinute: 5,
        minute: 0,
        currentText: dictNow,
        closeText: dictDone,
        ampm: false,
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        timeFormat: 'hh:mm',
        timeSuffix: '',
        timeOnlyTitle: dictChooseTime,
        timeText: dictTime,
        hourText: dictHour,
        minuteText: dictMinute,
        onClose: onCloseCallback
    });
}

function initVariantReductions(scope) {
    $(scope + ' [name="add_reduction"]').change(function() {
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
        if (types.indexOf('ticket') != -1 || types.indexOf('carnet') != -1) {
            $('.variants-dropdown').show();
            $('.add-ticket, .add-carnet').show();
            $('.add-ad, .add-ext-ad').hide();
        } else if (types.indexOf('ad') != -1 || types.indexOf('extternalad') != -1) {
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
            if (combination['specific_prices']) {
                $.each(combination['specific_prices'], function(i, specific_price) {
                    specific_price['index'] = i;
                });
            }
            renderCombination(combination);
        });
    } else {
        $('.no-variants').show();
    }
    $("input[type='checkbox']:not(.comparator)").uniform();
}

function renderCombination(combination) {
    $('.variants-container').append(tmpl("ticket-tmpl", combination));
    if (combination['specific_prices'].length > 0) {
        $('.variant-index-'+ combination['index'] +' .no-specific-prices').hide();
    } else {
        $('.variant-index-'+ combination['index'] +' .no-specific-prices').show();
    }
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
    if (validateScope(id_form)) {
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
        if ($(id_form + ' [name=add_reduction]').is(':checked')) {
            var array = [{
                reduction: data['reduction'],
                from: data['from'],
                to: data['to']
            }];
            data['specific_prices'] = array;
        } else {
            data['specific_prices'] = [];
        }
        productCombinations.push(data);
        clearCombinations();
        populateCombinations();
        updateCombinationsDropdown();
        $.fancybox.close();
        clearFancyboxForm(id_form);
        window.dispatchEvent(new Event('resize'));
    }
}

function closeVariantBox(id_form) {
    $.fancybox.close();
    clearFancyboxForm(id_form);
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

function removeSpecialPrice(cominationIndex, specificPriceIndex) {
    productCombinations[cominationIndex]['specific_prices'].splice(specificPriceIndex, 1);
    clearCombinations();
    populateCombinations();
    updateCombinationsDropdown();
}

function validateScope(id_form) {
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

function validateForm() {
    var valid = true;
    $('#edit-product-form .validate').each( function( index ) {
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
    if (!valid) {
        $('.validation-error').show();
        $('body').scrollTo('.validation-error');
    } else {
        $('.validation-error').hide();
    }
    return valid;
}

function startNewEventTour() {
    var tour = introJs();
    tour.setOption('tooltipPosition', 'auto');
    tour.setOption('positionPrecedence', ['top', 'left', 'right']);
    tour.setOption('showProgress', true);
    tour.setOption('exitOnOverlayClick', false);
    tour.setOption('showBullets', false);
    tour.setOption('scrollToElement', true);
    tour.setOption('disableInteraction', false);
    tour.setOption('showStepNumbers', false);
    tour.setOption('nextLabel', npsTourNext);
    tour.setOption('prevLabel', npsTourPrev);
    tour.setOption('skipLabel', npsTourSkip);
    tour.setOption('doneLabel', npsTourDone);
    tour.oncomplete(endNewEventTour);
    tour.onexit(endNewEventTour);
    tour.start();
}

function endNewEventTour() {
    localStorage.setItem("newEventTour", true);
}

function addQuestion(id_form) {
    if (validateScope(id_form)) {
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
        data['required'] = $(id_form + ' [name=required]').is(':checked');

        productQuestions.push(data);
        clearQuestions();
        populateQuestions();
        $.fancybox.close();
        clearFancyboxForm(id_form);
        window.dispatchEvent(new Event('resize'));
    }
}

function clearQuestions() {
    $('.questions-container').empty();
}

function populateQuestions() {
    if (productQuestions.length > 0) {
        $('.no-questions').hide();
        $.each(productQuestions, function(index, question) {
            question['index'] = index;
            renderQuestion(question);
        });
    } else {
        $('.no-questions').show();
    }
    $("input[type='checkbox']:not(.comparator)").uniform();
}

function renderQuestion(question) {
    $('.questions-container').append(tmpl("question-tmpl", question));
}