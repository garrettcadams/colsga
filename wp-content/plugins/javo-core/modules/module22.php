<?php
/**
 *
 *
 * @since	1.0
 */
class module22 extends Jvbpd_Module {


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
				'save' => "<i class='fa fa-heart'></i> Save",
				'unsave' => "<i class='fa fa-heart'></i> Saved"
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
				<div class="thumb mid-height">
					<?php echo $this->thumbnail( 'jvbpd-box-v', false, false, 'card-img-top img-responsive' ); ?>

					<div class="thumb-actions">
						<div class="action-left favorite"><?php $this->getFavoriteButton(); ?></div>
						<div class="action-right preview">
							<a href="javascript:" class="javo-infow-brief" data-post-id="<?php echo $this->post_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Preview", 'jvfrmtd' ); ?>">
								<i class="jvbpd-icon1-eyes"></i>
								<span class="brief-label"> <?php esc_html_e("Preview", 'jvfrmtd'); ?></span>
							</a>
						</div>
					</div><!-- thumb-actions -->
					<div class="jv-meta-distance"></div>
				</div> <!-- thumb -->
				<div class="card-block">
					<h3 class="card-title">
						<?php echo $this->title; ?>
						<span class="title-actions"></span>
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
					<!--// <hr class="separator"> -->
					<!--<p class="card-text"><?php echo $this->excerpt; ?></p>-->					
					<?php
					/* Price */
					$default_price = get_post_meta( $this->post_id, 'lvac_default_price', true  );
					if (is_numeric($default_price)){
						$default_price_format = number_format($default_price);
						$lvac_price_prefix = get_post_meta( $this->post_id, 'lvac_price_prefix', true  );
						$listing_price = $default_price_format . " " .$lvac_price_prefix;

					}

					$lvac_bedrooms = get_post_meta( $this->post_id, 'lvac_bedrooms', true  );
					$lvac_bathrooms = get_post_meta( $this->post_id, 'lvac_bathrooms', true  );
					$lvac_garages = get_post_meta( $this->post_id, 'lvac_garages', true  );
					$lvac_area = get_post_meta( $this->post_id, 'lvac_area', true  );
					$lvac_area_size_prefix = get_post_meta( $this->post_id, 'lvac_area_size_prefix', true  );
					?>
					<div class="listing-meta">
					<ul>
						<li><div class="meta-key"><?php echo esc_html__("Bedrooms", "jvfrmtd"); ?></div><div class="meta-value"><span><?php echo $lvac_bedrooms; ?></span></div></li>
						<li><div class="meta-key"><?php echo esc_html__("Bathroom", "jvfrmtd"); ?></div><div class="meta-value"><span><?php echo $lvac_bathrooms; ?></span></div></li>
						<li><div class="meta-key"><?php echo esc_html__("garages", "jvfrmtd"); ?></div><div class="meta-value"><span><?php echo $lvac_garages; ?></span></div></li>
						<li><div class="meta-key"><?php echo esc_html__("Size", "jvfrmtd"); ?></div><div class="meta-value size"><span><?php echo $lvac_area; ?><?php echo $lvac_area_size_prefix ?></span></div></li>
					</ul>
					</div> <!-- // listing meta -->



					<div class="meta-module">
						<div class="row">
							<div class="col-md-8 review-wrap">
								<?php 
									if ((isset($default_price_format))){
										echo $listing_price; 
									}								
								?>
								<?php //$this->getRating(); ?>
							</div>
							<div class="col-md-4 text-right working-hours-wrap">
								<span class="title-actions">
									<?php //$this->getOpenHours(); ?>
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
