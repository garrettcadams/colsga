( function( $ ){

	var lava_bpp_edit_term = function() {
		this.param = lv_edit_featured_taxonomy_variables || {};
		this.init();
	}

	lava_bpp_edit_term.prototype = {

		constructor : lava_bpp_edit_term,

		init : function() {
			this.bindUploadEvent();


		},

		bindUploadEvent : function() {
			var
				obj = this,
				wrap = $( 'div.lava-edit-term-wp-upload' );

			obj.uploadHandle = new Array();
			wrap.each( function( intElementKey, objElement ) {
				obj.setUploadHandle( this, intElementKey );
			} );
		},

		setUploadHandle : function( _element, _key ) {
			var
				obj = this,
				element = $( _element ),
				input = $( 'input[type="hidden"]', element ),
				preview = $( 'img.preview-upload', element );

			preview.width( 300 ).height( 'auto' );

			if( ! obj.uploadHandle[ _key ] ) {
				obj.uploadHandle[_key] = obj.getUploadInstance( function( instance ) {
					return function() {
						var response = instance.state().get( 'selection' ).first().toJSON();
						preview.prop( 'src', response.url );
						if( input.data( 'type' ) == 'url' ) {
							input.val( response.url );
						}else{
							input.val( response.id );
						}
					}
				} );
			}

			$( '.upload', element ).on( 'click', function() {
				obj.uploadHandle[_key].open();
			} );

			$( '.remove', element ).on( 'click', function() {
				input.val( '' );
				preview.prop( 'src', '' );
			} );
		},

		getUploadInstance : function( _callback ) {
			var
				obj = this,
				instance = wp.media.frames.file_frame = wp.media({
					title : obj.param.mediaBox_title,
					multiple : false,
					button : {
						text : obj.param.mediaBox_select
					}
				});

			instance.on( 'select', _callback( instance ) );
			return instance;
		}
	};

	new lava_bpp_edit_term;


	/**
	// Edit Taxonomy Uploader
	if( typeof lv_edit_featured_taxonomy_variables != 'undefined' ) {
		var
			uploader		=  false
			, params		= lv_edit_featured_taxonomy_variables || {};

		; $( document )
			.on("click", ".fileupload", function( e ){
				e.preventDefault();

				var
					target			= $( this ).data( 'target' )
					, input			= $( this ).data( 'featured-field' )
					, parent		= $( this).closest( '.form-field' )
					, srcOutput	= $( this ).data( 'result-src' );

				srcOutput		= typeof srcOutput != 'undefined';

				if( ! uploader ){
					uploader = wp.media.frames.file_frame = wp.media({
						title			: params.mediaBox_title
						, multiple	: false
						, button	: {
							text		: params.mediaBox_select
						}
					});
				}
				uploader.off( 'select' ).on( 'select', function(){
					var response	= uploader.state().get( 'selection' ).first().toJSON();

					$( target ).val( response.url );
					$( input ).val( response.id );

					if( srcOutput )
						$( input ).val( response.url );

					$( 'img', parent ).prop( 'src', response.url );

				}).open();
				return;
			})
			.on("click", ".fileupload-remove", function(){
				var
					container		= $( this ).closest( '.form-field' ),
					sender			= $( "input[type='hidden']", container ),
					previewer		= $( "img", container );

				sender.val( null );
				previewer.prop( 'src', '' );
			});
	}
	**/
} )( jQuery );