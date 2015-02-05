$(document).ready(function() {
    $('.nps-location-top').hover(function() {
      $(this).find('.location-selector').stop(true, true).delay(0).fadeIn(200);
    }, function() {
      $(this).find('.location-selector').stop(true, true).delay(400).fadeOut(200);
    });
    $('li.province').hover(function() {
      $(this).find('ul').stop(true, true).delay(0).fadeIn(200);
    }, function() {
      $(this).find('ul').stop(true, true).delay(200).fadeOut(200);
    });
});

function changeMainProvince(id_province) {
    $.fancybox.showLoading();
    $.ajax({
        url : npsAjaxUrl,
        type : "POST",
        headers : {
            "cache-control" : "no-cache"
        },
        dataType : "json",
        data: {
            action: 'changeProvince',
            id_province: id_province
        },
        success : function(result) {
            location.reload();
        },
        error : function() {
            $.fancybox.hideLoading();
        }
    });
}

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
        },
        error : function() {
            $.fancybox.hideLoading();
        }
    });
}
