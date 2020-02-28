<?php
class jvbpd_block14 extends Jvbpd_Shortcode_Parse
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
			<div class="row shortcode-output" ><?php $this->loop( $this->get_post() ); ?> </div>
		</div>
		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{
		$columns					= intVal( $this->columns );

		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) {

			$objModule1			= new module1(
				$post,
				Array(
					'thumbnail_size'	=> Array( 80, 80 ),
					'hide_content'		=> true
				)
			);
			$objModule2			= new module2( $post );

			echo "<div class=\"col-md-12\">";
			if( $index == 0 ) {
				echo $objModule2->output();
				echo "<hr>";
			}else{
				echo $objModule1->output();
			}
			echo "</div>";
		} endif;

		$this->sParams();
		$this->pagination();

	}
}