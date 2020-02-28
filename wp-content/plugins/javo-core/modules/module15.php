<?php
/**
 *
 *
 * @since	1.0
 */
class module15 extends Jvbpd_Module {

	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 10;
		$this->lghContent	= 30;
		parent::__construct( $post, $param );
	}

	public function getRating() {

		if( ! function_exists( 'lv_directoryReview' ) ) {
			return;
		}

		if( $this->post->post_type != 'lv_listing' ) {
			return;
		}

		$rating = intVal( get_post_meta( $this->post_id, 'rating_average', true ) );
		printf( '<div class="meta-rating-wrap"><div class="meta-rating" style="width:%1$s%%;"></div></div>', ( $rating / 5 * 100 ) );
		if( function_exists( 'lava_directory' ) && method_exists( lava_directory()->admin, 'reviewCount' ) ) {
			printf( '<div class="review-count"><span class="review-count-number">%1$s</span> %2$s</div>', lava_directory()->admin->reviewCount( $this->post_id ), esc_html__( "Reviews", 'jvfrmtd' ) );
		}
	}

	public function getOpenHours() {

		if( $this->post->post_type != 'lv_listing' ) {
			return;
		}

		$isOpen = false;
		$workingData = json_decode( get_post_meta( $this->post->ID, '_open_hours', true ) );
		$currentDateIndex = ( date( 'w', time() ) + 6 ) % 7;

		if( isset( $workingData[ $currentDateIndex ] ) ) {
			$currentData = $workingData[ $currentDateIndex ];
			if( $currentData->isActive ) {
				$currentHours = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
				$openHours = strtotime( $currentData->timeFrom );
				$closeHours = strtotime( $currentData->timeTill );
				if( $openHours < $currentHours && $currentHours < $closeHours ) {
					$isOpen = true;
				}
			}
		}
		printf(
			'<span class="label label-rounded label-default	working-hours %1$s">%2$s</span>',
			( $isOpen ? 'open' : 'closed' ),
			( $isOpen ? esc_html__( "Open Now", 'jvfrmtd' ) : esc_html__( "Closed Now", 'jvfrmtd' ) )
		);
	}

	public function getFavoriteButton() {

		if( !class_exists( 'lvDirectoryFavorite_button' ) ) {
			return;
		}
		$instance = new lvDirectoryFavorite_button( Array(
			'format' => '{text}',
			'post_id' => $this->post->ID,
			'save' => "<i class='fa fa-heart'></i>",
			'unsave' => "<i class='fa fa-heart'></i>"
		) );
		$instance->output();
	}

	public function output() {
		ob_start();
		$jvbpd_listing_category = $this->c( 'listing_category' );
		$jvbpd_listing_location = $this->c( 'listing_location' );
		?>
		<div <?php $this->classes( 'card mc1' ); ?>>
			<?php $this->before(); ?>
			<div class="thumb">
				<?php echo $this->thumbnail( 'jvbpd-box-v', false, false, 'card-img-top img-responsive' ); ?>
				<ul class="detail-icons">
					<li>
						<a href="#" class="move-marker" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Find it", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon2-mark"></i>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->permalink;?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Detail", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon1-link"></i>
						</a>
					</li>
					<li>
						<a href="#" class="javo-infow-brief" data-post-id="<?php echo $this->post_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Preview", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon1-eyes"></i>
						</a>
					</li>
				</ul>
			<div class="jv-meta-distance"></div>
			</div>
			<div class="card-block">
				<h3 class="card-title">
					<?php echo $this->title; ?>
					<span class="title-actions">
						<?php $this->getFavoriteButton(); ?>
					</span>
				</h3>
				<ul class="module-meta list-inline">
					<li class="jv-meta-category">
						<i class="fa fa-bookmark"></i>
						<?php echo $jvbpd_listing_category!='' ? $jvbpd_listing_category : __('No Category','jvfrmtd'); ?>
					</li><!-- jv-meta-category -->
					<li class="jv-meta-location">
						<i class="fa fa-map-marker"></i>
						<?php echo $jvbpd_listing_location!='' ? $jvbpd_listing_location : __('No Location','jvfrmtd'); ?>
					</li><!-- jv-meta-location -->
				</ul><!-- module-meta list-inline -->
				<hr class="separator">
				<p class="card-text"><?php echo $this->excerpt; ?></p>
				<div class="meta-module">
					<div class="row">
						<div class="col-md-8 review-wrap">
							<?php $this->getRating(); ?>
						</div>
						<div class="col-md-4 text-right working-hours-wrap">
							<span class="title-actions">
								<?php $this->getOpenHours(); ?>
							</span>
						</div>
					</div>
				</div>
				<?php echo $this->moreInfo(); ?>
			</div><!-- /.media-body -->
			<?php $this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}
