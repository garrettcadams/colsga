<?php

namespace Awps\Setup;

/**
 * Menus
 */
class Menus
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register()
    {
        add_action( 'after_setup_theme', array( $this, 'menus' ) );
    }

    public function menus()
    {
        /*
            Register all your menus here
        */
        $menus = Array(
            array( 'primary' => esc_html__( 'Primary', 'jvbpd' ) ),
            array( 'sidebar_left' => esc_html__( 'Sidebar - Left', 'jvbpd' ) ),
            array( 'sidebar_right' => esc_html__( 'Sidebar - Right', 'jvbpd' ) ),
        );
        foreach($menus as $menu){
            register_nav_menus($menu);
        }
    }
}
