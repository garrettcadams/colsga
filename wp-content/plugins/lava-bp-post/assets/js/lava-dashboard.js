( function( window, $, undef ) {
	"use strict";

	var lava_dir_dashboard_script = function( el ) {
		this.el = $( el );
		this.args = lava_dir_dashboard_args;
		this.init();
	}

	lava_dir_dashboard_script.prototype = {

		constructor : lava_dir_dashboard_script,

		init : function() {
			this.setEventBind();
		},

		setEventBind : function() {
			var obj = this;

			$( 'a[data-action="delete"]' ).on( 'click', function() {
				obj.actionTrash( $( this ).data( 'id' ) );
			} );
		},

		actionTrash : function( item_id ) {
			var
				form = $( '<form>' ),
				inputPostID = $( '<input>' ),
				inputSecurity = $( '<input>' ),
				inputAction = $( '<input>' ),
				post_id = item_id || 0;

			if( ! confirm( this.args.strings.strDeleteConfirm ) ) {
				return false;
			}

			form.prop( 'method', 'post' ).addClass( 'hidden' );
			inputPostID.prop({
				name : 'post_id',
				type : 'hidden',
				value : item_id
			});
			inputAction.prop({
				name : 'action',
				type : 'hidden',
				value : 'lava_bp_post_item_remove'
			});
			inputSecurity.prop({
				name : 'security',
				type : 'hidden',
				value : this.args.nonce
			});
			$([ inputPostID, inputAction, inputSecurity ]).map( function(){ $( this ).appendTo( form ) } );
			this.el.after( form );
			form.submit();
		}
	};

	$( '.lava-dir-shortcode-dashboard-wrap' ).each( function() {
		new lava_dir_dashboard_script( this );
	} );

} )( window, jQuery );

	/**
	var lava_bpp_mypage = function( el ) {

		this.el	= el;
		if( typeof this.instance === 'undefined' )
			this.init();
	}

	lava_bpp_mypage.prototype = {

		constructor : lava_bpp_mypage
		, init : function(){

			var obj			= this;
			obj.instance	= 1;

			$( document )
				.on( 'click', '[data-lava-bpp-trash]', obj.trash() );
		}

		, trash : function() {
			var obj = this;

			return function( e ) {
				e.preventDefault();

				var post_id		= $( this ).data( 'lava-bpp-trash' );

				if( confirm( strLavaTrashConfirm ) ) {BEAUTY
					obj.el.find( "[name='post_id']").val( post_id );
					obj.el.submit();
				}
			}
		}
	}
	new lava_bpp_mypage( $( "#lava-bpp-myapge-form" ) );
	*/