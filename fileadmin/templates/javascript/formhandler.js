jQuery(document).ready( function() {
	jQuery("#check_message").change( function() {
		if ( jQuery(this).filter(':checked').length == 0 ) {
			jQuery('#message').attr('disabled','disabled');
			jQuery('#message').addClass('disabledField');
			jQuery('#messageTitle').addClass('disabledField');
		} else {
			jQuery('#message').removeAttr('disabled');
			jQuery('#message').removeClass('disabledField');
			jQuery('#messageTitle').removeClass('disabledField');
		}
	}).trigger('change'); // directly call the handler
	jQuery('#check_message').change( function() {
		if ( jQuery('#check_message:checked,#newsletter:checked').length == 0 ) {
			jQuery('#newsletter').attr('checked','checked');
		}
	});
	jQuery('#newsletter').change( function() {
		if ( jQuery('#check_message:checked,#newsletter:checked').length == 0 ) {
			jQuery('#check_message').attr('checked','checked');
		}
	});
});
