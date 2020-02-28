<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class module11 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghContent	= 20;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes( 'panel panel-default panel-no-radius' ); ?>>
			<?php $this->before(); ?>
			<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
				<div class="thum">
					<a href="<?php echo $this->permalink; ?>" class="jv-image-gradient jv-big-grid-overlay"><?php echo $this->thumbnail( 'jvbpd-box-v', false ); ?></a>

					<div class="meta-tag-top">
						<span class="status jv-button-transition"><?php echo $this->category(); ?></span>
					</div>
				</div> <!-- thumb -->

				</div>
				</div>
			</div><!-- panel-body -->
			<ul class="list-group">
				<li class="list-group-item">
					<h4 class="meta-title">
						<?php echo $this->title; ?>
					</h4>
				</li>
			</ul>
			<div class="panel-footer options-wrap post-hidden">
				<div class="row">
					<?php echo $this->addtionalInfo(); ?>
				</div>
			</div><!-- panel-footer -->
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}