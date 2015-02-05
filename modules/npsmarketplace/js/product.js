/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    $('input[type=radio][name=product_type]').change(function() {
        if (this.value == '0')
            setTicketForm();
        else if (this.value == '1')
            setCarnetForm();
        else if (this.value == '2' || this.value == '3')
            setAddForm(this.value);
        $('#date_input, #time_input, #entries, #date_from, #date_to').val('');
    });

    $('input[type=radio][name=carnet_type]').change(function() {
        if (this.value == '0') {
            $('.number-entries').show();
            $('.time-period').hide();
            $('#entries, #date_from, #date_to').val('');
        }
        else if (this.value == '1') {
            $('.number-entries').hide();
            $('.time-period').show();
            $('#entries, #date_from, #date_to').val('');
        }
    });
    setTicketForm();
    $(".textarea-autosize").autosize();
    var type = $('input[type=radio][name=product_type]:checked').val();
    if (type == '0')
        setTicketForm();
    else if (type == '1')
        setCarnetForm();
    else if (type == '2' || type == '3')
        setAddForm(type);

    CollapsibleLists.apply();
    $('.collapsibleListClosed').click();
});

function setTicketForm() {
    $('.type-info-carnet, .type-info-ad, .type-info-outer-ad').hide('slow');
    $('.type-info-ticket').slideDown('slow');
    $('.price-amount').show();
    $('#product_price, #product_amount').attr('required', true);
    $('[for=date_input], [for=time_input]').addClass('required');

    $('#date_input, #time_input').attr('required', true);
    $('.ticket-attributes').show();
    $('.carnet-attributes').hide();
    $('.ad-attributes').hide();
}

function setCarnetForm() {
    $('.type-info-ticket, .type-info-ad, .type-info-outer-ad').hide('slow');
    $('.type-info-carnet').slideDown('slow');
    $('.price-amount').show();
    $('#product_price, #product_amount').attr('required', true);

    $('#date_input, #time_input').removeAttr('required');
    $('.ticket-attributes').hide();
    $('.carnet-attributes').show();
    $('.ad-attributes').hide();
    var carnettype = $('input[type=radio][name=carnet_type]:checked').val();
    if (carnettype == '0') {
        $('.number-entries').show();
        $('.time-period').hide();
    }
    else if (carnettype == '1') {
        $('.number-entries').hide();
        $('.time-period').show();
    }
}

function setAddForm(type) {
    if (type == '2') {
        $('.type-info-ticket, .type-info-carnet, .type-info-outer-ad').hide('slow');
        $('.type-info-ad').slideDown('slow');
    } else {
        $('.type-info-ticket, .type-info-carnet, .type-info-ad').hide('slow');
        $('.type-info-outer-ad').slideDown('slow');
    }
    $('.price-amount').hide();
    $('#product_price, #product_amount').removeAttr('required');

    $('#date_input, #time_input').attr('required', true);
    $('[for=date_input], [for=time_input]').addClass('required');
    $('.ticket-attributes').show();
    $('h3.ticket-attributes').hide();
    $('.carnet-attributes').hide();
    $('.ad-attributes').show();
}
