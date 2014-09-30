var geocoder;
var map;

function initialize() {
    geocoder = new google.maps.Geocoder();
    var map_canvas = document.getElementById('map-canvas');
    var map_options = {
        center : new google.maps.LatLng(50.0646, 19.9449),
        zoom : 12,
        mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(map_canvas, map_options);

    var address = $('#map-canvas').attr('data-target');
    geocoder.geocode({
        'address' : address
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map : map,
                position : results[0].geometry.location
            });
        }
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

$(document).ready(function(){
    Dropzone.options.dropzoneContainer = {

        url: 'empty',
        addRemoveLinks: true,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: maxAllowImages,
        maxFiles: maxAllowImages,
        previewsContainer: ".dropzone-previews",
        acceptedFiles: ".jpeg,.gif,.png,.jpg",
        maxFilesize: 8,
        dictDefaultMessage: dictDefaultMessage,
        dictFallbackMessage: dictFallbackMessage,
        dictFallbackText: dictFallbackText,
        dictFileTooBig: dictFileTooBig,
        dictInvalidFileType: dictInvalidFileType,
        dictResponseError: dictResponseError,
        dictCancelUpload: dictCancelUpload,
        dictCancelUploadConfirmation: dictCancelUploadConfirmation,
        dictRemoveFile: dictRemoveFile,
        dictRemoveFileConfirmation: null,
        dictMaxFilesExceeded: dictMaxFilesExceeded,
    };
});