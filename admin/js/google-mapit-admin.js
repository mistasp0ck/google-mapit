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