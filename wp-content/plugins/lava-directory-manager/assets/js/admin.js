;( function( $ ){

	// Json Generator
	var directoryManagerAdmin = function () {
		this.param = lava_dir_admin_param;
		this.init();
	}

	directoryManagerAdmin.prototype.constructor = directoryManagerAdmin;
	directoryManagerAdmin.prototype.init = function() {
		var self = this;
		$( document )
			.on( 'click', '.lava-data-refresh-trigger', this.onGenerator() )
			.on( 'click', '.fileupload', this.image_upload() )
			.on( 'click', '.fileuploadcancel', this.image_remove() )

		$('button[data-listing-type-generator]').each(function(){
			var listingType = $(this).data('listing-type-generator') || false;
			var Instance = self.onGenerator(listingType);
			$(this).on('click', Instance);
		});
		self.bindJsonEditor();
	}
	directoryManagerAdmin.prototype.onGenerator = function(type) {
		var obj = this;
		var GENERAL_PER_SECTION = 30;
		var listingType = type || false;

		return function ( e ) {

			var
				frm			= $( document ).find( "form#lava_common_item_refresh" ),
				langCode	= $( this ).data( 'lang' ) || null,
				items			= false,
				button_onoff = function( onoff ) {
					var button = $(this);
					if( onoff ) {
						button.removeClass( 'disabled' );
					}else{
						button.addClass( 'disabled' );
					}
					button.prop( 'disabled', !onoff );
				};

			obj.percentage( 0, 1 );
			obj.ajax( 'get_listings_count', function( counter_state ){
				var
					items = counter_state.result || [],
					total = items.length,
					send = function( _current, _renew  ) {
						var
							ids = [],
							current = _current || 0,
							refresh = _renew || false,
							loopMax = current + GENERAL_PER_SECTION;

						if( current < total ) {
							for( var i = current; i < loopMax; i++ ) {
								if( i < total ){
									ids.push( items[i] );
								}
							}
							obj.ajax( 'json_writer', function( writer_state ){
								if( writer_state.result ){
									current = current + ids.length;
									obj.percentage( current, total );
									send( current, true );
								}
							}, { items: ids, renew: !refresh, lang: langCode, type: listingType },
							function(){
								send( current, true );
							});
						}else{
							obj.percentage( 1, 1, true, 'Completed' );
							button_onoff( true );
						}
						return false;
				};
				obj.percentage( 0, 1, true, 'Find : ' + total );
				send();
			}, { lang: langCode, type: listingType } );
			button_onoff( false );
		}
	}
	directoryManagerAdmin.prototype.percentage = function(current, max, visible, strMessage) {
		var
			visibility = typeof visible == 'undefined' ? true : visible,
			progWrap = $( "#lava-setting-page-progressbar-wrap" ),
			progBar = $( '.progressbar', progWrap ),
			status = $( '.text', progWrap ),
			cur = parseInt( current / max * 100 ),
			text = cur + '%';
		if( visibility ) {
			progWrap.addClass( 'active' );
		}else{
			progWrap.removeClass( 'active' );
		}
		progBar.css( 'width', text );
		status.html( text );
		if( typeof strMessage == 'string' )
			status.html( strMessage );
	}
	directoryManagerAdmin.prototype.ajax = function(hookName, callback, args, failCallback) {
		var
				obj = this,
				param = obj.param,
				args = $.extend( true, {}, { action: param.ajax_hook + hookName }, args );
			$.post( param.ajaxurl, args, callback, 'json' ).fail( function(){ if( typeof failCallback == 'funciton' ) failCallback();  } );
	}
	directoryManagerAdmin.prototype.image_upload = function() {
		var file_frame;
		return function ( e, undef ) {
			e.preventDefault();

			var
				attahment,
				output_image,
				t				= $( this ).attr( 'tar' ),
				bxTitle		= $( this ).data( 'uploader_title' ) || "Upload",
				bxOK			= $( this ).data( 'uploader_button_text' ) || "Apply",
				bxMultiple	= false;

			if( file_frame ){
				file_frame.open();
				return;
			}

			file_frame = wp.media.frames.file_frame = wp.media({
				title				: bxTitle,
				button			: { text : bxOK },
				multiple			: bxMultiple
			});

			file_frame.on( 'select', function(){
				attachment			= file_frame.state().get('selection').first().toJSON();
				output_image		= attachment.url;

				if( attachment.sizes.thumbnail !== undef )
					output_image	= attachment.sizes.thumbnail.url;

				$("input[type='text'][tar='" + t + "']").val(attachment.url);
				$("img[tar='" + t + "']").prop("src", output_image );
			});
			file_frame.open();
		}
	}
	directoryManagerAdmin.prototype.image_remove = function() {
		return function ( e ) {
			var t = $(this).attr("tar");
			$("input[type='text'][tar='" + t + "']").val("");
			$("img[tar='" + t + "']").prop("src", "");
		}
	}
	directoryManagerAdmin.prototype.bindJsonEditor = function() {
		var editor = $('#jsoneditor');
		var editor_field = $('input[name="lava_directory_manager_settings_schema[schema_template]"]');
		var editorValue = editor_field.val();
		if(!editor.length) {
			return;
		}

		var options = {
			mode: 'tree',
			modes: ['code', 'form', 'text', 'tree', 'view', 'preview'], // allowed modes
			onError: function (err) {
				alert(err.toString())
			},
			onModeChange: function (newMode, oldMode) {
				console.log('Mode switched from', oldMode, 'to', newMode)
			}
		}

		var json = {
			"@context": "http://www.schema.org",
			"@type": "LocalBusiness",
			"@id": "[[:url]]",
			"name": "[[title]]",
			"legalName": "[[title]]",
			"description": "[[description]]",
			"logo": "[[logo]]",
			"url": "[[:url]]",
			"telephone": "[[phone]]",
			"email": "[[email]]",
			"priceRange": "[[price_range]]",
			"openingHours": "[[work_hours]]",
			"photo": "[[cover]]",
			"image": "[[cover]]",
			"photos": "[[gallery]]",
			"hasMap": "https://www.google.com/maps/@[[:lat]],[[:lng]]z",
			"sameAs": "[[links]]",
			"address": "[[location]]",
			"contactPoint": {
				"@type": "ContactPoint",
				"contactType": "customer support",
				"telephone": "[[phone]]",
				"email": "[[email]]"
			},
			"geo": {
				"@type": "GeoCoordinates",
				"latitude": "[[:lat]]",
				"longitude": "[[:lng]]"
			},
			"aggregateRating": {
				"@type": "AggregateRating",
				"ratingValue": "[[:reviews-average]]",
				"reviewCount": "[[:reviews-count]]",
				"bestRating": "[[:reviews-mode]]",
				"worstRating": 0
			}
		}
		if(editorValue) {
			json = JSON.parse(editorValue);
		}
		editor.data('json-editor', new JSONEditor(editor[0], options, json));
		editor.closest('form').on('submit', function(event){
			var schemaEditContent = editor.data('json-editor').get();
			editor_field.val(JSON.stringify(schemaEditContent));
		});
	}
	window.ldmAdmin = new directoryManagerAdmin;
})( window.jQuery );