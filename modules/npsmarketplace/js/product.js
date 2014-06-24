$(document).ready(function(){

    $("#company_logo").fileinput({
        browseClass: "btn btn-primary btn-block",
        showCaption: false,
        showUpload: false
    });

    $("#product_images").fileinput({
        showUpload: false,
        showCaption: false,
        showUpload: false,
        mainTemplate:
            "{preview}\n" +
            "<div class='input-group {class}'>\n" +
            "   <div class='input-group-btn'>\n" +
            "       {browse}\n" +
            "       {upload}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>"
    });

    $('#datePicker').datetimepicker({
        pickTime: false
    });
    $('#datePicker').on('changeDate', function(e) {
        $('#datePicker').datetimepicker('hide');
    });
    $('#product_date').on('click', function() {
        $('#datePicker').datetimepicker('show');
    });

    $('#timePicker').datetimepicker({
        pickDate: false,
        pickSeconds: false
    });
    $('#product_time').on('click', function() {
        $('#timePicker').datetimepicker('show');
    });
});