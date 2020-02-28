<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\PriceRange;
use WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Controllers\SearchFormController;

add_shortcode( 'wilcity_sidebar_related_listings', 'wilcitySidebarRelatedListings' );
function wilcitySidebarRelatedListings( $aArgs ) {

	global $post;
	$aAtts = is_array( $aArgs['atts'] ) ? $aArgs['atts'] : \WILCITY_SC\SCHelpers::decodeAtts( $aArgs['atts'] );

	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'        => 'Related Listings',
			'icon'        => 'la la-qq',
			'style'       => 'slider',
			'conditional' => ''
		)
	);

	if ( isset( $aAtts['isMobile'] ) ) {
		return apply_filters( 'wilcity/mobile/sidebar/related_listings', '', $post, $aAtts );
	}

	$aAdditionalArgs = array();
	switch ( $aAtts['conditional'] ) {
		case 'listing_location':
		case 'listing_category':
		case 'listing_tag':
			$taxonomy = $aAtts['conditional'];
			if ( $taxonomy == 'listing_category' ) {
				$taxonomy = 'listing_cat';
			}
			$aTerms = GetSettings::getPostTerms( $post->ID, $taxonomy );
			if ( empty( $aTerms ) ) {
				return '';
			}
			$aLocations = array();
			foreach ( $aTerms as $oRawLocation ) {
				$aLocations[] = $oRawLocation->term_id;
			}

			$aAdditionalArgs['tax_query'] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $aLocations
				),
			);
			break;
		case 'google_address':
			$aLatLng = GetSettings::getLatLng( $post->ID );
			if ( empty( $aLatLng ) ) {
				return false;
			}
			$aAtts['oAddress']           = array();
			$aAtts['oAddress']           = $aLatLng;
			$aAtts['oAddress']['unit']   = 'KM';
			$aAtts['oAddress']['radius'] = ! empty( $aAtts['radius'] ) ? abs( $aAtts['radius'] ) : 10;
			break;
		default:
			break;
	}

	if ( isset( $aAtts['key'] ) && $aAtts['key'] == 'relatedListings' ) {
		$aAdditionalArgs['post_type'] = $post->post_type;
	}

	$postsPerPage             = isset( $aAtts['postsPerPage'] ) ? $aAtts['postsPerPage'] : 30;
	$aAtts['aAdditionalArgs'] = ! isset( $aAtts['aAdditionalArgs'] ) || empty( $aAtts['aAdditionalArgs'] ) ? $aAdditionalArgs : array_merge( $aAtts['aAdditionalArgs'], $aAdditionalArgs );
	$aAtts['postsPerPage']    = $postsPerPage;
	if ( isset( $aAtts['orderby'] ) && ! empty( $aAtts['orderby'] ) ) {
		if ( in_array( $aAtts['orderby'], array( 'best_rated', 'best_viewed', 'recommended' ) ) ) {
			$aAtts[ $aAtts['orderby'] ] = 'yes';
		}
	}

	$aArgs = SearchFormController::buildQueryArgs( $aAtts );

	if ( ! empty( $aAtts['aAdditionalArgs'] ) ) {
		$aArgs = array_merge( $aArgs, $aAtts['aAdditionalArgs'] );
		unset( $aAtts['aAdditionalArgs'] );
	}

	if ( isset( $aArgs['isIgnorePostNotIn'] ) && $aArgs['isIgnorePostNotIn'] == 'yes' ) {
		unset( $aArgs['postNotIn'] );
	}

	$aArgs['post__not_in'] = isset( $aArgs['postNotIn'] ) && is_array( $aArgs['postNotIn'] ) ? array_merge( $aArgs['postNotIn'], array( $post->ID ) ) : array( $post->ID );

	$aAtts['aArgs'] = $aArgs;

	ob_start();
	switch ( $aAtts['style'] ) {
		case 'list':
			echo wilcityRenderSidebarList( array(
				'atts' => $aAtts
			) );
			break;
		case 'grid':
			echo wilcityRenderSidebarGrid( array(
				'atts' => $aAtts
			) );
			break;
		case 'slider':
			echo wilcityRenderSidebarSlider( array(
				'atts' => $aAtts
			) );
			break;
	}
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}