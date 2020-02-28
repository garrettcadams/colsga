<?php
class jvbpd_slider3 extends Jvbpd_Shortcode_Parse {

	public function output( $attr, $content='' ) {
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein is-slider">
			<div class="shortcode-header">
				<div class="shortcode-title">
					<?php echo $this->title; ?>
				</div>
				<div class="shortcode-nav">
					<?php $this->sFilter(); ?>
				</div>
			</div>
			<div class="shortcode-output"><?php $this->loop( $this->get_post() ); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts ) {

		$columns		= intVal( $this->columns );
		switch( $columns ){
			case 1: $strLoopFormat = "<div class=\"col-md-12 inner-item\">%s</div>"; break;
			case 2: $strLoopFormat = "<div class=\"col-md-6 inner-item\">%s</div>"; break;
			case 3:
			default:
				$strLoopFormat = "<div class=\"col-md-4 inner-item\">%s</div>"; break;
		}

		echo '<div class="slider-wrap flexslider">';
			echo '<ul class="slides">';
			$slide_open = false;

			// Query Start
			if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
				$objModuleBigGrid = new moduleBigGrid( $post, Array( 'no_lazy' => true ) );
				if( $index % $columns == 0 && !$slide_open ) {
					printf( "<li class=\"%s\">", $index==0? 'first' : false );
					$slide_open = true;
				}
				printf( $strLoopFormat, $objModuleBigGrid->output() );
				if( ( $index + 1) % $columns == 0 && $slide_open ) {
					echo '</li>';
					$slide_open = false;
				}
			endforeach; endif;

			if( $slide_open ) {
				echo '</li>';
			}
			echo "</ul>";
		echo '</div>';
		$this->sParams();
		$this->pagination();
	}
}