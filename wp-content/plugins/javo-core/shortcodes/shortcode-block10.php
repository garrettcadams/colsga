<?php
class jvbpd_block10 extends Jvbpd_Shortcode_Parse
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
		$columns		= intVal( $this->columns );

		$column2_open	 = $column3_open	= false;

		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) :
			$objModule2					= new module2( $post,
				Array(
					'thumbnail_size'	=> 'jvbpd-huge',
				)
			);
			$objModule1					= new module1( $post,
				Array(
					'length_title'			=> 15,
					'hide_content'		=> true,
					'thumbnail_size'	=> Array( 80, 80 ),
				)
			);

			switch( $columns ) {

				case 1:
					echo "<div class='col-md-12'>";
						echo $index == 0 ? $objModule2->output() : $objModule1->output();
					echo "</div>";
				break;

				case 2:

				if( $index == 0 ) {
						echo "<div class='col-md-6'>";
						echo $objModule2->output();
						echo "</div>";
					}else{
						if( ! $column2_open ) {
							$column2_open = true;
							echo "<div class='col-md-6'>";
						}
						echo $objModule1->output();
					}
				break;

				case 3:
					if( $index == 0 ) {
						echo "<div class='col-md-4'>";
						echo $objModule2->output();
						echo "</div>";
					}else{
						if( ! $column3_open ) {
							$column3_open = true;
							echo "<div class='col-md-4'>";
						}
						echo $objModule1->output();
						if( $index == 4 ){
							$column3_open = false;
							echo "</div>";
						}
					}
				break;

				case 4:
					if( $index == 0 ) {
						echo "<div class='col-md-3'>";
						echo $objModule2->output();
						echo "</div>";
					}else{
						if( ! $column3_open ) {
							$column3_open = true;
							echo "<div class='col-md-3'>";
						}
						echo $objModule1->output();
						if( $index % 4 == 0 ){
							$column3_open = false;
							echo "</div>";
						}
					}
				break;
			}
		endforeach; endif;
		if( $column2_open || $column3_open )
			echo "</div>";

		$this->sParams();
		$this->pagination();
	}
}