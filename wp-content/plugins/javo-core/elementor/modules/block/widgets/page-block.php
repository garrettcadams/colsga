<?php
namespace jvbpdelement\Modules\Block\Widgets;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;

class page_block extends Base {

	Const PREFIX = 'jv_bpd_block2_';

	public function get_name() { return parent::PAGE_BLOCK; }
	public function get_title() { return 'Post Block'; }
	public function get_icon() { return 'eicon-posts-group'; }

	protected function _register_controls() {

		$this->add_block_header_settings_controls();

		parent::_register_controls();

		$this->add_carousel_settings_controls();
		$this->add_filter_style_controls();

		$this->add_archive_block_settings_controls();

		$this->start_controls_section( 'section_module_extra_style', Array(
			'label' => __( 'Module', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );


		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'Title Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} a.module-card:hover .module-card__title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .module-card:hover .module-card__title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .single-item .item-value a:hover' => 'color: {{VALUE}}',
				],
				'description' => __( '<br/>It overwrites module hover color setting.', 'jvfrmtd' ),
			]
		);

		$this->end_controls_section();

		$this->start_injection( Array(
			'type' => 'section',
			'of' => 'section_post_type',
		) );
			$this->add_control( 'post__in_ids', Array(
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'By Post ID', 'jvfrmtd' ),
				'default' => '',
				'description' => __('Display specific listings by post id. (ex) 9031,9099,9022) It will not work with other selections', 'jvfrmtd'),
			) );

			$this->add_control( 'archive_page', Array(
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Is archive page', 'jvfrmtd' ),
				'default' => '',
			) );

		$this->end_injection();

		$this->add_masonry_contorls();
	}

	public function add_archive_block_settings_controls() {
		$this->start_injection( Array(
			'of' => 'section_post_type',
			'at' => 'start',
			'type' => 'section',
		) );

		$this->add_control( 'block_type', Array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Block Type', 'jvfrmtd' ),
			'default' => 'post',
			'options' => Array(
				'post' => esc_html__( "Post Block", 'jvfrmtd' ),
				'taxonomy' => esc_html__( "Taxonomy Block", 'jvfrmtd' ),
			)
		) );
		$this->end_injection();
	}

	protected function render() {

		$settings = $this->get_settings();
		$post_type = $this->get_settings( 'post_type' );
		$shortcode_attributes = '';
		$shortcode_attributes_args = Array(
			'title' => $this->get_settings( 'header_title' ),
			'subtitle' => $this->get_settings( 'subtitle' ),
			'block_type' => $this->get_settings( 'block_type' ),
			'post_type' => $this->get_settings( 'post_type' ),
			'carousel' => $this->getSliderOption(),
			'masonry' => $this->get_settings( 'use_masonry' ),
			'featured_' . $post_type => $this->get_settings( 'only_featured' ),

			'post__in' => $this->get_settings( 'post__in_ids' ),
			'module_click_popup' => $this->get_settings( 'module_click_popup' ),

			'module_contents_length' => $this->get_settings( 'contents_length' )['size'],
			'filter_style' => $this->get_settings( 'filter_style' ),

			'order_' => $this->get_settings( 'order_type' ),
			'order_by' => $this->get_settings( 'order_by' ),

			'filter_by' => $this->get_settings( $post_type . '_taxonomy' ),

			'columns' => $this->get_settings( 'block_columns' ),
			'count' => $this->get_settings( 'contents_count' )['size'],
			'loading_style' => $this->get_settings( 'loading_style' ),
			'pagination' => $this->get_settings( 'pagination' ),
			'title_text_transform' => $this->get_settings( 'title_text_transform' ),
			'block_display_type' => $this->get_settings( 'use_carousel' ) === 'yes' ? 'carousel' : null,
		);

		$this->add_render_attribute('_wrapper', 'data-effect', $this->get_settings('masonry_ani'));

		$terms_key = sprintf( '%1$s_%2$s_term', $post_type, $shortcode_attributes_args[ 'filter_by' ] );
		$custom_terms = $this->get_settings( $terms_key );
		if( !empty( $custom_terms ) ) {
			$custom_terms = is_array( $custom_terms ) ? join( ',', $custom_terms ) : '';
			$shortcode_attributes_args[ 'custom_filter_by_post' ] = true;
			$shortcode_attributes_args[ 'custom_filter' ] = $shortcode_attributes_args[ 'filter_terms' ] = $custom_terms;
		}

		if('yes' == $this->get_settings('display_custom_post_id')){
			$listingID = get_the_ID();
			$custom_posts = get_post_meta($listingID, '_post_block_custom_id', true);
			if(!empty($custom_posts)){
				$shortcode_attributes_args[ 'post_type' ] = 'any';
				$shortcode_attributes_args[ 'post__in' ] = $custom_posts;
				$shortcode_attributes_args[ 'order_by' ] = 'post__in';
				$shortcode_attributes_args[ 'order_' ] = 'DESC';
			}
		}

		if( 'yes' == $this->get_settings( 'archive_page' ) ) {
			$queried = get_queried_object();
			if( $queried instanceof \WP_Term ) {
				$shortcode_attributes_args[ 'taxonomy' ] = $queried->taxonomy;
				$shortcode_attributes_args[ 'term_id' ] = $queried->term_id;
				$shortcode_attributes_args[ 'filter_by' ] = $shortcode_attributes_args[ 'filter_terms' ] = $queried->taxonomy;
				$shortcode_attributes_args[ 'custom_filter' ] = $shortcode_attributes_args[ 'filter_terms' ] = join( ',', get_terms( Array( 'taxonomy' => $queried->taxonomy, 'parent' => $queried->term_id, 'fields' => 'ids' ) ) );
			}
		}

		$shortcode_attributes_args[ 'layout_type' ] = $this->get_settings( 'module_layout_type' );

		for( $columnI=1;$columnI<=4;$columnI++ ) {
			$shortcode_attributes_args[ 'column_' . $columnI ] = $this->get_settings( 'column' . $columnI . '_module' );
		}

		$this->add_render_attribute( 'params', $shortcode_attributes_args );

		$str_shortcode = sprintf(
			// '[jvbpd_%1$s %2$s]',
			'[jvbpd_block %2$s]',
			$this->get_settings( 'block_name' ),
			$this->get_render_attribute_string( 'params' )
		);

		add_filter( \Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'rating_query' ), 10, 2 );
		echo do_shortcode( $str_shortcode );
		remove_filter( \Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'rating_query' ), 10, 2 );
    }

	public function rating_query( $args, $obj ) {
		if( 'rating' != $this->get_settings( 'order_by' ) ) {
			return $args;
		}

		$args[ 'meta_query' ] = Array(
			'relation' => 'OR',
			'rating' => Array(
				'key' => 'rating_average',
				'type' => 'NUMERIC',
				'compare' => 'EXISTS',
			),
			'rating_not' => Array(
				'key' => 'rating_average',
				'type' => 'NUMERIC',
				'compare' => 'NOT_EXISTS',
			),
		);

		$args[ 'orderby' ] = Array(
			'rating' => $this->get_settings( 'order_type' ),
			'rating_not' => $this->get_settings( 'order_type' ),
		);

		unset( $args[ 'order' ] );

		return $args;
	}
}