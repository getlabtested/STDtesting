jQuery(document).ready(function() {
	try {

		if (! jQuery('#wpcf7-campaignmonitor-active').is(':checked'))
			jQuery('#campaignmonitor-fields').hide();

		jQuery('#wpcf7-campaignmonitor-active').click(function() {
			if (jQuery('#campaignmonitor-fields').is(':hidden')
			&& jQuery('#wpcf7-campaignmonitor-active').is(':checked')) {
				jQuery('#campaignmonitor-fields').slideDown('fast');
			} else if (jQuery('#campaignmonitor-fields').is(':visible')
			&& jQuery('#wpcf7-campaignmonitor-active').not(':checked')) {
				jQuery('#campaignmonitor-fields').slideUp('fast');
			}
		});

	} catch (e) {
	}
});