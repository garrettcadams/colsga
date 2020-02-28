( function( $ ) {

	var lava_adminAddons = function()
	{
		if( ! $.__lava_admin_addons )
			this.init();
	}

	lava_adminAddons.prototype = {

		constrcutor: lava_adminAddons

		, init : function()
		{
			var
				obj = this;

			obj.args = lavaAddonsVariable;

			$__lava_admin_addons = true;

			obj.update_check();

			$( document )
				.on( 'click' , '.lava-addon-input-license', obj.input_license() )
				.on( 'click' , '.lava-addon-deactive-license', obj.deactive_license() );
		}

		, input_license : function()
		{

			var
				obj				= this;

			return function( e ) {
				e.preventDefault();

				var
					parent			= $( this ).closest( '.lava-addons-license-field' )
					, txtLicense	= $( "input[type='text']", parent )
					, txtEmail		= $( "input[type='email']", parent )
					, addon			= $( this ).data( 'slug' );

				if( !txtEmail.val() ) {
					alert( lavaAddonsVariable.strEmailEmpty );
					txtEmail.focus();
					return false;
				}

				if( !txtLicense.val() ) {
					alert( lavaAddonsVariable.strLicenseEmpty );
					txtLicense.focus();
					return false;
				}

				obj.enable( txtLicense, false );
				obj.enable( txtEmail, false );

				$.post(
					lavaAddonsVariable.ajaxurl,
					{
						action			: 'lava' + '_' + lavaAddonsVariable.post_type + '_register_licensekey'
						, 'addon'		: addon
						, email			: txtEmail.val()
						, license_key	: txtLicense.val()
					},
					function( xhr ) {

						if( 'OK' === xhr.state ) {
							document.location.reload();
							return;
						}else{
							alert( lavaAddonsVariable.strLicenseRegErr );
						}
						obj.enable( txtEmail );
						obj.enable( txtLicense );
					},
					'json'
				)
				.fail( function( xhr ) {
					alert( lavaAddonsVariable.strLicenseRegErr );
					obj.enable( txtEmail );
					obj.enable( txtLicense );
				} );
			}
		}

		, deactive_license : function( control, onoff )
		{
			return function( e ) {
				e.preventDefault();

				var addon = $( this ).data( 'slug' );

				$.post(
					lavaAddonsVariable.ajaxurl,
					{
						action			: 'lava' + '_' + lavaAddonsVariable.post_type + '_deactive_licensekey'
						, 'addon'		: addon
					},
					function( xhr ) {
						document.location.reload();
					},
					'json'
				);
			}
		}
		, enable : function( control, onoff )
		{
			var onoff = typeof onoff == 'undefined' ? true : onoff;

			control.removeClass( 'disabled' );
			control.prop( 'disabled', false );

			if( !onoff ) {
				control.addClass( 'disabled' );
				control.prop( 'disabled', 'disabled' );
			}
		},

		update_check : function() {

			var
				obj = this,
				args = obj.args;

			$( document ).on( 'click', '.lava-addon-update-check', function(){

				var thisButton = $( this );

				obj.enable( thisButton, false );
				$( this ).closest( 'p' ).find( '.spinner' ).addClass( 'is-active' );

				$.post(
					args.ajaxurl,
					{
						action			: 'lava' + '_' + lavaAddonsVariable.post_type + '_update_check'
					},
					function( xhr ) {
						document.location.reload();
					},
					'json'
				)
				.fail( function() {
					obj.enable( thisButton, true );
				} );
			} );

		}
	}

	new lava_adminAddons;
} )( jQuery );