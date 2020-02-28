;( function( $, window, undef ) {

	var lava_directory_manager_submit = function( el ) {
		this.args = lava_directory_manager_submit_args;
		this.el = $( el );
		this.form = this.el.find( '> form' );
		this.notice = this.el.find( '.notice' );
		this.init();
	}

	lava_directory_manager_submit.prototype.constructor = lava_directory_manager_submit;

	lava_directory_manager_submit.prototype.init = function() {
		var obj = this;

		if( obj.form ) {
			obj.form.on( 'submit', obj.submit() );
		}

		$( document )

			// Keyword Search
			.on( 'keyup keypress', '.lava-add-item-map-search', this.trigger_geokeyword )
			.on( 'click', '.lava-add-item-map-search-find', this.geolocation_keyword() )

			// Street View Setup
			.on( 'change', '.lava-add-item-set-streetview', this.street_setup() )
			.on( 'keyup', '[name="lava_location[lat]"], [name="lava_location[lng]"]', this.type_latLgn )
			.ready( function() { obj.setAmenities(); } );

		obj.template = '<div class="upload-item{featured}"> {img} <input type="hidden" name="{name}" value="{val}" /><button type="button" class="button item-remove">{remove_label}</button></div>';

		obj.setupSelectize();
		obj.map_setup();
		obj.fileUpload();
		obj.WpFileUpload();
	};

	lava_directory_manager_submit.prototype.message = function( str ) {
		var obj = this;
		obj.notice
			.css({
				'backgroundColor' : 'red',
				'color' : '#fff',
				padding : '15px'
			});

		obj.notice.removeClass( 'hidden' );
		obj.notice.html( str );
		$( document ).scrollTop( 0 );
	}

	lava_directory_manager_submit.prototype.allow_submit = function( form, onoff  ) {
		var button = $( form ).find( 'button[type="submit"]' );
		if( onoff ) {
			button.prop( 'disabled', false ).removeClass( 'disabled' );
		}else{
			button.prop( 'disabled', true ).addClass( 'disabled' );
		}
	}
	lava_directory_manager_submit.prototype.checkRequiredField = function() {
		var
			self = this,
			verify,
			amenities = $( '[name="lava_additem_terms[listing_amenities][]"]', self.form ),
			featured = $( '[name="featured_id"]', self.form );
			detailImages = $( '[name="lava_additem_meta[detail_images][]"]', self.form );

		if( amenities.length && self.args.required.amenities ) {
			verify = false;
			amenities.each( function() {
				if( $( this ).is( ':checked' ) ) {
					verify = true;
				}
			} );

			if( ! verify ) {
				self.message( self.args.strings.not_found_amenity );
				return false;
			}
		}

		if( ! featured.val() && self.args.required.featured_image ) {
			self.message( self.args.strings.not_found_featured );
			return false;
		}

		if( ! detailImages.length && self.args.required.detail_images ) {
			self.message( self.args.strings.not_found_detail_images );
			return false;
		}

		verify = true;
		$( '.lava-listing-wp-media.required' ).each( function() {
			if( ! $( 'input[type="hidden" ]' ).val() ) {
				self.message( self.args.strings.not_found + $( this ).data( 'label' ) );
				verify = false;
			}
		} );

		if( ! verify ){ return verify; }

		if( "0" === $( '[name="lava_location[lat]"]' ).val() && "0" === $( '[name="lava_location[lng]"]' ).val() && self.args.required.map ) {
			self.message( self.args.strings.not_found_map );
			return false;
		}
		return true;
	}

	lava_directory_manager_submit.prototype.submit = function() {
		var obj = this;

		return function (e) {
			e.preventDefault();

			var
				thisForm = this,
				arrFields = obj.form.serialize(),
				ajaxURL = obj.args.ajaxurl,
				strSuccess = obj.args.strings.success;

			if( ! obj.checkRequiredField() ) {
				return false;
			}

			obj.allow_submit( thisForm, false );

			$( window ).trigger( 'lava:before_submit' );

			obj.form.ajaxSubmit({

				dataType			: 'json',
				url					: ajaxURL,
				contentType		: 'application/json',
				type				: 'post',
				success			: function( xhr ) {
					if( xhr.err ) {
						obj.message( xhr.err );
						return;
					}
					alert( strSuccess );
					//window.onbeforeunload	= function(){};
					$( document ).trigger( 'lava:submit_new_post' );
					window.location.href = xhr.link;
				},
				error : function( xhr, state, error ){ console.log( xhr, error ); },
				complete : function(){
					obj.allow_submit( thisForm, true );
				}
			});

			return false;
		}
	}

	lava_directory_manager_submit.prototype.trigger_geokeyword = function( e ) {
		var keyCode		= e.keyCode || e.which;
		if( keyCode	== 13 ) {
			e.preventDefault();
			$('.lava-add-item-map-search-find').trigger('click');
			return false;
		}
	}

	lava_directory_manager_submit.prototype.geolocation_keyword = function() {
		var obj = this;
		return function() {
			obj.update_address( false, $('.lava-add-item-map-search').val() );
		}
	}

	lava_directory_manager_submit.prototype.setupSelectize = function() {
		var
			self = this,
			term_elements = $( 'select.lava-add-item-selectize', self.el );
		var termsCallback = function() {
				var
					opt = { plugins: new Array( 'remove_button' ) },
					current_taxonomy = $( this ).data( 'tax' );

				opt.create = 0 < parseInt( $( this ).data( 'create' ) );
				opt.maxItems = null;
				if( 0 < parseInt( $( this ).data( 'limit' ) ) ) {
					opt.maxItems = parseInt( $( this ).data( 'limit' ) || 1 );
				}
				$( this ).selectize( opt );
			}
		term_elements.each(termsCallback);
	}

	lava_directory_manager_submit.prototype.map_setup = function() {
		var obj = this;

		obj.elLat = $( 'input[name="lava_location[lat]"]', obj.form );
		obj.elLng = $( 'input[name="lava_location[lng]"]', obj.form );
		obj.elStreetLat = $( 'input[name="lava_location[street_lat]"]', obj.form );
		obj.elStreetLng = $( 'input[name="lava_location[street_lng]"]', obj.form );
		obj.elStreetHeading = $( 'input[name="lava_location[street_heading]"]', obj.form );
		obj.elStreetPitch = $( 'input[name="lava_location[street_pitch]"]', obj.form );
		obj.elStreetZoom = $( 'input[name="lava_location[street_zoom]"]', obj.form );
		obj.country = $( "[name='lava_location[country]']", obj.form );
		obj.locality = $( "[name='lava_location[locality]']", obj.form );
		obj.political = $( "[name='lava_location[political]']", obj.form );
		obj.political2 = $( "[name='lava_location[political2]']", obj.form );
		obj.zipcode = $( "[name='lava_location[zipcode]']", obj.form );
		obj.address = $( "[name='lava_location[address]']", obj.form );

		obj.current_location = new google.maps.LatLng( parseFloat( ( obj.elLat.val() || 0 ) ), parseFloat( ( obj.elLng.val() || 0 ) ) );
		obj.map_container = $( '.map_area', obj.form );

		if( obj.map_container.length <= 0 ) {
			return false;
		}

		obj.autocomplete_element = $( '.lava-add-item-map-search', obj.form );

		obj.map = new google.maps.Map(
			obj.map_container.get(0), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				center: obj.current_location,
				zoom: 14
			}
		);

		obj.marker = new google.maps.Marker({
			map : obj.map,
			position: obj.current_location,
			draggable: true
		});

		obj.geocoder = new google.maps.Geocoder();
		obj.service = new google.maps.places.PlacesService( obj.map );
		obj.map_container.height( 500 );
		obj.map_events_setup();
		obj.map_autocomplete_setup();
	}

	lava_directory_manager_submit.prototype.map_events_setup = function() {
		var obj = this;

		obj.map.addListener( 'click', function( param ) {
			obj.marker.setPosition( param.latLng );
			obj.elStreetLat.val( param.latLng.lat() );
			obj.elStreetLng.val( param.latLng.lng() );
			$( window ).trigger( 'lava:update_add_item_streetview' );
		} );

		obj.marker.addListener( 'position_changed', function() {
			obj.elLat.val( obj.marker.getPosition().lat() );
			obj.elLng.val( obj.marker.getPosition().lng() );
			obj.elStreetLat.val( obj.marker.getPosition().lat() );
			obj.elStreetLng.val( obj.marker.getPosition().lng() );
			obj.update_address( obj.marker.getPosition() );
			if( ! obj.marker_draging ) {
				$( window ).trigger( 'lava:update_add_item_streetview' );
			}
		} );

		obj.marker.addListener( 'dragstart', function() {
			obj.marker_draging = true;
		} );

		obj.marker.addListener( 'dragend', function() {
			obj.marker_draging = false;
			$( window ).trigger( 'lava:update_add_item_streetview' );
		} );
	}

	lava_directory_manager_submit.prototype.map_autocomplete_setup = function() {
		var obj = this;
		var autoCompleteSettings = {};
		var params = obj.args.maps || {};
		var countries = {
			'au': {
				center: {lat: -25.3, lng: 133.8},
				zoom: 4
			},
			'br': {
				center: {lat: -14.2, lng: -51.9},
				zoom: 3
			},
			'ca': {
				center: {lat: 62, lng: -110.0},
				zoom: 3
			},
			'fr': {
				center: {lat: 46.2, lng: 2.2},
				zoom: 5
			},
			'de': {
				center: {lat: 51.2, lng: 10.4},
				zoom: 5
			},
			'mx': {
				center: {lat: 23.6, lng: -102.5},
				zoom: 4
			},
			'nz': {
				center: {lat: -40.9, lng: 174.9},
				zoom: 5
			},
			'it': {
				center: {lat: 41.9, lng: 12.6},
				zoom: 5
			},
			'za': {
				center: {lat: -30.6, lng: 22.9},
				zoom: 5
			},
			'es': {
				center: {lat: 40.5, lng: -3.7},
				zoom: 5
			},
			'pt': {
				center: {lat: 39.4, lng: -8.2},
				zoom: 6
			},
			'us': {
				center: {lat: 37.1, lng: -95.7},
				zoom: 3
			},
			'uk': {
				center: {lat: 54.8, lng: -4.6},
				zoom: 5
			}
		};

		if(typeof params.restrictions_country != 'undefined' && params.restrictions_country != 'all') {
			autoCompleteSettings.types = new Array('(cities)');
			autoCompleteSettings.componentRestrictions = {'country' : params.restrictions_country };
			obj.map.setCenter(countries[params.restrictions_country].center);
			obj.map.setZoom(countries[params.restrictions_country].zoom);
		}

		obj.autocomplete = new google.maps.places.Autocomplete( obj.autocomplete_element.get(0), autoCompleteSettings );

		obj.autocomplete.addListener( 'place_changed', function() {

			var lava_place = obj.autocomplete.getPlace();

			if( typeof lava_place.geometry == 'undefined' )
				return false;

			if( lava_place.geometry.viewport){
				obj.map.fitBounds( lava_place.geometry.viewport );
			}else{
				obj.map.setCenter( lava_place.geometry.location );
			}
			if( obj.marker ) {
				obj.marker.setPosition( lava_place.geometry.location );
			}

		});
	}

	lava_directory_manager_submit.prototype.update_address = function( latlng, _address ) {
		var
			obj = this,
			query = { location: latlng };

		if( _address ) {
			query = { address: _address };
		}

		obj.geocoder.geocode(query, function( results, state ) {
			var result;
			if( state == google.maps.GeocoderStatus.OK ) {

				if( _address ) {
					obj.marker.setPosition( results[0].geometry.location );
					obj.map.setCenter( results[0].geometry.location );
				}

				obj.address.val(JSON.stringify(results[0].address_components));

				$.each( results[0].address_components, function( iResult, arrAddress ) {

					if( arrAddress.types[0] === 'country' ) {
						obj.country.val( arrAddress.long_name );
					}

					if( arrAddress.types[0] === 'locality' ) {
						obj.locality.val( arrAddress.long_name );
					}

					if( arrAddress.types[0] === 'postal_code' ) {
						obj.zipcode.val( arrAddress.long_name );
					}

					if( arrAddress.types[1] === 'sublocality' ){
						if( arrAddress.types[2] === 'sublocality_level_1' ){
							obj.political.val( arrAddress.long_name );
						}else if( arrAddress.types[2] === 'sublocality_level_2' ) {
							obj.political2.val( arrAddress.long_name );
						}
					}
				} );
			}
		} );
	}

	lava_directory_manager_submit.prototype.type_latLgn = function( e ) {
		var _this		= this;
		var obj			= window.lava_add_item_func;
		this.lat		= parseFloat( $('[name="lava_location[lat]"]').val() );
		this.lng		= parseFloat( $('[name="lava_location[lng]"]').val() );

		if( isNaN( this.lat ) || isNaN( this.lng ) ){ return; }

		this.latLng		= new google.maps.LatLng( this.lat, this.lng );

		obj.el.gmap3({
			get:{
				name: "marker"
				, callback: function( marker )
				{

					if( typeof window.nTimeID != "undefiend" ){
						clearInterval( window.nTimeID );
					};
					window.nTimeID = setInterval( function(){
						marker.setPosition( _this.latLng );
						obj.el.gmap3('get').setCenter( _this.latLng );
						clearInterval( window.nTimeID );
					}, 1000 );
				}
			}
		});
	}

	lava_directory_manager_submit.prototype.street_setup = function() {
		var obj = this;
		return function (e) {

			if( ! $( this ).is( ':checked' ) ) {
				$('.lava_map_advanced').addClass('hidden');
				$( '.map_area_streetview' ).hide();
				return false;
			}else{
				$( '.map_area_streetview' ).show();
			}

			var streetViewContainer = $( '.map_area_streetview' );

			// Use StreetView
			$('.lava_map_advanced').removeClass('hidden');

			// Set Height
			streetViewContainer.height(350);

			$( window ).on( 'lava:update_add_item_streetview', function() {
				var streetViewPosition = new google.maps.LatLng(
					parseFloat( obj.elStreetLat.val() ),
					parseFloat( obj.elStreetLng.val() )
				);

				obj.streetview = new google.maps.StreetViewPanorama(
					streetViewContainer.get(0), {
						position: streetViewPosition,
						pov:{
							heading: parseFloat( obj.elStreetHeading.val() || 0 ),
							pitch: parseFloat( obj.elStreetPitch.val() || 0 ),
							zoom: parseFloat( obj.elStreetZoom.val() || 0 )
						}
					}
				);

				obj.streetview.addListener( 'pov_changed', function() {
					obj.elStreetHeading.val( obj.streetview.pov.heading || 0);
					obj.elStreetPitch.val( obj.streetview.pov.pitch || 0 );
					obj.elStreetZoom.val( obj.streetview.pov.zoom || 0 );
				} );

				obj.streetview.addListener( 'position_changed', function() {
					obj.elStreetLat.val( obj.streetview.getPosition().lat() || 0 );
					obj.elStreetLng.val( obj.streetview.getPosition().lng() || 0 );
				} );

				obj.map.setStreetView( obj.streetview );
			} ).trigger( 'lava:update_add_item_streetview' );
		}
	}

	lava_directory_manager_submit.prototype.ajax = function( _hook, _param, _callback, _failCallback ) {
		var
			obj = this
			hook = obj.args.ajaxhook + _hook,
			param = _param || {};

		param.action = hook;
		return $.post( obj.args.ajaxurl, param, _callback, 'json' ).fail( _failCallback );
	}

	lava_directory_manager_submit.prototype.appendSelector = function( _target, callback ) {
		var element = $( '<input>' ).prop( {
			type : 'file',
			name : 'source',
			'class' : 'selector'
		} );

		_target.append( element );
		element.on( 'change', callback );
	}

	lava_directory_manager_submit.prototype.addUploadItem = function( _template, data ) {
		var
			obj = this,
			type = data.type || false,
			template = _template || '';

		if( data.type ) {
			template = template.replace( /{img}/g, data.output || '' );
		}else{
			template = template.replace( /{img}/g, data.img || '' );
		}
		template = template.replace( /{name}/g, data.name || '' );
		template = template.replace( /{val}/g, data.val || '' );
		template = template.replace( /{featured}/g, data.f || '' );
		template = template.replace( /{remove_label}/g, obj.args.strings.btn_remove || '' );
		return template;
	}

	lava_directory_manager_submit.prototype.fileUpload = function() {
		var
			obj = this,
			containers = obj.form.find( '.lava-upload-wrap' ),
			loading = $( '<img>' ).prop( {
				src : obj.args.images.loading,
				'class' : 'loading'
			}).css({ width:18, height:18, opacity:'0.8' } ),
			uploader = $( '<form>' ).prop( {
				'enctype' :  "multipart/form-data",
				'action' : 'post',
				'class' : 'uploader'
			} ).addClass( 'hidden' ),
			action = $( '<input>' ).prop({
				type : 'hidden',
				name : 'action',
				value : obj.args.ajaxhook + 'upload_file'
			});

		action.appendTo( uploader );
		uploader.appendTo( obj.el );

		$( document ).on( 'click', containers.find( 'button.item-remove' ).selector, function(){
			$( this ).closest( '.upload-item' ).remove();
		} );

		containers.each( function() {
			var
				container = $( this ),
				element = $( '.selector', container ),
				limit = $( this ).data( 'limit' ),
				fieldName = container.data( 'field' ),
				values = container.data( 'value' ),
				output = $( '.upload-item-group', container ),
				upload_callback = function() {

					if( ! obj.CheckLimitDetailImage( $( '.upload-item', output ).length, limit ) ) {
						return false;
					}

					container.append( loading.clone() );
					$( '.uploader .selector', obj.el ).remove();
					$( '.uploader', obj.el ).append( $( this ) );
					$( '.uploader', obj.el ).ajaxSubmit({
						type : 'post',
						dataType : 'json',
						url : obj.args.ajaxurl,
						contentType : 'application/json',
						complete : function() {},
						success : function( xhr ) {
							if( xhr.STATE == 'OK' ) {
								obj.ajax( 'get_attachment_info', { id:xhr.ID }, function( image ) {
									var _return = {};
									_return.name = fieldName;
									_return.img = image.output;
									_return.val = xhr.ID;
									output.append( obj.addUploadItem( obj.template, _return ) );
								} ).always( function(){
									$( '.loading', container ).remove();
									obj.appendSelector( container, upload_callback );
								} );
							}
						}
					} );
				};

			obj.appendSelector( container, upload_callback );
			$.each( values, function( intIndex, _return ) {
				_return.name = fieldName;
				output.append( obj.addUploadItem( obj.template, _return ) );
			} );
		});
	}

	lava_directory_manager_submit.prototype.CheckLimitDetailImage = function( lenItem, limit, c ) {
		var strLimit = this.args.strings.limitDetailImages;
		if( ( 0 < limit ) && ( ( c ? limit <= lenItem : limit < lenItem ) ) ) {
			strLimit = strLimit.replace( /{limit}/g, limit || 0 );
			strLimit = strLimit.replace( /{count}/g, lenItem || 0 );
			alert( strLimit );
			return false;
		}
		return true;
	}

	lava_directory_manager_submit.prototype.WpFileUpload = function() {
		var
			obj = this,
			wndWpMedia,
			elements = obj.el.find( '.lava-listing-wp-media' );
		$( window ).on( 'lava:wpLibrarySingleImageUpload', function( event, preview ) {
			$( preview ).css({ width: 150, height: 150 } );
		} );

		elements.each( function() {
			var
				singleField,
				limit = $( this ).data( 'limit' ) || 0,
				selCount = 0,
				output = $( '.upload-item-group', this ),
				preview = $( '.upload-preview', this ),
				element = $( this ),
				_options = element.data(),
				values = _options.value,
				isMultipleUpload = _options.multiple || false;

			_options.element = element;

			if( element.hasClass( 'has-instance' ) ) {
				return true;
			}

			element.addClass( 'has-instance' );

			if( ! isMultipleUpload ) {
				singleField = $( 'input[name="' + _options.field + '"]' );
				preview.css({
					position: 'relative',
					'background-size': 'cover',
					'background-repeat': 'no-repeat'
				});

				if( singleField.val() ) {
					$( window ).trigger( 'lava:wpLibrarySingleImageUpload', preview );
				}
			}else{
				$('input[data-media-input]', element).remove();
				$( values ).each( function( intIndex, _data ) {
					_data.name = _options.field;
					output.append( obj.addUploadItem( obj.template, _data ) );
				} );
			}

			$( '.action-add-item', element ).on( 'click', function() {
				if( undef === wp.media ) {
					obj.message( "WP media load fail" );
					return false;
				}
				wndWpMedia = wp.media.frames.file_frame = wp.media({ title : _options.modalTitle || 'Upload', multiple : isMultipleUpload });
				wndWpMedia.on( 'select', function() {
					if( ! obj.CheckLimitDetailImage( wndWpMedia.state().get( 'selection' ).length, limit ) ) {
						return false;
					}

					if( isMultipleUpload ) {
						wndWpMedia.state().get('selection').map( obj.wpUploadMultipleAction( _options, output ) );
					}else{
						obj.wpUploadSingleAction( wndWpMedia.state().get('selection').first().toJSON(), _options, preview );
					}
				} );

				if( obj.CheckLimitDetailImage( $( '.upload-item', output ).length, limit, true ) ) {
					wndWpMedia.open();
				}

			} );

			$( document ).on( 'click', output.find( '.item-remove' ).selector, function( e ) {
				$( this ).closest( '.upload-item' ).remove();
			} );

			$( '.item-clear', element ).on( 'click', function( e ) {
				preview.css( 'background-image', 'none' );
				$( 'input[name="' + _options.field + '"]', element ).val( '' );
				$( '[data-media-input]', element ).val( '' );
			} );

		} );
	}

	lava_directory_manager_submit.prototype.wpUploadSingleAction = function( data, opt, element ) {
		var obj = this;
		$( 'input[name="' + opt.field + '"]' ).val( data.id );
		$( '[data-media-input]', opt.element ).val( data.id ).data( 'background-url', data.url );
		element.css( 'background-image', 'url(' + data.url + ')' );
		$( window ).trigger( 'lava:wpLibrarySingleImageUpload', [element] );
	}

	lava_directory_manager_submit.prototype.wpUploadMultipleAction = function( opt, element ) {
		var
			obj = this,
			values = opt.value;
		return function( data ) {
			var
				_data = {},
				item = data.toJSON(),
				template = obj.template;

			if( opt.attachment == true ) {
				_data.type = item.type;
				_data.output = '<a href="' + item.url + '" target="_blank">' + item.filename;
				_data.output += ' ( ' + obj.args.strings.download + ' ) ';
				_data.output += '</a>';
			}

			_data.name = opt.field;
			_data.val = item.id;
			_data.img = '<img src="' + item.url + '" style="width:150px; height:150px;">';
			element.append( obj.addUploadItem( obj.template, _data ) );
			$( window ).trigger( 'lava:wpLibraryMultipleImageUpload', [element] );
		}
	}

	lava_directory_manager_submit.prototype.createAmenitiesField = function( el ) {
		var template = '<div class="amenties-field no-effect">';
		template += '<label class="amenities-title"></label>';
		template += '<div class="output"></div>';
		template += '</div>';
		el.after( template );
	}

	lava_directory_manager_submit.prototype.setAmenitiesField = function( el, values ) {
		var
			obj = this, output, fTitle,
			post_id = obj.args.post_id || 0,
			AJAX_PROGRESS = 'ajax-process',
			is_processing = el.hasClass( AJAX_PROGRESS ),
			amenties_field = $( '.amenties-field' ),
			hasAmenitiesField = amenties_field.length > 0;

		if( is_processing ) {
			return false;
		}

		if( ! hasAmenitiesField ) {
			obj.createAmenitiesField( el );
			amenties_field = $( '.amenties-field' );
		}

		output = $( '.output', amenties_field );
		fTitle = $( '.amenities-title', amenties_field );

		el.addClass( AJAX_PROGRESS );
		amenties_field.addClass( AJAX_PROGRESS );

		obj.ajax( 'get_amenities_fields', { terms: values, object_id: post_id }, function( data ) {

			var buff = '';
			el.removeClass( AJAX_PROGRESS );
			fTitle.empty();
			amenties_field.removeClass( AJAX_PROGRESS );

			if( 0 < data.output.length ) {
				fTitle.html( data.title );
				$.each( data.output, function( intDataID, MetaData ) {
					var template = '<label><input type="checkbox" name="lava_additem_terms[listing_amenities][]" value="{val}"{checked}> {label}</label> ';
					template = template.replace( /{val}/g, MetaData.term_id || 0 );
					template = template.replace( /{label}/g, MetaData.name || '' );
					template = template.replace( /{checked}/g, ( MetaData.checked ? ' checked="checked"' : '' ) );
					buff += template;
				} );
				output.html( buff );
			}else {
				output.html( data.empty );
			}
		} );
	}

	lava_directory_manager_submit.prototype.setAmenities = function() {
		var
			obj = this
		$( '[data-tax="listing_category"]' ).on( 'change', function() {
			var selectize = $( this ).get(0).selectize;
			$( this ).blur();
			obj.setAmenitiesField( $( this ).closest( '.form-inner' ), $( this ).val() );
		} ).trigger( 'change' );
	}

	var callback = function() {
		$( '.lava-item-add-form' ).each( function() {
			if( ! $( this ).data( 'lava-submit' ) ) {
				$( this ).data( 'lava-submit', new lava_directory_manager_submit( this ) );
			}
		} );
	}
	$( window ).on( 'load', callback );
	callback();

} )( jQuery, window );