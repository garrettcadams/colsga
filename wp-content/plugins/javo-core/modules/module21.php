<?php
/**
 *
 *
 * @since	1.0
 */
class module21 extends Jvbpd_Base_Meta_Module {

	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 10;
		$this->lghContent	= 30;
		parent::__construct( $post, $param );
	}

	public function output() {
		ob_start();
		$jvbpd_listing_category = $this->c( 'listing_category' );
		$jvbpd_listing_location = $this->c( 'listing_location' );
		?>
		<div <?php $this->classes( 'card' ); ?>>
			<?php $this->before(); ?>
			<div class="thumb mid-height">
				<a href="<?php echo $this->permalink; ?>" class="meta-permalink">
					<?php echo $this->thumbnail( 'jvbpd-box-v', false, false, 'card-img-top img-responsive' ); ?>
					<div class="thumb-overlay"></div>
				</a>

				<div class="meta-pos-top-right">
					<div class="meta-listing_category"><?php echo $jvbpd_listing_category; ?></div>
					<div class="meta-listing_location"><?php echo $jvbpd_listing_location; ?></div>
				</div>

				<div class="meta-pos-bottom-left">
					<div class="meta-price">
						<?php echo $this->getPrice(); ?>
					</div>
					<div class="meta-area">
						<?php echo $this->getArea( false, '' ); ?>
					</div>
				</div>

				<div class="thumb-actions">
					<div class="action-favorite">
						<?php
						$this->getFavoriteButton( Array(
							'save' => "<i class='fa fa-heart'></i>",
							'unsave' => "<i class='fa fa-heart'></i>",
						) ); ?>
					</div>
					<div class="action-favorite">
						<a href="<?php echo esc_url( $this->permalink ); ?>">
							<i class="fa fa-plus"></i>
						</a>
					</div>
				</div><!-- thumb-actions -->

				<div class="jv-meta-distance"></div>
			</div> <!-- thumb -->

			<div class="card-block">
				<h3 class="card-title"><?php echo $this->title; ?></h3>
				<p class="meta-address"><?php echo $this->m( 'lv_listing_address', 'S California Ave, Chicago, IL, USA' ); ?></p>
				<div class="row meta-details">
					<div class="col-md-8 more-meta">
						<div class="meta-bedrooms"><span class="meta-label"><?php esc_html_e( "Beds", 'jvfrmtd' ); ?></span> : <span class="meta-value"><?php echo $this->m( 'lvac_bedrooms', 0 ); ?></span></div>
						<div class="meta-bathrooms"><span class="meta-label"><?php esc_html_e( "Baths", 'jvfrmtd' ); ?></span> : <span class="meta-value"><?php echo $this->m( 'lvac_bathrooms', 0 ); ?></span></div>
						<div class="meta-area"><?php echo $this->getArea( '<span class="meta-area-unit">%2$s</span>: <span class="meta-area-value">%1$s</span>', '' ); ?></div>
						<div class="term-type"><?php echo $this->c( 'listing_type', esc_html__( "No type", 'jvfrm' ) ); ?></div>
					</div>
					<div class="col-md-4">
						<a href="<?php echo esc_url( $this->permalink ); ?>" class="btn btn-primary btn-block meta-detail-link">
							<?php esc_html_e( "Details", 'jvfrmtd' ); ?>
							<i class="fa fa-chevron-right"></i>
						</a>
					</div>
				</div>
			</div><!-- /.media-body -->
			<div class="card-footer">
				<div class="meta-footer-left">
					<div class="meta-author-avatar">
						<?php echo $this->avatar; ?>
					</div>
				</div>
				<div class="meta-footer-right">
					<div class="meta-posted-date">
						<i class="fa fa-calendar"></i>
						<?php echo $this->date; ?>
					</div>
				</div>
			</div>
			<?php $this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}