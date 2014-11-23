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
        url : "modules/npsmarketplace/npsmarketplace_ajax.php?changeTown=1&id_town=" + id_town,
        type : "POST",
        headers : {
            "cache-control" : "no-cache"
        },
        dataType : "json",
        success : function(result) {
            location.reload();
        }
    });
}
