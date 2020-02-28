jQuery(function($){
	// Set all variables to be used in scope
	// ADD IMAGE LINK
	$(document).on( 'click', '.wiloke_upload_image', function( event ){
		var frame = null,
			$this = $(this),
			type = typeof $this.data('get') === 'undefined' ? 'id' : $this.data('get'),
			$controller = $this.closest('.wiloke-widget-control'),
			$preview = $controller.find('.media-widget-preview'),
			$imgIdInput  = $controller.find('.wiloke_image');
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( $this.data('open') ) {
			$this.data('open').open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Select or Upload Image',
			button: {
				text: 'Use this Image'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		$this.data('open', frame);


		// When an image is selected in the media frame...
		frame.on( 'select', function() {

			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();

			// Send the attachment URL to our custom image input field.
			$preview.html( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

			// Send the attachment id to our input field
			if ( type === 'id' ){
				$imgIdInput.val( attachment.id );
			}else{
				$imgIdInput.val( attachment.url );
			}
		});

		// Finally, open the modal on click
		frame.open();
	});
});