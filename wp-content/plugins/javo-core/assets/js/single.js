( function( $ ) {
	"use strict";

	var jvbpd_single_template_script = function( el ) {
		this.el = el;
		this.param	= jvbpd_custom_post_param;
		this.init();
	}

	jvbpd_single_template_script.prototype	= {

		constractor : jvbpd_single_template_script,

		init : function() {

			var
				obj = this,
				offy = $( "#wpadminbar" ).outerHeight() || 0;

			$( window ).on( 'load', function(){ obj.window_loaded = true; } );

			obj.featured_switcher();
			obj.applyAllSingleMapStyle();
			obj.singleParallax();

			$( document )
				.on( 'click', $( '.jvbpd-single-share-opener', this.el ).selector, obj.showShare() )
				.on( 'click', $( '.lava-wg-single-report-trigger', this.el ).selector, obj.showReport() )
				.on( 'click', $( '#lava-wg-url-link-copy', this.el ).selector, obj.copyLink() )
				.on( 'click', '.expandable-content-overlay', obj.readMore( obj ) )
				.on( 'click', '.jv-custom-post-content-trigger', obj.readMoreType2( obj ) )
				.on( 'click', '#javo-item-detail-image-section a.link-display', obj.imageMore() );

			$( document.body ).on( 'javo:single_container_loaded', obj.singleSwiperInit() );

			$( window ).on( 'resize', obj.single_resize() );

			$( window ).on( 'load', function() {
				if( typeof $.fn.tab != 'undefined' ) {
					$('#javo-single-tab-menu').tab();
				}
				// link to specific single-tabs
				var hash = location.hash
				  , hashPieces = hash.split('?')
				  , activeTab = hashPieces[0] != '' ? $('[href="' + hashPieces[0] + '"]') : null;
				activeTab && activeTab.tab('show');
				//$( this ).scrollTop( 0 );
			} );

			if( this.param.map_type != 'boxed' && this.param.single_type != 'type-grid' ){
				$( document )
					.on( 'lava:single-msp-setup-after', function(){
						$( window )
							.on( 'resize', obj.bindResize())
							.trigger( 'resize' );
					} );
			}

			if( this.param.widget_sticky != 'disable' && typeof $.fn.sticky != 'undefined' )
				this.el.find( '.panel' ).sticky({ topSpacing : parseInt( offy ) }).css( 'zIndex', 1 );

			$( ".sidebar-inner" ).css( 'background', '' );
			if( typeof $.fn._sticky != 'undefined' ) {
				$( ".lava-spyscroll" ).css({ padding:0, 'zIndex':2 })._sticky({ topSpacing : parseInt( offy ) });
			}
		},

		showShare : function() {
			var self = this;
			return function( e ) {
				e.preventDefault();

				jQuery.lava_msg({
					content			: $( 'script#jvbpd-single-share-contents' ).html(),
					classes			: 'lava-Di-share-dialog',
					close			: 0,
					close_trigger	: '.lava-Di-share-dialog .close',
					blur_close		: true
				});

				if( typeof Clipboard == 'function' ) {
					var objZC = new Clipboard( '#jvbpd-single-share-link' );
				}
				$( document ).trigger( 'jvbpd_sns:init' );
			}
		},

		showReport : function() {
			var obj		= this;
			return function( e ) {
				e.preventDefault();
				jQuery.lava_msg({
					content			: $( '#lava-wg-single-report-template' ).html()
					, classes		: 'lava-wg-single-report-dialog'
					, close			: 0
					, close_trigger	: '.lava-wg-single-report-dialog .close'
					, blur_close	: true
				});
			}
		},

		copyLink : function() {
			return function( e ) {
				e.preventDefault();
				// Todo : Code here.
			}
		},

		bindResize : function() {
			var self = this;
			return function() {
				var is_boxed = $( 'body' ).hasClass( 'boxed' );
				var container = $( ".jv-single-map-wapper" );
				var parent = container.parent();
				var posLeft = 0;
				if(!parent.length) {
					return;
				}

				var
					offset = parent.offset().left,
					dcWidth = $( window ).width(),
					pdParent = parseInt( parent.css( 'padding-left' ) );

				if( is_boxed )
					return;
				if( offset > 0 ) {
					posLeft		= -( offset );
				} else {
					posLeft		= 0;
				}
				container.css({
					marginLeft	: posLeft - pdParent
					, width		: dcWidth
				});
			}
		},

		single_resize : function() {
			var halfContainer = $( ".half-wrap" );
			return function() {
				// return false;
				var dcHeight = $( window ).height();
				$( '.half-left-wrap, .half-right-wrap', halfContainer ).height( dcHeight );
			}
		},

		readMore : function( obj ) {
			return function( e ) {
				e.preventDefault();
				$( this ).closest( '.expandable-content-wrap' ).addClass( 'loaded' );
			}
		},

		readMoreType2 : function( obj ) {
			return function( e ) {
				e.preventDefault();
				$( this ).closest( '.jv-custom-post-content' ).addClass( 'loaded' );
			}
		},

		imageMore : function(){
			return function(e){
				e.preventDefault();
				var
					container	= $( '.jvbpd-sinlge-gallery-wrap' ),
					parseImage	= container.data( 'images' );

				if('undefined' != typeof elementorFrontend) {
					if(elementorFrontend.isEditMode()){
						return;
					}
				}

				if( typeof $.fn.lightGallery != 'undefined' ){
					container.lightGallery({
						dynamic		: true,
						dynamicEl	: parseImage
					});
				}
				return;

			}
		},

		getStyles : function() {
			return [{featureType:"all",elementType:"all",stylers:[{color:"#c0e8e8"}]},{featureType:"administrative",elementType:"labels",stylers:[{color:"#4f4735"},{visibility:"simplified"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#e9e7e3"}]},{featureType:"poi",elementType:"labels",stylers:[{color:"#fa0000"},{visibility:"off"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"#73716d"},{visibility:"on"}]},{featureType:"road.highway",elementType:"all",stylers:[{color:"#ffffff"},{weight:"0.50"}]},{featureType:"road.highway",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"road.highway",elementType:"labels.text.fill",stylers:[{color:"#73716d"}]},{featureType:"water",elementType:"geometry.fill",stylers:[{color:"#7dcdcd"}]}];
		},

		init3DViewer : function( container ) {
			var template = $( 'script', container ).html();

			if( ! container.hasClass( 'loaded' ) ) {
				$( window ).on( 'load', function() {
					container.html( template );
				} );
				container.addClass( 'loaded' );
			}

		},

		loadContainer : function( type, container ) {
			var obj = this;
			container.each(function(){
				var
					$this = $( this ),
					bkInstance,
					callback,
					template;

				if( $this.hasClass( 'loaded' ) ) { return; }

				callback = function() {
					switch( type ) {
						case 'viewVideo' :
						case 'view3d' :
							template = $( 'script', $this ).html();
							$this.html( template );
							$this.addClass( 'loaded' );
							break;
						case 'grid' : break;
						case 'streetview':
							$this.data('street-view').set();
							$this.addClass( 'loaded' );
							break;
						default:
							if($this.data('background')) {
								bkInstance = $this.data('background');
								$("<img>").prop("src", bkInstance).load( function(){
									$( this ).remove();
									$( '.container-image-viewer', $this ).css({
										'background-image' : 'url("' + bkInstance + '")',
										'background-repeat' : 'no-norepeat',
										'background-size' : 'cover',
										'background-position' : 'center 50%'
									});
									$this.addClass( 'loaded' );
								});
							}else{
								$this.addClass( 'loaded' );
							}

					}
					$( document.body ).trigger( 'javo:single_container_loaded', { 'type' : type, 'el' : $this } );
				}
				if( obj.window_loaded ) {
					callback();
				}else{
					$( window ).on( 'load', function() { callback(); } );
				}
			});
		},

		featured_switcher : function() {
			var
				obj = this,
				userAgent = window.navigator.userAgent.toLowerCase(),
				isApplePhons = /iphone|ipot|ipad/.test( userAgent ),
				lat = $( 'input[data-item-lat]' ).val() || 0,
				lng = $( 'input[data-item-lng]' ).val() || 0,
				strOn = $( 'input[data-item-street-visible]' ).val() || 1,
				strlat = $( 'input[data-item-street-lat]' ).val() || 0,
				strlng = $( 'input[data-item-street-lng]' ).val() || 0,

				relative_items = new Array(),

				strheading = parseFloat( $( 'input[data-item-street-heading]' ).val() ) || 0,
				strpitch = parseFloat( $( 'input[data-item-street-pitch]' ).val() ) || 0,
				strzoom = parseFloat( $( 'input[data-item-street-zoom]' ).val() ) || 0,
				container = $( '.single-item-tab-feature-bg-wrap' ),

				featDIV = $( '.container-featured', container ),
				mainDIV = $( '.container-main', container ),
				mapDIV = $( '.container-map', container ),
				viewer3d = $( '.container-3dview', container ),
				videoDIV = $( '.container-video', container ),
				streetview = $( '.container-streetview', container ),

				catDIV = $( '.container-category-featured', container ),
				featuredDIV = $( '.container-featured', container ),
				gridDIV= $( '.container-grid', container ),

				btns = $( '.javo-core-single-featured-switcher' ),
				objPano = false,
				opt = {
					map: {
						options:{
							center: new google.maps.LatLng( lat, lng ),
							mapTypeControl : false,
							panControl : false,
							streetViewControl : false,
							zoomControlOptions : {
								position : google.maps.ControlPosition.LEFT_CENTER
							},
							zoom: 16
						}
					}
				},
				firstDIV = container.data( 'first' ),
				allDIV = new Array( mapDIV, viewer3d, videoDIV, streetview, catDIV, featuredDIV, gridDIV ),
				finder = {
					'map' : mapDIV,
					'streetview' : streetview,
					'view3d' : viewer3d,
					'viewVideo' : videoDIV,
					'featured' : featuredDIV,
					'listing_category' : catDIV,
					'grid_style' : gridDIV
				},
				findDIV = finder[ firstDIV ],
				finderIndex = allDIV.indexOf( findDIV );
			if( finderIndex != -1 ) {
				allDIV.splice( finderIndex, 1 );
				obj.loadContainer( firstDIV, findDIV );
			}

			obj.togglePanel( allDIV );

			if( typeof findDIV != 'undefined' ) {
				findDIV.each( function() {
					var thisFindDIV = $(this);
					/*
					thisFindDIV.css({
						'left' : '0%',
					}).addClass( 'active' ); */
					thisFindDIV.addClass( 'active' );
					if( thisFindDIV.data( 'background' ) ) {
						$( '.container-image-viewer', thisFindDIV ).css({
							'background-image': 'url("' + thisFindDIV.data( 'background' ) + '")',
							'background-repeat' : 'no-repeat',
							'background-size' : 'cover',
							'background-attachment' : ( isApplePhons ? 'scroll' : 'fixed' ),
							'background-position' : 'center center',
						} );
					}
				});
			}

			$( 'li.switch-map', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ mapDIV ], false );
				}else{
					obj.togglePanel( [ mapDIV ], true );
					obj.togglePanel( [ viewer3d, streetview, videoDIV, catDIV, featuredDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'map', mapDIV );
			} );

			$( 'li.switch-featured', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ featuredDIV ], false );
				}else{
					obj.togglePanel( [ featuredDIV ], true );
					obj.togglePanel( [ mapDIV, viewer3d, streetview, videoDIV, catDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'featured', featuredDIV );
			} );

			$( 'li.switch-streetview', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ streetview ], false );
				}else{
					obj.togglePanel( [ streetview ], true );
					obj.togglePanel( [ mapDIV, viewer3d, videoDIV, catDIV, featuredDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'streetview', streetview );
			} );

			$( 'li.switch-3dview', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ viewer3d ], false );
				}else{
					obj.togglePanel( [ viewer3d ], true );
					obj.togglePanel( [ mapDIV, streetview, videoDIV, catDIV, featuredDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'view3d', viewer3d );
			} );

			$( 'li.switch-video', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ videoDIV ], false );
				}else{
					obj.togglePanel( [ videoDIV ], true );
					obj.togglePanel( [ viewer3d, streetview, mapDIV, catDIV, featuredDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'viewVideo', videoDIV );
			} );

			$( 'li.switch-get-direction', btns ).on( 'click', function() {
				var isOpen = $( 'li.switch-map', btns ).hasClass( 'active' );
				if( ! isOpen ) {
					$( 'li', btns ).removeClass( 'active' );
					$( 'li.switch-map', btns ).addClass( 'active' );
					obj.togglePanel( [ mapDIV ], true );
					obj.togglePanel( [ viewer3d, streetview, videoDIV, catDIV, featuredDIV, gridDIV ], false );
				}
				$('#single-title-line-modal-get-dir').modal('show');
				obj.loadContainer( 'map', mapDIV );
			} );

			$( 'li.switch-grid', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ gridDIV ], false );
				}else{
					obj.togglePanel( [ gridDIV ], true );
					obj.togglePanel( [ viewer3d, streetview, videoDIV, catDIV, featuredDIV, mapDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'grid_style', gridDIV );
			} );

			$( 'li.switch-category', btns ).on( 'click', function() {
				var isOpen = $( this ).hasClass( 'active' );
				if( isOpen ) return false;
				$( 'li', btns ).removeClass( 'active' );
				if( isOpen ) {
					obj.togglePanel( [ catDIV ], false );
				}else{
					obj.togglePanel( [ catDIV ], true );
					obj.togglePanel( [ viewer3d, streetview, videoDIV, mapDIV, featuredDIV, gridDIV ], false );
					$( this ).addClass( 'active' );
				}
				obj.loadContainer( 'category_featured', catDIV );
			} );

			var icon = '';
			if( typeof jvbpd_common_args != 'undefined' && jvbpd_common_args.map_marker ) {
				icon = jvbpd_common_args.map_marker;
			}
			if( mapDIV.hasClass( 'relative-markers' ) && typeof obj.param.relative_items != 'undefined' ) {
				$.each( obj.param.relative_items, function( relative_id, relative_attr ) {
					var icon = icon || '';

					if( typeof obj.param.relative_marker_urls[ relative_attr.term_id ] != 'undefined' ) {
						if( obj.param.relative_marker_urls[ relative_attr.term_id ] ) {
							icon = obj.param.relative_marker_urls[ relative_attr.term_id ];
						}
					}
					relative_items.push( {
						'lat' : relative_attr.lat,
						'lng' : relative_attr.lng,
						'options' : { 'icon' : icon },
						'data' : { 'post_id' : relative_id },
					} );
				} );
				opt = $.extend( true, { marker: { values : relative_items } }, opt );
			}else{
				opt = $.extend( true, { marker: { latLng : new google.maps.LatLng( lat, lng ), options:{icon:icon} } }, opt );
			}

			if( strOn && strlat && strlng && false  ) {
				opt = $.extend( true, {
					streetviewpanorama : {
						options : {
							container : streetview[0],
							opts : {
								position : new google.maps.LatLng( strlat, strlng ) ,
								pov : { heading: strheading, pitch: strpitch, zoom: 0 }
							}
						}
					}
				}, opt );
			}

			if( !mapDIV.length ) {
				return;
			}

			mapDIV.each( function(mapIndex, mapEl) {
				var thisMap = $( this );
				thisMap.gmap3( opt );
				if( obj.param.map_style ){
					thisMap.gmap3( 'get' ).setOptions({ styles : JSON.parse( obj.param.map_style ) });
				}else{
					thisMap.gmap3( 'get' ).setOptions({ styles: obj.getStyles() });
				}
				thisMap.gmap3({ trigger: 'resize' });
				thisMap.gmap3( 'get' ).setCenter( new google.maps.LatLng( lat, lng ) );
				if( strOn && strlat && strlng ) {
					var streetViewControl = function(){
						this.map = thisMap.gmap3('get');
					}
					streetViewControl.prototype.constractor = streetViewControl;
					streetViewControl.prototype.set = function() {
						objPano = new google.maps.StreetViewPanorama(
							streetview[mapIndex], {
								position : new google.maps.LatLng( strlat, strlng ),
								pov : { heading: strheading, pitch: strpitch, zoom:0.1 }
							}
						);
						this.map.setStreetView( objPano );
					}
					$(streetview[mapIndex]).data('street-view', new streetViewControl);
				}
			});

			container.removeClass( 'hidden' );
			$( document ).trigger( 'lava:single-msp-setup-after', { el: mapDIV, latLng: new google.maps.LatLng( lat, lng ) } );
		},

		togglePanel : function( panels, onoff ) {
			$.each( panels, function( index, panel ) {
				if( !onoff ) {
					//$( this ).removeClass( 'active' ).animate( { 'left': '-100%'}, 500 );
					$( this ).removeClass( 'active' );
				}else{
					// $( this ).addClass( 'active' ).animate( { 'left': '0%' }, 500 );
					$( this ).addClass( 'active' );
				}
			} );
		},

		applyAllSingleMapStyle : function() {
			var obj = this;
			$( window ).on( 'load', function() {
				$( '.single-lv-map-style' ).each( function() {
					var relative_items = new Array();

					if( typeof $( this ).data( 'gmap3' ) != 'undefined' ) {
						if( $( this ).hasClass( 'relative-markers' ) && typeof obj.param.relative_items != 'undefined' ) {
							$.each( obj.param.relative_items, function( relative_id, relative_attr ) {
								var icon = '';
								if( typeof jvbpd_common_args != 'undefined' ) {
									icon = jvbpd_common_args.map_marker;
								}
								if( typeof obj.param.relative_marker_urls[ relative_attr.term_id ] != 'undefined' ) {
									if( obj.param.relative_marker_urls[ relative_attr.term_id ]){
										icon = obj.param.relative_marker_urls[ relative_attr.term_id ];
									}
								}
								relative_items.push( {
									'lat' : relative_attr.lat,
									'lng' : relative_attr.lng,
									'options' : { 'icon' : icon },
									'data' : { 'post_id' : relative_id },
								} );
							} );
						}
						$( this ).gmap3({ marker : { values : relative_items } });
						// featured_switcher function
						// $( this ).gmap3( 'get' ).setOptions({ styles: obj.getStyles() });
					}
				} );
			} );
		}
	}
	jvbpd_single_template_script.prototype.singleSwiperInit = function() {
		var self = this;
		return function( event, param, $this ) {
			if( param.type == 'grid_style' ) {
				$( '.container-grid .swiper-container' ).removeClass( 'hidden' );
				var opt = {
					pagination: '.container-grid .swiper-pagination',
					slidesPerView: 3,
					paginationClickable: true,
					spaceBetween: 0,
					nextButton: '.container-grid .swiper-button-next',
					prevButton: '.container-grid .swiper-button-prev',
					navigation: {
						prevEl: '.container-grid .swiper-button-prev',
						nextEl: '.container-grid .swiper-button-next',
					},
					breakpoints: {
						1024: {
							slidesPerView: 2,
							spaceBetween: 10
						},
						768: {
							slidesPerView: 2,
							spaceBetween: 10
						},
						640: {
							slidesPerView: 2,
							spaceBetween: 5
						},
						380: {
							slidesPerView: 1,
							spaceBetween: 0
						}
					},
					on:{
						init: function() {
							if( $this ) {
								$this.addClass( "loaded");
							}
						},
					}
				}
				var swiperInstance = new Swiper('.container-grid .swiper-container', opt );
				if('undefined' != typeof $.fn.magnificPopup){
					$('.swiper-slide', swiperInstance.el).css('cursor', 'pointer').magnificPopup( {
						type : 'image',
						// items : $( this ).data( 'images' ),
						fixedContentPos : false,
						// delegate : 'div.item-image',
						gallery	: { enabled: true },
						callbacks:{
							elementParse:function(item){
								item.src = $(item.el).data('src');
							}
						},
					} );
				}
			}
		}
	}

	jvbpd_single_template_script.prototype.singleParallax = function() {
		var
			container = $( '.single-item-tab-feature-bg-wrap' ),
			targetDiv = $( '.parallax-overlay > .container-image-viewer', container ),
			overlay = $( '.parallax-overlay > .header-parallax', container ),
			cpHeader = $( '.single-item-tab-feature-bg' ),
			cpOverlay = $( '> .jv-pallax', cpHeader ),
			callback = function(){

				var currentY = $( this ).scrollTop();

				if( overlay.length ){
					var
						endY = overlay.offset().top + overlay.height(),
						scrollY = parseFloat( currentY / endY );

					if( scrollY <= 1 ){
						targetDiv.css({
							'transform':
								'scale(' + parseFloat( 1 + ( scrollY * 0.12 ) ) + ')' +
								'translate3d( 0, ' + parseInt( scrollY * 10 ) + '%, 0 )'
						});

						overlay.css({
							'backgroundColor':'rgba(0,0,0, ' +  scrollY + ')',
						});
					}
				}

				if( cpOverlay.length ){
					var
						cpEndY = cpOverlay.offset().top + cpOverlay.height(),
						cpScrollY = parseFloat( currentY / cpEndY );

					if( cpScrollY <= 1 ){
						if( $( 'body' ).hasClass( 'type-half' ) ) {
							cpHeader.css({
								'backgroundPosition': '50% 50%',
								'transform': 'scale(' + parseFloat( 1 + ( cpScrollY * 0.12 ) ) + ')',
							});
						}else{
							cpHeader.css({
								'backgroundPosition': 'center ' + parseInt( 50 + ( cpScrollY * 50 ) ) + '%',
								'transform': 'scale(' + parseFloat( 1 + ( cpScrollY * 0.12 ) ) + ')',
							});
						}
						cpOverlay.css({
							'backgroundColor':'rgba(0,0,0, ' +  cpScrollY + ')',
						});
					}
				}
			}

		$( window ).on( 'scroll', callback );
		callback();
		return this;
	}


	window.jvbpd_singleInstance = new jvbpd_single_template_script( $( "form.lava-wg-author-contact-form" ) );

	;(function($){
		"use strict";
		// WordPress media upload button command.
		// Required: library/enqueue.php:  wp_media_enqueue();
		$("body").on("click", ".javo-fileupload", function(e){
			e.preventDefault();
			var $this = $(this);
			var file_frame;
			if(file_frame){ file_frame.open(); return; }
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $this.data('title'),
				multiple: $this.data('multiple')
			});
			file_frame.on( 'select', function(){
				var
					attachment,
					multipleTemplate = '<div class="col-md-3 jvbpd_dim_div mb-4"><div class="review-image"><img src="{image_url}" title="{title}" class="img-fluid rounded"></div><div class="reivew-image-remove"><button type="button" class="btn btn-xs btn-block jvbpd_detail_image_del">&times; {delete_label}</button><input type="hidden" name="jvbpd_dim_detail[]" value="{image_id}"></div></div>';
				if( $this.data('multiple') ){
					var selection = file_frame.state().get('selection');
					selection.map(function(attachment){
						var output = "";
						attachment = attachment.toJSON();

						if( $this.hasClass( 'other' ) ){
							output += "<li class=\"list-group-item jvbpd_dim_div\">";
							output += attachment.filename
							output += "<input type='hidden' name='jvbpd_attach_other[]' value='" + attachment.id + "'>";
							output += "<input type='button' value='Delete' class='btn btn-danger btn-sm jvbpd_detail_image_del'>";
							output += "</li>";
							$( $this.data('preview') ).append( output );

						}else{
							output = multipleTemplate;
							output = output.replace( /{image_url}/g, attachment.url || '' );
							output = output.replace( /{image_id}/g, attachment.id || '' );
							output = output.replace( /{title}/g, attachment.title || '' );
							output = output.replace( /{delete_label}/g, 'Remove' );
							$( $this.data('preview') ).append( output );
						}
					});

					$( window ).trigger( 'update_detail_image' );

				}else{
					attachment = file_frame.state().get('selection').first().toJSON();
					$( $this.data('input')).val(attachment.id);
					$( $this.data('preview') ).prop("src", attachment.url );

					$( window ).trigger( 'update_featured_image' );
				};
			});
			file_frame.open();
			// Upload field reset button
		}).on('click', '.javo-fileupload-cancel', function(){
			$($(this).data('preview')).prop('src', '');
			$($(this).data('input')).prop('value', '');
		}).on("click", ".jvbpd_detail_image_del", function(){
			var tar = $(this).data("id");
			$(this).parents(".jvbpd_dim_div").remove();
			$("input[name^='jvbpd_dim_detail'][value='" + tar + "']").remove();
			$( window ).trigger( 'update_detail_image' );
		});
		jQuery('.javo-color-picker').each(function(i, v){
			$(this).spectrum({
				clickoutFiresChange:true
				, showInitial: true
				, preferredFormat:'hex'
				, showInput: true
				, chooseText: 'Select'
			});
		});

		var jvbpd_share_func = function( selector ) {
			this.selector = selector;
			this.init();
		};

		jvbpd_share_func.prototype = {

			constructor : jvbpd_share_func

			, init : function () {

				var obj = this;

				$( document ).on( 'jvbpd_sns:init', function(){
					$( obj.selector ).off( 'click', obj.share() );
					$( obj.selector ).on( 'click', obj.share() );
				} ).trigger( 'jvbpd_sns:init' );

			}

			, share : function () {

				var obj			= this;

				return function ( e ) {

					e.preventDefault();

					var output_link		= new Array();

					if( $( this ).hasClass( 'sns-twitter' ) ) {
						output_link.push( "http://twitter.com/share" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&text=" + $( this ).data( 'title' ) );
					}

					if( $( this ).hasClass( 'sns-facebook' ) ) {
						output_link.push( "http://www.facebook.com/sharer.php" );
						output_link.push( "?u=" + $( this ).data( 'url' ) );
						output_link.push( "&t=" + $( this ).data( 'title' ) );
					}

					if( $( this ).hasClass( 'sns-google' ) ) {
						output_link.push( "https://plus.google.com/share" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-pinterest' ) ) {
						output_link.push( "https://www.pinterest.com/pin/find/" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-vk' ) ) {
						output_link.push( "https://vkontakte.ru/share.php" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&title=" + $( this ).data( 'title' ) );
						output_link.push( "&description=" + $( this ).data( 'description' ) );
						output_link.push( "&image=" + $( this ).data( 'image' ) );
					}


					if( $( this ).hasClass( 'sns-linkedin' ) ) {
						output_link.push( "https://www.linkedin.com/shareArticle?mini=true" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&title=" + $( this ).data( 'title' ) );
						output_link.push( "&summary=" + $( this ).data( 'text' ) );
						output_link.push( "&source=" + $( this ).data( 'url' ) );
					}


					if( $( this ).hasClass( 'sns-odnoklassniki' ) ) {
						output_link.push( "https://odnoklassniki.ru/dk?st.cmd=addShare&st.s=1" );
						output_link.push( "&st._surl=" + $( this ).data( 'url' ) );
					}


					if( $( this ).hasClass( 'sns-tumblr' ) ) {
						output_link.push( "https://tumblr.com/share/link?" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-delicious' ) ) {
						output_link.push( "https://del.icio.us/save?" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&title=" + $( this ).data( 'title' ) );
					}

					if( $( this ).hasClass( 'sns-reddit' ) ) {
						output_link.push( "https://reddit.com/submit" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&title=" + $( this ).data( 'title' ) );
					}


					if( $( this ).hasClass( 'sns-stumbleupon' ) ) {
						output_link.push( "https://www.stumbleupon.com/submit" );
						output_link.push( "&url=" + $( this ).data( 'url' ) );
					}


					if( $( this ).hasClass( 'sns-getpocket' ) ) {
						output_link.push( "https://getpocket.com/edit" );
						output_link.push( "&url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-whatsapp' ) ) {
						output_link.push( "whatsapp://send?" );
						output_link.push( "&url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-xing' ) ) {
						output_link.push( "https://www.xing.com/app/user?op=share" );
						output_link.push( "&url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-print' ) ) {
						output_link.push( "javascript:print()" );
					}

					if( $( this ).hasClass( 'sns-email' ) ) {
						output_link.push( "mailto:" );
						output_link.push( "?subject=" + $( this ).data( 'title' ) );
						output_link.push( "&body=" + $( this ).data( 'text' ) + "\n" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-telegram' ) ) {
						output_link.push( "https://telegram.me/share/url" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
						output_link.push( "&text=" + $( this ).data( 'text' ) );
					}

					if( $( this ).hasClass( 'sns-skype' ) ) {
						output_link.push( "https://web.skype.com/share" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-digg' ) ) {
						output_link.push( "https://digg.com/submit" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					if( $( this ).hasClass( 'sns-kakaostory' ) ) {
						output_link.push( "https://story.kakao.com/share" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}

					/*
					if( $( this ).hasClass( 'sns-line' ) ) {
						output_link.push( "http://line.me/R/msg/text/" );
						output_link.push( "?url=" + $( this ).data( 'url' ) );
					}*/

					window.open( output_link.join( '' ), '' );
				}
			}
		}
		new jvbpd_share_func( 'i.sns-facebook, i.sns-twitter, i.sns-google, .javo-share' );

		if( typeof $.fn.tooltip != 'undefined' ) {
			$('.javo-tooltip').each(function(i, e){
				var options = {};
				if( typeof( $(this).data('direction') ) != 'undefined' ){
					options.placement = $(this).data('direction');
				};
				$(this).tooltip(options);
			});
		}

		$(window).load(function(){
			$(this).trigger('resize');
		});
	})(jQuery);
})(window.jQuery);