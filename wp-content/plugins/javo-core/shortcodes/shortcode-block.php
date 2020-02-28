<?php
class jvbpd_block extends Jvbpd_Shortcode_Parse {

	public $items_tag = 'div';
	public $item_tag = 'div';

	public $templates = Array();
	public $column_open = false;

	public function output( $attr, $content='' ) {
		parent::__construct( $attr );
		if('yes'==$this->masonry){
			$this->items_tag = 'ul';
			$this->item_tag = 'li';
		}
		ob_start();
		$this->sHeader();
		?>
		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein">
			<?php
			if( 'taxonomy' != $this->block_type ) : ?>
				<div class="shortcode-header">
					<div class="shortcode-title">
						<div class="title_inner">
						<?php
						$header_title = $this->title;
						if (!empty($this->subtitle)&&'two_titles' == $this->filter_style){
						$header_title .= "<span class='subtitle'>".$this->subtitle."</span>";
						}
						echo $header_title;
						?>
						</div>
					</div>
					<div class="shortcode-nav">
						<?php $this->sFilter(); ?>
					</div>
				</div>
			<?php
			endif;
			$containerClass = apply_filters( 'javo_core/shortcode/block/output/css', Array( 'shortcode-output' ), get_class( $this ) );
			if( 'carousel' === $this->block_display_type ) {
				$containerClass[] = 'swiper-container';
			} ?>
			<div class="<?php echo join(' ', $containerClass); ?>">
				<?php
				$queried = Array();
				if( 'post' == $this->block_type ) {
					$queried = $this->get_post();
				}

				if( 'taxonomy' == $this->block_type ) {
					$queried = $this->get_terms();
				}
				$this->loop( $queried ); ?>
			</div>
			<?php
			if( 'carousel' === $this->block_display_type ) {
				echo $this->getSwiperControls();
			} ?>
		</div>
		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function getSwiperControls() {
		ob_start();
		?>
		<div class="swiper-pagination"></div>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
		<?php
		return ob_get_clean();
	}

	public function parse_queried( $obj ) {
		if( $obj instanceof \WP_Post ) {
			$obj->_id = $obj->ID;
		}
		if( $obj instanceof \WP_Term ) {
			$obj->_id = $obj->term_id;
		}
		return $obj;
	}

	public function loop( $queried ) {
		$output = sprintf('<%s class="row">', $this->items_tag );
		if( 'carousel' === $this->block_display_type ) {
			$output ='<div class="swiper-wrapper">';
		}
		$queried = array_map( Array( $this, 'parse_queried' ), $queried );

		// Query Start
		$column_open = false;
		if( ! empty( $queried ) ) :
			if( ! in_array( $this->layout_type, Array( 'type_d', 'type_e' ) ) ) {
				foreach( $queried as $index => $obj ) {
					if( method_exists( $this, $this->layout_type ) ) {
						$output .= call_user_func_array( Array( $this, $this->layout_type ), Array( $obj, $index ) );
					}
				}
			}else{
				if( method_exists( $this, $this->layout_type ) ) {
					$output .= call_user_func_array( Array( $this, $this->layout_type ), Array( $queried ) );
				}
			}
		endif;

		if( $this->column_open ) {
			$output .= '</div>';
			$this->column_open = false;
		}

		$output .= sprintf('</%s>', $this->items_tag);
		/*
		if( 'carousel' === $this->block_display_type ) {
			$output .= $this->getSwiperControls();
		} */
		echo $output;
		$this->sParams();
		$this->pagination();
	}

	public function getTemplates( $obj, $template ) {
		$wrapper_attribute = '';
		$wrapper_properties = apply_filters( 'javo_core/module/attribute', Array(
			'class' => 'module-item',
		), $obj);

		if( ! array_key_exists( $template, $this->templates ) ) {
			$templateContent = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( "{$this->$template}" );
			if('' == $templateContent) {
				$templateContent = '<div class="alert alert-warning" role="alert">';
				$templateContent .= esc_html__("Please select a module", 'jvfrmtd');
				$templateContent .= '</div>';
			}
			$this->templates[ $template ] = $templateContent;
		}
		if('yes'== $this->module_click_popup){
			$wrapper_properties['data-popup-post-id'] = $obj->_id;
		}
		foreach($wrapper_properties as $property => $value) {
			$wrapper_attribute .= sprintf('%s="%s"', $property, esc_attr($value)) . ' ';
		}
		$wrapper = '<div ' . $wrapper_attribute . '>';
		$wrapper .= $this->templates[ $template ];
		$wrapper .= '</div>';
		return $wrapper;
	}

	public function type_a( $obj, int $index=0 ) {
		$output = '';

		switch( intVal( $this->columns ) ) {
			case 2: $output .= '<div class="col-md-6">'; break;
			case 3: $output .= '<div class="col-md-4">'; break;
			case 4: $output .= '<div class="col-md-3">'; break;
			case 6: $output .= '<div class="col-md-2">'; break;
			default: $output .= '<div class="col-md-12">';
		}

		if( 'carousel' === $this->block_display_type ) {
			$output = '<div class="swiper-slide">';
		}
		if('yes'==$this->masonry){
			$output = sprintf('<%s class="masonry-item">', $this->item_tag);
		}

		$instance = new Jvbpd_Replace_Content( $obj->_id, $this->getTemplates( $obj, 'column_1' ), $this->block_type, $this->filter_by );
		$output .= $instance->render();
		$output .= sprintf('</%s>', $this->item_tag);
		return $output;
	}

	public function type_b( $obj, int $index=0 ) {
		$output = '';
		if( 0 == $index ) {
			$instance = new Jvbpd_Replace_Content( $obj->_id, $this->getTemplates( $obj, 'column_1' ), $this->block_type, $this->filter_by );
			$output .= '<div class="col-md-6">';
				$output .= $instance->render();
			$output .= '</div><div class="col-md-6">';
			$this->column_open = true;
		}else {
			$instance = new Jvbpd_Replace_Content( $obj->_id, $this->getTemplates( $obj, 'column_2' ), $this->block_type, $this->filter_by );
			$output .= $instance->render();
		}
		return $output;
	}

	public function type_c( $obj, int $index=0 ) {
		$output = '';
		if( intVal( $this->columns ) <= $index ) {
			$instance = new Jvbpd_Replace_Content( $obj->_id, $this->getTemplates( $obj, 'column_2' ), $this->block_type, $this->filter_by );
		}else{
			$instance = new Jvbpd_Replace_Content( $obj->_id, $this->getTemplates( $obj, 'column_1' ) , $this->block_type, $this->filter_by );
		}
		switch( intVal( $this->columns ) ) {
			case 2: $output .= '<div class="col-md-6">'; break;
			case 3: $output .= '<div class="col-md-4">'; break;
			case 4: $output .= '<div class="col-md-3">'; break;
			case 6: $output .= '<div class="col-md-2">'; break;
			default: $output .= '<div class="col-md-12">';
		}
			$output .= $instance->render();
		$output .= '</div>';
		return $output;
	}

	public function type_d( array $queried_posts=Array() ) {
		$output = '';
		for( $iColumn=1; $iColumn<=4; $iColumn++ ) {
			switch( $iColumn ) {
				case 1: $output .= '<div class="col-md-6">'; break;
				case 2: $output .= '<div class="col-md-6"> <div class="row"><div class="col-md-12">'; break;
				case 3: $output .= '</div><div class="row"><div class="col-md-6">'; break;
				case 4: $output .= '<div class="col-md-6">'; $this->column_open = true; break;
			}
			if( !empty( $queried_posts[ $iColumn ] ) ) {
				$instance = new Jvbpd_Replace_Content( $queried_posts[ $iColumn ]->_id, $this->getTemplates( $obj, 'column_1' ), $this->block_type, $this->filter_by );
				$output .= $instance->render();
			}
			$output .= '</div>';
		}
		return $output;
	}

	public function type_e( array $queried_posts=Array() ) {
		$output = '';
		for( $iColumn=1; $iColumn<=5; $iColumn++ ) {
			switch( $iColumn ) {
				case 1: $output .= '<div class="col-md-6">'; break;
				case 2: $output .= '<div class="col-md-6"> <div class="row"><div class="col-md-6">'; break;
				case 3: $output .= '<div class="col-md-6">'; break;
				case 4: $output .= '</div><div class="row"><div class="col-md-6">'; break;
				case 5: $output .= '<div class="col-md-6">'; $this->column_open = true; break;
			}
			if( !empty( $queried_posts[ $iColumn ] ) ) {
				$instance = new Jvbpd_Replace_Content( $queried_posts[ $iColumn ]->_id, $this->getTemplates( $obj, 'column_1' ), $this->block_type, $this->filter_by );
				$output .= $instance->render();
			}
			$output .= '</div>';
		}
		return $output;
	}

}