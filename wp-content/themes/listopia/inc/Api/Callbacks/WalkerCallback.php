<?php
/**
 * Class Name: jvnavwalker
 * GitHub URI: https://github.com/dominicbusinaro/bs4navwalker
 * Description: A custom WordPress nav walker class for Bootstrap 4 (v4.0.0-alpha.1) nav menus in a custom theme using the WordPress built in menu manager
 * Version: 0.1
 * Author: Dominic Businaro - @dominicbusinaro
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

class jvnavwalker extends Walker_Nav_Menu {

	// add classes to ul sub-menus
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
		// depth dependent classes
		$indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
		$display_depth = ( $depth + 1); // because it counts the first submenu as 0
		$classes = array(
			'nav sub-menu-second',
			( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
			( $display_depth >=2 ? 'nav nav-third-level' : '' ),
			'menu-depth-' . $display_depth
			);

		$class_names = implode( ' ', $classes );
		$render = '<ul class="' . $class_names . '">';
		$output .= "\n" . $indent . apply_filters( 'jvbpd/front/walker/start_lvl/output', $render, $depth, $args, $classes ) . PHP_EOL;

	}

     function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;

		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
		// depth dependent classes
		$depth_classes = array(
			( $depth == 0 ? 'nav-item main-menu-item' : 'sub-menu-item' ),
			( $depth >=2 ? 'sub-sub-menu-item' : '' ),
			( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
			'menu-item-depth-' . $depth
		);
		
		$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

		if( 0 < intVal( $args->walker->has_children ) ){
			$arrow = "<span class='fa fa-angle-down'></span>";
		}else{
			$arrow ="";
		}

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) ) );

		// build html
		$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

		// link attributes
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ' class="nav-link menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

		$strIcon = !empty( $item->nav_icon ) ? '<i class="nav-icon ' . $item->nav_icon . '"></i> ' : '<i class="nav-icon"></i>';

		$item_output = sprintf( '%1$s<a%2$s>%8$s<span class="menu-titles">%3$s%4$s%5$s%6$s</span></a>%7$s',
			$args->before,
			$attributes,
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			$arrow,
			$args->link_after,
			$args->after,
			$strIcon
		);

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}
