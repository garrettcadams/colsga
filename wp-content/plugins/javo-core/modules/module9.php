<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class module9 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghContent	= 9;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes( 'thumbnail' ); ?>>
			<?php $this->before(); ?>
			<div class="thumb-wrap">
				<?php echo $this->thumbnail( 'large', true ); ?>
				<div class="meta-status admin-color-setting"><?php echo $this->category(); ?></div>
				<div class="meta-wrap">
					<h4 class="meta-title"><?php echo $this->title; ?></h4><!-- /.meta-title -->
					<div class="more-meta">
						<?php echo $this->addtionalInfo(); ?>
					</div><!-- more-meta -->
				</div><!-- meta-wrap -->
			</div><!-- thumb-wrap -->
			<?php $this->after(); ?>
		</div><!-- Thumbnail -->
		<?php
		return ob_get_clean();
	}
}