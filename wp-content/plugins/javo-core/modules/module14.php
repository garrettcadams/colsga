<?php
/**
 *
 *
 * @since	1.0
 */
class module14 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 10;
		$this->lghContent	= 30;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		ob_start();
		$jvbpd_listing_category = $this->category();
		$jvbpd_listing_location = $this -> get_term('item_location');
		?>
		<div <?php $this->classes( 'media' ); ?>>
			<?php $this->before(); ?>
			<div class="media-left">
				<a href="<?php echo $this->permalink;?>">
					<?php echo $this->thumbnail( 'thumbnail' , false, false ); ?>
				</a>
			</div><!-- /.media-left -->
			<div class="media-body">
				<h4 class="media-heading"><?php echo $this->title; ?></h4>
				<?php echo $this->excerpt; ?>
				<ul class="module-meta list-inline">
					<li class="jv-meta-category">
						<i class="fa fa-user"></i>
						<?php echo $jvbpd_listing_category!='' ? $jvbpd_listing_category : __('No Category','jvfrmtd'); ?>
					</li><!-- jv-meta-category -->
					<li class="jv-meta-location">
						<i class="fa fa-map-marker"></i>
						<?php echo $jvbpd_listing_location!='' ? $jvbpd_listing_location : __('No Location','jvfrmtd'); ?>
					</li><!-- jv-meta-location -->
				</ul><!-- module-meta list-inline -->
				<?php echo $this->moreInfo(); ?>
			</div><!-- /.media-body -->
			<?php $this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}