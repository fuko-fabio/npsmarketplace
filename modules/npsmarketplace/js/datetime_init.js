/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    $('#date_picker').datetimepicker({
        pickTime: false
    });

    $('#date_picker').on('changeDate', function(e) {
        $('#date_picker').datetimepicker('hide');
        $('#expiry_date_picker').data("datetimepicker").setDate($('[name=date]').val());
    });

    $('[name=date]').on('click', function() {
        $('#date_picker').datetimepicker('show');
    });

    $('#time_picker').datetimepicker({
        pickDate: false,
        pickSeconds: false,
        minuteStepping: 15
    });

    $('#time_picker').on('changeDate', function(e) {
        $('#expiry_time_picker').data("datetimepicker").setDate($('[name=time]').val());
    });

    $('[name=time]').on('click', function(e) {
        if ($('[name=time]').val().length == 0) {
            $('#time_picker').data("datetimepicker").setValue(getDateTime());
        }
        $('#time_picker').datetimepicker('show');
    });
    
    $('#expiry_date_picker').datetimepicker({
        pickTime: false
    });

    $('#expiry_date_picker').on('changeDate', function(e) {
        $('#expiry_date_picker').datetimepicker('hide');
    });

    $('[name=expiry_date]').on('click', function() {
        $('#expiry_date_picker').datetimepicker('show');
    });

    $('#expiry_time_picker').datetimepicker({
        pickDate: false,
        pickSeconds: false,
        minuteStepping: 15
    });

    $('[name=expiry_time]').on('click', function(e) {
        if ($('[name=expiry_time]').val().length == 0) {
            $('#expiry_time_picker').data("datetimepicker").setValue(getDateTime());
        }
        $('#expiry_time_picker').datetimepicker('show');
    });
/*

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
    });*/

});

function getDateTime() {
    var now = new Date(); 
    now.setMinutes(0);
    now.setSeconds(0);
    return now;
}