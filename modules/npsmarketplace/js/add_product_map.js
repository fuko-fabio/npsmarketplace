function initialize() {

  var defaultLatLng = new google.maps.LatLng(50.0646, 19.9449);
  var mapOptions = {
    center : defaultLatLng,
    zoom : 10,
    mapTypeId : google.maps.MapTypeId.ROADMAP
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions );
  var geocoder = new google.maps.Geocoder();

  var marker = new google.maps.Marker({
    map: map,
    position: defaultLatLng,
    draggable: true,
    animation: google.maps.Animation.DROP,
  });

  var inputId = 'map-address-input';
  // Create the search box and link it to the UI element.
  var input = (document.getElementById(inputId));

    // Block Enter events
  $('#' + inputId).bind('keypress keydown keyup', function(e){
    if(e.keyCode == 13) { e.preventDefault(); }
  });

  $('#product_town').on('change', function() {
    $('#map-address-input').val(this.value);
    codeAddress(this.value);
  });

  codeAddress($('#' + inputId).val());

  //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  var searchBox = new google.maps.places.SearchBox((input));

  // [START region_getplaces]
  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    var bounds = new google.maps.LatLngBounds();

    marker.setTitle(places[0].name);
    marker.setPosition(places[0].geometry.location);
    bounds.extend(places[0].geometry.location);
    map.fitBounds(bounds);
    map.setZoom(16);
  });

  google.maps.event.addListener(marker, 'dragend', function() {
    var pos = marker.getPosition();
    $('#map-lat-input').val(pos.lat());
    $('#map-lng-input').val(pos.lng());
    geocodePosition(pos);
  });

  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });

  function geocodePosition(pos) {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode
    ({ latLng: pos }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        $('#' + inputId).val(results[0].formatted_address);
      }
    });
  }

  function codeAddress(address) {
    geocoder.geocode({ 'address' : address }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        marker.setPosition(results[0].geometry.location);
        map.setCenter(results[0].geometry.location);
      }
    });
  }
}

google.maps.event.addDomListener(window, 'load', initialize);

$(document).ready(function(){
    Dropzone.options.dropzoneContainer = {
    
        url: 'modules/npsmarketplace/dropzone.php?token=' + $('input[name="form_token"]').val(),
        addRemoveLinks: true,
        autoProcessQueue: true,
        uploadMultiple: false,
        parallelUploads: 1,
        maxFiles: maxImages,
        previewsContainer: ".dropzone-previews",
        acceptedFiles: ".jpeg,.gif,.png,.jpg",
        maxFilesize: maxImageSize,
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
        existingPreviewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n    <div class=\"dz-size\" data-dz-size></div>\n    <img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>✔</span></div>\n  <div class=\"dz-error-mark\"><span>✘</span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>",
        init: function() {
            var that = this;
            this.on("success", function(file, response) {
                n = file['name'];
                $('#' + that.element.id).append(
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][name]" value="' + response['name'] + '"/>' +
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][save_path]" value="' + response['save_path'] + '"/>' +
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][size]" value="' + response['size'] + '"/>' +
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][tmp_name]" value="' + response['tmp_name'] + '"/>' +
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][type]" value="' + response['type'] + '"/>' +
                    '<input class="hidden" data-target="' + n + '" name="images[' + n + '][error]" value="' + response['error'] + '"/>'
                );
            });
            this.on("removedfile", function(file, response) {
                $('input[data-target="' + file['name'] + '"]').remove();
                $.ajax({
                    url: that.options.url + '&name=' + file['name'],
                    type: 'DELETE',
                });
            });
            for (var i = 0; i < dropzoneImages.length; ++i) {
                var file = {
                    accepted: true,
                    dataUrl: dropzoneImages[i]['url'],
                    name: '',
                    idImage: dropzoneImages[i]['id_image']
                };

                if (this.previewsContainer) {
                    file.previewElement = Dropzone.createElement(this.options.existingPreviewTemplate.trim());
                    file.previewTemplate = file.previewElement;
                    this.previewsContainer.appendChild(file.previewElement);

                    if (this.options.addRemoveLinks) {
                        file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\" data-dz-remove>" + this.options.dictRemoveFile + "</a>");
                        file.previewElement.appendChild(file._removeLink);
                    }
                    var removeFileEvent = (function(_this, file) {
                        return function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            var item, _i, _len, _results;
                            _results = [];
                            for (_i = 0, _len = _this.files.length; _i < _len; _i++) {
                              item = _this.files[_i];
                              if (item !== file) {
                                _results.push(item);
                              }
                            }
                            _this.files = _results;
                            
                            _this.emit("removedfile", file);
                            if (_this.files.length === 0) {
                                _this.emit("reset");
                            }
                            $('#' + _this.element.id).append('<input class="hidden" name="removed_images[]" value="' + file.idImage + '"/>');
                        };
                    })(this, file);

                    var _ref2 = file.previewElement.querySelectorAll("[data-dz-remove]");
                    for ( _k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                        removeLink = _ref2[_k];
                        removeLink.addEventListener("click", removeFileEvent);
                    }

                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    file.previewElement.classList.add("dz-success");
                    var _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    for ( _i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        thumbnailElement.src = file.dataUrl;
                    }
                    this.files.push(file);
                }
            }
        }
    };
});