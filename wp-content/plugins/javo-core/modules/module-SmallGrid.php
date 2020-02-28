<?php
/**
 *
 *	000 Smail Grid Type
 * @since	1.0
 */
class moduleSmallGrid extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 8;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes(); ?>>
			<?php $this->before(); ?>
			<div class="effect-wrap jv-thumb">
				<a href="<?php echo $this->permalink;?>">
				<?php
				$jvbpd_thumbnail_size = 'jvbpd-large';
				if( isset( $this->shortcode_args[ 'columns' ] ) ){
					$lynk_shortcode_column = $this->shortcode_args[ 'columns' ];
					switch($lynk_shortcode_column){
						case 3 : $jvbpd_thumbnail_size = 'jvbpd-box-v'; break;
						case 2 : $jvbpd_thumbnail_size = 'jvbpd-huge'; break;
						case 1 : $jvbpd_thumbnail_size = 'jvbpd-item-detail'; break;
					}
				}
				?>
					<?php echo $this->thumbnail( $jvbpd_thumbnail_size , false, false ); ?>
				</a>
				<div class="meta-category"><?php echo $this->category(); ?></div>
			</div>
			<div class="jv-grid-meta">
				<h4 class="meta-title"><?php echo $this->title; ?></h4>
			</div>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}