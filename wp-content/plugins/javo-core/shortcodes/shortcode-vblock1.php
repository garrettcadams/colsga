<?php
class jvbpd_vblock1 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>
		<div id="<?php echo $this->sID; ?>" class="shortcode-container no-flex-menu fadein exists-wrap">
			<div class="shortcode-header">
				<div class="shortcode-title">
					<?php echo $this->title; ?>
				</div>
			</div>
			<div class="shortcode-content">
				<div class="shortcode-nav">
					<?php $this->sFilter(); ?>
				</div>
				<div class="shortcode-output">
					<?php $this->loop( $this->get_post() ); ?>
				</div>
				<div class="shortcode-banner">
					<?php $this->column3(); ?>
				</div>
			</div>
		</div>
		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{
		$columns		= intVal( $this->columns );

		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) {
			$arrModuleParam		= Array();
			switch( $columns ) :
				case 2:
					echo "<div class='col-md-6 col-sm-6'>";
					$arrModuleParam[ 'thumbnail_size' ] = 'jvbpd-box';
				break;
				case 3:
					echo "<div class='col-md-4 col-sm-6'>";
					$arrModuleParam[ 'thumbnail_size' ] = 'jvbpd-box-v';
				break;
				case 1:
				default:
					echo "<div class='col-md-12'>";
			endswitch;
				$javoArticle12	= new module4( $post, $arrModuleParam );
				echo $javoArticle12->output();
			echo "</div>";
		} endif;
		$this->pagination();
		$this->sParams();
	}

	public function column3(){
		if( class_exists( 'lynk_slider3' ) ) {
			$objShortcode	= new lynk_slider5;
			echo $objShortcode->output(
				Array(
					'colmn'			=> 1,
					'post_type'		=> $this->post_type,
					'primary_color'	=> $this->primary_color,
					'order_by'		=> 'rand',
					'featured_' . $this->post_type => '1'

				)
			);
		}
	}
}