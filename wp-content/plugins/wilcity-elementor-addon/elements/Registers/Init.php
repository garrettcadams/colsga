<?php

namespace WILCITY_ELEMENTOR\Registers;

class Init {
	public function __construct() {
		add_action( 'elementor/widgets/widgets_registered', array($this, 'on_widgets_registered') );
		add_action( 'elementor/frontend/after_register_scripts', function() {
			wp_register_script( 'wilcity-hero', plugin_dir_url(__FILE__) . 'js/hero.js', [ 'jquery' ], false, true );
		} );

	}

	public function on_widgets_registered() {
		$this->register_widget();
	}
//La Despensa de Eva Barcelona, Carrer d'Aribau, 254, 08006, Barcelona, Spain

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Heading() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new SearchForm() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Hero() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new EventsGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RestaurantListings() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TermBoxes() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RectangleTermBoxes() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ModernTermBoxes() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MasonryTermBoxes() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ListingsSlider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new EventsSlider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Pricing() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new BoxIcon() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Testimonials() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Canvas() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ContactUs() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new IntroBox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new TeamIntroSlider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ListingTabs() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ListingsTabs() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new CustomLogin() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new AuthorSlider() );
	}
}
