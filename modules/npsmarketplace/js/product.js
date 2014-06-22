$(document).ready(function(){
    // Tell FileDrop we want to integrate with jQuery
fd.jQuery();

var options = {iframe: {url: 'upload.php'}};

$('#zjquery')
  // Create FileDrop on the first node in this collection.
  .filedrop(options)
  .on('fdsend', function (jQueryEvent, files) {
    // 'fd' prefix is added to all FileDrop events that
    // are triggered on the zone element.
    files.each(function (file) {
      file.sendTo('upload.php');
    });

    // Or if you're more of a functional guy here's your call:
    //files.invoke('sendTo', 'upload.php');
  })
  .on('filedone', function (e, file, xhr) {
    // FileDrop binds events of all constructed Files to the
    // zone element and prefixes event names with 'file'.
    alert('Done uploading ' + file.name + ',' +
          ' response:\n\n' + xhr.responseText);
  })
  .on('fileerror', function (e, file, xhrError, xhr) {
    alert('Error uploading ' + file.name + ': ' +
          xhr.status + ', ' + xhr.statusText);
  })
  .on('fdiframedone', function (e, xhr) {
    alert('Done uploading via <iframe>, response:\n\n' + xhr.responseText);
  });

$('#zjquerym').change(function () {
  $('#zjquery')
    // Retrieve created FileDrop object (or create it):
    .filedrop()
    .multiple(true);
    // Would also work retaining 'this' as jQuery collection:
    //.filedrop('multiple', this.checked);
});
});