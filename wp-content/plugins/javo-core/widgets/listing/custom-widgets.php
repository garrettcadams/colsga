<?php
/**
Widget Name: Property ACF widget ( not used )
Author: Javo
Version: 1.0.0.0
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_pracf extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'jvbpd-pr1';
	}

	public function get_title() {
		return 'Pro1';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-gallery-grid';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
protected function _register_controls() {

//start of a control box
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Category Block', 'jvfrmtd' ),   //section name for controler view
			]
		);		
		$this->end_controls_section();
	}


	protected function render() {				//to show on the fontend
		$settings = $this->get_settings();
    ?>
		<ul>
			<li><?php the_field('lv_default_price'); ?></li>
			<li><?php the_field('lv_default_price_second'); ?></li>
			<li><?php the_field('lv_price_prefix'); ?></li>
			<li><?php

				if( !empty($pr1_image) ): ?>

					<img src="<?php echo $pr1_image['url']; ?>" alt="<?php echo $pr1_image['alt']; ?>" />

				<?php endif; ?>

			</li>
			<li><?php the_field('bedrooms'); ?></li>
			<li><?php the_field('lv_bathrooms'); ?></li>
			<li><?php the_field('lv_garages'); ?></li>
			<li><?php the_field('lv_garages_size'); ?></li>
			<li><?php the_field('lv_area'); ?></li>
			<li><?php the_field('lv_area_size_prefix'); ?></li>
			<li><?php the_field('lv_land_area'); ?></li>
			<li><?php the_field('lv_land_size_prefix'); ?></li>
			<li><?php the_field('lv_property_id'); ?></li>
			<li>
				<?php
				// get raw date
				$date = get_field('lv_built_year', false, false);

				// make date object
				$date = new \DateTime($date);

				?>
				<p>Event start date: <?php echo $date->format('j M Y'); ?></p>
				<?php
				// increase by 1 day
				$date->modify('+1 day');
				?>
			</li>
		</ul>

		<?php if( have_rows('floor_plan_details') ): ?>

		<ul class="slides">

		<?php while( have_rows('floor_plan_details') ): the_row();

			// vars
			$plan_title = get_sub_field('plan_title');
			$plan_bedrooms = get_sub_field('plan_bedrooms');
			$plan_bathrooms = get_sub_field('plan_bathrooms');
			$plan_price = get_sub_field('plan_price');
			$price_postfix = get_sub_field('price_postfix');
			$plan_size = get_sub_field('plan_size');
			$plan_image = get_sub_field('plan_image');
			$plan_description = get_sub_field('plan_description');
			?>

			<li class="slide">
			    <?php echo $plan_title; ?>
			    <?php echo $plan_bedrooms; ?>
			    <?php echo $plan_bathrooms; ?>
			    <?php echo $plan_price; ?>
			    <?php echo $price_postfix; ?>
			    <?php echo $plan_size; ?>
					<img src="<?php echo $plan_image['url']; ?>" alt="<?php echo $plan_image['alt'] ?>" />

			    <?php //echo $plan_image; ?>
			    <?php echo $plan_description; ?>
			</li>

		<?php endwhile; ?>

		</ul>

<?php endif; ?>


		<?php
		//ob_get_clean();
		$content = ob_get_clean();
		echo $content;
    }

}
