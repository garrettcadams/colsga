<?php
/*
 * Template Name: Wilcity Event Plans
 */
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use \WilokeListingTools\Framework\Helpers\Message as WilcityMessage;

get_header();

if ( have_posts() ){
	while (have_posts()){
		the_post();
		$eventPlans = GetWilokeSubmission::getField('event_plans');
		if ( empty($eventPlans) ) {
			WilcityMessage::error(esc_html__('There are no event plans. Please go to Wiloke Submission -> Event Plans -> Complete this setting', 'wilcity'));
		}else{
			echo do_shortcode('[wilcity_pricing post_type="event_plan" include="'.esc_attr($eventPlans).'"]');
		}
	}
}
wp_reset_postdata();

get_footer();