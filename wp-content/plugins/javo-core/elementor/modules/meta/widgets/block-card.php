<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Block_Card extends Base {

	public function get_name() { return 'jvbpd-block-card'; }
	public function get_title() { return 'Block Card'; }
	public function get_icon() { return 'eicon-image-rollover'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
		parent::__card_register_controls();
		$this->update_controls();
	}

	protected function render() { parent::__card_render(); }
	protected function content_template() { parent::__card_content_template(); }

	public function update_controls() {

		$this->update_control( 'title', Array(
			'label' => __( 'Title & Description', 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'This is the heading', 'jvfrmtd' ),
			'placeholder' => __( 'Your Title', 'jvfrmtd' ),
			'label_block' => true,
			'separator' => 'before',
		) );

		$this->update_control( 'description', Array(
			'label' => __( 'Description', 'jvfrmtd' ),
			'type' => Controls_Manager::TEXTAREA,
			'default' => __( 'Click000 edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'jvfrmtd' ),
			'placeholder' => __( 'Your Description', 'jvfrmtd' ),
			'title' => __( 'Input image text here', 'jvfrmtd' ),
			'separator' => 'none',
			'rows' => 5,
			'show_label' => false,
		) );

		$this->remove_control( 'title_length' );
		$this->remove_control( 'description_length' );
		$this->remove_control( 'section_badge' );

	}
}
