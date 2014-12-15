$(document).ready(function() {
    $('.nps-towns-top').hover(function() {
      $(this).find('.toogle_content').stop(true, true).delay(200).fadeIn(200);
    }, function() {
      $(this).find('.toogle_content').stop(true, true).delay(200).fadeOut(200);
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
