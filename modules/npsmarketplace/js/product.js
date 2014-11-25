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
        else if (this.value == '2')
            setAddForm();
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
    else if (type == '2')
        setAddForm();
    var carnettype = $('input[type=radio][name=carnet_type]:checked').val();
    if (carnettype == '0') {
        $('.number-entries').show();
        $('.time-period').hide();
    }
    else if (carnettype == '1') {
        $('.number-entries').hide();
        $('.time-period').show();
    }
});

function setTicketForm() {
    $('#product_amount, [for=product_amount]').show();
    $('#product_amount').attr('required', true);
    $('[for=date_input], [for=time_input]').addClass('required');

    $('#date_input, #time_input').attr('required', true);
    $('.ticket-attributes').show();
    $('.carnet-attributes').hide();
    $('.ad-attributes').hide();
}

function setCarnetForm() {
    $('#product_amount, [for=product_amount]').show();
    $('#product_amount').attr('required', true);

    $('#date_input, #time_input').removeAttr('required');
    $('.ticket-attributes').hide();
    $('.carnet-attributes').show();
    $('.ad-attributes').hide();
}

function setAddForm() {
    $('#product_amount, [for=product_amount]').hide();
    $('#product_amount').removeAttr('required');

    $('#date_input, #time_input').attr('required', true);
    $('[for=date_input], [for=time_input]').addClass('required');
    $('.ticket-attributes').show();
    $('h3.ticket-attributes').hide();
    $('.carnet-attributes').hide();
    $('.ad-attributes').show();
}
