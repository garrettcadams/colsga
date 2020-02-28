<?php
namespace WilcityWidgets\App;

use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Frontend\User;

class AuthorProfile extends \WP_Widget {
	public $aDef = array('title'=>'');

	public function __construct() {
		parent::__construct( 'wilcity_author_profile', WILCITY_WIDGET . ' (Single) Author Profile');
	}


	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		?>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
		</div>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
		if ( !is_single() ){
			return '';
		}

		global $post;
		echo $aAtts['before_widget'];
            if ( !empty($aInstance['title']) ){
	            echo $aAtts['before_title']; ?><i class="la la-users"></i> <span><?php echo esc_html($aInstance['title']); ?></span><?php echo $aAtts['after_title'];
            }

            echo '<div class="content-box_body__3tSRB pb-0">' . do_shortcode('[wilcity_author_profile user_id="'.$post->ID.'"]') . '</div>';
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