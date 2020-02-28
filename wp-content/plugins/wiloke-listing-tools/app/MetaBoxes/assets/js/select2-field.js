;(function ($) {
	'use strict';

	$(document).ready(function () {
		let $select2 = $('.wiloke-select2');

		$select2.each(function () {
			let $this = $(this);

			$this.select2({
				ajax: {
					url: ajaxurl,
					data: function (params) {
						return {
							q: params.term,
							action: $this.attr('ajax_action'),
							post_types: $this.attr('post_types')
						};
					},
					processResults: function (data, params) {
						if ( !data.success ){
							return false;
						}else{
							return typeof data.data.msg !== 'undefined' ? data.data.msg : data.data;
						}
					},
					cache: true
				},
				allowClear: true,
				placeholder: '',
				minimumInputLength: 1
			});
		});

	});

})(jQuery);