( function( $, window, undef ) {

	var lava_boxMap		= function( opt ) {

		this.attr		= $.extend( true, {}, {
			map			: null
			, params	: null
		}, opt );

		this.items		= {};
		this.el			= $( this.attr.map );
		this.param		= $( this.attr.params );
		this.prefix		= $( "[key='prefix']", this.param ).val();
		this.jFilename	= $( "[key='json_file']", this.param ).val();
		this.el_filter	= $( "[key='filter']", this.param ).val();
		this.ajaxurl	= $( "[key='ajaxurl']", this.param ).val();

		if( !this.instance )
			this.init();
	}

	lava_boxMap.prototype	= {

		constructor			: lava_boxMap,
		init : function() {
			var
				obj					= this
				, param				= obj.param
				, is_cross_domain	= $( "[key='crossdomain']", param ).val() == '1'
				, security	= $( "[key='security']", param ).val()
				, json_ajax_url		= obj.jFilename
				, parse_json_url	= json_ajax_url;

			obj.instance	= true;

			obj.infoWindo = obj.CreateInfoBubble();

			if( is_cross_domain ) {
				parse_json_url = obj.ajaxurl;
				parse_json_url += "?action=lava_" + obj.prefix + "_get_json";
				parse_json_url += "&fn=" + json_ajax_url.replace( '.json', '' );
				parse_json_url += "&security=" + security;
				parse_json_url += "&callback=?";
			}

			// DATA
			$.getJSON( parse_json_url, function( response ) {
				obj.items = response;
				obj.filter();

				if( $( "[lava-is-geoloc]" ).val() )
					$( ".lava-my-position" ).trigger( 'click' );
			})
			.fail( function( xhr ) {
				console.log( xhr.responseText );
				obj.items = [];
			} );
			this.bindEvents();
		},

		CreateInfoBubble : function() {
			return new InfoBubble({
				minWidth:362
				, minHeight:190
				, overflow:true
				, shadowStyle: 1
				, padding: 0
				, borderRadius: 10
				, arrowSize: 20
				, borderWidth: 1
				, disableAutoPan: false
				, hideCloseButton: false
				, arrowPosition: 50
				, arrowStyle: 0
			});
		},

		bindEvents : function () {

			var
				obj			= this
				, filter	= obj.el_filter;

			$( document )
				.on( 'change', $( 'select[data-filter]', filter ).selector, obj.filterTrigger() )
				.on( 'click', $( '#lava-map-search', filter ).selector, obj.filterTrigger() )
				.on( 'keypress', $( '[name="keyword"]', filter ).selector, obj.filterTrigger_input() );
		}

		, filterTrigger : function()
		{
			var obj			= this;

			return function( e ){
				e.preventDefault();
				obj.filter();
			}
		}

		, filterTrigger_input : function()
		{
			var obj			= this;

			return function( e ){
				if( e.keyCode ==13 )
					obj.filter();
			}
		}

		, filter : function() {

			var
				obj			= this
				items		= obj.items;

			items			= obj.parseFilters( items );

			obj
				.bindMarker( items )
				.loadListing( items );

		}

		, parseFilters : function( data ) {

			var
				obj					= this
				, is_filter			= false
				, filter			= obj.el_filter
				, items				= data
				, objFilter			= $( "[data-filter]", filter );


			objFilter.each( function( i, element ) {
				var
					arrResult		= new Array()
					, strValue		= $( this ).val() || false
					, strFilter		= $( this ).data( 'filter' );

				if( strValue ) {
					$.each( items, function( j, json ) {

						var strValues;
						strValues = json[strFilter].toString().toLowerCase();

						if( strValues.indexOf( strValue.toLowerCase() ) > -1 )
							arrResult.push( json );
					} );
					items			= arrResult;
				}
			} );
			return items;
		}

		, map_clear : function( marker_with )
		{
			var
				elements	= new Array( 'rectangle' );

			if( ! $( '.lava-my-position' ).hasClass( 'active' ) )
				elements.push( 'circle' );

			if( marker_with )
				elements.push( 'marker' );

			this.el.gmap3({ clear:{ name:elements } });
		}

		, bindMarker		: function( response  ) {

			var
				obj				= this
				, _opt			= { map:{} }
				, param			= obj.params
				, marker_icon	= $( "[key='marker_icon']", param ).val() || ''
				, item_markers	= new Array();

			obj.map_clear( true );

			if( response ) {
				$.each( response, function( i, item ){

					if( typeof item != "undefined" && item.lat != "" && item.lng != "" )
					{
						item_markers.push( {
							//latLng		: new google.maps.LatLng( item.lat, item.lng )
							lat			: item.lat
							, lng		: item.lng
							, options	: { icon: item.icon || marker_icon }
							, id		: "mid_" + item.post_id
							, data		: item
						} );
					}
				});
			}

			if( item_markers.length > 0 )
			{

				_opt = { marker:{ values:item_markers } };
				if( $( "[lava-cluster-onoff]" ).val() == "disable" ) {

					_opt.marker.cluster = {
						radius: parseInt( $("[lava-cluster-level]").val() ) || 100
						, 0:{ content:'<div class="lava-map-cluster admin-color-setting">CLUSTER_COUNT</div>', width:52, height:52 }
						, events:{
							click: function( c, e, d )
							{
								var $map = $(this).gmap3('get');
								var maxZoom = new google.maps.MaxZoomService();
								var c_bound = new google.maps.LatLngBounds();

								// IF Cluster Max Zoom ?
								maxZoom.getMaxZoomAtLatLng( d.data.latLng , function( response ){
									if( response.zoom <= $map.getZoom() && d.data.markers.length > 0 )
									{
										var str = '';

										str += "<ul class='list-group'>";

										str += "<li class='list-group-item disabled text-center'>";
											str += "<strong>";
												str += $("[lava-cluster-multiple]").val();
											str += "</strong>";
										str += "</li>";

										$.each( d.data.markers, function( i, k ){
											str += "<a onclick=\"window.lava_map_box_func.marker_trigger('" + k.id +"');\" ";
												str += "class='list-group-item'>";
												str += k.data.post_title;
											str += "</a>";
										});

										str += "</ul>";
										obj.infoWindo.setContent( str );
										obj.infoWindo.setPosition( c.main.getPosition() );
										obj.infoWindo.open( $map );

									}else{
										if( d.data.markers ) {
											$.each( d.data.markers, function( i, k ) {
												c_bound.extend( new google.maps.LatLng( k.lat, k.lng ) );
											} );
										}

										$map.fitBounds( c_bound );
										/*
										$map.setCenter( c.main.getPosition() );
										$map.setZoom( $map.getZoom() + 2 );
										*/
									}
								} ); // End Get Max Zoom
							} // End Click
						} // End Event
					} // End Cluster
				} // End If
			} // End if

			this.el.gmap3( _opt , "autofit" );

			return this;
		}

		, loadListing : function( data ) {
			var
				obj			= this
				, intLoop	= 0
				, param		= obj.param
				, element	= $( $( "[key='output']", param ).val() )
				, bufOutput	= ""
				, not_found	= $( $( "[key='output-not-found']", param ).val() ).html();

			$.ajaxSetup({
				beforeSend : function() {
					$( "*", obj.el_filter ).prop( 'disabled', true ).addClass( 'disabled' );
				}
				, complete : function() {
					$( "*", obj.el_filter ).prop( 'disabled', false ).removeClass( 'disabled' );
				}
			});

			if( data.length ) {
				$.each( data, function( i, json ) {
					$.post( obj.ajaxurl, { action:'lava_' + obj.prefix + '_map_list', post_ids: json.post_id },
						function( xhr ){
							bufOutput	+= obj.apply_listing( xhr[0] );

							intLoop++;

							if( data.length === intLoop )
								element.html( bufOutput );
						}
						, 'json'
					);
				} );
			}else{
				element.html( not_found );
			}

		}

		, apply_listing : function( data ) {
			var
				obj			= this
				, param		= obj.param
				, template	= $( $( "[key='output-template']", param ).val() ).html();

			template		= template.replace( /{post_id}/g		, data.post_id || '' );
			template		= template.replace( /{post_title}/g		, data.post_title || '' );
			template		= template.replace( /{post_author}/g	, data.author_name || '' );
			template		= template.replace( /{permalink}/g		, data.permalink || '' );

			template		= template.replace( /{category}/g		, data.category || '' );
			template		= template.replace( /{type}/g				, data.type || '' );
			template		= template.replace( /{thumbnail_url}/g		, data.thumbnail_url || '' );
			template		= template.replace( /{item-city}/g		, data.listing_location || '' );
			template		= template.replace( /{item-type}/g		, data.listing_category || '' );
			return template;
		}



	}

	$.lava_boxMap			= function( opt ) {
		new lava_boxMap( opt );
	}



})( jQuery, window );