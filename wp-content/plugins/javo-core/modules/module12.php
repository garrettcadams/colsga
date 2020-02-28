<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class module12 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghContent	= 50;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes( 'thumbnail' ); ?>>
			<?php $this->before(); ?>
			<div class="thumb-wrap">
				<a href="<?php echo $this->permalink; ?>" class="jv-module12-link">
					<?php echo $this->thumbnail( 'large', true ); ?>
					<div class="javo-item-status-tag meta-category">
						<?php echo $this->category(); ?>
					</div><!-- /.javo-item-status-tag -->
					<div class="jv-module-featured-label">
						<?php _e('Featured', 'jvfrmtd'); ?>
					</div>
					<div class="author"><?php echo $this->avatar; ?></div>
				</a>
				<div class="jv-module-thumb-inner-button">
					<?php $this->hover_layer();?>
				</div>
			</div><!-- /.thumb-wrap -->
			<div class="caption">
				<div class="row">
					<h4 class="item-title-list meta-title">
						<?php echo $this->title; ?>
					</h4>
					<div class="meta">
						<?php echo $this->addtionalInfo(); ?>
					</div><!-- /.meta -->
					<div class="group inner list-group-item-text item-excerpt-list">
						<?php echo $this->excerpt; ?>
					</div>
				</div><!-- row -->
			</div><!-- Caption -->
			<?php $this->after(); ?>
		</div><!-- /.thumbnail -->
		<?php
		return ob_get_clean();
	}
}