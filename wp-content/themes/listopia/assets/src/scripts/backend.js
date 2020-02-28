/**
 *
 * Backend Script
 * @author javo Themes
 * @since 1.0.0
 * @description Backend(Admin Pages) Scripts
 *
 */
;( function( $, window, undef ) {
	"use strict";

	var jvbpd_admin_script = function() {
		if( typeof jvbpd_wizard_param != 'undefined' ) {
			this.args = jvbpd_wizard_param;
			this.wizard_plugin();
		}
	}

	jvbpd_admin_script.prototype.constructor = jvbpd_admin_script;
	jvbpd_admin_script.prototype.wizard_plugin = function() {

		var
			self = this;



		function PluginManager(){
			var complete;
			var items_completed = 0;
			var current_item = '';
			var $current_node;
			var current_item_hash = '';

			function ajax_callback(response){
				if(typeof response == 'object' && typeof response.message != 'undefined'){
					$current_node.find('span').text(response.message);
					if(typeof response.url != 'undefined'){
						// we have an ajax url action to perform.

						if(response.hash == current_item_hash){
							$current_node.find('span').text("failed");
							find_next();
						}else {
							current_item_hash = response.hash;
							jQuery.post(response.url, response, function(response2) {
								process_current();
								$current_node.find('span').text(response.message + self.args.verify_text);
							}).fail(ajax_callback);
						}

					}else if(typeof response.done != 'undefined'){
						// finished processing this plugin, move onto next
						find_next();
					}else{
						// error processing this plugin
						find_next();
					}
				}else{
					// error - try again with next plugin
					$current_node.find('span').text("done");
					find_next();
				}
			}
			function process_current(){
				if(current_item){
					// query our ajax handler to get the ajax to send to TGM
					// if we don't get a reply we can assume everything worked and continue onto the next one.
					jQuery.post(self.args.ajaxurl, {
						action: $( '#jvbpd-wizard-plugins' ).data( 'type' ) == 'addons' ? 'jvbpd_wizard_addons' : 'jvbpd_wizard_plugins',
						wpnonce: self.args.wpnonce,
						slug: current_item
					}, ajax_callback).fail(ajax_callback);
				}
			}
			function find_next(){
				var do_next = false;
				if($current_node){
					if(!$current_node.data('done_item')){
						items_completed++;
						$current_node.data('done_item',1);
					}
					$current_node.find('.spinner').css('visibility','hidden');
				}
				var $li = $('.envato-wizard-plugins li');
				$li.each(function(){
					if(current_item == '' || do_next){
						current_item = $(this).data('slug');
						$current_node = $(this);
						process_current();
						do_next = false;
					}else if($(this).data('slug') == current_item){
						do_next = true;
					}
				});
				if(items_completed >= $li.length){
					complete();
				}
			}

			return {
				init: function(btn){
					$('.envato-wizard-plugins').addClass('installing');
					complete = function(){
						// loading_content();
						window.location.href=btn.href;
					};
					find_next();
				}
			}
		}

		$( document ).ready( function() {
			$( '#jvbpd-wizard-plugins' ).on( 'click', function( e ) {
				e.preventDefault();
				var paInstance = new PluginManager;
				paInstance.init( this );
			} );
		} );
	}

	new jvbpd_admin_script;


	/**
	 *	Widget Ajax
	 *
	 */
	var jvbpd_dtl_func_instanct = function(){

		var elements = $( "[data-javo-dtl-el]" );
		elements.each( function( i, k )
		{
			var
				element_selector	= $( this ).data( "javo-dtl-el" )
				, target					= $( this ).find( $( this ).data( 'javo-dtl-tar' ) )
				, value					= $( this ).data( 'javo-dtl-val' );

			$( document )
				.off( 'change', element_selector )
				.on( 'change', element_selector, function( e ) {

					target.slideUp( 'fast' );
					if( $( this ).is( ":checked" ) && $( this ).val() == value ) {
						target.slideDown( 'fast' );
					}
				} )
				.find( element_selector ).trigger( 'change' );
		} );
	};
	jvbpd_dtl_func_instanct();
	$.ajaxSetup({ complete:function(){ jvbpd_dtl_func_instanct(); } });

	/**
	 *	Widget Ajax Complete after colorpicker set
	 *
	 */
	var jv_wgColorPicker = function(){
		if( $.__jvwgColorPicker__ ){
			return;
		}

		if(typeof $.fn.wpColorPicker != 'function') {
			return;
		}

		$.__jvwgColorPicker__ = true;

		$( '#wpbody', document ).ajaxComplete(
			function(){
				$( '.wp_color_picker' ).each(
					function( index, element ) {
						$( this ).wpColorPicker();
						$( this ).parent().find( '.wp-color-result' ).trigger( 'clock' );
					}
				);
			}
		);
	}
	jv_wgColorPicker();

	var jvbpd_backend_uploader = function( el ) {
		this.el = $( el );
		this.handle = false;
		this.createUploadHandle();
		this.setElements();
		this.bindEvents();
	}

	jvbpd_backend_uploader.prototype.setElements = function() {
		this.BTN_UPLOAD = $( '.upload', this.el );
		this.BTN_REMOVE = $( '.remove', this.el );
		this.INPUT = $( 'input', this.el );
		this.PREVIEW = $( '.preview-container', this.el );
		this.PREVIEW.css({
			'postion' : 'relative',
			'width' : '300',
			'height' : '250',
			'background-size' : 'cover',
			'background-repeat' : 'no-repeat',
		});
	}

	jvbpd_backend_uploader.prototype.createUploadHandle = function() {
		var handle_param = {
			multiple : false,
			title : this.el.data( 'handle-title' ) || 'Title',
			button : {
				text : this.el.data( 'select-button' ) || 'Select',
			}
		};
		this.handle = wp.media.frames.file_frame = wp.media( handle_param );
	}

	jvbpd_backend_uploader.prototype.bindEvents = function() {
		this.BTN_UPLOAD.on( 'click', this.uploadHandleShow() );
		this.BTN_REMOVE.on( 'click', this.remove() );
		this.handle.on( 'select', this.uploaded() );
	}

	jvbpd_backend_uploader.prototype.uploadHandleShow = function() {
		var self = this;
		return function() {
			self.handle.open();
		}
	};

	jvbpd_backend_uploader.prototype.uploaded = function() {
		var self = this;
		return function() {
			var response = self.handle.state().get( 'selection' ).first().toJSON();
			self.INPUT.val( response.id );
			self.PREVIEW.css( 'background-image', 'url(' + response.url + ')' );
		}
	};

	jvbpd_backend_uploader.prototype.remove = function() {
		var self = this;
		return function() {
			self.INPUT.val( '' );
			self.PREVIEW.css( 'background-image', 'none' );
		}
	};

	$( '.jv-media-uploader-hepler' ).each( function() {
		new jvbpd_backend_uploader( this );
	} );




	/*
	 *	Theme Settings : Tabs
	 *
	 */

	if( typeof jvbpd_ts_variable != 'undefined' ) {

		var jv_theme_settings_func = function() {
			this.args = jvbpd_ts_variable;
			this.init();
		};

		jv_theme_settings_func.prototype = {

			constructor : jv_theme_settings_func,
			init : function() {

				var obj = this;

				obj.container = $( 'form#jvbpd_ts_form' );
				obj.saveButton();
				obj.swapTab( 'general' );

				$( 'a.javo-opts-group-tab-link-a', this.container ).on( 'click', function( event ){
					event.preventDefault();
					obj.swapTab( $( this ).attr( 'tar' ) );
				} );
			},

			saveButton : function() {

				var
					obj = this,
					form = obj.container,
					exportField = $( ".jv-export-textarea", form ),
					btnSave = $( '.jvbpd_btn_ts_save', form ),
					args = obj.args;

				btnSave.on('click', function() {
					form.addClass( 'disabled process' );

					$.post(
						args.ajaxurl,
						form.serialize(),
						function( response ){

							form.removeClass( 'process' );

							if( response.state == 'OK' ){
								exportField.val( response.code );
								form.addClass( 'saved' );
							}else{
								form.addClass( 'failed' );
							}
						},
						'json'
					)
					.fail( obj.saveError );
					obj.cleanWindow( form );
				} );
				return this;
			},

			saveError : function( e ) {
				console.log(  e );
			},

			cleanWindow : function( form ) {
				var nTimeID = setInterval( function(){
					form
						.removeClass( 'disabled' )
						.removeClass( 'process' )
						.removeClass( 'saved' )
						.removeClass( 'failed' );
					clearInterval( nTimeID );
				}, 5000 );
			},

			swapTab : function( tabSlug ){
				var
					container = this.container,
					wrap = $( '#javo-opts-main', container ),
					tabs = $( '#javo-opts-group-menu', container ),
					tabs_contents = $( '.jvbpd_ts_tab', container );

				$( 'li', tabs ).removeClass( 'active' );
				$( 'a[tar="' + tabSlug + '"]', container ).closest( 'li' ).addClass( 'active' );
				tabs_contents.addClass( 'hidden' );

				$( ".jvbpd_ts_tab[tar='" + tabSlug + "']" ).removeClass( 'hidden' );
			}
		}
		new jv_theme_settings_func;


		/*
		 *	Theme Settings : Import/Export/Reset
		 *
		 */
		$("body").on("click", ".javo-btn-ts-reset", function(){
			if(!confirm( jvbpd_ts_variable.strReset )) return false;
			$("#javo-ts-admin-field").val('');

			if( $(this).hasClass('default') ){
				$('#javo-ts-admin-field').val( $('[data-javo-ts-default-value]').val() );
			}; // Set Default ThemeSettings Values.

			$("form#javo-ts-admin-form").submit();
		}).on("click", ".javo-btn-ts-import", function(){
			if( $('.javo-ts-import-field').val() == "") return false;
			if(!confirm( jvbpd_ts_variable.strImport )) return false;
			$("#javo-ts-admin-field").val( $('.javo-ts-import-field').val() );
			$("form#javo-ts-admin-form").submit();
		});


		/*
		 *	Theme Settings : Media Library Includer
		 *
		 */
		// WordPress media upload button command.
		$("body").on("click", ".fileupload", function(e){
			var attachment;
			var t = $(this).attr("tar");
			e.preventDefault();
			var file_frame;
			if(file_frame){ file_frame.open(); return; }
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery( this ).data( 'uploader_title' ),
				button: {
					text: jQuery( this ).data( 'uploader_button_text' ),
				},
				multiple: false
			});
			file_frame.on( 'select', function(){
				attachment = file_frame.state().get('selection').first().toJSON();
				$("input[type='text'][tar='" + t + "']").val(attachment.url);
				$("img[tar='" + t + "']").prop("src", attachment.url);
			});
			file_frame.open();
			// Upload field reset button
		}).on("click", ".fileuploadcancel", function(){
			var t = $(this).attr("tar");
			$("input[type='text'][tar='" + t + "']").val("");
			$("img[tar='" + t + "']").prop("src", "");
		});

		/*
		 *	Theme Settings : Type to only number textfield.
		 *
		 */
		$('.only_number').each( function(){
			$(this).on('keyup', function(e){
				this.value = this.value.replace(/[^0-9.]/g, '');
			});
		});

		/*
		 *	Theme Settings : Font Control slider
		 *
		 */
		$( document ).on( 'javo:theme_settings_after',
			function(){
				$(".jvbpd_setting_slider").each(function(){
					$(this).noUiSlider({
						start: $(this).data('val')
						, step:1
						, range:{ min:[7], max:[100] }
						, connect:'lower'
						, serialization:{
							lower:[$.Link({
								target: $($(this).data('tar'))
								, format:{decimals:0}
							})]
						}
					});
				});
				$('.jvbpd_setting_slider.noUi-connect').css('background', '#454545');
			}
		);
	} // Undefined != jv_ts_variable


	/*
	 *	Metabox : Scripts
	 *
	 */
	var jvbpd_post_meta_scripts = function(){
		if( $( 'body' ).hasClass( 'nav-menus-php' ) || ( typeof jvbpd_metabox_variable != 'undefined' ) ) {
			this.init();
		}
	}

	jvbpd_post_meta_scripts.prototype = {

		constructor : jvbpd_post_meta_scripts,

		init:function() {

			var optionBox	= $( "div#jvbpd_page_settings" );

			if( typeof jvbpd_metabox_variable != 'undefined' ) {
				this.others();
			}

			$( document )
				.on( 'change', "input[name='post_format']", this.format_meta() )
				.on( 'change', 'select[data-docking]', this.opacity_docking )
				.on( 'change', 'select[name="page_template"]', this.visibilityTab() )
				.on( 'click', $( "ul.jv-page-settings-nav > li.jv-page-settings-nav-item", optionBox ).selector, this.tabOptionPanel() )
				.on( 'click', '.jv-uploader-wrap > button.upload', this.fileUploader() )
				.on( 'click', '.jv-uploader-wrap > button.remove', this.fileRemove() )

			;$( "select[name='page_template'], select[data-docking]" ).trigger('change');
			;$( "input[name='post_format']" ).trigger( 'change' );
			;$( 'input[name="jvbpd_map_opts[cluster]"]' ).trigger('change');
		}

		, tabOptionPanel : function() {
			return function() {
				var
					container		= $( this ).closest( 'div.jv-page-settings-wrap' )
					, navs			= $( 'li.jv-page-settings-nav-item', container )
					, contents		= $( '.jv-page-settings-content', container )
					, tarContent	= $( 'div' + $( this ).data( 'content' ), container );

				navs.removeClass( 'active' );
				contents.removeClass( 'active' );
				tarContent.addClass( 'active' );
				$( this ).addClass( 'active' );
			}
		}

		, visibilityTab : function() {
			return function( e ){
				var
					container		= $( "div.jv-page-settings-wrap" )
					, nav			= $( "ul.jv-page-settings-nav", container )
					, items			= $( "li.jv-page-settings-nav-item.require-template" )
					, template		= $( this ).val();

				items.addClass( 'hidden' );
				$( "li[data-require='" + template + "']", nav ).removeClass( 'hidden' );
				$( "li:first-child", nav ).trigger( 'click' );
			}
		}

		, opacity_docking: function( e )
		{
			var target = $( this ).closest( 'tr' ).find( 'input[type="text"]' );

			if( $( this ).val() != "enable" )
			{
				target.prop( 'disabled', true );
			}else{
				target.prop( 'disabled', false );
			}
		}

		, format_meta : function ()
		{
			var obj			= this;
			return function (e)
			{
				e.preventDefault();

				$( "[id^='jvbpd_postFormat']" ).hide();
				$( "#lynk_postFormat_" + $( 'input[name="post_format"]:checked' ).val() ).show();
			}
		}

		, fileUploader : function() {
			var
				obj				= this
				, uploader		= false;
			return function( e ) {
				e.preventDefault();
				var
					container	= $( this ).closest( '.jv-uploader-wrap' )
					, input		= $( 'input[type="text"]', container )
					, preview	= $( "img", container );

				if( !uploader ) {
					uploader	= wp.media.frames.file_frame = wp.media( {
						title		: $( this ).data( 'title' ) || 'Uploader',
						button	: {
							text	: $( this ).data( 'btn' ) || 'Select',
						},
						multiple	: false
					} );
				}

				// Events
				uploader.off( 'select' ).on( 'select',
					function() {
						var response	= uploader.state().get( 'selection' ).first().toJSON();

						if( typeof input.data( 'id' ) != 'undefined' ) {
							input.val( response.id );
						}else{
							input.val( response.url );
						}
						preview.prop( 'src', response.url );
					}
				).open();
				return;
			}
		}

		, fileRemove : function()  {
			var obj				= this;
			return function( e ) {
				e.preventDefault();
				var
					container	= $( this ).closest( '.jv-uploader-wrap' )
					, input		= $( 'input[type="text"]', container )
					, preview	= $( "img", container );

				input.val( null );
				preview.prop( 'src', '' );
			}
		}


		, others: function(){
			$( document ).on("click", ".jvbpd_pmb_option", function(){
				if( $(this).hasClass("sidebar") ) $(".jvbpd_pmb_option.sidebar").removeClass("active");
				if( $(this).hasClass("header") ) $(".jvbpd_pmb_option.header").removeClass("active");
				if( $(this).hasClass("fancy") ) $(".jvbpd_pmb_option.fancy").removeClass("active");
				if( $(this).hasClass("slider") ) $(".jvbpd_pmb_option.slider").removeClass("active");
				$(this).addClass("active");
			}).on("change", "input[name='jvbpd_opt_header']", function(){
				$("#jvbpd_post_header_fancy, #jvbpd_post_header_slide").hide();
				switch( $(this).val() ){
					case "fancy": $("#jvbpd_post_header_fancy").show(); break;
					case "slider": $("#jvbpd_post_header_slide").show(); break;
				};
			});

			$("body").on("change", "input[name='jvbpd_opt_slider']", function(){
				$(".jvbpd_pmb_tabs.slider")
					.children("div")
					.removeClass("active");
				$("div[tab='" + $(this).val() + "']").addClass("active");
			});
			var t = jvbpd_metabox_variable.strHeaderSlider;
			if(t != "")$("input[name='jvbpd_opt_slider'][value='" + t + "']").trigger("click");

			var t = jvbpd_metabox_variable.strHeaderFancy;
			if(t != "")$("input[name='jvbpd_opt_fancy'][value='" + t + "']").trigger("click");

		// End Other Function
		}
	}
	new jvbpd_post_meta_scripts;

	/*
	 *	Helper : Scripts
	 *
	 */
	var jv_admin_helper_script = function() {
		this.init();
	}

	jv_admin_helper_script.prototype= {
		constructor : jv_admin_helper_script
		, init : function() {
			var container	= $( "table.jv-default-setting-status-table" );
			$( document ).on( 'click', $( 'thead > tr', container ).selector , this.collapseToggle );
			$( window ).on( 'load', this.progressCounter );
		}

		, collapseToggle : function()
		{
			var
				container		= $( this ).closest( 'table.jv-default-setting-status-table' )
				, wrap			= $( this ).closest( 'div.jv-default-setting-status-wrap' )

			$( 'table', wrap ).not( container ).removeClass( 'collapse' );

			if( container.hasClass( 'collapse' ) ) {
				container.removeClass( 'collapse' );
			}else{
				container.addClass( 'collapse' );
			}
		}

		, progressCounter : function()
		{
			var
				wrap				= $( 'div.jv-default-setting-status-wrap' )
				, count			= $( 'div.jv-default-setting-status-progress', wrap )
				, total			= $( 'table > thead > tr', wrap ).length
				, active			= $( 'table > thead > tr', wrap ).not( '.update' ).length
				, cur				= ( active / total  ) * 100;
			$( { progress: 0 } )
				.animate(
					{progress : cur }
					, {
						duration : 3500
						, easing: 'swing'
						, step : function() {
							$( 'span', count ).text( Math.ceil( this.progress ) + ' %' );
						}
					}
				);
		}
	}
	new jv_admin_helper_script;

	$( 'select[data-show-field]' ).each( function() {
		var cond = $( this ).data( 'show-field' );
		$( this ).on( 'change', function() {
			var _current = $( this ).val();
			$.each( cond, function( option, selectors ) {
				$.each( selectors, function() {
					$( this ).addClass( 'hidden' );
					if( option.toString() == _current ) {
						$( this ).removeClass( 'hidden' );
					}
				} );
			} );
		} );
	} ).trigger( 'change' );



	jQuery( '#menu-to-edit').on( 'click', 'a.item-edit', function() {
		var settings  = jQuery(this).closest( '.menu-item-bar' ).next( '.menu-item-settings' );
		var css_class = settings.find( '.edit-menu-item-classes' );

		if( css_class.val().indexOf( 'jvbpd-menu' ) === 0 ) {
			css_class.attr( 'readonly', 'readonly' );
			settings.find( '.field-url' ).css( 'display', 'none' );
		}
	});

	$( ".theme-actions .button" ).on( 'click', function() {
		$( "div#jv-default-setting-plugins-wrap" ).addClass( "processing" );
	} );


} )( jQuery, window );