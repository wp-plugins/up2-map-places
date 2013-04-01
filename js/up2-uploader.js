(function($) {
	var frame,
		field,
		fieldold;

	$( function() {
		// Build the choose from library frame.
		$('.up2-uploader-button').on( 'click', function(e) {
			e.preventDefault();

			fieldold = field;
			field = $(this).attr('id').replace('-button','');
			//console.log(wp.media.frames);

			// If the media frame already exists, reopen it.
			if ( frame && ( fieldold === field ) ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media.frames.customUp2 = wp.media({
				// Set the title of the modal.
				title: 'Choose Media',

				// Tell the modal to show only images.
//				library: {
//					type: 'application/pdf'
//				},

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: 'Select'
				}
			});

			// When an image is selected, run a callback.
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first().toJSON();
				// console.log(field);
				// Do something with attachment.id and/or attachment.url here
				$('#'+field).val(attachment.url);
				$('#map-icon-view').attr('src', attachment.url);
			});

			frame.open();
		});
	});
}(jQuery));