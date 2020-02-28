(function($){
	'use strict';

	$(document).ready(function () {
		$('#wilcity-notice-after-updating').on('click', '.notice-dismiss', function () {
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'wilcity_read_notice_after_updating'
				}
			})
		})
	})
})(jQuery);