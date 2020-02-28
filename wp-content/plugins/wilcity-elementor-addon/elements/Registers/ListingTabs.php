<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class ListingTabs extends Widget_Base {
	use Helpers;
	public function get_name() {
		return WILCITY_WHITE_LABEL.'-testimonials';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return WILCITY_EL_PREFIX. 'Listing Tabs';
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-picture-o';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'theme-elements' ];
	}

	protected function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );

		$options = [ '' => '' ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}


	protected function _register_controls() {
		$this->start_controls_section(
			'grid_general_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'listing_tabs',
			[
				'label'   => 'Listing Tabs',
				'type'    => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name'      => 'tab_name',
						'label'     => 'Tab Name',
						'type'      => Controls_Manager::TEXT,
						'default'   => ''
					],
					[
						'name'      => 'tab_id',
						'label'     => 'Tab Name',
						'type'      => Controls_Manager::TEXT,
						'default'   => uniqid('tab_')
					],
					[
						'name'      => 'icon',
						'label'     => 'Icon',
						'type'      => Controls_Manager::ICON,
						'default'   => ''
					],
					[
						'name'  => 'toggle_viewmore',
						'label' => 'Toggle Viewmore',
						'type' => Controls_Manager::SELECT,
						'default'=> 'disable',
						'options'     => array(
							'disable'   => 'Disable',
							'enable'    => 'Enable'
						)
					],
					[
						'name'  => 'viewmore_btn_name',
						'label' => 'Viewmore Button',
						'type' => Controls_Manager::TEXT,
						'default'=> 'View All',
						'condition'     => array(
							'toggle_viewmore'   => 'enable'
						)
					],
					[
						'name'      => 'post_type',
						'label'     => 'Post Type',
						'type'      => Controls_Manager::SELECT,
						'default'   => 'listing',
						'options'	=> SCHelpers::getPostTypeOptions()
					],
					[
						'name'  => 'listing_locations',
						'label' => 'Enter in Listing Location IDs',
						'description' => 'Each Listing Location is separated by a comma. For example: 1,2,3',
						'type' => Controls_Manager::TEXT,
						'label_block' => true
					],
					[
						'name'  => 'listing_cats',
						'label' => 'Enter in Listing Category IDs',
						'type' => Controls_Manager::TEXT,
						'label_block' => true
					],
					[
						'name'  => 'listing_tags',
						'label' => 'Enter in Listing Tag IDs',
						'type' => Controls_Manager::TEXT,
						'label_block' => true
					],
					[
						'name'  => 'orderby',
						'label' => 'Order By',
						'type' => Controls_Manager::SELECT,
						'default'=> 'post_date',
						'options'     => array(
							'post_date'     => 'Listing Date',
							'post_title'    => 'Listing Title',
							'menu_order'    => 'Listing Order',
							'best_viewed'   => 'Popular Viewed',
							'best_rated'    => 'Popular Rated',
							'best_shared'   => 'Popular Shared',
							'rand'          => 'Random',
							'premium_listings' => 'Premium Listings',
							'nearbyme'      => 'Near By Me'
						)
					],
					[
						'name'          => 'radius',
						'label'         => 'Radius',
						'description'   => 'Fetching all listings within x radius',
						'value'         => 10,
						'condition'     => array(
							'orderby'   => 'nearbyme'
						),
						'type'          => Controls_Manager::TEXT
					],
					[
						'name'  => 'unit',
						'type'  => Controls_Manager::SELECT,
						'label'         => 'Unit',
						'condition'     => array(
							'orderby'   => 'nearbyme'
						),
						'options'       => array(
							'km'    => 'KM',
							'm'     => 'Miles'
						),
						'default' => 'km'
					]
				]
			]
		);

		$this->add_control(
			'tab_position',
			[
				'name'      => 'tab_position',
				'label'     => 'Tab Position',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'horizontal',
				'options'	=> array(
					'horizontal' => 'Horizontal',
					'vertical'   => 'Vertical'
				)
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => 'Maximum Items',
				'type' => Controls_Manager::TEXT,
				'default'=> 6
			]
		);

		$this->add_control(
			'img_size',
			[
				'label' => 'Image Size',
				'description' => 'For example: 200x300. 200: Image width. 300: Image height',
				'type' => Controls_Manager::TEXT,
				'default'=> 'wilcity_360x200'
			]
		);

		$this->add_control(
			'extra_class',
			[
				'label'         => 'Extra Class',
				'type' => Controls_Manager::TEXT
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'grid_devices_settings',
			[
				'label' => 'Devices Settings',
			]
		);

		$this->add_control(
			'maximum_posts_on_lg_screen',
			[
				'label' => 'Items / row on >=1200px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-lg-4',
				'options'     => array(
					'col-lg-2'  => '6 Items / row',
					'col-lg-3'  => '4 Items / row',
					'col-lg-4'  => '3 Items / row',
					'col-lg-12' => '1 Item / row'
				)
			]
		);

		$this->add_control(
			'maximum_posts_on_md_screen',
			[
				'label'         => 'Items / row on >=960px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-md-3',
				'options'     => array(
					'col-md-2'  => '6 Items / row',
					'col-md-3'  => '4 Items / row',
					'col-md-4'  => '3 Items / row',
					'col-md-12' => '1 Item / row'
				)
			]
		);

		$this->add_control(
			'maximum_posts_on_sm_screen',
			[
				'label'         => 'Items / row on >=720px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-sm-12',
				'options'     => array(
					'col-sm-2'  => '6 Items / row',
					'col-sm-3'  => '4 Items / row',
					'col-sm-4'  => '3 Items / row',
					'col-sm-12' => '1 Item / row'
				)
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$aSettings = $this->get_settings();
		$wrapperClass = 'wilTab_module__jlr12 wil-tab';
		if ( $aSettings['tab_position'] == 'vertical' ){
			$wrapperClass .= ' wilTab_vertical__2iwYo';
		}
		?>
		<div class="<?php echo esc_attr($wrapperClass) ?>">
			<ul class="wilTab_nav__1_kwb wil-tab__nav">
				<?php
				foreach ($aSettings['listing_tabs'] as $order => $aTab){
					?>
					<li><a href="#<?php echo esc_attr($aTab['tab_id']); ?>" class="<?php if($order==0){echo 'active';} ?>"><?php if ( !empty($aTab['icon']) ): ?><i class='<?php echo esc_attr($aTab['icon']); ?>'></i><?php endif; ?> <?php echo esc_html($aTab['tab_name']); ?></a></li>
					<?php
				}
				?>
			</ul>
			<div class="wilTab_content__2j_o5 wil-tab__content">
				<?php
				$aArgs = array(
					'extra_class'=>$aSettings['extra_class'],
					'maximum_posts_on_lg_screen'=>$aSettings['maximum_posts_on_lg_screen'],
					'maximum_posts_on_md_screen'=>$aSettings['maximum_posts_on_md_screen'],
					'maximum_posts_on_sm_screen'=>$aSettings['maximum_posts_on_sm_screen'],
					'img_size'=>$aSettings['img_size'],
					'maximum_posts'=>$aSettings['posts_per_page'],
                    'posts_per_page'=>$aSettings['posts_per_page'],
                    'style' => 'grid',
                    'toggle_viewmore' => 'disable',
                    'viewmore_btn_name' => ''
				);

				if ( $aArgs['toggle_viewmore'] == 'enable' ) : ?>
                    <div class="btn-view-all-wrap clearfix">
                        <a class="btn-view-all wil-float-right" href="<?php echo SCHelpers::getViewAllUrl($aArgs); ?>"><?php echo esc_html($aArgs['viewmore_btn_name']); ?></a>
                    </div>
				<?php endif; ?>
                <?php
				foreach ($aSettings['listing_tabs'] as $order => $aGridSettings) :
				?>
				<div id="<?php echo esc_attr($aGridSettings['tab_id']); ?>" class="wilTab_panel__wznsS wil-tab__panel <?php if($order==0){echo 'active';} ?>">
					<?php
					$aGridSettings = wp_parse_args($aGridSettings, $aArgs);
					wilcity_sc_render_grid($aGridSettings);
					?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
