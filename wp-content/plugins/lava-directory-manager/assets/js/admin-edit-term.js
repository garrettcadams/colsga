( function( $ ){

	var lava_directory_edit_term = function( el ) {
		this.param = lv_edit_featured_taxonomy_variables || {};
		this.el = $( el );
		this.args = this.el.data( 'args' );
		this.init();
	}

	lava_directory_edit_term.prototype = {

		constructor : lava_directory_edit_term,

		init : function() {
			var self = this;

			self.bindPreview();
			self.setUploadHandle();
		},

		bindPreview : function() {
			var
				self = this,
				wrap = $( '.preview-wrap', self.el ),
				imageSrc = wrap.data( 'image' ) || false,
				preview = $( 'img.preview-upload', wrap );

			if( false === imageSrc ) {
				return false;
			}

			imageSrc = imageSrc.replace( ' ', '' );

			if( !imageSrc ) {
				preview.remove();
				return false;
			}

			if( ! preview.length ) {
				preview = $( '<img>' ).addClass( 'preview-upload' ).appendTo( wrap );
			}

			preview.prop( 'src', imageSrc ).css({
				'height' : 'auto',
				'margin' : '15px 0 0',
				'max-width' : '300px'
			});

		},

		setUploadHandle : function() {
			var
				self = this,
				input = $( 'input[type="hidden"]', self.el );

			if( ! self.el.data( 'modal' ) ) {
				var instance = self.getUploadInstance( function( instance ) {
					return function() {
						var response = instance.state().get( 'selection' ).first().toJSON();
						$( '.preview-wrap', self.el ).data( 'image', response.url );
						self.bindPreview();
						if( input.data( 'type' ) == 'url' ) {
							input.val( response.url );
						}else{
							input.val( response.id );
						}
					}
				} );
				self.el.data( 'modal', instance );
			}

			$( '.upload', self.el ).on( 'click', function() {
				self.el.data( 'modal' ).open();
			} );

			$( '.remove', self.el ).on( 'click', function() {
				input.val( '' );
				$( '.preview-wrap', self.el ).data( 'image', ' ' );
				self.bindPreview();
			} );
		},

		getUploadInstance : function( _callback ) {
			var
				self = this,
				instance = wp.media.frames.file_frame = wp.media({
					title : self.args.title,
					multiple : false,
					button : {
						text : self.args.select
					}
				});
			instance.on( 'select', _callback( instance ) );
			return instance;
		}
	};

	$( '.lava-edit-term-wp-upload' ).each( function() {
		new lava_directory_edit_term( this );
	} );

} )( jQuery );