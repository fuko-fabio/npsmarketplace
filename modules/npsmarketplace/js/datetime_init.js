$(document).ready(function(){
    $('#datePicker').datetimepicker({
        pickSeconds: false
    });

    $('#date_time_input').on('click', function() {
        $('#datePicker').datetimepicker('show');
    });
});