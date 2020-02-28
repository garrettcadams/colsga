<?php

namespace WilcityWidgets\App;


class MailChimp extends \WP_Widget {
	public $aDef = array('title'=>'Join us on', 'description'=>' We don’t send spam so don’t worry.', 'toggleSocialNetworks'=>'disable');

	public function __construct() {
		// Instantiate the parent object
		parent::__construct( 'wilcity_mailchimp', WILCITY_WIDGET . ' MailChimp');
		add_filter('wiloke/mailchimp/data', array($this, 'maybeHasTerm'));
	}

	public function maybeHasTerm($aData){
	    if ( !class_exists('Wiloke') ){
	        return false;
        }

	    $aThemeOptions = \Wiloke::getThemeOptions();
	    if ( $aThemeOptions['toggle_terms_and_conditionals'] == 'enable' ){
		    $aData['hasTerm'] = true;
        }
        return $aData;
    }

	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		?>
        <div class="widget-group">
            <label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
        </div>
        <div class="widget-group">
            <label for="<?php echo $this->get_field_id('description'); ?>">Description</label>
            <textarea type="text" class="widefat" name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>"><?php echo esc_textarea($aInstance['description']); ?></textarea>
        </div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('toggleSocialNetworks'); ?>">Toggle Social Networks</label>
			<p><i>You can setup your social networks at Appearance -> Theme Options</i></p>
			<select class="widefat" name="<?php echo $this->get_field_name('toggleSocialNetworks'); ?>" id="<?php echo $this->get_field_id('toggleSocialNetworks'); ?>">
				<?php foreach (array('enable'=>'Enable', 'disable'=>'Disable') as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['toggleSocialNetworks']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
	    global $wiloke;
		if ( !class_exists('Wiloke') ){
			return '';
		}

		$aInstance = wp_parse_args($aInstance, $this->aDef);
		echo $aAtts['before_widget'];
            if ( !empty($aInstance['title']) ){
                echo $aAtts['before_title'] . $aInstance['title'] . $aAtts['after_title'];
            }

//            if ( !get_option('pi_mailchimp_api_key') ) {
//                if ( current_user_can( 'edit_theme_options' ) ) {
//                    \WilokeMessage::message(array(
//                        'status' => 'danger',
//                        'msg'    => 'You have not configured MailChimp API. Please go to Settings -> Wiloke MailChimp to complete it'
//                    ));
//                }
//                return '';
//            }

            wp_enqueue_script('wilcity-widget-mailchimp');
		?>
            <div class="content-box_body__3tSRB">
                <div class="widget-subsc">
                    <div class="widget-subsc__text"><?php \Wiloke::ksesHTML($aInstance['description']); ?></div>

                    <div class="alert_module__Q4QZx alert_danger__2ajVf error hidden">
                        <div class="alert_icon__1bDKL"><i class="la la-frown-o"></i></div>
                        <div class="alert_content__1ntU3 err-msg"></div>
                    </div>

                    <form class="wilcity-mailchimp-form widget-subsc__form">

                        <div class="form-item">
                            <input type="email" placeholder="<?php esc_html_e('Email...', 'wilcity-widgets'); ?>" required/>
                        </div>
                        <div class="form-submit">
                            <button type="submit"><i class="la la-envelope"></i></button>
                        </div>
                        <?php if ( $wiloke->aThemeOptions['toggle_terms_and_conditionals'] == 'enable' ) : ?>
                        <div class="form-item">
                            <div class="checkbox_module__1K5IS mt-15 mb-15 js-checkbox">
                                <label class="checkbox_label__3cO9k">
                                    <input class="checkbox_inputcheck__1_X9Z" type="checkbox" value="1" name="agreeToTerm" required>
                                    <span class="checkbox_icon__28tFk bg-color-primary--checked-after bd-color-primary--checked" style="border-color: rgba(255, 255, 255, 0.2);"><i class="la la-check"></i><span class="checkbox-iconBg"></span></span>
                                </label>
                                <span class="checkbox_text__3Go1u text-ellipsis">
                                    <?php \Wiloke::ksesHTML($wiloke->aThemeOptions['terms_and_conditionals_desc']); ?>
                                    <span class="checkbox-border"></span>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>

                    <?php if ( $aInstance['toggleSocialNetworks'] == 'enable' ) : ?>
                    <div class="social-icon_module__HOrwr">
                        <?php \WilokeSocialNetworks::render_socials(array(
                                'linkClass' => 'social-icon_item__3SLnb'
                        )); ?>
                    </div>
                    <?php endif; ?>
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