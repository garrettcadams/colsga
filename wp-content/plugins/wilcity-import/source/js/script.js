(function ($) {
	'use strict';

	function importDemo(mode, $this, didit){
		let canUnzip = (typeof didit !== 'undefined');

		let homepage = $('#homepage').val();
		let pageBuilder = $('#pagebuilder').val();
		$this.find('.button').addClass('loading');
		$this.find('.notification').addClass('visible');

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wilcity_importing_demo',
				homepage: homepage,
				pagebuilder: pageBuilder,
				didit: didit,
				canUnzip: canUnzip,
				mode: mode
			},
			success: function (response) {
				if ( typeof response.data.done === 'undefined' ){
					$this.find('.list').append('<li>'+response.data.msg+'</li>');
					let nextProgress = setTimeout(function () {
						importDemo(mode, $this, response.data.didit);
						clearTimeout(nextProgress);
					}, 500);
				}else{
					if ( typeof response.data.item_error !== 'undefined' && response.data.item_error ){
						$this.find('.list').append('<p class="error" style="color: red">'+response.data.msg+'</p>');
					}else{
						$this.find('.list').append('<p>'+response.data.msg+'</p>');
					}

					$this.find('.system-running').remove();
					$this.find('.button').removeClass('loading');
				}
			}
		})
	}

	$('#wilcity-import-everything').on('submit', function (event) {
		event.preventDefault();
		importDemo('everything', $(this));
	});

	$('#wilcity-import-homes').on('submit', function (event) {
		event.preventDefault();
		importDemo('homes', $(this));
	});

})(jQuery);