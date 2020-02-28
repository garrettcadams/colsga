<?php
/**
 *
 *	Featured Item Block
 * @since	1.0
 */

class module13 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle	= 10;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		?>
		<div id="javo-featured-items-wrap" <?php $this->classes( 'row' ); ?>>
			<?php $this->before(); ?>
			<div class="col-md-12 col-xs-12 left-big-img">
				<div class="row">
					<div class="col-md-2 col-sm-2 col-xs-12 jv-featured-property-img">
						<?php echo $this->thumbnail( 'large', false ); ?>
					</div><!--/.col-md-4-->
					<div class="col-md-10 col-sm-10 col-xs-12 jv-featured-property-meta-wrap">
						<div class="row">
							<div class="col-md-4 col-sm-4 col-xs-12 jv-featured-property-title">
								<h4><?php echo $this->title; ?></h4>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 jv-featured-property-des">
								<span><?php echo $this->author_name; ?></span>
								<span><?php echo $this->category(); ?></span>
							</div>
							<div class="col-md-2 col-sm-2 col-xs-12 jv-featured-property-view">
								<a href="<?php echo $this->permalink; ?>" class="btn jv-button-transition"><?php _e( "view", 'jvfrmtd' );?></a>
							</div>
						</div><!--/.row-->
					</div><!--/.col-md-8-->
				</div><!--/.row-->
			</div><!-- /.col-md-6 -->
			<?php $this->after(); ?>
		</div><!-- /#javo-featured-items-wrap -->
		<?php
		return ob_get_clean();
	}
}