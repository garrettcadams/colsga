<?php
class jvbpd_vslider1 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein is-slider slider-vertical slide-show">
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

	public function loop( $queried_posts )
	{
		$slide_open		= false;
		echo "<div class='slider-wrap flexslider'>";
			echo "<ul class='slides'>";
			// Query Start
			if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
				$objModuleBigGrid	= new module1( $post, Array( 'no_lazy' => true, 'hide_content' => true ) );
				if( $index++ % 2 == 0 ) {
					printf( "<li class='%s'>", ( $index == 1 ? 'first' : false ));
					$slide_open = true;
				}
				echo $objModuleBigGrid->output();
				if( $index % 2 == 0 && $slide_open ){
					echo "</li>";
					$slide_open = false;
				}
			endforeach; endif;
			if( $slide_open ){
				echo "</li>";
				$slide_open = false;
			}
			echo "</ul>";
		echo '</div>';

		$this->sParams();
		$this->pagination();
	}
}