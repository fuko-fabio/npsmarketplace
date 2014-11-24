$(document).ready(function() {
    $(".towns").mouseover(function() {
        $(this).addClass("towns_hover");
        $(".towns_ul").addClass("towns_ul_hover");
    });
    $(".towns").mouseout(function() {
        $(this).removeClass("towns_hover");
        $(".towns_ul").removeClass("towns_ul_hover");
    });
});

function changeMainTown(id_town) {
    $.fancybox.showLoading();
    $.ajax({
        url : npsAjaxUrl,
        type : "POST",
        headers : {
            "cache-control" : "no-cache"
        },
        dataType : "json",
        data: {
            action: 'changeTown',
            id_town: id_town
        },
        success : function(result) {
            location.reload();
        }
    });
}
