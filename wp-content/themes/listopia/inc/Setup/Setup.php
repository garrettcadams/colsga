<?php

namespace Awps\Setup;

class Setup
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action( 'after_setup_theme', array( $this, 'setup' ) );
        add_action( 'after_setup_theme', array( $this, 'old_core_check' ) );
        add_action( 'after_setup_theme', array( $this, 'content_width' ), 0);
        add_filter( 'login_redirect', Array($this, 'login_out_redirect'), 10, 3);
        add_filter( 'logout_redirect', Array($this, 'login_out_redirect'), 10, 3);

    }

    public function setup()
    {
        /*
         * You can activate this if you're planning to build a multilingual theme
         */

        /*
         * Default Theme Support options better have
         */
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'customize-selective-refresh-widgets' );

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        add_theme_support( 'custom-background', apply_filters( 'awps_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );

        /*
         * Activate Post formats if you need
         */
        add_theme_support( 'post-formats', array(
            'aside',
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video',
            'audio',
            'chat',
        ) );

        add_theme_support( 'woocommerce' );
        add_theme_support( 'header-footer-elementor' );
        add_image_size( 'jvbpd-tiny', 80, 80, true );     	// for img on widget
        add_image_size( 'jvbpd-avatar', 250, 250, true);  		// User Picture size
        add_image_size( 'jvbpd-box-v', 400, 250, true );  		// for long width blog
        add_image_size( 'jvbpd-large', 500, 400, true );  		// extra large
        add_image_size( 'jvbpd-large-v', 650, 700, true );  		// extra large
        set_post_thumbnail_size( 132, 133, true );

        load_theme_textdomain( 'jvbpd', get_template_directory() . '/languages' );
    }

    public function old_core_check() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $ActivatedOldCore = Array();
        foreach( Array('lynk', 'listopia', 'javo-spot', 'javo-directory', 'javo-home') as $coreName ) {
            $corePlugin = sprintf('%1$s-core/%1$s-core.php', $coreName);
            if( function_exists('jvbpd_active_plugin') && jvbpd_active_plugin($corePlugin) ) {
                $ActivatedOldCore[] = $corePlugin;
                deactivate_plugins($corePlugin);
            }
        }
        if(0 < sizeof($ActivatedOldCore)){
            wp_die(
                esc_html__( "The old version of Core plugin has been deactivated due to compatibility. Please activate Javo Core plugin to the newest version.", 'jvbpd' ) .
                sprintf( '<p><a href="%1$s" target="_self" title="%2$s">%2$s</a></p>', admin_url( 'plugins.php' ), esc_html__( "Go to plugin page", 'jvbpd' ) )
            );
        }
    }

    public function content_width() {

    }

    public function login_out_redirect( $url, $req, $user ) {
        if( empty( $user ) || is_wp_error( $user ) ){
            return $url;
        }
		switch( jvbpd_tso()->get('login_redirect', '') ) {
			// Go to the Main Page
            case 'home' : $url = esc_url( home_url( '/' ) ); break;

			// Everything no working
			case 'current' :
				// This page is WP Login Page ?
				if(
					isset( $_POST['log'] ) &&
					isset( $_POST['pwd'] ) &&
					isset( $_POST['wp-submit'] )
				) {
					$url = admin_url();
				}else{
					$url = $req;
				}

			break;
			// Go to the Profile Page
            case 'admin' :
            default:
                $url = admin_url();
        }
		return $url;
    }
}
