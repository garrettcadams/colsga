;( function( $ ){

	var lava_bpp_form_script = function() { if( ! this.loaded ) this.init(); }

	lava_bpp_form_script.prototype = {

		constructor : lava_bpp_form_script

		, init : function()
		{

			var obj = this;

			obj.loaded	 = 1;
			obj.attr = lava_bpp_admin_meta_args;

			$( window )
				.on( 'load'	, obj.item_meta() )

			$( document )
				.on( 'keyup', '[name="lava_pt[map][lat]"], [name="lava_pt[map][lng]"]', obj.type_latLng() )
				.on( 'click', '[name="lava_pt[map][street_visible]"]', obj.toggle_streetview() );

		}

		, item_meta: function() {
			var obj			= this;
			return function( e, undef ) {

				e.preventDefault();

				var file_frame = false;

				obj.el			= $(".lava-item-map-container");
				obj.st_el		= $(".lava-item-streetview-container");
				obj.streetView = $("[name='lava_pt[map][street_visible]']").is(":checked");

				obj.st_el.height(350);

				// This Item get Location
				/*
				obj.latLng = $("input[name='lava_pt[map][lat]']").val() != "" && $("input[name='lava_pt[map][lng]']").val() != "" ?
					new google.maps.LatLng($("input[name='lava_pt[map][lat]']").val(), $("input[name='lava_pt[map][lng]']").val()) :
					new google.maps.LatLng( 40.7143528, -74.0059731 ); */

				// Initialize Map Options
				/*
				obj.map_options = {
					map:{ options:{ zoom:10, center: obj.latLng } }
					, marker:{
						latLng		: obj.latLng
						, options:{
							draggable	: true
						}
						, events:{
							position_changed: function( m )
							{
								$('input[name="lava_pt[map][lat]"]').val( m.getPosition().lat() );
								$('input[name="lava_pt[map][lng]"]').val( m.getPosition().lng() );
								$('input[name="lava_pt[map][street_lat]"]').val( m.getPosition().lat() );
								$('input[name="lava_pt[map][street_lng]"]').val( m.getPosition().lng() );

								if( $("[name='lava_pt[map][street_visible]']").is(":checked") )
								{

									$(this).gmap3({
										get:{
											name:'streetviewpanorama'
											, callback: function( streetView )
											{
												if( typeof streetView != 'undefined' )
												{
													streetView.setPosition( m.getPosition() );
													streetView.setVisible();
												}
											}
										}
									});
								}
							}

						}
					}, streetviewpanorama:{
						options:{
							container				: obj.st_el.get(0)
							, opts:{
								position			: new google.maps.LatLng(
									$('[name="lava_pt[map][street_lat]"]').val()
									, $('[name="lava_pt[map][street_lng]"]').val()
								)
								, pov				: {
									heading			: parseFloat( $('[name="lava_pt[map][street_heading]"]').val() )
									, pitch			: parseFloat( $('[name="lava_pt[map][street_pitch]"]').val() )
									, zoom			: parseFloat( $('[name="lava_pt[map][street_zoom]"]').val() )
								}
								, addressControl	: false
								, clickToGo			: true
								, panControl		: true
								, linksControl		: true
							}
						}
						, events:{
							pov_changed:function( pano ){
								$('[name="lava_pt[map][street_heading]"]').val( parseFloat( pano.pov.heading ) );
								$('[name="lava_pt[map][street_pitch]"]').val( parseFloat( pano.pov.pitch ) );
								$('[name="lava_pt[map][street_zoom]"]').val( parseFloat( pano.pov.zoom ) );
							}
							, position_changed: function( pano ){
								$('[name="lava_pt[map][street_lat]"]').val( parseFloat( pano.getPosition().lat() ) );
								$('[name="lava_pt[map][street_lng]"]').val( parseFloat(  pano.getPosition().lng() ) );
							}
						}
					}
				}

				obj.el.css("height", 300).gmap3( obj.map_options );
				obj.map = obj.el.gmap3('get');

				if( !obj.streetView && obj.el.length > 0 ){
					obj.map.getStreetView().setVisible( false );
				}

				*/

				$( document )
					.on("click", ".lava_pt_detail_del", function(){
						var t = $(this);
						t.parents(".lava_pt_field").remove();
					})
					.on( 'click', '.lava_pt_detail_add', function( e ){
						e.preventDefault();
						var
							attachment,
							t = $( this );

						if( file_frame ){ file_frame.open(); return; }

						file_frame = wp.media.frames.file_frame = wp.media({
							title: $( this ).data( 'uploader_title' ),
							button: {
								text: $( this ).data( 'uploader_button_text' ),
							},
							multiple: true
						});
						file_frame.on( 'select', function(){
							attachment = file_frame.state().get('selection');
							attachment.map(function(attachment){
								var str="";
								attachment = attachment.toJSON();
								str += "<div class='lava_pt_field' style='float:left;'>";
								str += "<img src='" + attachment.url + "' width='150'> <div align='center'>";
								str += "<input name='lava_attach[]' value='" + attachment.id + "' type='hidden'>";
								str += "<input class='lava_pt_detail_del button' type='button' value='Delete'>";
								str += "</div></div>";
								t.parents("td").find(".lava_pt_images").append(str);
							});
						});
						file_frame.open();
					})
					.on("keyup keypress", ".lava_txt_find_address", function ( e ){

						var keyCode		= e.keyCode || e.which;

						if(e.keyCode == 13){
							e.preventDefault();
							$(".lava_btn_find_address").trigger("click");
							return false;
						}

					})
					.on("click", ".lava_btn_find_address", function(){

						var _addr = $(".lava_txt_find_address").val();
						$(".lava-item-map-container").gmap3({
							getlatlng:{
								address:_addr,
								callback:function(r){
									if(!r){
										alert( obj.attr.fail_find_address );
										return false;
									}
									var _find = r[0].geometry.location;
									$("input[name='lava_pt[map][lat]']").val(_find.lat());
									$("input[name='lava_pt[map][lng]']").val(_find.lng());
									$(".lava-item-map-container").gmap3({
										get:{
											name:"marker",
											callback:function(m){
												m.setPosition(_find);
												$(".lava-item-map-container").gmap3({map:{options:{center:_find}}});
											}
										}
									});
								}
							}
						});
					});
			}
		}

		, toggle_streetview: function()
		{
			var obj		= this;

			return function()
			{
				if( $(this).is(":checked") )
				{
					obj.st_el.removeClass('hidden');
					obj.map.getStreetView().setVisible( true );
				}else{
					obj.st_el.addClass('hidden');
					obj.map.getStreetView().setVisible( false );
				}
			}
		}

		, type_latLng: function()
		{
			var obj				= this;
			return function( e )
			{
				e.preventDefault();

				var _this		= this;

				this.lat		= parseFloat( $('[name="lava_pt[map][lat]"]').val() );
				this.lng		= parseFloat( $('[name="lava_pt[map][lng]"]').val() );

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
		}
	}
	new lava_bpp_form_script;
} )( jQuery );