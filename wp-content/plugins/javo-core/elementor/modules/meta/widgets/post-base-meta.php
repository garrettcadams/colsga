<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

class Post_Base_Meta extends Base {
	public function get_name() { return parent::POST_META; }
	public function get_title() { return 'Post Meta'; }
	public function get_icon() { return 'eicon-sidebar'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
		parent::_register_controls();
		$this->update_controls();
		$this->add_label_settings_control();
	}

	protected function render() {
		$this->_render();
	}

	public function getModuleMeta() {
		$field = $this->get_settings( 'meta' );
		return $this->getFieldMeta( $field );
	}

	protected function update_controls() {
		$this->update_control( 'field_type', Array(
			'type' => Controls_Manager::HIDDEN,
			'default' => 'base',
		) );
		$this->update_control( 'meta', Array(
			'condition' => Array(),
			'default' => 'txt_title',
			'options' => Array(
				'txt_title' => esc_html__( "Title", 'jvfrmtd' ),
				'txt_content' => esc_html__( "Content", 'jvfrmtd' ),
				'_featured' => esc_html__( "Featured", 'jvfrmtd' ),
				'post_author' => esc_html__( "Author", 'jvfrmtd' ),
				'post_date' => esc_html__( "Date", 'jvfrmtd' ),
				'post_category' => esc_html__( "Category", 'jvfrmtd' ),
				'post_tag' => esc_html__( "Tags", 'jvfrmtd' ),
			),
		) );
	}
}