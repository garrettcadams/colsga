;( function( $ ){

	// Json Generator
	var lava_bpp_admin_settings = function () {
		this.param = lava_dir_admin_param;
		this.init();
	}

	lava_bpp_admin_settings.prototype = {

		constructor : lava_bpp_admin_settings,

		init : function() {
			$( document )
				.on( 'click', '.lava-data-refresh-trigger'		, this.onGenerator() )
				.on( 'click', '.fileupload'					, this.image_upload() )
				.on( 'click', '.fileuploadcancel'			, this.image_remove() )
		},

		onGenerator : function () {

			var
				obj = this,
				GENERAL_PER_SECTION = 30;

			return function ( e ) {

				var
					frm			= $( document ).find( "form#lava_common_item_refresh" ),
					langCode	= $( this ).data( 'lang' ) || null,
					items			= false,
					button_onoff = function( onoff ) {
						var button = $( ".lava-data-refresh-trigger" );
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
								}, { items: ids, renew: !refresh, lang: langCode },
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
				}, { lang: langCode } );
				button_onoff( false );
			}
		},

		percentage : function( current, max, visible, strMessage ) {
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
		},

		ajax : function( hookName, callback, args, failCallback ) {
			var
				obj = this,
				param = obj.param,
				args = $.extend( true, {}, { action: param.ajax_hook + hookName }, args );
			$.post( param.ajaxurl, args, callback, 'json' ).fail( function(){ if( typeof failCallback == 'funciton' ) failCallback();  } );
		},

		image_upload : function () {
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
		},

		image_remove : function () {
			return function ( e ) {
				var t = $(this).attr("tar");
				$("input[type='text'][tar='" + t + "']").val("");
				$("img[tar='" + t + "']").prop("src", "");
			}
		}
	}

	new lava_bpp_admin_settings;

})( window.jQuery );