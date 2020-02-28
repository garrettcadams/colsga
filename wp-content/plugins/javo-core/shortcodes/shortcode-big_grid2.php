<?php
class jvbpd_big_grid2 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		$this->fixCount			= 5;
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
			<div class="row shortcode-output" ><?php $this->loop( $this->get_post() ); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{

		$column_lv1_open = $column_lv2_open = false;

		// Query Start
		if( ! empty( $queried_posts ) ) :
			echo "<div class=\"col-md-6\">";
				if( !empty( $queried_posts[0] ) ) {
					$objoutput	= new moduleBigGrid( $queried_posts[0] );
					echo $objoutput->output();
				}
			echo "</div>";
			echo "<div class=\"col-md-6\">";
				echo "<div class=\"row\">";
					echo "<div class=\"col-sm-6\">";
						if( !empty( $queried_posts[1] ) ) {
							$objoutput	= new moduleSmallGrid( $queried_posts[1] );
							echo $objoutput->output();
						}
					echo "</div>";
					echo "<div class=\"col-sm-6\">";
						if( !empty( $queried_posts[2] ) ) {
							$objoutput	= new moduleSmallGrid( $queried_posts[2] );
							echo $objoutput->output();
						}
					echo "</div>";
				echo "</div>";
				echo "<div class=\"row\">";
					echo "<div class=\"col-sm-6\">";
						if( !empty( $queried_posts[3] ) ) {
							$objoutput	= new moduleSmallGrid( $queried_posts[3] );
							echo $objoutput->output();
						}
					echo "</div>";
					echo "<div class=\"col-sm-6\">";
						if( !empty( $queried_posts[4] ) ) {
							$objoutput	= new moduleSmallGrid( $queried_posts[4] );
							echo $objoutput->output();
						}
					echo "</div>";
				echo "</div>";
			echo "</div>";
		endif;
		$this->sParams();
		$this->pagination();
	}
}