( function( $ ) {
	var
		CLASS_LAZY = 'jvbpd-lazy',
		CLASS_LAZYLOAD = 'lazy-loaded';

	var jvbpd_core_elementor = function() {
		this.isExistsElmentor = typeof elementor !== 'undefined';
		this.args = jvbpd_elementor_args;
		this.init();
	}

	jvbpd_core_elementor.prototype = {
		selectors : {
			carousel : 'jvbpd-swiper-carousel',
			thumbnail : 'jvbpd-thumbs-swiper',
		}
	};

	jvbpd_core_elementor.prototype.constructor = jvbpd_core_elementor;

	jvbpd_core_elementor.prototype.init = function() {
		var
			self = this,
			loadModuleCallbacks = function() {
				self.moduleBriefOnBlockWidget();
				self.moduleDetailImagesOnBlockWidget();
				self.sliderDetailImagesOnBlockWidget();
				self.moduleVideoOnBlockWidget();
				self.authorReviewModal();
				self.jvbpdPopupModal();
			};

		//if( !$( 'body' ).hasClass( 'lv-map-template' ) ) {
			loadModuleCallbacks();
		//}

		$( window ).on( 'javo:map/items/get/after', function() {
			loadModuleCallbacks();
		} );

		$(window).on('javo:map/items/get/after', self.itemGetAfterMasonry());

		$( window ).on( 'jvbpd_core/shortcode/loaded', function( event, obj ) {
			$( this ).on( 'jvbpd_core/shortcode/filter/after.' + obj.ID, function() {
				loadModuleCallbacks();
			});
		});

		if( self.isExistsElmentor ) {
			$( document ).ready( function() {
				self.bindEvents();
			} );
		}

		$( document ).ready( function() {
			self.setupMapnoUISlider();
			self.setupTooltips();
		} );

		self.searchFormSubmit();
		self.scrollSpy();
		self.applySlimScroll();
		self.postBlockAnimOnScroll();
		// self.singleGalleryMasonry();
		self.allBlockEffectAni();
		self.parallaxSections();
		// self.fullHeightSections();
		self.postBlockPopups();
		self.testimoonialPopups();
		self.mapBlockOrder();
		self.navMetisMenus();
		// self.mapBlockAnimOnScroll();
		self.mapBlockSetCount();
		self.authorReviewModal();
		self.jvbpdPopupModal();

		$( document ).ready( function() {
			// self.mapListFilterSlimScroll();
			self.mapListFilterCollapse();
			self.mapTotalCounter();
			self.bindStickyElements();
			self.customLavaAS();
		} );

		$( window ).on( 'load', self.navMenuScript() );
		$( window ).on( 'load', self.carousel() );
		$( window ).on( 'load', self.slick() );
		self.loginForm();
		self.getMemberForm();
		$( window ).on( 'load', self.loginForm() );
		$( window ).on( 'elementor/frontend/init', self.elementor_edithook() );
		$( document ).ready( self.bind_reset_filter() );
		$( document ).ready( self.bind_list_grid_toggle() );
		$( document ).ready( self.block_bind_mansorny() );
		$( window ).on( 'load', self.map_sort_dropdown() );
		$( window ).on( 'load', self.map_list_banner() );
		$( window ).on( 'jvbpd_core/canvas/get/completed', function() {
			self.navMetisMenus();
		});
		$( document ).ready( self.menu_opener() );
		$( document ).on( 'lava:single-msp-setup-after', self.applyMapStyle() );
		$('body:not(.lv-map-template) .jvbpd-search-form-section').on('submit', self.search_section_submit());

		$('.lava-ajax-search-form-wrap').on('lava:init/before', self.searchFormAjax());

		if( 'enable' == self.args.settings.lazyload ) {
			$( window ).on( 'load', function() {
				$( this ).on( 'scroll' , self.lazyRollover() ).trigger( 'scroll' );
			} );

			$( window ).on( 'lava:post-review/add-listing/template/before-insert', function( event, template ) {
				self.lazyload( template );
				$( window ).trigger( 'scroll' );
			} );

			$( window ).on( 'jvbpd_core/shortcode/loaded', function( event, obj ) {
				self.lazyload( $( '.shortcode-output', obj.el ) );
				$( window ).trigger( 'scroll' );
			} );

			$( document ).ready( function(){
				self.lazyload( $( 'body' ) );
			} );
		}
	}

	jvbpd_core_elementor.prototype.bindEvents = function() {
		var self = this;
		self.previewChange( 'search_shortcode_in_header', function( value ) {
			var header = $( '.jvbpd-header-map-filter-container' );
			if( value == 'yes' ) {
				header.removeClass( 'hidden' );
			}else{
				header.addClass( 'hidden' );
			}
		} );
		self.previewChange( '', function( value ) {
			this.save( function() {
				elementor.reloadPreview();
			});
		} );
	}

	jvbpd_core_elementor.prototype.previewChange = function( setting, callback ) {
		elementor.settings.page.addChangeCallback( setting, callback );
	}

	jvbpd_core_elementor.prototype.setupNavMenuWidget = function() {
		var
			callback,
			property = {},
			megaMenu = $( '.wide-nav-overlay' ),
			megaMenuWidth = 1024;

		callback = function() {
			var
				winX = $( this ).width(),
				megaX = megaMenu.offset().left,
				offX = 0;

			offx = Math.max( 0, ( winX - megaMenu.width() ) / 2 );
			offX = offx - megaX;
			if( winX >= megaMenuWidth ) {
				property.width = megaMenuWidth;
				property.left = offX + 'px';
			}else{
			}
			megaMenu.css( property );
		}

		$( window ).on( 'resize', callback );
		callback();
	}

	jvbpd_core_elementor.prototype.getNOUISliderParam = function( element ) {
		var
			el = $( element ),
			property = {};

		property.min = parseFloat( el.data( 'min' ) || 0 );
		property.max = parseFloat( el.data( 'max' ) || 0 );
		property.prefix = el.data( 'prefix' ) || '';
		property.suffix = el.data( 'suffix' ) || '';
		property.current = parseFloat( el.data( 'current' ) || 0 );
		property.step = parseFloat( el.data( 'step' ) || 1 );

		return {
			start : [ property.min, property.max ],
			step : property.step,
			connect : true,
			range : { 'min' : property.min, 'max' : property.max },
			tooltips: [ false, false, true ],
			serialization : {
				lower :[
					$.Link( {
						target : $( '.tooltip-min', el ),
						method : function(v) {
							$( this ).html( '<span>' + property.prefix + v + property.suffix +'</span>' );
						},
						format : { decimals : 0, thousand : ',' }
					})
				],
				upper : [
					$.Link({
						target : $( '.tooltip-max', el ),
						method : function(v) {
							$( this ).html( '<span>' + property.prefix + v + property.suffix + '</span>' );
						},
						format : { decimals : 0, thousand : ',' }
					})
				]
			}
		}
	}

	jvbpd_core_elementor.prototype.setupMapnoUISlider = function() {
		var self = this;
		$( 'body.lv-map-template .jvbpd-ui-slider' ).each( function() {
			$( '.slider', this ).noUiSlider( self.getNOUISliderParam( this ) );
			$( '.slider', this ).on( 'set', function( event, _value ) {
				$( window ).on( 'javo:map/filter/before', function( event, _items ) {
					var
						items = _items.items,
						_value = $( '.jvbpd-ui-slider .slider' ).val(),
						value = {
							min : parseFloat( _value[0] ),
							max : parseFloat( _value[1] )
						};
					_items.set( new Array() );
					$.each( items, function( index, data ) {
						var price = parseFloat( data.price );
						if(0< parseFloat( data.sale_price )){
							price = parseFloat( data.sale_price )
						}
						data = data || {};
						if( price >= value.min && price <= value.max ) {
							_items.add( items[ index ] );
						}
					} );
				} );
				$( document ).trigger( 'javo:map_refresh' );
			} );
		} );
	}

	jvbpd_core_elementor.prototype.setupTooltips = function() {
		var bindCallback = function() {
			$('[data-toggle="tooltip"]').each(function(){
				var $this = $(this);
				if($this.data('bind-tooltip')) {
					return true;
				}
				$this.data('bind-tooltip', true);
				$this.tooltip();
			});
		}
		$(window).on('scroll', bindCallback);
		bindCallback();
	}

	jvbpd_core_elementor.prototype.getAdvancedFields = function( form ) {
		$( '.jvbpd-advanced-button' ).each( function() {
			var
				div = $( '<div>' ).addClass( 'hidden' ),
				section = $( $( this ).data( 'target' ) ),
				controls = $( 'input, select', section );
			if( section.length ) {
				controls.clone().appendTo( div );
				div.appendTo( form );
			}
		} );
	}

	jvbpd_core_elementor.prototype.searchFormSubmit = function() {
		var self = this;

		$( '.jvbpd-search-submit' ).each( function() {
			var
				$this = $( this ),
				parentForm = $this.closest( '.jvbpd-search-form-section' ),
				parentFormExists = parentForm.length;

			if( $this.data('bind-submit') ) {
				return;
			}

			if( parentFormExists ) {
				$( this ).on( 'click', function( event ) {
					event.preventDefault();
					self.getAdvancedFields( parentForm );
					parentForm.submit();
				} );
				$this.data('bind-submit', 'true');
			}
		} );
	}

	jvbpd_core_elementor.prototype.search_section_submit = function() {
		var self = this;
		return function(event) {
			var
				$form = $(this),
				fields = {},
				submit_form = $('#jvbpd-search-submit-form');
			event.preventDefault();

			if(submit_form.length) {
				submit_form.remove();
			}

			$('input[type="text"], input[type="hidden"]', $form).each(function(){
				var
					$this = $(this),
					key = $this.prop('name'),
					val = $this.val();

				if(val){
					fields[key] = val;
				}
			});

			$('input[type="checkbox"]:checked', $form).each(function(){
				var
					$this = $(this),
					key = $this.prop('name'),
					val = $this.val();

				if(val){
					if(typeof fields[key] != 'object'){
						fields[key] = new Array();
					}
					fields[key].push(val);
				}
			});

			$('select', $form).each(function(){
				var
					val = '',
					$this = $(this),
					instance = this.selectize,
					key = $this.prop('name');

				if(instance){
					val = instance.getValue();
					if(val){
						fields[key] = val;
					}
				}
			});

			submit_form = $('<form>').prop({
				'id': 'jvbpd-search-submit-form',
				'method': 'get',
				'action': $form.attr('action'),
			}).addClass('hidden');

			$.each(fields, function(fieldKey, fieldValue){
				if(typeof fieldValue != 'object') {
					$('<input>').prop({
						type: 'hidden',
						name: fieldKey,
						value: fieldValue,
					}).appendTo(submit_form);
				}else{
					$.each(fieldValue, function(){
						$('<input>').prop({
							type: 'hidden',
							name: fieldKey,
							value: this,
						}).appendTo(submit_form);
					});
				}
			});
			submit_form.appendTo($('body'));
			submit_form.submit();
		}
	}

	jvbpd_core_elementor.prototype.navMenuScript = function() {
		var
			self = this,
			menus = $( 'div[id^="jvbpd-nav-menu-id"], .jvbpd-collapse-section' );

		return function() {
			if( typeof elementorFrontend == 'undefined' ) {
				return;
			}
			menus.each( function() {
				var
					obj = $( this ),
					parent = $( this ).closest( '.elementor-element' ),
					isFullWidth = parent.hasClass( 'jvbpd-nav-menu-full-width' );

				if( typeof elementorFrontend.modules == 'undefined' ) {
					return;
				}

				if( ! isFullWidth && ! obj.is( '.jvbpd-collapse-section' ) ) {
					return ;
				}

				if( ! obj.data( 'stretch' ) ) {
					obj.data( 'stretch', new elementorFrontend.modules.StretchElement( { element: obj } ) );
				}

				obj.on( 'show.bs.collapse', function() {
					obj.removeClass( 'collapse' );
					if( obj.is( '.jvbpd-collapse-section-absolute' ) ) {
						obj.css( 'position', 'absolute' );
					}
					obj.data( 'stretch' ).stretch();
					obj.css( 'top', obj.data( 'position-top' ) );
					obj.addClass( 'collapse' );
				} );

				obj.on( 'hidden.bs.collapse', function() {
					if( ! obj.is( '.jvbpd-collapse-section-absolute' ) ) {
						obj.data( 'stretch' ).reset();
					}
				} );

			} );

			if( ! elementorFrontend.isEditMode() ) {
				elementorFrontend.addListenerOnce( 'nav_menu_resize', 'resize', function() {
					menus.each( function() {
						var
							obj = $( this ),
							parent = obj.closest( '.elementor-element' ),
							isFullWidth = parent.hasClass( 'jvbpd-nav-menu-full-width' );

						if($(window).width() < 767){
							parent.removeClass('cur-device-mobile').addClass('cur-device-tablet');
						}else if($(window).width() < 486){
							parent.removeClass('cur-device-tablet').addClass('cur-device-mobile');
						}else{
							parent.removeClass('cur-device-tablet').removeClass('cur-device-mobile');
						}

						if( ! isFullWidth && $( this ).is( '.jvbpd-collapse-section' ) ) {
							return true;
						}

						obj.css( 'top', obj.data( 'position-top' ) );

						if( obj.data( 'stretch' ) ) {
							if( $( window ).width() < 767 ) {
								obj.data( 'stretch' ).stretch();
							}else{
								obj.data( 'stretch' ).reset();
							}
						}

					} );
				} );
			}
		}
	}

	jvbpd_core_elementor.prototype.mapListFilterSlimScroll = function() {
		var
			listFilter = $( '.jvbpd_map_list_sidebar_wrap' ),
			cbContainer = $( '.ui-checkbox .panel-body', listFilter );

		if( typeof $.fn.slimScroll !== 'function' || ! cbContainer.length ) {
			return false;
		}
		cbContainer.each( function() {
			var containerHeight = $( this ).outerHeight();
			$( this ).slimScroll({
				height: containerHeight + 'px',
				distance: '6px',
				railVisible: true,
				alwaysVisible: true,
				railColor: '#ccc',
				railOpacity: 0.8,
			});
		} );
	}

	jvbpd_core_elementor.prototype.mapListFilterCollapse = function() {
		$('.elementor-widget-jvbpd-map-list-filters').each(function(){
			var $widget = $(this);
			$('input[type="checkbox"][data-tax]:checked', $widget).each(function(){
				$(this).closest('.panel-collapse').addClass('show');
			});
		});
	}

	jvbpd_core_elementor.prototype.mapTotalCounter = function() {
		var self = this;
		$('.elementor-widget-jvbpd-map-list-filter-total-count').each(function(){
			var $widget = $(this);
			var $output = $('.counter-output', $widget);
			$(window).on('javo:map/items/get/after', function(event, args, obj){
				var count = obj.apply_item.length;
				$output.html(
					count.toString().replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,')
				);
			});
		});
		/*
		$( '.jvbpd-map-list-total-count-wrap' ).each( function() {
			var
				output = $( '.counter-output', this );
			if( output.length ) {
				$( window ).on( 'javo:map/filter/after', function( event, item ) {
					item.unique();
					output.html( item.items.length.toString().replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,') );
				} );
			}
		} ); */
	}

	jvbpd_core_elementor.prototype.bindStickyElements = function() {
		var
			self = this,
			custom_settings = {},
			adminbar = $('#wpadminbar'),
			header = $('.jvbpd-sticky-element.header-elementor'),
			elements = $('.jvbpd-sticky-element');

		elements.each( function() {
			var $this = $( this );
			var offY = 0;
			var settings = $this.data('settings') || {};

			if($this.data('offset')) {
				$this = $( '>div', $this);
				offY = $this.offset().top;
			}else{
				if( header.length ) {
					offY += header.height();
				}
				if( adminbar.length ) {
					offY += adminbar.height();
				}
				/*
				custom_settings = $this.data('settings');
				if(custom_settings.jvbpd_sticky_offset&&0<parseInt(custom_settings.jvbpd_sticky_offset.size)) {
					offY += parseInt(custom_settings.jvbpd_sticky_offset.size);
				} */
			}
			$this._sticky({topSpacing:offY, zIndex:settings.jvbpd_sticky_zIndex||1000});
		});
	}

	jvbpd_core_elementor.prototype.waitButton = function( button, available ) {
		var _DISABLE = 'disabled';
		if( available ) {
			button.removeClass( _DISABLE ).removeAttr( _DISABLE ).prop( _DISABLE, false );
		}else{
			button.addClass( _DISABLE ).attr( _DISABLE, _DISABLE ).prop( _DISABLE, true );
		}
	}

	jvbpd_core_elementor.prototype.preview_map = function( container ) {
		var
			self = this,
			map = $( '.jvbpd-preview-map', container ),
			latLng = new google.maps.LatLng( map.data( 'lat' ) || 0, map.data( 'lng' ) || 0 );
		map.height(300).gmap3({
			map: {
				options: {
					'center' : latLng,
					'zoom' : 16
				}
			},
			marker: {
				'latLng' :  latLng,
			}
		});
	}

	jvbpd_core_elementor.prototype.ajax = function( _action, _param, callback, failcallback ) {
		var
			self = this,
			param = $.extend( true, {}, { action: 'jvbpd_' + _action }, _param );
		$.post( self.args.ajaxurl, param, function( data ) {
			if( typeof callback == 'function' ) {
				callback( data );
			}
		}, 'JSON')
		.fail( function( xhr, err ){
			if( typeof failcallback == 'function' ) {
				failcallback( xhr, err );
			}
		} );
	}

	jvbpd_core_elementor.prototype.moduleBriefOnBlockWidget = function() {
		var
			self = this,
			previewOpen = false,
			callback = function( event ) {
				event.preventDefault();
				var
					modal = $( '#javo-infow-brief-window' ),
					body = $( '.modal-body', modal ),
					button = $( this ),
					item_id = button.closest( '.module' ).data( 'post-id' );

				if( button.data( 'post-id' ) ) {
					item_id = button.data( 'post-id' );
				}

				if( previewOpen ) {
					return;
				}

				previewOpen = true;

				self.waitButton( button, false );
				self.ajax( 'map_brief', { post_id: item_id }, function( data ) {
					body.html( data.html ).scrollTop(0);
					modal.off( 'shown.bs.modal' ).on( 'shown.bs.modal', function() {
						self.preview_map( body );
					} );
					modal.modal( 'show' );
					self.waitButton( button, true );
					previewOpen = false;
				} );
			};

		$( '.module[data-post-id] .javo-infow-brief, a.javo-infow-brief' ).each( function() {
			if( ! $( this ).data( 'preview_instance' ) ) {
				$( this ).on( 'click', callback );
				$( this ).data( 'preview_instance', true );
			}
		} );
	}

	jvbpd_core_elementor.prototype.moduleDetailImagesOnBlockWidget = function() {
		var
			self = this,
			previewOpen = false,
			callback = function( event ) {
			event.preventDefault();
			var
				modal = $( '#javo-infow-brief-window' ),
				body = $( '.modal-body', modal ),
				button = $( this ),
				item_id = button.closest( '.module' ).data( 'post-id' );

			if( button.data( 'post-id' ) ) {
				item_id = button.data( 'post-id' );
			}

			if( previewOpen ) {
				return;
			}

			previewOpen = true;

			self.waitButton( button, false );
			self.ajax( 'detail_images', { post_id: item_id }, function( data ) {
				body.html( data.html ).scrollTop(0);
				modal.off( 'shown.bs.modal' ).on( 'shown.bs.modal', function() {
					var mySwiper = new Swiper( $( '.swiper-container', body ), {
						loop: true,
						pagination: {
							el: '.swiper-pagination',
						},
						navigation: {
							nextEl: '.swiper-button-next',
							prevEl: '.swiper-button-prev',
						},
						// And if we need scrollbar
						scrollbar: {
							el: '.swiper-scrollbar',
						},
					});
				} );
				modal.modal( 'show' );
				self.waitButton( button, true );
				previewOpen = false;
			} );
		};

		$( '.jvbpd-preview-detail' ).each( function() {
			if( ! $( this ).data( 'preview_instance' ) ) {
				$( this ).on( 'click', callback );
				$( this ).data( 'preview_instance', true );
			}
		} );
	}

	jvbpd_core_elementor.prototype.sliderDetailImagesOnBlockWidget = function() {
		var self = this, callback;
		callback = function(event) {
			var
				$this = $(this),
				images = $this.data('images'),
				adminbar = $('#wpadminbar');
			event.preventDefault();
			if( typeof $.fn.lightGallery != 'undefined' ){
				$this.on('onBeforeOpen.lg', function(event) {
					adminbar.length && adminbar.addClass('hidden');
				}).on('onBeforeClose.lg', function(event){
					adminbar.length && adminbar.removeClass('hidden');
				});
				$this.lightGallery({
					dynamic		: true,
					dynamicEl	: images,
				});
			}
		}
		$( '.jvbpd-slider-detail' ).each( function() {
			if( ! $( this ).data( 'preview_instance' ) ) {
				$( this ).on( 'click', callback );
				$( this ).data( 'preview_instance', true );
			}
		} );
	}

	jvbpd_core_elementor.prototype.moduleVideoOnBlockWidget = function() {
		var
			self = this,
			previewOpen = false,
			callback = function( event ) {
				event.preventDefault();
				var
					modal = $( '#javo-infow-brief-window' ),
					body = $( '.modal-body', modal ),
					button = $( this ),
					video_url = $(this).data('video'),
					output;

				if( previewOpen ) {
					return;
				}

				previewOpen = true;
				self.waitButton( button, false );
				output = $('<iframe>').prop({
					'width': $(window).width() / 2,
					'height': $(window).height() / 2,
					'frameborder': '0',
					'src' : video_url,
				});
				body.empty().append(output).scrollTop(0);
				modal.modal( 'show' );
				self.waitButton( button, true );
				previewOpen = false;
			};

		$( '.jvbpd-preview-video' ).each( function() {
			if( ! $( this ).data( 'preview_instance' ) ) {
				$( this ).on( 'click', callback );
				$( this ).data( 'preview_instance', true );
			}
		} );
	}

	jvbpd_core_elementor.prototype.carousel = function( $scope ) {
		var self = this;
		return function() {
			if( typeof window.Swiper == 'undefined' ) {
				return false;
			}

			var selector = $( '.elementor-widget-jv-media-carousel, .elementor-widget-jv-media-carousel-single-listing, .is-jvcore-swiper' );

			if( $scope ) {
				selector = $scope;
			}

			selector.each( function() {
				var
					$this = $( this ),
					mainSlider,
					mainSliderParam,
					thumbSlider,
					thumbSliderParam,
					selectors = self.selectors,
					settings = JSON.parse( $this.find( '.slider-value' ).val() ),
					isSlideShow = settings.skin === 'slideshow';

				mainSliderParam = settings;

				if( 'yes' == settings.autoplay ) {
					mainSliderParam.autoplay = settings.autoplay_speed;
				}

				mainSliderParam.speed = settings.speed;
				mainSliderParam.lazyLoading = true;
				mainSliderParam.navigation = {};
				mainSliderParam.navigation.nextEl = mainSliderParam.nextButton = '.jvbpd-nav-button-next .eicon-chevron-right';
				mainSliderParam.navigation.prevEl = mainSliderParam.prevButton = '.jvbpd-nav-button-prev .eicon-chevron-left';

				if( isSlideShow ) {
					mainSliderParam.slidesPerView = 1;
					mainSliderParam.slideToClickedSlide = true;
					thumbSliderParam = {
						slideToClickedSlide: true,
						slidesPerView: mainSliderParam.slideshow_slides_per_view,
						onSlideChangeEnd : function( swiper ) {
							swiper.fixLoop();
						}
					}
				}else{
					mainSliderParam.loop = 'yes';
				}

				if( $( '.' + selectors.carousel, $this ).length ) {
					mainSlider = new Swiper( $( '.' + selectors.carousel, $this ), mainSliderParam );
				}

				if( $( '.' + selectors.thumbnail, $this ).length ) {
					thumbSlider = new Swiper( $( '.' + selectors.thumbnail, $this ), thumbSliderParam );
					mainSlider.params.control = thumbSlider;
					thumbSlider.params.control = mainSlider;
				}

				if( $( '.jvbpd-swiper', $this ).hasClass( 'lightbox-active' ) ) {
					if( ! $( '.jvbpd-swiper', $this ).data( 'has-lightbox' ) ) {
						$( '.jvbpd-swiper', $this ).data( 'has-lightbox', true );
						$( '.jvbpd-swiper', $this ).lightGallery( {
							selector: '.swiper-slide'
						} );
					}
				}

			} );
		}
	}

	jvbpd_core_elementor.prototype.slick = function( $scope ) {
		var self = this;
		return function() {
			var selector = $( '.jvbpd-slider-wrap' );

			if( $scope ) {
				selector = $( '.jvbpd-slider-wrap', $scope );
			}

			selector.each( function() {
				var
					$this = $( this ),
					settings = $this.data( 'settings' );

				if($this.data('bindSlick')) {
					return;
				}

				$.each( new Array( 'adaptiveHeight', 'autoplay', 'arrows', 'dots', 'fade', 'infinite' ), function( index, key ) {
					settings[ key ] = 'yes' === settings[ key ];
				} );

				if( typeof settings.slidesToShow != 'undefined' ) {
					settings.slidesToShow = parseInt(settings.slidesToShow);
					settings.slidesToScroll = settings.slidesToShow;
				}
				$this.slick( settings );
				$this.data('bindSlick', true);
			} );
		}
	}

	var jvbpd_form = function( el, parent, type ) {
		this.el = $( el );
		this.type = type;
		this.output = $( '.submit-wrap', this.el );
		this.parent = parent;
		this.init();
	}

	jvbpd_form.prototype.constructor = jvbpd_form;

	jvbpd_form.prototype.init = function() {
		var self = this;
		self.createMessageBox();
		if(self.el) {
			self.el.on( 'submit', self.submit() );
		}
	}

	jvbpd_form.prototype.createMessageBox = function() {
		var self = this;
		$( '<div>' ).addClass( 'jvbpd-form-msgbox' ).css({ 'display': 'none' }).appendTo( self.output );
	}

	jvbpd_form.prototype.message = function( str, type, _button ) {
		var
			self = this,
			output = self.output,
			button = $( 'button[type="submit"]', output ),
			content = $( '.jvbpd-form-msgbox', output ),
			contentInit,
			types = {
				'success': 'state-success',
				'processing': 'state-process',
				'danger': 'state-danger',
				'warning': 'state-warning',
			};

		contentInit = function() {
			$.each( types, function( typeKey, typeSelector ) {
				content.removeClass( typeSelector );
			} );
		}

		if( false === str ) {
			button.prop( 'disabled', false );
			content.slideUp();
			return false;
		}

		content.html( str );

		if( type && ( -1 < Object.keys( types ).indexOf( type ) ) ) {
			contentInit();
			content.addClass( types[ type ] );
		}

		if( _button ) {
			button.prop( 'disabled', false );
		}else{
			button.prop( 'disabled', true );
		}
		content.slideDown();
	}

	jvbpd_form.prototype.getProcessingTemplate = function( _string ) {
		var
			self = this,
			parent = self.parent,
			template = '<i class="{class}"></i><span>{text}</span>';
		template = template.replace( /{class}/g, 'loader-icon' );
		template = template.replace( /{text}/g, _string || parent.args.strings.login.strProcessing );
		return template;
	}

	jvbpd_form.prototype.getParams = function( form ) {
		var
			self = this,
			parent = self.parent,
			form = $( form ),
			output = {};

		output.security = parent.args.strings.login.security;

		if( self.type == 'login' ) {
			output.redirect = form.data( 'redirect' );
			output.log = $( '[name="log"]', form ).val();
			output.pwd= $( '[name="pwd"]', form ).val();
			output.referer = $( '[name="referer"]', form ).val();
			output.remember = false;
		}

		if( self.type == 'join' ) {
			$.each( form.serializeArray(), function( i, data ) {
				output[ data.name ] = data.value;
			} );

			if( output.user_pass != output.user_con_pass ) {
				self.message( parent.args.strings.join.errPasword, 'warning', true );
				output = false;
			}
		}


		return output;
	}

	jvbpd_form.prototype.loginSuccess = function( xhr ) {
		var
			self = this,
			parent = self.parent;

		if( typeof xhr.error != 'undefined' ) {
			self.message( xhr.error, 'warning', true );
		}else{
			self.message( self.getProcessingTemplate( parent.args.strings.login.strSuccessLogin ), 'processing' );
			if( 'refresh' != xhr.redirect ) {
				window.location.href = xhr.redirect;
			}else{
				window.location.reload();
			}
		}
	}

	jvbpd_form.prototype.joinSuccess = function( xhr ) {
		var
			self = this,
			parent = self.parent;

		if( typeof xhr.err != 'undefined' ) {
			self.message( xhr.err, 'warning', true );
		}else{
			if( xhr.state == 'success' ){
				self.message( self.getProcessingTemplate( parent.args.strings.join.strSuccessJoin ), 'processing' );
				window.location.href = xhr.link;
			}else{
				self.message( xhr.comment, 'warning', true );
			}
		}
	}

	jvbpd_form.prototype.submit = function() {
		var
			self = this,
			parent = self.parent;
		return function( event ) {
			var params;
			event.preventDefault();

			params = self.getParams( this );

			if( false === params ) {
				return false;
			}

			self.message( self.getProcessingTemplate(), 'processing' );
			$( self ).trigger( 'jvbpd_form/submit/before', params, self );

			parent.ajax( 'ajax_user_' + self.type, params, function( xhr ) {

				if( self.type == 'login' ) {
					self.loginSuccess( xhr );
				}

				if( self.type == 'join' ) {
					self.joinSuccess( xhr );
				}

				$( self ).trigger( 'jvbpd_form/submit/after', xhr, self );
			}, function() {
				self.message( parent.args.strings.login.errLoginServer, 'danger', true );
			});
		}
	}

	jvbpd_core_elementor.prototype.loginForm = function() {
		var self = this;
		return function() {
			$( 'form.jvbpd-login-form' ).each( function(){
				if( ! $( this ).data( 'jvbpd-form' ) ) {
					$( this ).data( 'jvbpd-form', new jvbpd_form( this, self, 'login' ) );
				}
			} );
			$( 'form[data-jvbpd-signup-form]' ).each( function(){
				if( ! $( this ).data( 'jvbpd-form' ) ) {
					$( this ).data( 'jvbpd-form', new jvbpd_form( this, self, 'join' ) );
				}
			} );
		}
	}

	jvbpd_core_elementor.prototype.getMemberForm = function() {
		var
			self = this,
			loginAfterCallback = self.loginForm(),
			modals = {
				LoginForm : $( "#login_panel"),
				SignUpForm : $( "#register_panel"),
			},
			loadingTemplate = '<div class="sk-three-bounce">' +
				'<div class="sk-child sk-bounce1"></div>' +
				'<div class="sk-child sk-bounce2"></div>' +
				'<div class="sk-child sk-bounce3"></div>' +
			'</div>';

		$.each( modals, function( action, el){
			if(!el.hasClass( 'loaded')){
				$( ".modal-content", el).html( loadingTemplate );
				el.on('show.bs.modal', function(event){
					self.ajax( 'get_' + action, {}, function( data ) {
						$( ".modal-content", el).html( data.render );
						loginAfterCallback();
						el.addClass('loaded');
					});
				});
			}
		});
	}

	var reset_filter = function( el, parent ) {
		this.el = $( el );
		this.output = '.elementor-widget-jvbpd-map-list-reset-filter .items';
		this.parent = parent;

		if(!window.jvbpd_reset){
			window.jvbpd_reset = {};
			window.jvbpd_reset.filters = {
				address : {
					label: parent.args.strings.map_list_reset_filter.address,
					field: [
						{ type: 'input', selector: '#javo-map-box-location-trigger' }
					],
				},
				category: {
					label: parent.args.strings.map_list_reset_filter.category,
					field: [
						{ type: 'selectize', selector: '.ui-select [name="list_filter[listing_category]"]' },
						{ type: 'checkbox', selector: 'input[type="checkbox"][data-tax="listing_category"]' },
					]
				},
				location: {
					label: parent.args.strings.map_list_reset_filter.location,
					field: [
						{ type: 'selectize', selector: '.ui-select [name="list_filter[listing_location]"]' },
						{ type: 'checkbox', selector: 'input[type="checkbox"][data-tax="listing_location"]' },
					]
				},
				amenities: {
					label: parent.args.strings.map_list_reset_filter.amenities,
					field: [
						{ type: 'selectize', selector: '.ui-select [name="list_filter[listing_amenities]"]' },
						{ type: 'checkbox', selector: 'input[type="checkbox"][data-tax="listing_amenities"]' },
					]
				},
			}
		}
		if(Object.keys(jvbpd_elementor_args.more_taxonomy).length) {
			window.jvbpd_reset.filters = $.extend(true, {},
				window.jvbpd_reset.filters,
				jvbpd_elementor_args.more_taxonomy
			);
        }
		this.init();
	}

	reset_filter.prototype.constructor = reset_filter;

	reset_filter.prototype.init = function() {
		var self = this;
		self.mapInstance = window.jvbpd_map_box_func;

		if( ! self.mapInstance ) {
			return;
		}

		self.scanFilter();
		$( window ).on( 'javo:map/filter/before', function() {
			self.scanFilter();
		} );

		$( 'span[data-filter="all-reset"]', self.el ).on( 'click', function( event ) {
			$.each( window.jvbpd_reset.filters, function( key, data ) {
				$( 'span.filter-item[data-filter="' + key + '"]' ).remove();
				self.resetFilter( key );
			} );
			self.otherReset();
			self.mapInstance.filter();
		} );
	}

	reset_filter.prototype.isActiveFilter = function( filter ) {
		return -1 < window.jvbpd_reset.activateFilter.indexOf( filter );
	}

	reset_filter.prototype.checkFilter = function( data ) {
		var
			output = false,
			instance;

		$.each( data.field, function( index, field ) {
			var existValue;

			if( ! $( field.selector ).length ) {
				return true;
			}

			switch( field.type ) {
				case 'input' :
				case 'selectize' : existValue = 0 < ($( field.selector ).val() || new Array()).length; break;
				case 'checkbox' : existValue = 0 < $( field.selector + ':checked' ).length; break;
			}
			if( existValue ) {
				output = true;
				return false;
			}
		} );
		return output;
	}

	reset_filter.prototype.resetFilter = function( filter ) {
		var
			self = this,
			target = false,
			instance,
			filters = Object.keys( window.jvbpd_reset.filters );

		if( -1 < filters.indexOf( filter ) ) {
			target = window.jvbpd_reset.filters[filter];
			$.each( target.field, function( targetIndex, targetField ) {

				if( ! $( targetField.selector ).length ) {
					return true;
				}

				switch( targetField.type ) {
					case 'input' : $( targetField.selector ).val( '' ); break;
					case 'selectize' :
						$( targetField.selector ).each(function(){
							var
								$this = $(this),
								instance = $this.get(0).selectize;
							// instance.clear();
							$this.val(new Array());
						});
						break;
					case 'checkbox' :
						$( targetField.selector ).prop( 'checked', false ); break;
				}

			} );

			if( self.isActiveFilter( filter ) ) {
				window.jvbpd_reset.activateFilter.splice( window.jvbpd_reset.activateFilter.indexOf( filter ), 1 );
			}
		}
	}

	reset_filter.prototype.otherReset = function() {
		var
			searchForm = $('form.jvbpd-search-form-section'),
			// search_fields = $('.field-ajax_search'),
			search_fields = $('form.jvbpd-search-form-section'),
			amenities_fields = $('.amenities-filter-area'),
			selectize_fields = $('.shortcode-jvbpd_search_field [data-selectize], select[data-tax].selectized'),
			keyword_fields = $('[name="keyword"]', searchForm);

		// Search field
		search_fields.each(function() {
			var
				$this = $(this),
				input = $('input[name="keyword"], input[name="category"]', $this);
			input.val('');
		});

		// Amenities field
		amenities_fields.each(function(){
			var
				$this = $(this),
				checkboxes = $('input[data-tax]',$this);
			checkboxes.prop('checked', false);
		});

		// Selectize field
		window.jvbpd_core_map_block_filter = true;
		selectize_fields.each(function(){
			var
				$this = $(this),
				instance = $this.get(0).selectize;

			if(instance) {
				instance.clear();
			}
		});
		window.jvbpd_core_map_block_filter = false;
	}

	reset_filter.prototype.scanFilter = function() {
		var self = this;
		window.jvbpd_reset.activateFilter = new Array();
		$.each( window.jvbpd_reset.filters, function( key, data ) {
			$( 'span.filter-item[data-filter="' + key + '"]' ).remove();
			if( self.checkFilter( data ) && !self.isActiveFilter( key ) ) {
				self.add({ filter: key, label: data.label });
			}
		} );
	}

	reset_filter.prototype.getTemplate = function( data ) {
		var template = '<span class="filter-item" data-filter="{filter}">{label} <i class="filter-item-remove-icon">&times;</i></span>';
		template = template.replace( /{filter}/g, data.filter || '' );
		template = template.replace( /{label}/g, data.label || '' );
		return template;
	}

	reset_filter.prototype.add = function( data ) {
		var
			self = this,
			button = self.getTemplate( data );

		$(self.output).each(function(){
			$(this).append( $(button).on( 'click', function(){ self.remove( this ); } ) );
		});
		window.jvbpd_reset.activateFilter.push( data.filter );
	}

	reset_filter.prototype.remove = function( _button ) {
		var
			self = this,
			button = $( _button );
		button.remove();
		self.resetFilter( button.data( 'filter' ) );
		self.mapInstance.filter();
	}

	jvbpd_core_elementor.prototype.bind_reset_filter = function() {
		var self = this;
		return function( items ) {
			$( '.jvbpd-map-list-reset-filter-wrap' ).each( function() {
				if( ! $( this ).data( 'reset_filter' ) ) {
					$( this ).data( 'reset_filter', new reset_filter( this, self ) );
				}
			} );
		}
	}

	jvbpd_core_elementor.prototype.bind_list_grid_toggle = function() {
		var self = this;
		return function( event ) {
			var
				instance = window.jvbpd_map_box_func,
				TOGGLE_ACTIVE = 'active';

			$.each( { map: '.module-switcher input[name="module_switcher"]', list: '.jvbpd-map-list-grid-toogle-wrap' }, function( type, selector ) {
				$( selector ).each( function() {
					var
						items,
						items_event,
						$this = $(this),
						widget = $this.closest('.elementor-widget-jvbpd-map-list-grid-toggle'),
						settings = widget.data('settings');

					if( 'map' == type ) {
						items = $this;
						items_event = 'change';
					}else{
						items = $( '.toggle-item', $this );
						items_event = 'click';
					}

					items.on( items_event, function( event ) {
						var $this;

						if( 'map' == type ) {
							$this = $( this ).parent();
						}else{
							$this = $( this );
						}

						if( $this.hasClass( TOGGLE_ACTIVE ) ) {
							return;
						}

						items.removeClass( TOGGLE_ACTIVE );

						if( 'map' != type ) {
							$this.addClass( TOGGLE_ACTIVE );
						}
						$( window ).on( 'javo:map/items/get/before', function( event, args ) {
							var module_name = $this.data( 'module' );
							var columns_count = $this.data( 'columns' );
							if( module_name && columns_count ) {
								args.parametter.map.module = module_name;
								args.parametter.map.columns = columns_count;
								args.parametter.list.module = module_name;
								args.parametter.list.columns = columns_count;
							}
						});
						self.loopCount = 0;
						instance.filter();
					} );
				} );
			} );

		}
	}

	jvbpd_core_elementor.prototype.block_bind_mansorny = function() {
		$( window ).on( 'javo:map/items/get/before', function( event, args ) {
			/*
			var
				module_name = $this.data( 'module' ),
				columns_count = $this.data( 'columns' );
			if( module_name && columns_count ) {
				args.parametter.map.module = module_name;
				args.parametter.map.columns = columns_count;
				args.parametter.list.module = module_name;
				args.parametter.list.columns = columns_count;
			} */

			$.each([args.controls.map.output,args.controls.list.output], function(){
				var
					$blockThis = $(this),
					widget = $blockThis.closest('.elementor-widget-jvbpd-map-listing-blocks, .elementor-widget-jvbpd-map-list-listing-blocks'),
					settings = widget.data('settings'),
					effect;

				if(!settings) {
					return;
				}
				if('yes' == settings.use_masonry) {
					if(settings.masonry_ani) {
						effect  = 'effect-' + settings.masonry_ani;
						$blockThis.addClass('jvbpd-grid').addClass(effect);
					}
					/*
					widget.removeClass(function(i, className){
						return (className.match (/(^|\s)columns-\S+/g) || []).join(' ');
					}); */
					widget.addClass('masonry-yes');
				}else{
					widget.removeClass('masonry-yes');
					$blockThis.removeClass('jvbpd-grid');
				}
			});
		} );
	}

	jvbpd_core_elementor.prototype.elementor_edithook = function() {
		var self = this;
		return function() {
			var
				instance = window.jvbpd_map_box_func,
				carusel_callback = function( $scope, $ ) { var callback = self.carousel( $scope ); callback(); },
				slick_callback = function( $scope, $ ) { var callback = self.slick( $scope ); callback(); };

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jv-media-carousel.default', carusel_callback );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/jv-media-carousel-single-listing.default', carusel_callback );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/jv-page-slider.default', slick_callback );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd_testimonial.default', slick_callback );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd_testimonial_wide.default', slick_callback );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd_featured_block.default', slick_callback );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-map-list-listing-blocks.default', function( $scope, $ ) {
				if( elementorFrontend.isEditMode() ) {
					instance.filter();
				}
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-map-list-filters.default', function( $scope, $ ) {
				if( elementorFrontend.isEditMode() ) {
					instance.setDistanceBar();
				}
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-single-header.default', function( $scope, $ ) {
				if( elementorFrontend.isEditMode() ) {
					window.jvbpd_singleInstance.init();
				}
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-map-maps.default', function( $scope, $ ) {
				if( elementorFrontend.isEditMode() ) {
					instance.init();
					instance.filter();
				}
			} );

			var ApplyMasonryScript = function( $scope, $ ) {
				if( elementorFrontend.isEditMode() ) {
					self.postBlockAnimOnScroll();
				}
			}

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-page-block.default', ApplyMasonryScript );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/jvbpd-single-gallery.default', ApplyMasonryScript );

			if( elementorFrontend.isEditMode() ) {
				elementor.hooks.addFilter('editor/style/styleText', function(css, view) {
					var model = view.getEditModel();
					var jv_css = model.get('settings').get('jv_custom_css');
					if(jv_css) {
						css += jv_css;
					}
					return css;
				});
			}
		}
	}

	jvbpd_core_elementor.prototype.map_sort_dropdown = function() {
		var
			self = this,
			MAP_SORT_TYPE = 'map-sort-type';
		return function( event ) {
			var
				apply = false,
				filter = { by:'', type: '' },
				instance = window.jvbpd_map_box_func,
				filter_selector = 'select[data-map-sort-by]',
				order_selector = 'button[data-' + MAP_SORT_TYPE + ']';

			$( filter_selector ).on( 'change', function( event ) {
				apply = true;
				filter.by = $( this ).val();
				instance.filter();
			} );
			$( order_selector ).on( 'click', function( event ) {
				var currentOrder = $( this ).data( MAP_SORT_TYPE );
				$( 'i', this ).addClass( 'hidden' );
				if( 'asc' == currentOrder ) {
					$( this ).data( MAP_SORT_TYPE, 'desc' );
					$( 'i[data-sort="desc"]' ).removeClass( 'hidden' );
				}else{
					$( this ).data( MAP_SORT_TYPE, 'asc' );
					$( 'i[data-sort="asc"]' ).removeClass( 'hidden' );
				}
				apply = true;
				filter.type = $( this ).data( MAP_SORT_TYPE );
				instance.filter();
			} );

			$( window ).on( 'javo:map/filter/after', function( event, item ) {
				var
					items = item.items,
					sortBy = filter.by,
					sortType = filter.type;

				if( !apply ) {
					return;
				}

				if( 0 <= new Array( 'reviewed', 'rating' ).indexOf( sortBy ) ) {
					items.sort( instance.order_raty( sortBy ) );
				}else{
					items.sort( instance.compare( sortBy ) );
				}

				if( 'asc' == sortType ) {
					items.reverse();
				}
				item.set( items );
			} );
		}
	}

	jvbpd_core_elementor.prototype.map_list_banner = function() {
		var
			self = this,
			CATEGORY = 'listing_category',
			selector = '.jvbpd-map-list-category-banner-wrap',
			filter = 'select[data-tax="' + CATEGORY + '"]';
		return function( event ) {

			$( filter ).on( 'change', function() {
				var term = $( this ).val();
				self.ajax( 'get_listing_category_featured_iamge', {term_id: term }, function( xhr ) {
					$( selector ).each( function() {
						$( this ).css( {
							'background-image': 'url(' + xhr.full +')',
							'background-size': 'cover',
							'background-position': 'center',
							'height':150,
						} );
					} );
				} );
			} );
		}
	}

	jvbpd_core_elementor.prototype.lazyload = function( wrap ) {
		var
			exludeClasses = new Array(
				'.wide-nav-overlay',
				'.module-rating-wrap',
				'.module-ratings'
			);

		$( '*:not(.' + CLASS_LAZY + ')', wrap ).each( function() {
			var
				$this = $( this ),
				hasExcludeSelector = false,
				bg = $this.css( 'background-image' );

			$.each( exludeClasses, function( excIndex, excSelector ) {
				if( $this.is( excSelector ) ) {
					hasExcludeSelector = true;
					return false;
				}
			} );

			if( hasExcludeSelector ) {
				return;
			}

			if( -1 < bg.indexOf( 'url' ) ) {
				$this.css( 'background-image', '' );
				$this.data( 'src', bg );
				$this.addClass( CLASS_LAZY );
			}
		} );

		$( 'img:not(.' + CLASS_LAZY + ')', wrap ).each( function() {
			var
				$this = $( this ),
				hasExcludeSelector = false;
			$.each( exludeClasses, function( excIndex, excSelector ) {
				if( $this.is( excSelector ) ) {
					hasExcludeSelector = true;
					return false;
				}
			} );

			if( hasExcludeSelector ) {
				return;
			}

			$.each( new Array( 'src', 'srcset' ), function( imgIndex, imgProp ) {
				var value = $this.attr( imgProp );
				if( value ) {
					$this.data( imgProp, value );
					$this.addClass( CLASS_LAZY );
					$this[0].setAttribute( imgProp, '' );
				}
			} );
		} );
	}

	jvbpd_core_elementor.prototype.lazyRollover = function() {
		var
			self = this,
			lazyStarted		= false,
			intWinScrollTop	= 0,
			intWinHeight	= 0,
			intSumOffset	= 0,
			intCurrentIDX	= 0,
			intInterval		= 100,
			objWindow		= $( window );
		return function( event ){
			intWinScrollTop	= objWindow.scrollTop();
			intWinHeight	= objWindow.height() * 0.9;
			intSumOffset	= intWinScrollTop + intWinHeight;

			$( '.' + CLASS_LAZY ).each( function() {
				var
					$this = $( this ),
					bg = $this.data( 'src' ), src;

				if( bg ) {
					src =  bg.replace( /(^url\()|(\)$|[\"\'])/g, '' );
					if( intSumOffset > $this.offset().top ) {
						$( '<img/>' ).attr( 'src', src ).on( 'load', function() {
							$( this ).remove();
							$this.addClass( CLASS_LAZYLOAD );
							$this.css( 'background-image', bg );
						} );
					}
				}
			} );

			$( 'img' ).each( function() {
				var $this = $( this );
				$.each( new Array( 'src', 'srcset' ), function( imgIndex, imgProp ) {
					var value = $this.data( imgProp );
					if( value ) {
						if( intSumOffset > $this.offset().top ) {
							$this.attr( imgProp, value );
						}
					}
				} );
				$this.on( 'load', function() {
					$this.addClass( CLASS_LAZYLOAD );
				} );
			} );
		}
	}

	jvbpd_core_elementor.prototype.menu_opener_bind_events = function( wrap ) {
		$( '.jvbpd-menu-closer', wrap ).on( 'click', function() {
			$( 'body' ).removeClass( 'jvbpd-menu-active' );
			wrap.removeClass( 'jvbpd-visible' );
		} );
	}

	jvbpd_core_elementor.prototype.menu_opener = function() {
		var self = this;
		return function() {
			$( '.jvbpd-menu-opener' ).each( function() {
				var
					$this = $( this ),
					uniqID = $this.data( 'id' ),
					templateID = $this.data( 'template' ),
					containerSelector = '.jvbpd-canvas-container';

				$this.on( 'click', function() {
					var
						winY = $( window ).height(),
						canvas = $( containerSelector + '[data-id="' + uniqID + '"]' );

					if( ! canvas.hasClass( 'loaded' ) ) {
						$( window ).trigger( 'jvbpd_core/canvas/get/before', [ $this ] );
						self.ajax( 'get_canvas', { template: templateID }, function( xhr ) {
							canvas.addClass( 'loaded' );
							$( window ).trigger( 'jvbpd_core/canvas/get/after', [ $this, xhr ] );
							canvas.html( xhr.render );
							$( window ).trigger( 'jvbpd_core/canvas/get/completed', [ $this, xhr ] );
							self.menu_opener_bind_events( canvas );
						} );
					}
					canvas.addClass( 'jvbpd-visible' ).css( 'min-height', winY );
					$( 'body' ).addClass( 'jvbpd-menu-active' );
				} );
			} );
		}
	}

	jvbpd_core_elementor.prototype.scrollSpy = function() {
		var trigger = $('.javo-single-nav > a');
		trigger.on('click', function( event ){
			var
				targetID = $(this).attr("href"),
				target = $( targetID ),
				adminbar = $('#wpadminbar'),
				stickHeader = $('.header.jvbpd-sticky-element'),
				offY = 0;

			if( target.length ) {
				offY += target.offset().top;
				offY -= $( this ).closest(".jvbpd-sticky-element").height();
				if( adminbar.length ) {
					offY -= adminbar.height();
				}
				if( stickHeader.length ) {
					offY -= stickHeader.height();
				}
				offY = 0 < offY ? offY : 0;
				$("html, body").animate({scrollTop:offY}, 500);
			}
			return false;
		});
	}

	jvbpd_core_elementor.prototype.applySlimScroll = function() {
		var elements = $(".item-list-page-wrap, .list-block-wrap");
		if( typeof $.fn.slimScroll !== 'function' ) {
			return;
		}
		elements.each( function(){
			var $this = $(this);
			if( !$this.hasClass('set-vscroll')){
				return true;
			}
			$this.slimScroll({
				height: $this.height() + 'px',
				distance: '0px',
				railVisible: true,
				alwaysVisible: true,
				railColor: '#ccc',
				railOpacity: 0.8,
			});
		});
	}

	jvbpd_core_elementor.prototype.postBlockAnimOnScroll = function() {
		$('.elementor-widget-jvbpd-page-block:not(.bind-masonry), .elementor-widget-jvbpd-single-gallery:not(.bind-masonry)').each(function(){
			/*
				effect,
				is_masonry = false, // $(this).hasClass('masonry-yes'),
				settings = $(this).data('settings'),
				container =
			*/

			var $widget = $(this);
			var settings = $widget.data('settings') || {};
			var container = new Array();
			var is_masonry = false;
			var animateType = settings.masonry_ani || '';

			if($widget.is('.elementor-widget-jvbpd-page-block')) {
				is_masonry = 'yes' == settings.use_masonry;
				container = $('.shortcode-output > .row', $widget);
			} else if($widget.is('.elementor-widget-jvbpd-single-gallery')) {
				is_masonry = 'masonry' == settings.render_type;
				container = $('ul.mansory-wrap', $widget);
			}

			if(!is_masonry || !container.length){
				return;
			}

			container
				.addClass( 'jvbpd-grid' )
				.addClass( 'effect-' + animateType );

			if( !container.attr('id')){
				container.attr('id','animon-id-' + $widget.data('id'));
			}

			var aosInstance = new AnimOnScroll(container.get(0), {
				minDuration: 0.4,
				maxDuration: 0.7,
				viewportFactor: 0.8
			});

			var parentTab = $widget.closest('.elementor-widget-jv_tabs');
			if(parentTab.length) {
				$('a.nav-link', parentTab).on('click', function(event){
					/*
					var instanceInTab = new Masonry(aosInstance.el, {
						itemSelector: 'li',
						transitionDuration: 0
					});
					var interval = setInterval(function(){
						instanceInTab.layout();
						instanceInTab.on('layoutComplete', function(){
							console.log('Complete !');
							$(window).trigger('scroll');
						});
						clearInterval(interval);
					}, 100); */
					aosInstance._init();
					var interval = setInterval(function(){
						aosInstance._init();
						clearInterval(interval);
					}, 500);
				});
			}

			$widget.addClass('bind-masonry');
		});
	}

	jvbpd_core_elementor.prototype.singleGalleryMasonry = function() {
		$('.elementor-widget-jvbpd-single-gallery').each(function(){
			var $widget = $(this);
			var settings = $widget.data('settings') || {};
			if('masonry' == settings.render_type){
				var $masonryWrap = $('ul.mansory-wrap', $widget);
				$masonryWrap.masonry({
					itemSelector: '.gallery-item',
				});
			}
		});
	}

	jvbpd_core_elementor.prototype.allBlockEffectAni = function() {
		$('.elementor-widget-jvbpd-page-block, .elementor-widget-jvbpd-map-listing-blocks, .elementor-widget-jvbpd-map-list-listing-blocks').each(function(){
			var
				$this = $(this),
				settings = $this.data('settings'),
				blockTarget = settings.animation_on_target == 'block',
				animation_delay = parseInt(settings.animation_delay || 1000),
				css_from = {},
				css_to = {};


			if(''==settings.animation_property) {
				return;
			}

			switch(settings.animation_property){
				case 'fadeIn':
					css_from = {opacity: 0,};
					css_to = {opacity: 1,};
					break;
				case 'zoomIn':
					css_from = {transform: 'scale(.5)', opacity:0, };
					css_to = {transform: 'scale(1)', opacity:1, };
					break;
				case 'zoomOut':
					css_from = {transform: 'scale(1.5)', opacity:0,};
					css_to = {transform: 'scale(1)', opacity:1,};
					break;
				case 'fromTop':
					css_from = {transform: 'translateY(-100px)', opacity:0,};
					css_to = {transform: 'translateY(0px)', opacity:1,};
					break;
				case 'fromLeft':
					css_from = {transform: 'translateX(-100px)', opacity:0,};
					css_to = {transform: 'translateX(0px)', opacity:1,};
					break;
				case 'fromBottom':
					css_from = {transform: 'translateY(100px)', opacity:0,};
					css_to = {transform: 'translateY(0px)', opacity:1,};
					break;
				case 'fromRight':
					css_from = {transform: 'translateX(100px)', opacity:0,};
					css_to = {transform: 'translateX(0px)', opacity:1,};
					break;
				case 'fromTopLeft':
					css_from = {transform: 'translate(-100px, -100px)', opacity:0,};
					css_to = {transform: 'translate(0px, 0px)', opacity:1,};
					break;
				case 'fromTopRight':
					css_from = {transform: 'translate(100px, -100px)', opacity:0,};
					css_to = {transform: 'translate(0%, 0%)', opacity:1,};
					break;
				case 'fromBottomLeft':
					css_from = {transform: 'translate(-100px, 100px)', opacity:0,};
					css_to = {transform: 'translate(0px)', opacity:1,};
					break;
				case 'fromBottomRight':
					css_from = {transform: 'translate(100px, 100px)', opacity:0,};
					css_to = {transform: 'translate(0px)', opacity:1,};
					break;
				case 'slideTop':
					css_from = {transform: 'translateY(-200%)', opacity:0,};
					css_to = {transform: 'translateY(0%)', opacity:1,};
					break;
				case 'slideLeft':
					css_from = {transform: 'translateX(-200%)', opacity:0,};
					css_to = {transform: 'translateX(0%)', opacity:1,};
					break;
				case 'slideBottom':
					css_from = {transform: 'translateY(200%)', opacity:0,};
					css_to = {transform: 'translateY(0%)', opacity:1,};
					break;
				case 'slideRight':
					css_from = {transform: 'translateX(200%)', opacity:0,};
					css_to = {transform: 'translateX(0%)', opacity:1,};
					break;
				case 'slideTopLeft':
					css_from = {transform: 'translate(-200%, -200%)', opacity:0,};
					css_to = {transform: 'translate(0%, 0%)', opacity:1,};
					break;
				case 'slideTopRight':
					css_from = {transform: 'translate(200%, -200%)', opacity:0,};
					css_to = {transform: 'translate(0%, 0%)', opacity:1,};
					break;
				case 'slideBottomLeft':
					css_from = {transform: 'translate(-200%, 200%)', opacity:0,};
					css_to = {transform: 'translate(0%, 0%)', opacity:1,};
					break;
				case 'slideBottomRight':
					css_from = {transform: 'translate(200%, 200%)', opacity:0,};
					css_to = {transform: 'translate(0%, 0%)', opacity:1,};
					break;
				case 'flipX':
					css_from = {transform: 'rotate3d(1,0,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) rotate3d(1,0,0,0deg) scale(1)', opacity:1,};
					break;
				case 'flipY':
					css_from = {transform: 'rotate3d(0,1,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) rotate3d(0,1,0,0deg) scale(1)', opacity:1,};
					break;
				case 'flipZ':
					css_from = {transform: 'perspective(2000px) rotate3d(0,0,1,45deg) scale(0.2)', opacity:0,};
					css_to = {transform: 'perspective(2000px) rotate3d(0,0,1,0deg) scale(1)', opacity:1,};
					break;
				case 'fromTopFlipX':
					css_from = {transform: 'translateY(-100px) rotate3d(1,0,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromTopFlipY':
					css_from = {transform: 'translateY(-100px) rotate3d(0,1,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromTopFlipZ':
					css_from = {transform: 'perspective(2000px) translateY(-100px) rotate3d(0,0,1,45deg) scale(0.2)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)', opacity:1,};
					break;
				case 'fromLeftFlipX':
					css_from = {transform: 'translateX(-100px) rotate3d(1,0,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateX(0) rotate3d(1,0,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromLeftFlipY':
					css_from = {transform: 'translateX(-100px) rotate3d(0,1,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateX(0) rotate3d(0,1,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromLeftFlipZ':
					css_from = {transform: 'perspective(2000px) translateX(-100px) rotate3d(0,0,1,45deg) scale(0.2)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateX(0) rotate3d(0,0,1,0deg) scale(1)', opacity:1,};
					break;
				case 'fromBottomFlipX':
					css_from = {transform: 'translateY(100px) rotate3d(1,0,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromBottomFlipY':
					css_from = {transform: 'translateY(100px) rotate3d(0,1,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromBottomFlipZ':
					css_from = {transform: 'perspective(2000px) translateY(100px) rotate3d(0,0,1,45deg) scale(0.2)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)', opacity:1,};
					break;
				case 'fromRightFlipX':
					css_from = {transform: 'translateY(100px) rotate3d(1,0,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(1,0,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromRightFlipY':
					css_from = {transform: 'translateY(100px) rotate3d(0,1,0,90deg) scale(0.8)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,1,0,0deg) scale(1)', opacity:1,};
					break;
				case 'fromRightFlipZ':
					css_from = {transform: 'perspective(2000px) translateY(100px) rotate3d(0,0,1,45deg) scale(0.2)', opacity:0,};
					css_to = {transform: 'perspective(2000px) translateY(0) rotate3d(0,0,1,0deg) scale(1)', opacity:1,};
					break;
				case 'perspectiveX':
					css_from = {transform: 'perspective(2000px) rotateX(45deg)', opacity:0,};
					css_to = {transform: 'perspective(2000px)', opacity:1,};
					break;
				case 'perspectiveY':
					css_from = {transform: 'perspective(2000px) rotateY(45deg)', opacity:0,};
					css_to = {transform: 'perspective(2000px)', opacity:1,};
					break;
				case 'perspectiveZ':
					css_from = {transform: 'matrix3d(0.70592, 0.02465, 0.37557, -0.00062, -0.06052, 0.79532, 0.06156, -0.0001, -0.46435, -0.10342, 0.87958, -0.00146, -21.42566, 4.13698, 4.81749, 0.99197085)', opacity:0,};
					css_to = {transform: 'matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)', opacity:1,};
					break;
				case 'falling_rotate':
					css_from = {transform: 'matrix3d(0.71,0.71,0.00,0,-0.71,0.71,0.00,0,0,0,1,0,-50,-250,0,1)', opacity:0,};
					css_to = {transform: 'matrix3d(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1)', opacity:1,};
					break;
			}

			var moduleBindEventCallback = function($item, prepare) {
				var prepare = prepare || false;
				var clearProperty = function(){
					$item.css({
						transitionDuration: '',
						transitionProperty: '',
						transition: '',
						transform: '',
					});
				}
				var from = function() {
					$item.css(css_from);
				}
				var to = function() {
					var nTimeID = setInterval(function(){
						clearInterval(nTimeID);
						clearProperty();
						return false;
					}, parseInt(settings.animation_speed));
					css_to.transitionDuration = parseInt(settings.animation_speed) + 'ms';
					css_to.transitionProperty = 'all';
					$item.css(css_to);
				}
				var callback = function(event){
					var $window = $(this);
					if(!blockTarget){
						if(($window.scrollTop() + $window.height()) >= $item.offset().top) {
							to();
						}
					}
				}
				if($item.data('bind-ani')){
					return true;
				}
				if(prepare){
					from();
					return false;

				}
				$item.addClass('animated');
				$item.data('bind-ani', true);
				clearProperty();
				from();
				if(blockTarget){
					to();
				}else{
					$(window).on('scroll', callback );
					callback();
				}
			}
			$(window).on('scroll javo:map/items/get/after', function(event) {
				var intStack = 0;
				var $window = $(this);
				$('.module-item:not(.animated), .jvbpd-module:not(.animated)', $this).each(function(){
					var $item = $(this);
					if(blockTarget){
						if(($window.scrollTop() + $window.height()) >= $this.offset().top) {
							moduleBindEventCallback($item, true);
							setInterval(function(){
								moduleBindEventCallback($item);
							}, intStack * animation_delay);
							intStack++;
						}
					}else{
						moduleBindEventCallback($item);
					}
				});
			});
			$(window).on('jvbpd_core/shortcode/loaded', function(event){
				$(this).trigger('scroll');
			});
		});
	}

	jvbpd_core_elementor.prototype.getLightBox = function(selector) {
		var
			self = this,
			$selector = $('#' + selector);

		if(!$selector.length) {
			$selector = $('<div>').prop({
				'class': 'modal fade',
				'id': selector,
				'role': 'dialog'
			}).append(
					$('<div>').prop('class', 'modal-dialog').append(
					$('<div>').prop('class', 'modal-content text-right').append(
						new Array(
							$('<button>').attr({
								type: 'button',
								class: 'close text-right',
								'data-dismiss': 'modal',
							}).html(
								'<span aria-hidden="true">&times;</span>'
							),
							$('<div>').prop('class', 'modal-body text-left')
						)
					)
				)
			).appendTo($('body'));
		}
		return $selector;
	}

	jvbpd_core_elementor.prototype.postBlockPopups = function() {
		var
			self = this,
			lightBox = self.getLightBox('post-block-lightbox');
		$('.elementor-widget-jvbpd-page-block').each(function(){
			var
				$this = $(this),
				settings = $this.data('settings'),
				items = $('[data-popup-post-id]', $this);
			if('yes' == settings.module_click_popup) {
				items.css({
					'cursor':'pointer',
				}).on('click', function( event) {
					var
						$item = $(this),
						item_id = $item.data('popup-post-id');
					event.preventDefault();
					$('.modal-body', lightBox).html('<i class="fa fa-spinner fa-spin"></i>');
					$('.close', lightBox).addClass('hidden');
					lightBox.modal('show');
					self.ajax( 'page_block_content', { post_id: item_id }, function( data ) {
						$('.close', lightBox).removeClass('hidden');
						$('.modal-body', lightBox).html(data.html);
					});
				});
			}
		});
	}

	jvbpd_core_elementor.prototype.testimoonialPopups = function() {
		var
			self = this,
			lightBox = self.getLightBox('testimoonial-lightbox');

		$('script[data-item-additional-content]').each(function() {
			var
				$this = $(this),
				content = $this.html(),
				block = $this.closest('.testimoni-wrapper');

			block.css('cursor', 'pointer').on('click', function(event){
				event.preventDefault();
				$('.modal-body', lightBox).html(content);
				lightBox.modal('show');
			});
		});
	}

	jvbpd_core_elementor.prototype.mapBlockAnimOnScroll = function() {
		$('.elementor-widget-jvbpd-map-listing-blocks, .elementor-widget-jvbpd-map-list-listing-blocks').each(function(){
			var
				effect,
				$this = $(this),
				is_masonry = $this.hasClass('masonry-yes'),
				settings = $this.data('settings'),
				container = $('.list-group > .row, .space > .row', $this);
			if(!is_masonry){
				return;
			}
			effect  = 'effect-' + settings.masonry_ani;
			container.addClass( 'jvbpd-grid' ).addClass(effect);
			if( !container.attr('id')){
				container.attr('id','animon-id-' + $(this).data('id'));
			}
			$this.addClass('map-block-container');
		});
	}

	jvbpd_core_elementor.prototype.mapBlockSetCount = function() {
		var self = this;
		$('.elementor-widget-jvbpd-map-listing-blocks, .elementor-widget-jvbpd-map-list-listing-blocks').each(function(){
			var
				$this = $(this),
				settings = $this.data('settings') || false;

			if(!settings){
				return;
			}

			var firstcount = parseInt(settings.count_first_loadmore) || false;
			var moreCount = parseInt(settings.count_loadmore) || false;
			self.loopCount = 0;
			$( window ).on( 'javo:map/filter/before', function( event, item, obj ) {
				self.loopCount = 0;
			});
			$( window ).on( 'javo:map/items/prepare', function( event, obj ) {
				if(!obj){
					return false;
				}
				if(0 < moreCount){
					obj.args.loadmore_amount = moreCount;
				}
				if(self.loopCount == 0&& 0 < firstcount){
					obj.args.loadmore_amount = firstcount;
				}
				self.loopCount++;
			});
		});
	}

	jvbpd_core_elementor.prototype.authorReviewModal = function() {
		var self = this;
		var lightBox = self.getLightBox('author-review-lightbox');
		var $widgets = $('.elementor-widget-jvbpd-single-author-reviews:not(.binded)');
		$widgets.each(function(){
			var $widget = $(this);
			var $template = $('script[type="text/html"]', $widget).html() || '';
			$('.jvbpd-modal-popup-opener', $widget).on('click', function(){
				$('.modal-body', lightBox).html($template);
				lightBox.modal('show');
			});
			$widget.addClass('binded');
		});
	}

	jvbpd_core_elementor.prototype.jvbpdPopupModal = function() {
		var self = this;
		var lightBox = self.getLightBox('jvbpd-modal-lightbox');
		var $widgets = $('.elementor-widget-jvbpd-modal-popup:not(.binded)');
		$widgets.each(function(){
			var $widget = $(this);
			var $template = $('script[type="text/html"]', $widget).html() || '';
			$('.jvbpd-modal-popup-opener', $widget).on('click', function(){
				$('.modal-body', lightBox).html($template);
				lightBox.modal('show');
			});
			$widget.addClass('binded');
		});
	}

	jvbpd_core_elementor.prototype.itemGetAfterMasonry = function() {
		var self = this;
		return function( event, args ){
			$.each([args.controls.map.output,args.controls.list.output], function(){
				var
					aosInstance,
					$this = $(this);
				if(!$this.hasClass('jvbpd-grid')){
					return;
				}
				aosInstance = new AnimOnScroll($this.get(0), {
					minDuration: 0.4,
					maxDuration: 0.7,
					viewportFactor: 0.8
				});
				$this.data('instance', aosInstance);
			});
		}
	}

	jvbpd_core_elementor.prototype.customLavaAS = function() {
		var self = this;
		$('.lava-ajax-search-form-wrap > input').each(function(){
			var
				$this = $(this),
				instance = false,
				dropdown = false,
				widget = $this.closest('.elementor-widget-jvbpd-search-form-listing'),
				widgetID = widget.data('id'),
				settings = widget.data('settings') || {};
			instance = $this.data('ui-autocomplete');
			if( instance ) {
				dropdown = instance.menu.element;
				dropdown.addClass('jvbpd-ajax-search-' + widgetID );
				$(window).on('lava:ajax-search-open', function(event, args){
					if(settings && 'yes' == settings.ajax_sub_menu_position){
						dropdown.css({
							'top': '+=' + settings.ajax_sub_menu_position_top.size + 'px',
							'left': '+=' + settings.ajax_sub_menu_position_left.size + 'px',
							'width': (settings.ajax_sub_menu_position_width.size) + 'px',
						})
					}
				});
			}
		});
	}

	jvbpd_core_elementor.prototype.searchFormAjax = function() {
		var self = this;
		return function(event, lasInstance) {
			var $this = $(this);
			var $widget = $this.closest('.elementor-widget-jvbpd-search-form-listing');
			var settings = $widget.data('settings') || {};
			if('yes' == settings.ajax_defult_category) {
				lasInstance.args.listing_category = '[]';
			}

		}
	}

	jvbpd_core_elementor.prototype.parallaxSections = function() {
		var
			self = this,
			sections = parallax_section_data;
		if(!sections){
			return;
		}
		$.each(sections, function(section_id, section_items){
			var
				output = new Array(),
				scrolls = new Array(),
				mousemoves = new Array(),
				section = $('[data-id="' + section_id + '"].elementor-section');

			$.each(section_items, function(item_index, item){
				var itemEl = $('<div>').prop({
					'class': 'parallax-item item-id-' + item._id + ' ' + 'type-' + item.type,
				}).css({
				}).prepend(
					$('<div>').prop({
						'class' : 'parallax-item-image',
					}).data({
						'speed' : item.speed.size,
						'transform' : item.transform,
						'position-y' : item.backgroundPositionY.size,
					}).css({
						'z-ndex' : item.zIndex,
						'background-size': item.backgroundSize,
						'background-position': item.backgroundPositionX.size + '%' + ' ' + item.backgroundPositionY.size + '%',
						'background-image': 'url(' + item.image.url  + ')',
					})
				);
				output.push(itemEl);
				if('scroll' == item.type)  {
					scrolls.push(itemEl);
				}
				if('mouse_move' == item.type)  {
					mousemoves.push(itemEl);
				}
			});

			$(window).on('scroll', function() {
				var $win = $(this);
				$.each( scrolls, function() {
					var
						$this = $(this),
						$thisHeight = $this.innerHeight(),
						$image = $('.parallax-item-image', $this),
						$imageY = $image.offset().top,
						$imageBgY = $image.data('position-y') || '0',
						scrollTop = $win.scrollTop(),
						winHeight = $win.height(),
						speed = $image.data('speed') || 100,
						transform = $image.data('transform');

					speed = speed / 100;

					switch(transform) {
						case 'back_pos':
							var imgPercent = (scrollTop-$imageY+winHeight)/$thisHeight*100;

							if ( scrollTop < $imageY - winHeight ) imgPercent = 0;
							if ( scrollTop > $imageY + $thisHeight) imgPercent = 200;
							imgPercent *= speed;
							$image.css({
								'background-position-y': 'calc('+ imgPercent + 'px + ' + parseInt($imageBgY) + '%)',
							});
							break;
						case 'transform':
						default:
							var winBottom = scrollTop + winHeight;
							if (winBottom > $imageY && scrollTop < $imageY + $thisHeight) {
								var imgBottom = ((winBottom - $imageY) * speed);
								var imgTop = scrollTop + $thisHeight;
								var imgPercent = ((imgBottom / imgTop) * 100) + (50 - (speed * 50));
							}
							$image.css({
								transform: 'translateY('+ imgPercent + '%)',
							});
					}
				});
			});

			$.each( mousemoves, function(mousemove_index, mousemove) {
				$(section).on('mousemove', function(event){
					var
						$this = $(mousemove),
						$image = $('.parallax-item-image', $this),
						speed = $image.data('speed') || 100,
						x = event.pageX - $this.offset().left - $this.width() / 2,
						y = event.pageY - $this.offset().top - $this.height() / 2;

					speed = speed / 100;

					x = ( -x * speed ) + 'px';
					y = ( -y * speed ) + 'px';
					$image.css({
						transform: 'translate('+ x + ',' + y + ')',
					});
				});
			});
			section.prepend(output);
		});
	}

	jvbpd_core_elementor.prototype.fullHeightSections = function() {
		$('section.elementor-element.full-height-yes').each(function(){
			var $section = $(this);
			var settings = $section.data('settings');
			var callback = function() {
				var $window = $(this);
				$section.css({
					position:'absolute',
					top:0 + 'px',
					bottom:0 + 'px',
				});
			}
			$(window).on('load', callback);
			callback();
		});
	}

	jvbpd_core_elementor.prototype.applyMapStyle = function() {
		return function() {
			$('.elementor-widget-jvbpd-single-header, .elementor-widget-jvbpd-single-small-map').each(function(){
				var
					$this = $(this),
					map = $('.container-map', $this),
					mapInstance = $(map).gmap3('get'),
					settings = $this.data('settings');
				if(map.length && settings && mapInstance ) {
					var styleCode = settings.google_map_style || '{}';
					mapInstance.setOptions({styles: JSON.parse(styleCode)});
					var zoomLv = parseInt(settings.map_zoom_level) || false;
					if(zoomLv) {
						mapInstance.setZoom(zoomLv);
					}
				}
			});
		}
	}

	jvbpd_core_elementor.prototype.mapBlockOrder = function() {
		var
			self = this,
			instance = window.jvbpd_map_box_func;

		$('.elementor-widget-jvbpd-map-listing-blocks, .elementor-widget-jvbpd-map-list-listing-blocks').each(function(){
			var
				$this = $(this),
				settings = $this.data('settings'),
				orderBy = settings.order_by || false,
				order = settings.order_type || 'desc';

			if(!orderBy)  {
				return true;
			}

			orderBy = 'title' == orderBy ? 'name' : orderBy;
			orderBy = 'rand' == orderBy ? 'random' : orderBy;
			order = order.toLowerCase();

			$( window ).on( 'javo:map/filter/after', function( event, item ) {
				var items = item.items;
				if( 'rating' == orderBy ) {
					items.sort( instance.order_raty( orderBy ) );
				}else{
					items.sort( instance.compare( orderBy ) );
				}

				if( 'asc' == order ) {
					items.reverse();
				}
				item.set( items );
			});
		});
	}

	jvbpd_core_elementor.prototype.navMetisMenus = function() {
		$('.elementor-widget-jvbpd_nav_menu').each(function(){
			var $this = $(this);
			var $menu = $('ul.jvbpd-nav-menu', $this);
			if('binded' == $menu.data('bind-metis')) {
				return true;
			}
			$menu.metisMenu({
				triggerElement:'li:not(.wide-container) > a.nav-link',
				parentTrigger:'.nav-item',
				subMenu:'.menu-depth-1',
			}).data('bind-metis', 'binded');
		});
	}
	window.jvbpd_core_elementor = new jvbpd_core_elementor;

} )( jQuery, window );