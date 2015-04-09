/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    initDatePicker('#date_picker', '#t_expiry_date_picker');
    initDatePicker('#t_expiry_date_picker');
    initTimePicker('#time_picker', '#t_expiry_time_picker');
    initTimePicker('#t_expiry_time_picker');

    initDatePicker('#c_expiry_date_picker');
    initTimePicker('#c_expiry_time_picker');
    initDatePicker('#a_expiry_date_picker');
    initTimePicker('#a_expiry_time_picker');
    initDatePicker('#oa_expiry_date_picker');
    initTimePicker('#oa_expiry_time_picker');

    $('#fromDatePicker').datetimepicker({
        pickTime: false
    });

    $('#fromDatePicker').on('changeDate', function(e) {
        $('#fromDatePicker').datetimepicker('hide');
    });

    $('#fromDatePicker').on('click', function() {
        $('#fromDatePicker').datetimepicker('show');
    });

    $('#toDatePicker').datetimepicker({
        pickTime: false
    });

    $('#toDatePicker').on('changeDate', function(e) {
        $('#toDatePicker').datetimepicker('hide');
    });

    $('#toDatePicker').on('click', function() {
        $('#toDatePicker').datetimepicker('show');
    });

});

function initDatePicker(id_el, target_id_el) {
    $(id_el).datetimepicker({
        pickTime: false
    });
    
    $(id_el + ' input').on('click', function() {
        $(id_el).datetimepicker('show');
    });
    
    $(id_el).on('changeDate', function(e) {
        $(id_el).datetimepicker('hide');
        if (target_id_el) {
            $(target_id_el).data("datetimepicker").setDate(e.date.valueOf());
        }
    });
}

function initTimePicker(id_el, target_id_el) {
    $(id_el).datetimepicker({
        pickDate: false,
        pickSeconds: false,
        minuteStepping: 15
    });

    $(id_el).on('changeDate', function(e) {
        if (target_id_el) {
            $(target_id_el).data("datetimepicker").setDate(e.date.valueOf());
        }
    });

    $(id_el + ' input').on('click', function() {
        if ($(this).val().length == 0) {
            $(id_el).data("datetimepicker").setValue(getDateTime());
        }
        $(id_el).datetimepicker('show');
    });
}

function getDateTime() {
    var now = new Date(); 
    now.setMinutes(0);
    now.setSeconds(0);
    return now;
}