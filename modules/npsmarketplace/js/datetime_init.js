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
        pickSeconds: false
    });

    $('#time_input').on('click', function(e) {
        $('#timePicker').datetimepicker('show');
    });
    
    $('#availableDatePicker').datetimepicker({
        pickTime: false
    });

    $('#availableDatePicker').on('changeDate', function(e) {
        $('#availableDatePicker').datetimepicker('hide');
    });

    $('#available_date_input').on('click', function() {
        $('#availableDatePicker').datetimepicker('show');
    });
});