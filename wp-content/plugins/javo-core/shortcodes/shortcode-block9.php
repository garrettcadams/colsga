<?php
class jvbpd_block9 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein">
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
		echo "<div class=\"row\">";

		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) {

			$objModule2	= new module2( $post );
			$objModule1	= new module1( $post,
				Array(
					'hide_content'		=> true,
					'thumbnail_size'	=> Array( 80, 80 ),
				)
			);

			if( $index == 0 ) {
				echo "<div class='col-md-6'>";
				echo $objModule2->output();
				echo "</div><!-- /.col-md-6 -->";
			}else if( $index == 1 ) {
				echo "<div class='col-md-6'>";
				echo $objModule2->output();
				echo "</div><!-- /.col-md-6 -->";
				echo "</div><!-- /.row -->";
				echo "<div class=\"row\">";
			}else{
				echo "<div class='col-md-6'>";
				echo $objModule1->output();
				echo "</div>";
			}
		} endif;

		echo "</div>";

		$this->sParams();
		$this->pagination();
	}
}