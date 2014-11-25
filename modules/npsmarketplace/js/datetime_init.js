$(document).ready(function(){
    $('#datePicker').datetimepicker({
        pickTime: false
    });

    $('#datePicker').on('changeDate', function(e) {
        $('#datePicker').datetimepicker('hide');
        $('#availableDatePicker').data("datetimepicker").setDate($('#date_input').val());
    });

    $('#date_input').on('click', function() {
        $('#datePicker').datetimepicker('show');
    });

    $('#timePicker').datetimepicker({
        pickDate: false,
        pickSeconds: false,
        minuteStepping: 15
    });

    $('#time_input').on('click', function(e) {
        $('#timePicker').data("datetimepicker").setValue(getDateTime());
        $('#timePicker').datetimepicker('show');
    });
    
    $('#availableDatePicker').datetimepicker({
        pickTime: false
    });

    $('#availableDatePicker').on('changeDate', function(e) {
        $('#availableDatePicker').datetimepicker('hide');
    });

    $('#availableDatePicker').on('click', function() {
        $('#availableDatePicker').datetimepicker('show');
    });

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

function getDateTime() {
    var now = new Date(); 
    now.setMinutes(0);
    now.setSeconds(0);
    return now;
}