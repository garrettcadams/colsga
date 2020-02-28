<?php
class jvbpd_slider2 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein is-slider circle-nav slide-show">
			<div class="shortcode-output" ><?php $this->loop( $this->get_post() ); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{
		echo "<div class='slider-wrap flexslider'>";
			echo "<ul class='slides'>";
			// Query Start
			if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
				$objModuleBigGrid	= new module3( $post, Array( 'no_lazy' => true ) );
				printf( "<li class=\"%s\">", $index==0? 'first' : false );
					echo $objModuleBigGrid->output();
				echo "</li>";
			endforeach; endif;
			echo "</ul>";
		echo '</div>';
		$this->sParams();
		$this->pagination();
	}
}