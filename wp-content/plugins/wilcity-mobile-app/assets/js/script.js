(function ($) {
	'use strict';

	$(document).ready(function () {
		$('#wilcity-compiler-code').on('click', function (event) {
			event.preventDefault();

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'wilcity_app_compiler',
					postID: $('#post_ID').val()
				},
				success: response => alert(response.data)
			})
		})
	})

})(jQuery);