<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class module10 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghContent	= 8;
		$this->lghTitle	= 8;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div<?php $this->classes( 'panel panel-default panel-no-radius' ); ?>>
			<?php $this->before(); ?>
			<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
				<div class="thum">
					<a href="<?php echo $this->permalink; ?>" class="jv-image-gradient jv-big-grid-overlay"><?php echo $this->thumbnail( 'jvbpd-large', false ); ?></a>
					<div class="meta-category admin-color-setting">
							<a href="<?php echo $this->permalink; ?>">
								<?php echo $this->category(); ?>
							</a>
					</div>
				</div> <!-- thumb -->

				</div>
				</div>
			</div><!-- panel-body -->
			<!-- List group -->
			<ul class="list-group">
				<li class="list-group-item section-more-meta">
					<div class="row">
						<div class="col-md-12">
							<?php echo $this->addtionalInfo(); ?>
						</div>
					</div>
				</li>
				<li class="list-group-item section-excerpt">
					<a href="<?php echo $this->permalink; ?>"><?php echo $this->excerpt; ?></a>
				</li>
				<li class="list-group-item section-link">
					<a href="<?php echo $this->permalink; ?>" class="btn btn-primary btn-block admin-color-setting"><?php _e('Detail', 'jvfrmtd') ?></a>
				</li>
			</ul>
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}