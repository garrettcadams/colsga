<?php

namespace WilcityWidgets\App;


use WilokeListingTools\Framework\Helpers\GetSettings;

class ContactForm extends \WP_Widget {
	public $aDef = array('title'=>'', 'shortcode'=>'');

	public function __construct() {
		// Instantiate the parent object
		parent::__construct( 'wilcity_contact_form', WILCITY_WIDGET . ' Contact Form');
	}

	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		?>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
		</div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('shortcode'); ?>">Shortcode</label>
			<p><i>Wilcity currently support Contact Form 7 shortcode only</i></p>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('shortcode'); ?>" id="<?php echo $this->get_field_id('shortcode'); ?>" value="<?php echo esc_attr($aInstance['shortcode']); ?>">
		</div>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
	    if ( is_single() || empty($aInstance['shortcode']) ){
	        global $post;

	        if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_contact_form') ){
	            return '';
            }
        }

		echo $aAtts['before_widget'];
			if ( !empty($aInstance['title']) ){
				echo $aAtts['before_title'] . $aInstance['title'] . $aAtts['after_title'];
			}
			echo do_shortcode($aInstance['shortcode']);
		echo $aAtts['after_widget'];
	}

	public function update( $aNewInstance, $aOldInstance ) {
		$aInstance = $aOldInstance;
		foreach ($aNewInstance as $key => $val){
			$aInstance[$key] = strip_tags($val);
		}
		return $aInstance;
	}
}