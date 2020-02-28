<?php
class jvbpd_block3 extends Jvbpd_Shortcode_Parse
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

		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) {

			$objModule8	= new module8( $post );
			switch( $columns ) :
				case 2: echo "<div class='col-md-6 col-sm-6'>"; break;
				case 3: echo "<div class='col-md-4 col-sm-6'>"; break;
				case 4: echo "<div class='col-md-3 col-sm-6'>"; break;
				case 1:
				default: echo "<div class='col-md-12'>";
			endswitch;
				echo $objModule8->output();
			echo "</div>";
		} endif;

		$this->sParams();
		$this->pagination();

	}
}