;(function ($) {
	'use strict';

	$(document).ready(function () {
		let $cmb2DaysChecked = $('.cmb2-id-aDaysChecked');

		$('#frequency.cmb2_select').on('change', function () {
			console.log($(this).val());
			if ( $(this).val() === 'weekly' ){
				$cmb2DaysChecked.fadeIn('slow');
			}else{
				$cmb2DaysChecked.fadeOut('slow');
			}
		}).trigger('change');

		$('#cmb2-metabox-event_settings').on('change', 'input, select, textarea', function () {
			$('#isFormChanged').val(1);
		})
	});

})(jQuery);