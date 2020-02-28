( function( $ ) {
	/**
	 * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	 */
	var WidgetHelloWorldHandler = function( $scope, $ ) {
		var $slider = $scope.find('#wilcity-swiper-preview');
		if ( $slider.length ){
			if ( typeof $scope.data('swiperInstance') !== 'undefined' ){
				$scope.data('swiperInstance').destroy(true, true);
			}

			var wrapper = $('.swiper-wrapper', $slider),
			optData = $slider.data('options'),
			optDefault = {
				paginationClickable: true,
				pagination: {
					el: $slider.find('.swiper-pagination-custom')
				},
				navigation: {
					nextEl: $slider.find('.swiper-button-next-custom'),
					prevEl: $slider.find('.swiper-button-prev-custom')
				},
				spaceBetween: 30
			},
			options = $.extend(optDefault, optData);
			wrapper.children().wrap('<div class="swiper-slide"></div>');
			var swiper = new Swiper($slider, options);
			$scope.data('swiperInstance', swiper);
		}

		//new wilHasValue('.js-field');
		$($scope).find('.js-select2').select2();
	};

	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/wilcity-hero.default', WidgetHelloWorldHandler );
	} );
} )( jQuery );