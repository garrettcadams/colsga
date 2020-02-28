<?php

namespace Awps\Setup;

/**
 * Enqueue.
 */
class Enqueue
{
	/**
	 * register default hooks and actions for WordPress
	 * @return
	 */
	public function register()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', Array( $this, 'enqueue_google_font' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Notice the mix() function in wp_enqueue_...
	 * It provides the path to a versioned asset by Laravel Mix using querystring-based
	 * cache-busting (This means, the file name won't change, but the md5. Look here for
	 * more information: https://github.com/JeffreyWay/laravel-mix/issues/920 )
	 */
	public function enqueue_scripts()
	{
		// Deregister the built-in version of jQuery from WordPress
		if ( ! is_customize_preview() ) {
			//wp_deregister_script( 'jquery' );
		}
       	//wp_enqueue_script('jquery');

		// CSS
		wp_enqueue_style( 'main', t_mix('css/style.css'), array(), '1.0.0', 'all' );
		// wp_add_inline_style( 'main', jvbpd_tso()->get( 'custom_css') );

		// JS
		wp_enqueue_script( 'popper', get_template_directory_uri() . '/assets/dist/js/popper.min.js', array('jquery') );
		wp_enqueue_script( 'bootstrap-min', get_template_directory_uri() . '/assets/dist/js/bootstrap.min.js', array() );
		//wp_enqueue_script( 'mainfest', mix('js/manifest.js'), array(), '1.0.0', true );
		//wp_enqueue_script( 'vendor', mix('js/vendor.js'), array(), '1.0.0', true );
		wp_enqueue_script( 'theme-app', t_mix('js/app.js'), array(), '1.0.0', true );
		//wp_enqueue_script( 'theme-app', mix('js/front.js'), array(), '1.0.0', true );

		// Activate browser-sync on development environment
		if ( getenv( 'APP_ENV' ) === 'development' ) :
			wp_enqueue_script( '__bs_script__', getenv('WP_SITEURL') . ':3000/browser-sync/browser-sync-client.js', array(), null, true );
		endif;

		// Extra
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	public function enqueue_google_font() {
		/*
		 * '%7c' :  Separator for validator.w3.org
		 */
		$strLoadFonts = add_query_arg(
			Array(
				'family' => 'Open+Sans:300,400,500,600,700,800,900' .'%7c'.
				'Poppins' .'%7c'. 'Raleway' .'%7c'. 'Roboto',
				'subset' => urlencode( 'latin,latin-ext' )
			), '//fonts.googleapis.com/css'
		);
		$strLoadFonts = esc_url_raw( $strLoadFonts );
		wp_enqueue_style( 'jv-google-fonts', $strLoadFonts, Array(), null );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'jvbpd-admin', get_template_directory_uri() . '/assets/dist/js/admin.js', array('jquery'), '1.0.0', true );
	}
}