<?php
class jvbpd_slider4 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein is-slider">

			<div class="shortcode-output" ><?php $this->loop( $this->get_post() ); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function virtual_nav() {
		?>
		<div class="slider-header">
			<div class="slider-title"></div>
			<div class="slider-control">
				<ul class="flex-direction-nav">
					<li class="flex-nav-prev">
						<a class="flex-prev" href="#"><?php _e( "Previous", 'jvfrmtd' );?></a>
					</li>
					<li class="flex-nav-next">
						<a class="flex-next" href="#"><?php _e( "Next", 'jvfrmtd' ); ?></a>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	public function loop( $queried_posts )
	{
		echo "<div class='slider-wrap flexslider'>";
			$this->virtual_nav();
			echo "<ul class='slides'>";
			$slide_open		= false;
			// Query Start
			if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
				$objModuleBigGrid	= new moduleBigGrid( $post, Array( 'no_lazy' => true ) );
				printf( "<li class=\"%s\"><div class=\"row\">", $index==0? 'first' : false );
				printf( "<div class=\"col-md-12 inner-item\">%s</div>", $objModuleBigGrid->output() );
				echo "</li>";
			endforeach; endif;
			if( $slide_open )
				echo "</li>";
			echo "</ul>";
		echo '</div>';
		$this->sParams();
		$this->pagination();
	}
}