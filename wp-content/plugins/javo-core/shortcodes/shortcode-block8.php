<?php
class jvbpd_block8 extends Jvbpd_Shortcode_Parse
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
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
			$objModuleGrid	= new moduleSmallGrid( $post,
				Array(
					'hide_content'		=> true,
					'length_title'		=> 10,
					'thumbnail_size'	=> 'jvbpd-large',
				)
			);
			$objModule1		= new module1( $post,
				Array(
					'length_title'			=> 15,
					'hide_content'		=> true,
					'thumbnail_size'	=> Array( 80, 80 ),
				)
			);

			switch( $columns ) :

				case 2:
					echo "<div class=\"col-md-6\">";
					if( $index < 2 ) {
						echo $objModuleGrid->output();
					}else{
						echo $objModule1->output();
					}
					echo "</div>";
					break;
				case 1:
				default:
					echo "<div class=\"col-md-12\">";
						if( 0 == $index )
							echo $objModuleGrid->output();
						else
							echo $objModule1->output();
					echo "</div>";
			endswitch;
		endforeach; endif;
		$this->sParams();
		$this->pagination();
	}
}