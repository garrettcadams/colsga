<?php

abstract class Jvbpd_Base_Meta_Module Extends Jvbpd_Module {

	public function getPrice( $format=false, $default=false ) {
		if( $format === false ) {
			$format = '<span class="meta-price-unit">%1$s</span> <span class="meta-price-value">%2$s</span>';
		}
		$price = floatVal( get_post_meta( $this->post_id, 'lvac_default_price', true ) );
		$unit = get_post_meta( $this->post_id, 'lvac_after_price_label', true );
		$output = sprintf( $format, $unit, number_format( $price ) );
		return $output;
	}

	public function getArea( $format=false, $default=false ) {
		if( $format === false ) {
			$format = '<span class="meta-area-value">%1$s</span> <span class="meta-area-unit">%2$s</span>';
		}
		$area = floatVal( get_post_meta( $this->post_id, 'lvac_area', true ) );
		$unit = get_post_meta( $this->post_id, 'lvac_area_size_prefix', true );
		$output = sprintf( $format, number_format( $area ), $unit );
		if( $default !== false && $area <= 0 ) {
			$output = $default;
		}
		return $output;
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

	public function getFavoriteButton( $attr=Array() ) {
		if( !class_exists( 'lvDirectoryFavorite_button' ) ) {
			return;
		}

		$args = shortcode_atts( Array(
			'format' => '{text}',
			'post_id' => $this->post->ID,
			'save' => "<i class='fa fa-heart'></i> Save",
			'unsave' => "<i class='fa fa-heart'></i> Saved"
		), $attr );

		$instance = new lvDirectoryFavorite_button( $args );
		$instance->output();
	}

}