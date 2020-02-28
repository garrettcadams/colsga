<?php

namespace WilcityWidgets\App;


use WilokeListingTools\Framework\Helpers\GetSettings;

class FollowUs extends \WP_Widget {
	public $aDef = array('title'=>'');

	public function __construct() {
		parent::__construct( 'wilcity_follow_us', WILCITY_WIDGET . ' Follow Us');
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
		$aSocialNetworks = \WilokeSocialNetworks::getUsedSocialNetworks();
        $aThemeOptions = \Wiloke::getThemeOptions();
        $aSocial = array();
		foreach ( $aSocialNetworks as $icon )
		{
			$key = 'social_network_'.$icon;
			if ( isset($aThemeOptions[$key]) && !empty($aThemeOptions[$key]) ){
				$aSocial[$icon] = $aThemeOptions[$key];
            }
		}

		if ( empty($aSocial) ){
		    return '';
        }

		echo $aAtts['before_widget'];
			if ( !empty($aInstance['title']) ){
				echo $aAtts['before_title'] . $aInstance['title'] . $aAtts['after_title'];
			}
			?>
            <div class="content-box_body__3tSRB">
                <div class="icon-box-1_module__uyg5F mt-20 mt-sm-15">
                    <div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
                        <?php
                        foreach ($aSocial as $icon => $link) :
                            ?>
                            <a class="social-icon_item__3SLnb" href="<?php echo esc_url($link); ?>" target="_blank"><i class="fa fa-<?php echo esc_attr($icon); ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
			<?php
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