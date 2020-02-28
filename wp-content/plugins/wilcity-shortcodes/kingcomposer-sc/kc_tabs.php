<?php

$title = $tabs_position = $nav_item_style = $interval = $open_mouseover = $class = $navigation = $auto_play = $auto_height = $auto_play = $tab_nav_class = $title_slider = '';
$speed = 4500; $pagination = 'yes'; $items = $items1 = $items2 = 1;
extract( $atts );

$css_class = apply_filters( 'wilcity-el-class', $atts );

$tabs_option = array(
	'open-on-mouseover' => $open_mouseover,
	'tab-active' => 1,
	'effect-option' => $effect_option,
);

$tabs_option_data = array();
foreach( $tabs_option as $name => $value ){
	array_push( $tabs_option_data, 'data-'.esc_attr( $name ).'="'.esc_attr( $value ).'"' );
}

$css_class = array_merge($css_class, array( 'kc_tabs', 'group' ));

if( isset( $css ) && !empty( $css ) ){
	$css_class[] = $css;
}

if( isset( $class ) && !empty( $class ) ) {
	$css_class[] = $class;
}

$tab_nav_class = 'wilTab_nav__1_kwb wil-tab__nav';
$aWrapperClass = array('wilTab_module__jlr12 wil-tab');

if( $type == 'vertical_tabs' ){
	$css_class[] = 'kc_vertical_tabs';
	$tab_nav_class .= ' ' . $tabs_position;
	$aWrapperClass[] = 'wilTab_vertical__2iwYo';
	$aWrapperClass[]  = $nav_item_style;
} else if( $type == 'slider_tabs' ){
	$css_class[] = 'kc-tabs-slider';

	$owl_option = array(
		"items" => intval($items)?intval($items):1,
		"speed" => intval( $speed ),
		"navigation" => $navigation,
		"pagination" => $pagination,
		"autoheight" => $autoheight,
		"autoplay" => $autoplay,
		"tablet" => intval($tablet)?intval($tablet):1,
		"mobile" => intval($mobile)?intval($mobile):1
	);

	$owl_option = strtolower( json_encode( $owl_option ) );

	echo '<div class="'.implode( ' ', $css_class ).'">';
	if( $title_slider === 'yes' ){
		echo '<ul class="kc-tabs-slider-nav kc_clearfix">';
		preg_replace_callback( '/kc_tab\s([^\]\#]+)/i', 'kc_process_tab_title' , $content );
		echo '</ul>';

	}
	echo '<div class="owl-carousel" data-owl-options=\''. $owl_option .'\'>';
	    echo do_shortcode( str_replace('kc_tabs#', 'kc_tabs', $content ) );
	echo '</div>';
	echo '</div>';

	return;

} else{
	$tab_nav_class .= ' ' . $tabs_position;
	$aWrapperClass[]  = $nav_item_style;
}

$tabs_option_data[] = 'class="'. esc_attr( implode(' ', $css_class) ) .'"';
global $wilcityHasActivatedTab, $wilcityHasTabContentActive;
$wilcityHasActivatedTab = '';
$wilcityHasTabContentActive = '';
?>
<div <?php echo implode( ' ', $tabs_option_data ); ?>>
	<div class="<?php echo implode(' ', $aWrapperClass) ?>">
		<ul class="<?php echo esc_attr($tab_nav_class); ?>">
			<?php preg_replace_callback( '/kc_tab\s([^\]\#]+)/i', 'wiloke_kc_process_tab_title' , $content ); ?>
		</ul>
        <div class="wilTab_content__2j_o5 wil-tab__content">
		    <?php echo do_shortcode( str_replace('kc_tabs#', 'kc_tabs', $content ) ); ?>
        </div>
	</div>
</div>


