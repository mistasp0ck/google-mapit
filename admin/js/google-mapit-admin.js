(function( $ ) {
	'use strict';

	$( document ).ready(function($) {

	  $('.upload-media').click(function(e) {
      var $this = $(this);
      var imgIdInput = $this.parent().find( '.meta-img-id');
      e.preventDefault();
      var image = wp.media({ 
           title: 'Upload Image',
           // mutiple: true if you want to upload multiple files at once
           multiple: false
      }).open()
      .on('select', function(e){

           // This will return the selected image from the Media Uploader, the result is an object
           // Get media attachment details from the frame state
           var attachment = image.state().get('selection').first().toJSON();
           // Output to the console uploaded_image
           // Let's assign the url value to the input field
           $this.prev().val(attachment.url);
           if ($this.parent().find('.image-preview').length > 0) {
               $this.parent().find('.image-preview').attr( 'src' , attachment.url);    
           } else {
               $this.prevAll('.preview').html('<img src='+attachment.url+' class="image-preview" style="max-width:100%" />');   
               $this.val('Change Image'); 
           }
           // Send the attachment id to our hidden input
           imgIdInput.val( attachment.id );

      });
    });

    $('.reset-btn').click(function(e) {
      e.preventDefault();
      var $this = $(this);
      var imgIdInput = $this.parent().find( '.meta-img-id');

      $this.parent().find('.image-preview').remove();
      imgIdInput.val('');
    });

   $('.colorpicker').wpColorPicker();


  });




})( jQuery );    


var placeSearch, autocomplete;
var componentForm = {
  street_addy: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  postal_code: 'short_name',
  country: 'short_name',
  lat: 'short_name',
  lng: 'short_name'
};

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
      {types: ['geocode']});

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();

  console.log(place);
  for (var component in componentForm) {
  	console.log(component);
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }

  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
  if(componentForm['street_addy']) {
  	var val = place.name;
  	document.getElementById('street_addy').value = val;
  }

  // Add Latitude and Longitude to hidden fields
  if (componentForm['lat']) {
  	var val = place.geometry.location.lat();
  	document.getElementById('lat').value = val;

  }
  if (componentForm['lng']) {
  	var val = place.geometry.location.lng();
  	document.getElementById('lng').value = val;

  }
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}
