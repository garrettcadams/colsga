/******************************
** lavaThemes Custom Modal
** ver 1.0
******************************/
;(function($){
	"use strict";

	var lava_message_box_func = {

		init: function( attr, callback )
		{
			this.DEFAULT		= {
				content			: 'No Message'
				, delay			: 500000
				, close			: true
				, button		: "OK"
				, classes		: ''
				, blur_close 	: false
			}

			this.attr		= $.extend( true, {}, this.DEFAULT, attr );

			this.callback	= callback;
			this.button		= '<div class="text-center">';
				this.button	+= '<input type="button" id="lava-alert-box-close" class="" value="' + this.attr.button + '">'
			this.button		+= '</div>';
			this.el			= '#lava-alert-box'
			this.el_bg		= '#lava-alert-box-bg';
			this.el_button	= '#lava-alert-box-close';

			if( typeof window.lava_mbf_TimeID != "undefined" )
			{
				$( this.el_bg + ", " + this.el ).remove();
				clearInterval( window.lava_mbf_TimeID );
				window.lava_mbf_TimeID	= null;
			}

			this.open( this.attr );

			$( document ).on( 'click', this.el_button, this.close );
			if( typeof this.attr.close_trigger != "undefined" )
				$( document ).on( 'click', this.attr.close_trigger, this.close );

			if( this.attr.blur_close )
				$( document ).on( 'click', this.el_bg, this.close );
		}

		, open: function( attr )
		{
			var obj = lava_message_box_func;

			$( document )
				.find('body')
				.prepend( '<div id="lava-alert-box" class="' + attr.classes + '" tabindex="-1"></div><div id="lava-alert-box-bg"></div>' );

			$( this.el )
				.html( '<h5>' + attr.content + '</h5>' )
				.css({ marginLeft : -( $( this.el ).outerWidth(true) ) / 2 })
				.animate({ marginTop : -( $( this.el ).outerHeight(true) ) / 2 }, 300, function(){
					window.lava_mbf_TimeID = setInterval( obj.close, attr.delay );
				});

			if( attr.close ){
				$( this.el ).append( this.button );
			}

		}

		, close: function( e )
		{
			if( typeof e != 'undefined' ){
				e.preventDefault();
			}

			var obj = lava_message_box_func;

			if( typeof window.lava_mbf_TimeID != 'undefined')
			{
				clearInterval( window.lava_mbf_TimeID );
				obj.nTimeID == null;
			}

			$( obj.el_bg ).fadeOut('fast', function(){ $(this).remove(); });
			$( obj.el ).fadeOut('fast', function(){ $(this).remove(); });

			if( typeof obj.callback == 'function' ){
				obj.callback();
			};
		}
	};

	$.lava_msg = function( attr, callback ){
		lava_message_box_func.init( attr, callback );
	}

})(jQuery);