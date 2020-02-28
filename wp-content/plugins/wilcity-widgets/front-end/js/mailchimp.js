;(function ($) {
	'use strict';

	$(document).ready(function () {
		var $form = $('.wilcity-mailchimp-form');

		$form.on('submit', function (event) {
			var $target = $(event.target);
			event.preventDefault();
			$target.prev().addClass('hidden');

			$.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {
					action: 'wiloke_mailchimp_subscribe',
					data:{
						email: $target.find('input[type="email"]').val(),
						agreeToTerm: $target.find('input[name="agreeToTerm"]:checked').val()
					}
				},
				success: function (response) {
					if ( response.success ){
						$target.html('<div class="alert_module__Q4QZx alert_success__1nkos"><div class="alert_icon__1bDKL"><i class="la la-smile-o"></i></div><div class="alert_content__1ntU3">'+response.data+'</div>');
					}else{
						$target.prev().find('.err-msg').html(response.data);
						$target.prev().removeClass('hidden');
					}
				}
			})
		})
	});

})(jQuery);