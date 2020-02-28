<?php
/**
 *
 *	000 Big Grid Type
 * @since	1.0
 */
class moduleBigGrid extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 5;
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
					<?php echo $this->thumbnail( 'jvbpd-medium', true, false ); ?>
				</a>
			</div>
			<div class="jv-grid-meta">
				<div class="meta-category"><?php echo $this->category(); ?></div>
				<h4 class="meta-title"><?php echo $this->title; ?></h4>
				<div class="more-meta"><?php echo $this->addtionalInfo(); ?></div>
			</div>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}