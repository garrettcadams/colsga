<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class module6 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle	= 10;
		$this->lghContent	= 20;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div <?php $this->classes( 'panel panel-default panel-no-radius box-module1' ); ?>>
			<?php $this->before(); ?>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="thum">

							<a href="<?php echo $this->permalink; ?>" class="jv-image-gradient jv-big-grid-overlay">
								<?php echo $this->thumbnail( 'jvbpd-large', false ); ?>
								</a>

							<div class="meta-gradient">
								<div class="meta-category">
									<a href="<?php echo $this->permalink; ?>"><?php echo $this->category(); ?></a>
								</div>
							</div><!-- / meta-gradient-->
						</div> <!-- thumb -->
					</div> <!-- col-md-6 -->
					<div class="col-md-6 col-sm-6 col-xs-6">
						<h4 class="meta-title">
							<?php echo $this->title; ?>
							<?php
							if(
								( $intAreaNumber	 = $this->m( '_area' ) ) &&
								( $strAreaPrefix	 = $this->m( '_area_prefix' ) )
							) echo "({$intAreaNumber} {$strAreaPrefix})";
							?>
						</h4>
						<a href="<?php echo $this->permalink; ?>"><?php echo $this->excerpt; ?></a>
					</div> <!-- col-md-6 -->
				</div> <!-- row -->
			</div><!-- panel-body -->
			<div class="panel-footer options-wrap post-hidden">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->addtionalInfo(); ?>
					</div>
				</div>
			</div><!-- panel-footer -->
			<?php $this->after(); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}