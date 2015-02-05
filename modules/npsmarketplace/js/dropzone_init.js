/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

$(document).ready(function(){
    $('.waitForImages').hide();
    Dropzone.options.dropzoneContainer = {
    
        url: dropzoneServerUrl,
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
            this.on('processing', function() {
                $('.waitForImages').slideDown('slow');
                $('button[type="submit"]').prop('disabled', true);
            });
            this.on('queuecomplete', function() {
                $('.waitForImages').hide('slow');
                $('button[type="submit"]').prop('disabled', false);
            });
            this.on("success", function(file, response) {
                var path = response['save_path'];
                $('#' + that.element.id).append(
                    '<input class="hidden" data-target="' + path + '" name="images[' + path + '][name]" value="' + response['name'] + '"/>' +
                    '<input class="hidden" data-target="' + path + '" name="images[' + path + '][size]" value="' + response['size'] + '"/>' +
                    '<input class="hidden" data-target="' + path + '" name="images[' + path + '][tmp_name]" value="' + response['tmp_name'] + '"/>' +
                    '<input class="hidden" data-target="' + path + '" name="images[' + path + '][type]" value="' + response['type'] + '"/>' +
                    '<input class="hidden" data-target="' + path + '" name="images[' + path + '][error]" value="' + response['error'] + '"/>'
                );
            });
            this.on("removedfile", function(file) {
                if (!file.xhr) {
                    return;
                }
                var response;
                try {
                    response = JSON.parse(file.xhr.response);
                } catch(error) {
                    console.warn('Unable to remove file: ' + error);
                    return;
                }
                var path = response['save_path'];
                if (path) {
                    $('input[data-target="' + path + '"]').remove();
                    $.ajax({
                        url: that.options.url + '&name=' + path,
                        type: 'DELETE',
                    });
                }
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