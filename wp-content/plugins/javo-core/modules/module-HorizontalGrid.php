<?php
/**
 *
 *	000 Horizontal Grid Type
 * @since	1.0
 */
class moduleHorizontalGrid extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle		= 10;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes(); ?>>
			<?php $this->before(); ?>
			<div class="effect-wrap jv-thumb thumb-wrap">
				<a href="<?php echo $this->permalink;?>">
					<?php echo $this->thumbnail( 'jvbpd-huge', false, false ); ?>
				</a>
				<div class="meta-category"><?php echo $this->category(); ?></div>
			</div>
			<div class="jv-grid-meta">
				<h4 class="meta-title"><?php echo $this->title; ?></h4>
				<div class="more-meta"><?php echo $this->addtionalInfo(); ?></div>
			</div>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}