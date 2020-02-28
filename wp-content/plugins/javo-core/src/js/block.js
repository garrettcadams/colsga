(  function($, window, undef ) {
	"use strict";

	var ajax_cache_obj = function() {
		this.data = new Array();
	}
	ajax_cache_obj.prototype.constructor = ajax_cache_obj;
	ajax_cache_obj.prototype.get = function( key ) {
		var key_name = this.sanitize_key( key );
		return this.data[key_name] || false;
	};

	ajax_cache_obj.prototype.set = function( key, value ) {
		var key_name = this.sanitize_key( key );
		this.data[key_name] = value;
	};

	ajax_cache_obj.prototype.sanitize_key = function( key ) {
		var
			objKey = key.attr || {},
			_return = '';

		if( typeof objKey == 'object' ) {
			$.each( objKey, function( i, k ) {
				if( k == '' || k == false || k == 'false' || k == null ) {
					delete objKey[ i ];
				}
			} );
			_return = JSON.stringify( objKey );
		}
		return _return;
	}

	window.JvbpdAjaxShortcodeCache = new ajax_cache_obj();

	/**
	 *
	 * Basic Shortcode
	 */

	var jvbpd_ajaxShortcode = function( el, attr ) {
		var
			exists,
			target = $( '#js-' + el ).closest( '.shortcode-container' ).attr( 'id' );

		this.ID = el;
		this.orginID	= target;
		this.el = $( '#' +	 this.orginID );
		this.attr = attr;
		exists = this.el.length;

		if( exists ) {
			this.init();
		}
	}

	jvbpd_ajaxShortcode.prototype = {

		constructor : jvbpd_ajaxShortcode,

		init : function() {

			var
				obj = this;
			obj.loadmore = false;
			obj.param = {};
			obj.param.attr = obj.attr;
			obj.param.action = 'jvbpd_ajaxShortcode_loader';

			JvbpdAjaxShortcodeCache.set( obj.param, $( '.shortcode-output', obj.el ).html() );

			obj.bindEvents();
			// obj.apply_lazyload();
			// obj.lazyload();

		},

		apply_lazyload : function() {
			var
				self = this,
				output = $( '.shortcode-output', self.el );

			$( '*', output ).each( function() {
				var bg = $( this ).css( 'background-image' );
				if( bg ) {
					$( this )
						.data( 'src', bg )
						.css( 'background-image', '' );
				}
			} );

			$( 'img', output ).each( function() {
				var $this = $( this );
				$.each( new Array( 'src', 'srcset' ), function( imgIndex, imgProp ) {
					var value = $this.attr( imgProp );
					if( value ) {
						$this.data( imgProp, value );
						$this.prop( imgProp, '' );
					}
				} );
			} );
		},

		bindEvents : function() {
			var
				obj = this,
				el = obj.el,
				flexMenuInstance;
			$( document )
				.on( 'click.' + obj.ID, el.find( "ul[data-tax] li:not(.flexMenu-viewMore)" ).selector, obj.category_filter() )
				.on( 'click.' + obj.ID, el.find( "a.page-numbers" ).selector, obj.pagination() )
				.on( 'jv:sc' + obj.ID, obj.slideShow() ).trigger( 'jv:sc' + obj.ID );

			$( window )
				.on( 'jvbpd_core/load.' + obj.ID, obj.carousel() )
				.on( 'jvbpd_core/shortcode/filter/before.' + obj.ID, obj.filterBefore() )
				.on( 'jvbpd_core/shortcode/filter/after.' + obj.ID, obj.filterAfter() )
				.trigger( 'jvbpd_core/shortcode/loaded', obj )
				.trigger( 'jvbpd_core/load.' + obj.ID )

			if( ! el.hasClass( 'no-flex-menu' ) ) {
				flexMenuInstance = obj.stackable();
				flexMenuInstance();
				$( window ).on( 'resize.' + obj.ID, flexMenuInstance );
			}
		},

		_carousel : function() {
			$( window ).on( 'load', function() {
				if( el.closest( '.javo-shortcode' ).hasClass( 'is-carousel' ) ) {
					/**
					var
						CAROUSEL_ITEMS = 'carousel-items',
						carouselOptions = el.closest( '.javo-shortcode' ).data( 'carousel' );
					$( '.shortcode-output script', el ).remove();
					carouselOptions.navText = new Array( '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' );
					$( '.shortcode-output', el ).addClass( 'owl-carousel owl-theme nav-pos-' + carouselOptions.nav_pos ).owlCarousel( carouselOptions );
					if( carouselOptions.mousewheel ) {
						$( '.shortcode-output', el ).on( 'mousewheel', '.owl-stage', function( e ) {
							if( e.originalEvent.deltaY >= 0 ) {
								$( '.shortcode-output', el ).trigger( 'next.owl' );
							}else{
								$( '.shortcode-output', el ).trigger( 'prev.owl' );
							}
							e.preventDefault();
						} );
					}
					return false;
					**/
				}
			} );
		},

		carousel : function() {
			var self = this;
			return function( event ) {
				var
					container = self.el.closest( '.javo-shortcode' ),
					output = $( '.shortcode-output', container ),
					isActiveCarousel = container.hasClass( 'is-carousel' ),
					sliderOptions = {},
					outputInstance;

				if( isActiveCarousel ) {
					sliderOptions = container.data( 'carousel' );
					if('yes' == sliderOptions.autoplay) {
						sliderOptions.autoplay = {
							'delay': sliderOptions.autoplay_speed,
						}
					}
					sliderOptions.breakpoints = {
						575: {
							slidesPerView : 1,
						},
						991: {
							slidesPerView : sliderOptions.slidesPerView > 1 ?  2 : 1,
						},
					};
					sliderOptions.loop = 'yes' == sliderOptions.loop;
					sliderOptions.navigation = {};
					sliderOptions.navigation.prevEl = sliderOptions.prevButton = '.swiper-button-prev';
					sliderOptions.navigation.nextEl = sliderOptions.nextButton = '.swiper-button-next';
				}else{
					return false;
				}

				if( typeof window.Swiper == 'undefined' ) {
					return false;
				}

				if( container.data( 'swiper' ) ) {
					container.data( 'swiper' ).destroy();
				}

				output.addClass( 'swiper-container' );
				$( '> .row > [class^="col-md"]', output ).prop( 'class', 'swiper-slide' );
				$( '> .row', output ).prop( 'class', 'swiper-wrapper' );
				container.data( 'swiper', new Swiper( $( output ), sliderOptions ) );
			}
		},

		category_filter : function() {
			var
				obj = this,
				element = obj.el;
			return function( e ) {
				e.preventDefault();

				var
					term_id = $(  this ).data( 'term' ),
					taxonomy = $(  this ).closest( 'ul.shortcode-filter' ).data( 'tax' );

				$( "ul[data-tax].shortcode-filter li", element ).removeClass( 'current' );
				$( this ).addClass( 'current' );

				if( taxonomy && obj.param.attr !== undef ){
					obj.param.attr.term_id = term_id;
					obj.param.attr.taxonomy	= taxonomy;
					obj.param.attr.paged = 1;
				}
				obj.filter( true );
				return;
			}
		},

		filterBefore : function() {
			var self = this;
			return function( event, element ){
				var output = $( '.shortcode-output', element );
				output.addClass( 'loading' ).removeClass( 'loaded' ).css( 'opacity', '.1' );
			}
		},

		filterAfter : function() {
			var self = this;
			return function( event, element ){
				var output = $( '.shortcode-output', element );
				output.removeClass( 'loading' ).addClass( 'loaded' ).css( 'opacity', '1' );
			}
		},

		pagination : function() {

			var obj		= this;
			return function( e ) {
				var
					current			= 0,
					direction		= '',
					strPagination	= $( this ).data( 'href' ),
					is_disabled		= $( this ).hasClass( 'disabeld' ),
					is_loadmore		= $( this ).hasClass( 'loadmore' ),
					is_prevNext		= $( this ).hasClass( 'prevNext' );

				e.preventDefault();

				if( is_disabled ) {
					return;
				}

				if( !is_loadmore && !is_prevNext ) {
					strPagination = $( this ).attr( 'href' );
				}

				if( strPagination ){
					strPagination = strPagination.split( '|' );
					if( strPagination[1]  !== 'undefined' ) {
						current = strPagination[1];
					}
				}

				if( is_loadmore ) {
					obj.loadmore = true;
					obj.param.attr.pagination = 'loadmore';
				}

				obj.param.attr.paged = current;
				obj.filter();
				return;
			}
		},

		filter : function( is_setTerm ) {
			var
				obj = this,
				param = obj.param,
				element = obj.el,
				parent = element.parent(),
				is_loadmore = obj.loadmore,
				loading = $( ".output-cover, .output-loading" , parent ),
				_cached = JvbpdAjaxShortcodeCache.get( param ),
				callbackComplete = function( xhr ) {
					var output = $( '> .shortcode-output', element );

					JvbpdAjaxShortcodeCache.set( param, xhr );

					if( element.hasClass( 'exists-wrap' ) ) {
						output = $( '> .shortcode-content > .shortcode-output', element );
					}

					$( document ).off( 'click.' + obj.ID + ' ' + 'jv:sc' + obj.ID );
					$( window ).off( 'resize.' + obj.ID + ' ' + 'scroll.' + obj.ID );
					delete window.__javoAjaxShortcode_instance;

					if( is_setTerm ) {
						output.html( xhr );
					}else{
						if( is_loadmore ) {
							$( ".loadmore", element ).closest( '.jv-pagination' ).remove();
							output.append( xhr );
						}else{
							output.html( xhr );
						}
					}

					loading.removeClass( 'active' );
					$( window )
						.trigger( 'scroll' )
						.trigger( 'jvbpd_core/shortcode/filter/after.' + obj.ID, element );
				};

			loading.addClass( 'active' );

			$( window ).trigger( 'jvbpd_core/shortcode/filter/before.' + obj.ID, element );
			if( false !== _cached ) {
				callbackComplete( _cached );
			}else{
				$.post( jvbpd_elementor_args.ajaxurl, param, callbackComplete, 'html' ).fail( function( xhr ) { console.log( xhr.responseText ); } );
			}
			return false;
		},

		stackable : function() {
			var
				obj = this,
				element	= obj.el,
				nav = $( "ul.shortcode-filter", element ),
				divPopup = $( "ul.flexMenu-popup", nav ),
				btnMore = $( "> li.flexMenu-viewMore > a", nav );

			return function(){
				var
					containerWidth	= $( ".shortcode-header", element ).innerWidth()
					, titleWidth	= $( ".shortcode-title", element ).outerWidth()
					, offsetX		= containerWidth - ( titleWidth + 1 );

				// nav.width( offsetX );
				nav.flexMenu({ undo : true }).flexMenu({
					showOnHover : true
					//, linkText	: nav.data( 'more' )
					, linkText : '<i class="fas fa-bars"></i>'
					//, linkTextAll	: nav.data( 'mobile' )
					, linkTextAll : '<i class="fas fa-bars"></i>'
				});
			}
		},

		lazyload : function(){

			var
				self = this,
				lazyStarted		= false,
				intWinScrollTop	= 0,
				intWinHeight	= 0,
				intSumOffset	= 0,
				intCurrentIDX	= 0,
				intInterval		= 100,
				objWindow		= $( window );

			return function(){
				var output = $( '.shortcode-output', self.el );
				intWinScrollTop	= objWindow.scrollTop();
				intWinHeight	= objWindow.height() * 0.9;
				intSumOffset	= intWinScrollTop + intWinHeight;

				$( '*', output ).each( function() {
					var
						$this = $( this ),
						bg = $this.data( 'src' );
					if( bg ) {
						if( intSumOffset > $this.offset().top ) {
							$( this ).css( 'background-image', bg );
						}
					}
				} );

				$( 'img', output ).each( function() {
					var $this = $( this );
					$.each( new Array( 'src', 'srcset' ), function( imgIndex, imgProp ) {
						var value = $this.attr( imgProp );
						if( value ) {
							if( intSumOffset > $this.offset().top ) {
								$this.data( imgProp, value );
								$this.prop( imgProp, '' );
							}
						}
					} );
				} );

				/*
				if( ( intSumOffset > self.el.offset().top ) && !lazyStarted ) {
					$( 'img.jv-lazyload, div.jv-lazyload', obj.el ).each( function( i, el ){
						var nTimeID = setInterval( function(){
							$( el ).addClass( 'loaded' );
							clearInterval( nTimeID );
						}, i * intInterval );
					});
				} */
			}
		},

		slideShow : function(){
			var
				obj				= this
				, el				= obj.el
				, output		= $( "> .shortcode-output", el )
			return function(){
				if( !el.hasClass( 'is-slider' ) || !$.fn.flexslider )
					return;

				$( "> .slider-wrap", output ).flexslider({
					animation		: 'slide'
					// Note : Flexslider.css Dot nav Padding Problem...
					, controlNav	: el.hasClass( 'circle-nav' )
					, slideshow		: el.hasClass( 'slide-show' )
					, direction		: el.hasClass( 'slider-vertical' ) ? 'vertical' : 'horizontal'
					, smoothHeight: true
				});
			}
		}
	}

	$.jvbpd_ajaxShortcode = function( element, args ){
		window.__javoAjaxShortcode_instance = new jvbpd_ajaxShortcode( element, args );
	};

	/**
	 *
	 * Buddypress Shortcode
	 */

	var jvbpd_bp_shortcode = function( el ){
		this.el = $( el );
		this.id = this.el.data('id');
		this.type = this.el.data('type');
		this._filter = $( '.shortcode-nav', this.el ).data( 'param' ) || {};
		this._filter.append = false;
		this._filter.page = 1;
		this.init();
	}

	jvbpd_bp_shortcode.prototype.constructor = jvbpd_bp_shortcode;

	jvbpd_bp_shortcode.prototype.init = function() {
		this.bindEvent();
		this.bindAnimOnScroll();
	}

	jvbpd_bp_shortcode.prototype.bindAnimOnScroll = function() {
		var
			self = this,
			output = $( '.item-list', self.el );

		if( !output.attr('id')){
			output.attr('id','animon-id-' + self.id);
		}
		$('.hidden', output).remove();
		new AnimOnScroll(output.get(0), {
			minDuration: 0.4,
			maxDuration: 0.7,
			viewportFactor: 0.8
		});
	}

	jvbpd_bp_shortcode.prototype.bindEvent = function() {
		var
			self = this,
			navi = $('.jvbpd-bp-dir-nav li', self.el);

		// Filter
		$( '.item-options a', self.el ).on( 'click', function( e ) {
			e.preventDefault();
			$( '.item-options a', self.el ).removeClass('active');
			$(this).addClass('active');
			self._filter.filter = $(this).data('filter');
			self.filter(self._filter);
			return false;
		} );

		// Navigation
		navi.on('click', function(event){
			var $this = $(this);
			event.preventDefault();
			if($this.hasClass('active')){
				return false;
			}
			navi.removeClass('active');
			$this.addClass('active');
			self._filter.scope = $this.data('key');
			self.filter(self._filter);
			return false;
		});

		// LoadMore
		$('.jvbpd-bp-list-loadmore', self.el).on('click', function(event){
			event.preventDefault();
			self._filter.append = true;
			self._filter.page++;
			self.filter(self._filter);
			return false;
		});

		// Keyword
		$('#search-groups-form, #search-members-form', self.el ).on('submit', function(event){
			event.preventDefault();
			self._filter.search_terms = $('input[type="text"]', this).val();
			self.filter(self._filter);
			return false;
		});
		self.bindPagination();
	}

	jvbpd_bp_shortcode.prototype.bindPagination = function() {
		var self = this;
		// Pagination
		$('.bp-pagination-links a.page-numbers', self.el).on('click', function(e){
			e.preventDefault();
			var
				url = $(this).prop('href'),
				splitURL = ( -1 !== url.indexOf( '?' ) ) ? '?' + url.split( '?' )[1] : '',
				param = splitURL.replace( /(^\?)/, '' ).split( '&' ).map( function( n ) {
					return n = n.split( '=' ), this[n[0]] = n[1], this;
				}.bind( {} ) )[0];
			self._filter.page = param['grpage'];
			self.filter(self._filter);
		});
	}

	jvbpd_bp_shortcode.prototype.filter = function(param, callback) {
		var
			self = this,
			param = $.extend(true, {}, {
				page:1,
			}, param );
		self.el.addClass('ajax-loading');
		$.post( jvbpd_elementor_args.ajaxurl, param, self.loopResponsive(callback), 'json' );
	}

	jvbpd_bp_shortcode.prototype.loopResponsive = function(callback) {
		var self = this;
		return function( xhr ) {
			var
				pagiDIV = $('.bp-pagination.top',self.el),
				pagination = $('[data-pagination]','<div>'+xhr+'</div>');

			if(pagination.length){
				pagiDIV.after(pagination.html());
				pagiDIV.remove();
				self.bindPagination();
			}

			self.el.removeClass('ajax-loading');

			if(self._filter.append) {
				$( '.item-list', self.el ).append( xhr.output );
			}else{
				$( '.item-list', self.el ).html( xhr.output );
			}

			self.bindAnimOnScroll();
			if(typeof callback == 'function'){
				callback();
			}
		}
	}

	$('.jvbpd-bp-section').each(function(){
		if(!$(this).data('bp-shortchode')){
			$(this).data('bp-shortchode', new jvbpd_bp_shortcode(this));
		}
	});

} )( jQuery, window );