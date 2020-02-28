<?php

namespace Awps\Core;

use Awps\Custom\Admin;

/**
 * Sidebar.
 */
class Layout
{
    /**
     * register default hooks and actions for WordPress
     * @return
     */
    public function register() {
        add_action( 'Javo/Header/Render', array( $this, 'header_render' ) );
        add_action( 'Javo/Footer/Render', array( $this, 'footer_render' ) );
        add_action( 'Javo/Footer/Render', array( $this, 'left_sidebar_render' ) );
        add_action( 'Javo/Footer/Render', array( $this, 'right_sidebar_render' ) );
        add_action( 'Javo/Footer/Render', array( $this, 'floating_menu_render' ) );
        add_action( 'jvbpd_core/wrapper/after/render', Array( $this, 'wrapper_after_render' ) );

        add_filter( 'nav_menu_css_class', Array($this, 'menu_css_class'), 10, 3);
        add_filter( 'walker_nav_menu_start_el', array( $this, 'nav_menu_start_el' ), 10, 4 );
    }

    public function header_render() {
        $header_file = 'headers/header.php';
        if( function_exists( 'jvbpdCore' ) ) {
            $header_file = 'headers/header-elementor.php';
        }
        $this->load_template( $header_file );
    }

    public function footer_render() {
        if( !function_exists( 'jvbpdCore' ) ) {
            $footer_file = 'footers/footer.php';
            $this->load_template( $footer_file );
        }
    }

    public static function is_active_left_sidebar() {
        $isActive = 'enabled' == jvbpd_tso()->get('sidebar_left');
        $pageSettings = get_post_meta( get_queried_object_id(), Admin::PAGE_SETTINGS_KEY, true);
        if(isset($pageSettings['sidebar_left'])){
            $isActive = 'enabled' == $pageSettings['sidebar_left'];
        }
        return $isActive;
    }

    public static function is_active_member_sidebar() {
        $isActive = 'enabled' == jvbpd_tso()->get('sidebar_right');
        $pageSettings = get_post_meta( get_queried_object_id(), Admin::PAGE_SETTINGS_KEY, true);
        if(isset($pageSettings['sidebar_right'])){
            if('enabled'==$pageSettings['sidebar_right']) {
                $isActive = true;
            }elseif('disabled' == $pageSettings['sidebar_right']) {
                $isActive = false;
            }
        }
        return $isActive;
    }

    public function left_sidebar_render() {
        if(self::is_active_left_sidebar()){
            $this->load_template('sidebars/sidebar-left.php');
        }
    }

    public function right_sidebar_render() {
        if(self::is_active_member_sidebar()){
            $this->load_template('sidebars/sidebar-right.php');
        }
    }

    public function floating_menu_render() {
        if('enable' != jvbpd_tso()->get('floating_menu', 'disable')){
            return;
        }
        $classes = Array('floating-menu');
        if('enable' == jvbpd_tso()->get('floating_menu_show_scroll', 'disable')) {
            $classes[] = 'show-to-scroll';
        }
        $output = '<div class="' . join(' ', $classes) . '">';
            $output .= do_shortcode(jvbpd_tso()->get('floating_menu_content'));
        $output .= '</div>';
        printf($output);
    }

    public function load_template( $strFileName=false, $args=Array(), $options=Array() ){
		$options = shortcode_atts(
			Array(
                'once' => false,
                'path' => get_template_directory() . '/views',
			), $options
        );

		if( is_Array( $args ) ) {
            extract( $args, EXTR_SKIP );
        }

        $strFileName  = $options['path'] . '/' . $strFileName;

		if( file_exists( $strFileName ) ){
			if( $options[ 'once' ] ) {
				require_once( $strFileName );
			}else{
				require( $strFileName );
			}
			return true;
		}
		return false;
    }

    public function wrapper_after_render() {
        if( function_exists( 'jvbpd_listing_footer_content' ) ){
			echo '<div class="jvbpd-page-builder-footer">';
				jvbpd_listing_footer_content();
			echo '</div>';
		}
    }

    public function filterMenuSlug( $menu_item=Array() ) {
		return isset( $menu_item[ 'slug' ] ) ? $menu_item[ 'slug' ] : '';
    }

    public function menu_css_class($classes=Array(), $item, $args=Array()) {
        if('yes' == get_post_meta($item->ID, '_wide_menu', true)){
            $classes[] = 'wide-container';
        }
        return $classes;
    }

    public function setup_navi_item( $menu_item ) {
		$strFindString = '';
		if( is_array( $menu_item->classes ) ) {
			$strFindString = implode(' ', $menu_item->classes );
		}
		preg_match('/\sjvbpd-(.*)-nav/', $strFindString, $matches);

		if( empty( $matches[1] ) ) {
			return $menu_item;
		}

		$arrAppendMenus = jvbpd_customNav()->getAppendMenus();
		$arrAppendMenuSlugs = array_map( array( $this, 'filterMenuSlug' ), $arrAppendMenus );
		if( in_array( $matches[1], $arrAppendMenuSlugs ) ) {
			$menu_item->jv_menu = str_replace( '_', '-', $matches[1] );
		}
		return $menu_item;
	}

    public function nav_menu_start_el($output, $item=Array(), $depth=0, $args=Array()) {
        if('yes' == get_post_meta($item->ID, '_wide_menu', true)) {
            $classes[] = 'wide-nav-overlay';
            $toClass = join( ' ', array_filter( $classes ) );
            ob_start();
			$this->load_template( 'headers/menu-post-block.php', Array( 'item' => $item ) );
			$outputBlock = ob_get_clean();
            $output_format = '<ul class="%1$s" style="%2$s"><li>%3$s</li></ul>';
            $output .= sprintf(
                $output_format,
                $toClass,
                '', $outputBlock
            );
        }
        return $output;
    }

    public static function is_active_sidebar() {
        return apply_filters('Javo/Content/Sidebar_Active', true, get_the_ID());
    }

    public static function content_attributes() {
        $output = NULL;
        $content_css = Array('col-sm-12');
        if(self::is_active_sidebar()) {
            $content_css = Array('col-sm-9');
        }
        $attr_args = Array(
            'class' => join(' ', apply_filters('Javo/Content/Attributes/Css', $content_css) ),
        );
        foreach($attr_args as $attr => $value ) {
            printf('%1$s="%2$s"', $attr, esc_attr($value) ) . ' ';
        }
    }

}
