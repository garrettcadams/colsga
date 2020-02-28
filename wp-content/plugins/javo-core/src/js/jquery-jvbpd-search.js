( function( $ ) {

	if( typeof jvbpd_search1_param == 'undefined' ) {
		jvbpd_search1_param = {};
	}

	var jv_search1_shortcode = function( id, request ){
		this.el = $( id );
		this.param = jvbpd_search1_param || {};
		this.request = request;
		this.initialize();
	}

	jv_search1_shortcode.prototype = {
		constructor	: jv_search1_shortcode,

		initialize : function() {
			var
				obj			= this,
				is_mobile	= obj.el.hasClass( 'is-mobile' ),
				elSelectize	= $( '[data-selectize]', obj.el );

			!is_mobile && elSelectize.length && typeof $.fn.selectize == 'function' && obj.bindSelectize( elSelectize );

			if( window.jvbpd_core_elementor ){
				window.jvbpd_core_elementor.searchFormSubmit();
			}

			if($.lava_ajax_search) {
				$.lava_ajax_search();
			}

			obj
				.setAutoComplete()
				.setupGooglePlaceField()
				.setupGoogleAutocompleteField()
				.amenitiesOpener()
				.lavaAjaxSearch();

			$( document )
				.on( 'click', obj.el.find( '.jv-search1-form-opener' ).selector, obj.toggleForm() )
				.on( 'click', obj.el.find( '.jv-search1-morefilter-opener' ).selector, obj.toggleMoreFilter() )
				.on( 'click', obj.el.find( '.javo-geoloc-trigger' ).selector, obj.GeoLocation() )
				.on( 'click', obj.el.find( '.jvbpd-switcher' ).selector, obj.locate_google_switch() )
				.on( 'keypress', obj.el.find( '.field-listing_location_with_google_search input[type="text"]' ).selector, obj.inputSubmit() );
		},

		setupGooglePlaceField : function() {

			var element		= $( "[name='radius_key']", this.el )[0];

			if( typeof element != 'undefined' ) {
				new google.maps.places.Autocomplete( element );
			}

			return this;
		},

		getGoogleCurrentLocation : function(element) {
			var $el = $(element);
			var geoCode = new google.maps.Geocoder();
			if(navigator.geolocation){
				navigator.geolocation.getCurrentPosition(function(pos){
					var gLatLng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
					geoCode.geocode({latLng:gLatLng}, function(results, status){
						if(status == google.maps.GeocoderStatus.OK && results[0]){
							$el.val(results[0].formatted_address);
						}
					});
				});
			}
		},

		setupGoogleAutocompleteField : function() {
			/* var jsonURI = 'https://maps.googleapis.com/maps/api/place/queryautocomplete/json'; */
			var self = this;
			var strings = self.param.strings;

			$('.field-google_current_loadtion_search').each(function(){
				var $this = $(this);
				var $input = $('input[name="radius_key"]', $this);
				$input.autocomplete({
					source:[
						{
							type: 'get',
							value: 'current',
							label: strings.current_location,
						}
					],
					search: function(){
						if(0 < this.value.length) {
							return false;
						}
					},
					select: function(event, ui) {
						var term = ui.item.value;
						if('current' == term) {
							self.getGoogleCurrentLocation(this);
						}
						return false;
					},
					minLength:0,

				}).on('focus', function(event){
					$(this).autocomplete('instance').search($(this).val());
				}).autocomplete('instance')._renderItem = function(ul, item) {
					return $('<li>').css({
						color:'blue'
					})
					.append($('<i class="fa fas fa-location-arrow"></i>' + ' '))
					.append(item.label)
					.appendTo(ul);
				}
			});
			return this;
		},

		setAutoComplete : function() {

			var
				obj = this,
				tags = [],
				keyword = $( '.field-keyword input[name="keyword"]', obj.el );

			if( typeof jvbpd_search_field_tags != 'undefined' ) {
				tags = jvbpd_search_field_tags;
			}

			keyword.typeahead( {
				hint					: false
				, highlight			: true
				, minLength		: 1
			}, {
				name			: 'tags'
				, displayKey	: 'value'
				, source		: this.keywordMatchesCallback( tags )
			}).closest('span').css( { width: '100%' } );
			return obj;
		},

		keywordMatchesCallback : function( tags ) {
			return function keywordFindMatches( q, cb ) {
				var matches, substrRegex;

				substrRegex		= new RegExp( q, 'i');
				matches			= [];

				$.each( tags, function( i, tag ){
					if( substrRegex.test( tag ) ){
						matches.push({ value : tag });
					}
				});
				cb( matches );
			}
		},

		toggleForm : function() {
			var obj				= this;
			return function( e ){
				e.preventDefault();
				var
					container	= $( this ).closest( '.javo-shortcode' )
					, form		= $( '[data-form]', container );

				if( container.hasClass( 'active' ) ) {
					form.slideUp( function() {
						container.removeClass( 'active' );
					} );

				}else{
					container.addClass( 'active' );
					form.slideDown();
				}
			}
		},

		toggleMoreFilter : function() {
			var obj				= this;

			return function (e)
			{
				e.preventDefault();

				var
					container		= $( this ).closest( '.javo-shortcode' )
					, morePanel	= $( '.jv-search1-morefilter-row', container );

				if( container.hasClass( 'more-open' ) ) 	{

					morePanel.slideUp( function() {
						container.removeClass( 'more-open' );
					});

				}else{
					container.addClass( 'more-open' );
					morePanel.slideDown();
				}
			}
		},

		GeoLocation : function() {
			var
				self = this,
				strings = self.param.strings,
				geocoder = new google.maps.Geocoder;

			return function( e ) {
				e.preventDefault();

				var
					parent = $( this ).closest( '.search-box-inline' ),
					field = $( 'input[name="radius_key"]', parent );
				navigator.geolocation.getCurrentPosition(function(position) {

					var
						address ='',
						geolocate = new google.maps.LatLng( position.coords.latitude, position.coords.longitude ) ;

					geocoder.geocode({ 'location' : geolocate }, function( results, status ) {

						if( status === 'OK' ) {
							address = results[1].formatted_address;
							field.val( address );
						}else{
							alert( strings.geolocation_fail );
						}

					});

				});


				/*
				var form = $( this ).closest( 'form' );

				alert( 'aaaaaaaa' );

				if( self.el.data( 'not-submit' ) ) {
					$( document ).trigger( 'getMyPosition' );
					return false;
				}

				$( this ).addClass( 'fa-spin' );

				$( "<input>" )
					.attr({
						name	: 'geolocation',
						type		: 'hidden',
						value		: '1'
				}).appendTo( form );

				form.submit();
				*/

			}
		},

		locate_google_switch : function() {
			var self = this;
			return function() {
				var
					is_google = ! $( this ).hasClass( 'active' ),
					parent = $( this ).parent();

				if( is_google ) {
					$( '.field-google', parent ).addClass( 'hidden' );
					$( '.field-location', parent ).removeClass( 'hidden' );
					$( this ).addClass( 'active' );
				}else{
					$( '.field-location', parent ).addClass( 'hidden' );
					$( '.field-google', parent ).removeClass( 'hidden' );
					$( this ).removeClass( 'active' );
				}

			}

		},

		getSelectizeOption : function( element, taxonomy, keyword ) {
			var
				self = this,
				is_with_keyword = typeof keyword != 'undefined';
			if( is_with_keyword ) {
				return {
					preload: 'focus',
					mode : element.data( 'mode' ) || false,
					plugins : [ 'remove_button' ],
					valueField: 'term_id',
					labelField: 'name',
					searchField: [ 'name' ],
					options: [],
					persist: false,
					loadThrottle: 600,
					create: false,
					allowEmptyOption: false,
					render : {
						option : function( item, escape ) {
							var
								icons = JSON.parse( self.param.icons ),
								icon = typeof icons[ item.term_id ] != 'undefined' ? icons[ item.term_id ] : ( taxonomy == 'listing_location' ? 'fa fa-map-marker' : '' ),
								title = typeof item.name != 'undefined' ? item.name : item.text;
							return '<div class=""><i class="' + icon + '"></i>' + escape( title ) + '</div>';
						}
					},
					load : function( query, callback ){
						if( ! query.length ){
							return callback();
						}
						self.el.addClass( 'ajax-processing' );
						$.post(
							self.request,
							{ action: 'jvbpd_search_get_keywords', 'query': query },
							function( xhr ) { callback( xhr.slice( 0, 5 ) ) }, 'JSON'
						).done( function(){ self.el.removeClass( 'ajax-processing' ); } );
					}
				}
			/*}else if( taxonomy == 'listing_location' ) {
				return {

				}; */
			}else{
				return {
					maxItems : element.data( 'max-items' ) || null,
					mode : element.data( 'mode' ) || false,
					plugins : [ 'remove_button' ],
					render : {
						option : function( item, escape ) {
							var
								icons = JSON.parse( self.param.icons ),
								icon = typeof icons[ item.term_id ] != 'undefined' ? icons[ item.term_id ] : ( taxonomy == 'listing_location' ? 'fa fa-map-marker' : '' ),
								title = typeof item.name != 'undefined' ? item.name : item.text;
							return '<div class=""><i class="' + icon + '"></i>' + escape( title ) + '</div>';
						}
					}
				};
			}

		},

		bindSelectize : function( elements ){
			var self = this;
			elements.each( function( idx, element ){
				var
					$this = $(this),
					$tax = $this.data('tax');
				$this.selectize( self.getSelectizeOption( $this, $this.data( 'tax' ), $this.data( 'keyword' ) ) );
				$this.get(0).selectize.on('change', function(_value){
					var $elFilter = $('select[data-tax="' + $tax + '"].selectized');
					if(window.jvbpd_search_filtering){
						return;
					}
					window.jvbpd_search_filtering = true;
					$elFilter.each(function(){
						var $elFilterSelectize = $(this).get(0).selectize;
						$elFilterSelectize.setValue(_value);
					});
					window.jvbpd_search_filtering = false;
				});
			} );
			if( typeof $.fn.slimScroll == 'function' ) {
				$( '.selectize-dropdown-content' ).slimScroll({
					height: '150px'
				});
			}
			return true;
		},

		amenitiesOpener : function() {

			var obj = this;

			$( document ).on(
				'click',
				obj.el.find( '.bottom-amenities-opener-button' ).selector,
				function( e ){
					e.preventDefault();
					obj.el.toggleClass( 'advance-collapse' );
				}
			);
			return this;
		},

		lavaAjaxSearch : function() {
			var
				self = this,
				container = $( ".field-ajax_search", self.el ),
				searchbar = $( 'input[data-search-input]', container );

			searchbar.data( 'origin-width', searchbar.outerWidth() );

			/**
			searchbar.on( 'focus', function() {

				/*
				$( this ).animate({ width: '+=100' }, 'fast', function(){
					$( '.lava_ajax_search' ).outerWidth( $( this ).outerWidth() + 100 );
				}, { step: function(){  console.log( 'step' ); } } ); * /
				$( this ).animate({
					width: '+=100',
				}, {
					duration: '10',
					easing: 'linear',
					step: function( now ) {
						$( '.lava_ajax_search' ).width( now );
					}
				});
			} ).on( 'blur', function() {
				$( this ).animate({ width: searchbar.data( 'origin-width' ) }, 'fast' );
			} ); **/

			$( '.clear', container ).on( 'click', function() {
				var form = container.closest('form');
				searchbar.val( '' );
				$('[name="category"]', container).val('');
				if(form.length) {
					form.submit();
				}
				$( this ).addClass( 'hidden' );
			} );

			searchbar.on( 'keyup', function() {
				if( $( this ).val().length ) {
					$( '.clear', container ).removeClass( 'hidden' );
				}else{
					$( '.clear', container ).addClass( 'hidden' );
				}
			} );


			if( searchbar.data( 'uiAutocomplete' ) ) {
				searchbar.autocomplete( 'option', 'select', function( event, ui ) {
					if( ui.item.type == 'listing_category' ) {
						event.preventDefault();
						searchbar.val( ui.item.label );
						$( 'input[name="category"]', container ).val( ui.item.label );
						$( '.clear', container ).removeClass( 'hidden' );
						return true;
					}
					return false;
				} );
			}

			$( window ).on( 'lava:ajax-search-before-send', function( event, form ) {
				$( form ).closest( '.search-box-inline.field-ajax_search' ).addClass( 'ajax-loading' );
			} );

			$( window ).on( 'lava:ajax-search-complete', function( event, form ) {
				$( form ).closest( '.search-box-inline.field-ajax_search' ).removeClass( 'ajax-loading' );
			} );

			$( window ).on( 'javo:ajax-search-select', function( event, ui ) {
				$( '.clear', container ).removeClass( 'hidden' );
			} );
		},

		inputSubmit : function() {
			return function( event ) {
				if( event.keyCode ==13 ) {
					$( this ).closest( 'form' ).submit();
				}
			}
		}
	};
	$.jvbpd_search_shortcode = function( i, r ) {
		new jv_search1_shortcode( i, r );
	}

		$( '.jvbpd-btn-search1-opener' ).on( 'click', function() {
				var header = $( '.jvbpd-header-map-filter-container' );
				if( header.hasClass( 'show' ) ) {
					header.removeClass( 'show' );
				}else{
					header.addClass( 'show' );
				}
			} );

} )( jQuery );