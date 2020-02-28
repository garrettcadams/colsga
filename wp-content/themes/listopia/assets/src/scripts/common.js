(function($){
    var jvbpdCommon = function(){
        this.setLeftMenu();
        this.floatingMenu();
    }
    jvbpdCommon.prototype.constructor = jvbpdCommon;
    jvbpdCommon.prototype.setLeftMenu = function() {
        var
            obj = this,
            INT_COLLAPSE_WIDTH = 60,
            BTN_SIDEBAR_SWITCHER = $( '.dashboard-sidebar-switcher' ),
            HEADER_ELEMENT = $( '.navbar-default.navbar' ),
            BRAND_ELEMENT = $( '.navbar-header div.navbar-brand' ),
            SIDEBAR_ELEMENT = $( '.navbar-default.sidebar' ),
            CONTENT_ELEMENT = $( '#content-page-wrapper' ),
            LOGO_ELEMENT = $( '.navbar-brand', HEADER_ELEMENT ),
            INT_SIDEBAR_WIDTH = 220, //BRAND_ELEMENT.width(), //SIDEBAR_ELEMENT.width(),
            COOKIE_KEY = 'javobp_mypage_sidebar_switcher_onoff';
            //IS_SWITCHER_COLLAPSE = this.getCookie( COOKIE_KEY ) == 'yes';

        // SIDEBAR_ELEMENT.find( '#nav-wrap' ).width( INT_SIDEBAR_WIDTH );
        BTN_SIDEBAR_SWITCHER.on( 'click', function( e, _param ) {
            var param = _param || {};
            if( true || $( window ).width() > 768 ) {
                if( ! $( this ).hasClass( 'active' ) ) {
                    $( 'body' ).removeClass( 'sidebar-active' );
                    BTN_SIDEBAR_SWITCHER.addClass( 'active' );
                }else{
                    $( 'body' ).addClass( 'sidebar-active' );
                    BTN_SIDEBAR_SWITCHER.removeClass( 'active' );
                }
            }

            /**
            BTN_SIDEBAR_SWITCHER.toggleClass( 'active' );
            */
            if( true === param.activeClass ) {
                $( 'body' ).removeClass( 'sidebar-active-init' );
            }

            $( window ).trigger( 'resize' );
        } );

        $( '#nav-wrap', SIDEBAR_ELEMENT ).css( 'width', 'auto' );
    }
    jvbpdCommon.prototype.setRightMenu = function() {
        var
            opener = $( '.overlay-sidebar-opener' ),
            panel = $( '.quick-view' ),
            panel_width = panel.width(),
            body_color = $( 'body' ).css( 'background-color' );

        panel.find( 'ul' ).width( panel_width );
        opener.on( 'click', function() {
            if( opener.hasClass( 'active' ) ) {
                panel.css( 'margin-right', -(panel_width) + 'px' );
                $( '.jv-my-page' ).removeClass( 'overlay' );
            }else{
                panel.css( 'margin-right', 0 );
                $( '.jv-my-page' ).addClass( 'overlay' );
            }
            opener.toggleClass( 'active' );
        } );
    }
    jvbpdCommon.prototype.floatingMenu = function() {
        $('.show-to-scroll').each(function(){
            var
                $this = $(this),
                callback = function(event) {
                    if(100 < $(window).scrollTop() ){
                        $this.fadeIn();
                    }else{
                        $this.fadeOut();
                    }
                }
            $(window).on('scroll', callback);
            callback();
        });
    }
    window.jvbpd = new jvbpdCommon;

})(jQuery);