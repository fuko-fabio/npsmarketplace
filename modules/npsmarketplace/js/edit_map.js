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
    var text = '';
    if (this.value > 0) {
      text = $('#product_town').find('option[value=' + this.value + ']').text();
    }
    $('#map-address-input').val(text);
    $('#product_district').val('');
    codeAddress(text);
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
	var pos = marker.getPosition();
    $('#map-lat-input').val(pos.lat());
    $('#map-lng-input').val(pos.lng());
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
