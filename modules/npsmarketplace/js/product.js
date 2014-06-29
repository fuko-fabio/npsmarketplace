$(document).ready(function(){

/*
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
    });*/
// 

    $('#datePicker').datetimepicker({
        pickTime: false
    });
    $('#datePicker').on('changeDate', function(e) {
        console.log(e);
        $('#datePicker').datetimepicker('hide');
    });
    $('#product_date').on('click', function() {
        $('#datePicker').datetimepicker('show');
    });

    $('#timePicker').datetimepicker({
        pickDate: false,
        pickSeconds: false
    });
    $('#product_time').on('click', function(e) {
        console.log(e);
        $('#timePicker').datetimepicker('show');
    });

    $('#add_product').change(function() {
        toggleProductForm();
    }); 
    toggleProductForm();
});

function toggleProductForm() {
    var el = $('#add_product');
    if (el.length > 0) {
        if (el.is(':checked')) {
            $('#add_offer').show();
            $('#add_offer').find('.is_required').each(function() {
                $(this).attr('required', '');
            });
        } else {
            $('#add_offer').hide();
            $('#add_offer').find('.is_required').each(function() {
                $(this).removeAttr('required');
            });
        }
    }
}