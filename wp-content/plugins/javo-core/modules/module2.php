<?php
/**
 *
 *	000 Large Block Type
 * @since	1.0
 */
class module2 extends Jvbpd_Module {
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle		= 10;
		$this->lghContent	= 25;
		parent::__construct( $post, $param );
	}

	public function output() {
		ob_start();
		?>
		<div <?php $this->classes('jv-effect-zoom-in'); ?>>
			<?php $this->before(); ?>
				<div class="effect-wrap jv-thumbnail">
					<a href="<?php echo $this->permalink;?>" class="jv-thumb">
						<?php echo $this->thumbnail( 'large', true ); ?>
						<div class="meta-category"><?php echo $this->category(); ?></div>
					</a>
				</div>
				<h4 class="meta-title"><?php echo $this->title; ?></h4>
				<?php echo $this->addtionalInfo(); ?>
				<?php echo $this->excerpt; ?>
			<?php $this->after(); ?>
		</div><!-- /.row -->
		<?php
		return ob_get_clean();
	}
}