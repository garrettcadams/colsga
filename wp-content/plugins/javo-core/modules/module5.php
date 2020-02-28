<?php
/**
 *
 *
 * @since	1.0
 */

class module5 extends Jvbpd_Module
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
			<div class="jv-thumb thumb-wrap">
				<a href="<?php echo $this->permalink;?>">
					<?php echo $this->thumbnail( Array(700,400), false, false ); ?>
				</a>
			</div>
			<div class="jv-grid-meta">
				<div class="meta-category"><?php echo $this->category(); ?></div>
				<h4 class="meta-title primary-bg-a">
					<?php echo $this->title; ?>
					<div class="more-meta"><?php echo $this->addtionalInfo(); ?></div>
				</h4>

			</div>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}