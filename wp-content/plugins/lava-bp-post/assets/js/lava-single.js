( function( window, $, undef ) {
	"use strict";

	var lava_single_core_func = function( opt ) {
		var
			opt = $.extend( true, {
				map : null,
				param : null,
				slider : null,
				street : null
			}, opt ),
			param = opt.param,
			lat = parseFloat( $( "[data-item-lat]", param ).val() ) || 0,
			lng = parseFloat( $( "[data-item-lng]", param ).val() ) || 0,
			radius = parseInt( $( "[data-latlng-radius]", param ).val() ) || 0,
			panel = $( "[data-cummute-panel]", param ).val(),
			marker_icon	= $( "[data-marker-icon]", param ).val() || '';

		if( typeof window.google != 'undefined' ) {
			this.lat = lat;
			this.lng = lng;
			this.setMapInstance( opt );
		}

		this.slider = opt.slider;
		this.param = param;
		this.init();
	}

	lava_single_core_func.prototype.constructor = lava_single_core_func;

	lava_single_core_func.prototype.init = function() {
		var self = this;

		if( self.slider.length ) {
			self.attach_slider();
		}

	}

	lava_single_core_func.prototype.setMapInstance = function( opt ) {
		return false;
		this.el = opt.map;
		this.street = opt.street;
		this.latLng = new google.maps.LatLng( this.lat, this.lng );
		this.pano = new google.maps.StreetViewService;
		this.radius = radius;
		this.panel = panel;
		this.marker_icon = marker_icon;
		this.fullWidth = param.find( "[data-fullwidth]" ).val() || false;
	}

	lava_single_core_func.prototype.attach_slider = function() {

		var
			self = this,
			el = self.slider;

		if( typeof $.fn.flexslider == 'undefined' ) {
			return;
		}

		$( document ).ready( function() {
			el.removeClass( 'hidden' ).flexslider( {
			animation				: 'slide',
				controlNav			: false,
				smoothHeight	: true,
				slideshow			: true
			} );
		} );

	}


	/*
	lava_detail_item.prototype = {

		constructor: lava_detail_item

		, init : function()
		{
			var obj				= this;

			obj.instance		= true;

			// this.setCoreMapContainer();
			// $( window ).on( 'load',  this.document_loaded() );
			$( document ).ready( obj.document_loaded() );

		}

		, setCoreMapContainer : function( allowPanorama )
		{

			var
				obj								= this
				, el								= obj.el
				, elStreetview					= obj.street
				, latLng						= obj.latLng
				, param							= obj.param
				, iMapHeight					= $( '[data-map-height]', param ).val() || 300
				, iStreetHeight					= $( '[data-street-height]', param ).val() || 500
				, usePanorama					= parseFloat( $( '[data-item-street-visible]', param ).val() || 0 )
				, isLat							= parseFloat( $( '[data-item-street-lat]', param ).val() || 0 )
				, isLng							= parseFloat( $( '[data-item-street-lng]', param ).val() || 0 )
				, isHeading						= parseFloat( $( '[data-item-street-heading]', param ).val() || 0 )
				, isPitch						= parseFloat( $( '[data-item-street-pitch]', param ).val() || 0 )
				, isZoom						= parseFloat( $( '[data-item-street-zoom]', param ).val() || 0 )
				, isNotloaded					= $( '[key="strNotStreetview"]', param ).val() || 'Not data found'
				, isLatLng						= new google.maps.LatLng( isLat, isLng );

			obj.panoramaAllow			= false

			var options		= {
				map : {
					options : {
						center					: latLng
						, mapTypeId				: google.maps.MapTypeId.ROADMAP
						, mapTypeControl		: true
						, panControl			: false
						, scrollwheel			: false
						, draggable				: false
						, streetViewControl		: false
						, zoomControl			: true
					}
				}
				, panel							: {
					options						: { content: "<div id=\"lava-Di-map-panel\"></div>" }
					, top						: true
					, left						: true
				}
			}

			if( obj.street.length && usePanorama && isLat && isLng && allowPanorama ) {
				obj.panoramaAllow				= true;
				options.streetviewpanorama		= {
					options						: {
						container				: obj.street.get(0)
						, opts					: { position : isLatLng, pov : { heading : isHeading, pitch : isPitch, zoom : isZoom } }
					}
				}
			}else{
				obj.street.html( isNotloaded ).css({ textAlign: 'center', height:'auto' });
			}

			if( obj.radius )
			{
				options.map.options.zoom		= 16;
				options.circle					= {
					options						: { center : latLng, radius : obj.radius, fillColor : "#008BB2", strokeColor : "#005BB7" }
				}

			}else{

				options.map.options.zoom		= 16;
				options.marker					= {
					latLng						: latLng
					, options					: { icon : obj.marker_icon }
				}
			}

			$( window ).on(
				'resize'
				, function(){

					el.css( 'height', parseInt( iMapHeight ) );
					if( elStreetview.length && obj.panoramaAllow )
						elStreetview.css( 'height', parseInt( iStreetHeight ) );

				}
			).trigger( 'resize' );

			if( this.panel )
				options.panel					= false;

			this.map							= el.gmap3( options );

			if( ! this.panel ) {
				$( "#lava-Di-map-panel-inner" )	.appendTo( '#lava-Di-map-panel' );

				this.getPlaceService(
					$( ".lava-Di-locality" )
					, "restaurant|movie_theater|bar"
					, $( '.lava-Di-map-filter' )

				);

				this.getPlaceService(
					$( ".lava-Di-commutes" )
					, "airport|bus_station|train_station"
				);

			}

			$( document ).trigger( 'lava:single-msp-setup-after', this );

			return this;
		}

		, getPlaceService : function( el, filterKeyword, trigger )
		{
			var
				results
				, places		= {}
				, obj			= this
				, filters		= filterKeyword.split( "|" );

			var callback = function( response )
			{
				if( "OK" === response.result.status )
				{
					results = response.result.results;

					$.each(
						results
						, function( place_index, place_meta )
						{
							if( filters ) {
								for( var j  in filters )
									if( place_meta.types.indexOf( filters[ j ] ) > -1 )
										if( typeof places[ filters[ j ] ] == "undefined" ) {
											places[ filters[ j ] ] = new Array( place_meta );
										}else{
											places[ filters[ j ] ].push( place_meta );
										}
							}
						}
					); // End results each

					$.each(
						places
						, function( type_name, types )
						{
							var str = "";
							$.each(
								types
								, function( item_index, place_item )
								{
									var parseDistance = function( index, length ){
										return function( result, STATUS )
										{
											if( STATUS === "OK" )
											{
												var meta = result.rows[0].elements[0];

												if( meta.status === "OK" ) {
													str += "<li>";
														str += "<div>";
															str += "<div>";
																str += place_item.name;
															str += "</div>";
															str += "<div>";
																str += meta.distance.text + "/" + meta.duration.text;
															str += "</div>";
														str += "</div>";
													str += "</li>";
												}

												if( index == length )
													$( el.selector + "[data-type='" + type_name + "']" ).html( str );
											}
										}
									}

									obj.map.gmap3({
										getdistance:{
											options:{
												origins			: [ obj.latLng ]
												, destinations	: [ place_item.geometry.location ]
												, travelMode	: google.maps.TravelMode.DRIVING
											}
											, callback : parseDistance( item_index, ( types.length -1) )
										}
									});	// End Gmap3

								}
							); // End place item each

						}
					); // End Types each
				}
			}

			obj.getPlacesJSON( filterKeyword, callback );
		}

		, getPlacesJSON : function( args, callback )
		{
			var obj = this;

			$.getJSON(
				$( "[data-admin-ajax-url]" ).val()

				, {
					action		: 'lava_single_place_commute'
					, nonce		: $( "[data-ajax-nonce]" ).val()
					, types		: args
					, lat		: obj.latLng.lat()
					, lng		: obj.latLng.lng()
				}

				, function( response )
				{
					if( ! response.error ) {
						if( typeof callback == "function" )
							callback( response );
					}else{
						jQuery.lava_msg({ content: response.error });
					}
				}
			)
			.fail(
				function( response )
				{
					console.log( response.responseText );
				}
			);
		}

		, setCompareDistance : function ( p1, p2 )
		{
			// Google Radius API
			var R = 6371;
			var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
			var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
			var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
			Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
			Math.sin(dLon / 2) * Math.sin(dLon / 2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
			var d = R * c;
			return d;
		}

		, availableStreeview : function()
		{
			var
				obj					= this
				, pano				= obj.pano
				, param				= obj.param
				, streetPosition	= new google.maps.LatLng(
					parseFloat( $( '[data-item-street-lat]', param ).val() || 0 )
					, parseFloat( $( '[data-item-street-lng]', param ).val() || 0 )
				);

			// param1: Position, param2: Round, param3: callback
			pano.getPanoramaByLocation( streetPosition, 50
				, function( result, state ) {
					obj.setCoreMapContainer( state === google.maps.StreetViewStatus.OK )
				}
			);
			return this;
		}

		, attach_slider : function() {

			var
				obj			= this
				, el		= obj.slider;

			$( document ).ready(
				function() {
					el
						.removeClass( 'hidden' )
						.flexslider( {
							animation				: 'slide'
							, controlNav			: false
							, smoothHeight	: true
							, slideshow			: true
						} );
				}
			);

		}

		, document_loaded : function()
		{
			var
				obj				= this
				, param			= obj.param
				, mapNotLoaded	= $( '[key="strNotLocation"]', param ).val() || 'Not found data';

			return function()
			{
				if( obj.el.length )
					if( obj.lat && obj.lng ) {
						obj.availableStreeview();
					}else{
						obj.el.closest( 'div' ).addClass( 'no-marked' );
						obj.el.html( '<h6>' + mapNotLoaded + '</h6>' );
					}

				if( obj.slider.length )
					obj
						.attach_slider();

			}
		}
	}
	*/

	$.lava_single = function( options ) {
		new lava_single_core_func( options );
	}

} )( window, jQuery );