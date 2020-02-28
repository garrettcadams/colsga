<?php
class jvbpd_slider1 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
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
			<div class="shortcode-output" ><?php $this->loop( $this->get_post() ); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{
		echo "<div class='slider-wrap flexslider'>";
			echo "<ul class='slides'>";
			$slide_open		= false;
			// Query Start
			if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
				$objModule12	= new module12( $post, Array( 'no_lazy' => true ) );
				if( $index % 3 == 0 && !$slide_open ) {
					printf( "<li class=\"%s\"><div class=\"row\">", $index==0? 'first' : false );
					$slide_open = true;
				}
				printf( "<div class=\"col-md-4\">%s</div>", $objModule12->output() );
				if( ( $index + 1) % 3 == 0 && $slide_open ) {
					echo "</div></li>";
					$slide_open = false;
				}
			endforeach; endif;
			if( $slide_open )
				echo "</div></li>";
			echo "</ul>";
		echo '</div>';
		$this->sParams();
		$this->pagination();
	}
}