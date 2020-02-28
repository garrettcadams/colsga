;(function ($) {
    "use strict";

    $(document).ready(function () {
        let $imgField = $('.wiloke-image-field-id');
	    if ( $imgField.length > 0 )
	    {
		    $imgField.each(function() {
			    $(this).wilokePostFormatMediaPopup({multiple: false});
		    });
	    }

	    $(document).ajaxComplete(function (event, xhr, settings) {
	        setTimeout(function () {
                if ( $('#ajax-response').find('.error').length ){
                    return false;
                }

                let oSettings = {};

		        if ( (typeof settings.data !== 'undefined') && (settings.data.search('action=add-tag') === 0) ){
			        let $form = $('#addtag');
			        oSettings.color = $form.find('.wiloke-colorpicker').attr('value');
			        oSettings.headerImg = $form.find('.wiloke-wiloke_cat_settings_featured_image-wrapper .list-wiloke-image-media img').attr('src');

			        $form.find('input[name*=wiloke_]').attr('value', '');
			        $form.find('.wiloke-colorpicker').trigger('change');
			        $form.find('textarea[name*=wiloke_]').html('').trigger('change');
			        $form.find('.sp-preview-inner').css('background-color', '');
			        $form.find('.wiloke-image img').attr('src', '');
			        $form.find('.wiloke-image img').attr('src', '__');
			        if ( $form.find('.wiloke-use-select2').length ){
				        $form.find('.wiloke-use-select2').val('').trigger('change');
			        }

			        let $row = $('#the-list').find('tr:first');

			        if ( $row.find('.featured_image').length ){
			        	$row.find('.featured_image').html('<img width="50" height="50" src="'+oSettings.headerImg+'">');
			        }

			        if ( $row.find('.column-header_overlay').length ){
				        $row.find('.column-header_overlay').html('<span style="background-color: '+oSettings.color+'; width: 50px; height: 50px; display: inline-block; text-align: center; border-radius: 50%;"></span>');
			        }
		        }
	        }, 400);
	    });
    });

})(jQuery);