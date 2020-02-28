<?php
/**
 *
 *	000 Simple Inline Block Type
 * @since	1.0
 */
class module3 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle = 30;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes(); ?>>
			<?php $this->before(); ?>
			<h4 class="section-header">
				<?php echo $this->title; ?>
			</h4>
			<div class="section-excerpt">
				<a href="<?php echo $this->permalink; ?>">
					<?php echo $this->excerpt; ?>
				</a>
			</div>
			<div class="section-meta">
				<div class="col-md-4 meta-tumbnail">
					<a href="<?php echo $this->permalink; ?>">
						<?php echo $this->thumbnail( 'jvbpd-tiny' ); ?>
					</a>
				</div>
				<div class="col-md-8 more-meta">
					<div class="meta-category no-background">
						<?php echo $this->addtionalInfo(); ?>
					</div>
				</div>
			</div>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}