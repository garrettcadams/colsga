<?php

class Jvbpd_Replace_Content {

	public $object_type = null;

	public $post = null;
	public $post_id = null;

	public $taxonomy = null;
	public $term = null;
	public $term_id = null;

	public $template = null;

	public $lvdr_admin_instance = null;

	public $defaults = Array(
		'title' => Array(
			'length' => 100,
		),
		'excerpt' => Array(
			'length' => 180,
		),
		'content' => Array(
			'length' => 180,
		),
		'term_content' => Array(
			'length' => 180,
		),

	);

	public function __construct( int $object_id, string $template='', string $object_type='post', string $taxonomy='' ) {
		$this->object_type = $object_type;
		$this->parseObject( $object_id, $taxonomy );
		$this->template = $template;
	}

	public function getTermMeta( string $key='', $default=false, array $args=Array() ) {
		$output = $default;
		$args = wp_parse_args( $args, Array(
			'taxonomy' => $this->taxonomy,
			'term' => $this->term,
		) );

		if( function_exists( 'lava_directory' ) ) {
			$output = lava_directory()->admin->getTermOption( $args[ 'term' ], $key, $args[ 'taxonomy' ] );
		}
		return $output;
	}

	public function parseObject( int $object_id=0, string $taxonomy='' ) {
		$this->post_id = $object_id;
		$this->post = (object) Array(
			'ID' => $object_id,
			'post_title' => '',
			'post_date' => '',
			'post_content' => '',
			'post_author' => 0,
		);

		$this->term_id = $object_id;
		$this->taxonomy = $taxonomy;
		$this->term = (object) Array(
			'term_id' => $object_id,
			'slug' => '',
			'name' => '',
			'parent' => 0,
			'count' => 0,
			'description' => '',
			'taxonomy' => $this->taxonomy,
		);

		if( 'post' == $this->object_type ) {
			if( false !== get_post_status( $object_id ) ) {
				$this->post = get_post( $object_id );
				$this->post_id = $object_id;
			}
		}

		if( 'taxonomy' == $this->object_type ) {
			if( taxonomy_exists( $taxonomy ) ) {
				$this->term_id = $object_id;
				$this->taxonomy = $taxonomy;
				$this->term = get_term( $this->term_id, $this->taxonomy );
			}
		}
	}

	public function spliceWord( string $content='', int $max=0 ) {
		$content = wp_strip_all_tags( $content, true );
		$content = strip_shortcodes( $content );
		if( 0 === $max ) {
			return $content;
		}
		$max++;
		$readMore = '...';

		if( mb_strlen( $content ) > $max ) {
			$subex = mb_substr( $content, 0, $max - mb_strlen( $readMore ) );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut ) . $readMore;
			} else {
				return $subex . $readMore;
			}
		} else {
			return $content;
		}
	}

	public function getTermData( array $args=Array() ) {
		$args = wp_parse_args(
			$args, Array(
				'taxonomy' => 'category',
				'field' => 'names',
				'single' => true
			)
		);
		$terms = wp_get_object_terms( $this->post_id, $args[ 'taxonomy' ], Array( 'fields' => $args[ 'field' ] ) );
		return $args['single'] ? (isset( $terms[0] ) ? $terms[0] : NULL) : $terms;
	}

	public function getTermMetaData( array $args=Array() ) {
		$args = wp_parse_args(
			$args, Array(
				'taxonomy' => 'category',
				'term_id' => 0,
				'key' => '',
			)
		);

		$output = false;
		if( function_exists( 'lava_directory' ) ){
			$output = lava_directory()->admin->getTermOption( $args[ 'term_id' ], $args[ 'key' ], $args[ 'taxonomy' ] );
		}
		return $output;
	}

	public function getAuthorData( string $key='', $default=false ) {
		$output = $default;
		$user = get_user_by( 'id', $this->post->post_author );
		if( $user instanceof \WP_User ) {
			if( isset( $user->{$key} ) ) {
				$output = $user->{$key};
			}
		}
		return $output;
	}

	public function getMetaData( array $args=Array() ) {
		$args = wp_parse_args(
			$args, Array(
				'key' => '_',
			)
		);
		return get_post_meta( $this->post_id, $args[ 'key' ], true );
	}

	public function post_id() { return $this->post_id; }

	public function post_meta($attr) { return $this->getMetaData( Array( 'key' => $attr) );}

	public function post_title( $length=false ) {
		$length = false == $length ? $this->defaults[ 'title' ][ 'length' ] : $length;
		return $this->spliceWord( get_the_title( $this->post_id ), $length );
	}

	public function post_content( $length ) {
		$length = false == $length ? $this->defaults[ 'content' ][ 'length' ] : $length;
		return $this->spliceWord( $this->post->post_content, $length );
	}

	public function post_excerpt( $length=false ) {
		$length = false == $length ? $this->defaults[ 'excerpt' ][ 'length' ] : $length;
		return $this->spliceWord( get_the_excerpt( $this->post_id ), $length );
	}

	public function post_author() { return $this->getAuthorData( 'display_name' ); }

	public function post_author_avatar( $imageSize='thumbnail' ) {
		$user_id = $this->getAuthorData( 'ID' );
		$avatar = null;
		if( function_exists( 'bp_core_fetch_avatar' ) ) {

			$imageSize = apply_filters( 'jvbpd_core/elementor/relace/avatar/size', $imageSize );
			$avatarArgs = Array(
				'item_id' => $user_id,
				'type' => $imageSize,
				'class' => 'rounded-circle',
			);

			if( 'wh-50px' == $imageSize ) {
				$avatarArgs['width'] = 50;
				$avatarArgs['height'] = 50;
			}
			$avatar = bp_core_fetch_avatar( $avatarArgs );
		}
		return $avatar;
	}

	public function post_date() { return date_i18n( get_option( 'date_format' ), strtotime( $this->post->post_date ) ); }

	public function comment_count() { return wp_count_comments( $this->post_id )->approved; }

	public function _address() { return $this->getMetaData( Array( 'key' => '_address' ) ); }

	public function _email() { return $this->getMetaData( Array( 'key' => '_email' ) ); }

	public function _website() { return $this->getMetaData( Array( 'key' => '_website' ) ); }

	public function _phone1() { return $this->getMetaData( Array( 'key' => '_phone1' ) ); }

	public function _phone2() { return $this->getMetaData( Array( 'key' => '_phone2' ) ); }

	public function _price() { return $this->getMetaData( Array( 'key' => '_price' ) ); }

	public function _sale_price() { return $this->getMetaData( Array( 'key' => '_sale_price' ) ); }

	public function _print_price($attr) {
		$attr = strtolower($attr);
		$format = '<span class="regular-price>%1$s</span>';
		$hasSalePrice = false;
		$price = $this->getMetaData(Array('key' => '_price'));
		$salePrice = $this->getMetaData(Array('key' => '_sale_price'));
		$price = !empty($price) ? floatVal($price) : 0;
		if(is_numeric($salePrice)) {
			$hasSalePrice = true;
		}

		$price = floatVal($price);
		$salePrice = floatVal($salePrice);

		if('before' == $attr){
			$format = $hasSalePrice ? '<span class="regular-price price-middle-line">%2$s%1$s</span> <span class="sale-price">%2$s%3$s</span>' : '<span class="regular-price">%2$s%1$s</span>';
		}
		if('after' == $attr){
			$format = $hasSalePrice ? '<span class="regular-price price-middle-line">%1$s%2$s</span> <span class="sale-price">%3$s%2$s</span>' : '<span class="regular-price">%1$s%2$s</span>';
		}
		return sprintf($format, number_format($price), $this->getMetaData(Array('key' =>'_currency_unit')), number_format($salePrice));
	}

	public function _price_range($attr) {
		$output = '';
		$output_tooltip = esc_html__( "Not to say", 'jvfrmtd' );
		$count_char = 0;
		$price_range = $this->getMetaData(Array('key' => '_price_range'));
		switch($price_range) {
			case 'inexpensivve': $count_char = 1; $output_tooltip = esc_html__( "Inexpensive", 'jvfrmtd' ); break;
			case 'moderate': $count_char = 2; $output_tooltip = esc_html__( "Moderate", 'jvfrmtd' ); break;
			case 'pricey': $count_char = 3; $output_tooltip = esc_html__( "Pricey", 'jvfrmtd' ); break;
			case 'ultra_high': $count_char = 4; $output_tooltip = esc_html__( "Ultra High", 'jvfrmtd' ); break;
		}
		if(0<$count_char){
			$output = sprintf(
				'<div class="price-range" data-toggle="tooltip" title="%2$s">%1$s</div>',
				str_repeat('<i class="fa fa-usd"></i>', $count_char),
				$output_tooltip
			);
		}
		return $output;
	}

	public function _logo( $imageSize='jvbpd-tiny' ) {
		$logoID = $this->getMetaData( Array( 'key' => '_logo' ) );
		$imageSize = apply_filters( 'jvbpd_core/elementor/relace/logo/size', $imageSize );
		$logoURL = wp_get_attachment_image_url( $logoID, $imageSize );
		return sprintf( '<img src="%1$s" class="meta-logo">', $logoURL );
	}

	public function category() { return $this->getTermData( Array( 'taxonomy' => 'category' ) ); }

	public function listing_category() { return $this->getTermData( Array( 'taxonomy' => 'listing_category' ) ); }

	public function image_category_featured( $attr ) {
		$names = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'names', 'single' => false ) );
		$output = '<img src="' . ELEMENTOR_ASSETS_URL . 'images/placeholder.png"/>';
		$term_id = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'ids' ) );
		$featured_id = $this->getTermMetaData( Array( 'taxonomy' => 'listing_category', 'key' => 'featured', 'term_id' => $term_id ) );
		$image = wp_get_attachment_image($featured_id, 'thumbnail', false, Array(
			'data-toggle' => 'tooltip',
			'title' => join(', ', $names),
		));
		if($image) {
			$output = $image;
		}
		return $output;
	}

	public function icon_category_featured( $attr ) {
		$names = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'names', 'single' => false ) );
		$term_id = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'ids' ) );
		$icon = $this->getTermMetaData( Array( 'taxonomy' => 'listing_category', 'key' => 'icon', 'term_id' => $term_id ) );
		return sprintf('<div class="meta-category-icon-wrap" title="%s" data-toggle="tooltip"><i class="%s"></i></div>', join(', ', $names), $icon);
	}

	public function listing_category_featured_image( $attr ) {
		$attrs = explode('|', $attr);
		$imageSize = $attrs[0];
		$term_id = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'ids' ) );
		$featured_id = $this->getTermMetaData( Array( 'taxonomy' => 'listing_category', 'key' => 'featured', 'term_id' => $term_id ) );
		$imageSize = apply_filters( 'jvbpd_core/elementor/relace/listing_category-image/size', $imageSize );
		$output = wp_get_attachment_image_url( $featured_id, $imageSize );
		if(!$output){
			$output = $attrs[1];
		}
		return $output;
	}

	public function listing_category_featured_icon() {
		$term_id = $this->getTermData( Array( 'taxonomy' => 'listing_category', 'field' => 'ids' ) );
		return $this->getTermMeta( 'icon', '', Array( 'taxonomy' => 'listing_category', 'term_id' => $term_id ) );
	}

	public function listing_location() { return $this->getTermData( Array( 'taxonomy' => 'listing_location' ) ); }

	public function listing_keyword() { return $this->getTermData( Array( 'taxonomy' => 'listing_keyword' ) ); }

	public function thumbnail_url( $attr ) {
		if(false === strpos($attr, '|')) {
			$attr = $attr . '|';
		}
		$attrs = explode('|', $attr);
		$imageSize = $attrs[0];
		$thumbnailID = get_post_thumbnail_id( $this->post_id );
		$imageSize = apply_filters( 'jvbpd_core/elementor/relace/thumbnail/size', $imageSize );
		$imageURL = wp_get_attachment_image_url( $thumbnailID, $imageSize );
		if(!$imageURL){
			$imageURL = $attrs[1];
		}
		return $imageURL;
	}

	public function permalink_url() { return get_permalink( $this->post_id ); }

	public function open_hours() {
		$isOpen = false;
		$workingData = json_decode( $this->getMetaData( Array( 'key' => '_open_hours' ) ) );
		$currentDateIndex = ( date( 'w', time() ) + 6 ) % 7;

		if(is_array($workingData)) {
			$permanentlyClosed = true;
			foreach( $workingData as $dayData ) {
				if(null !== $dayData && $dayData->isActive){
					$permanentlyClosed = false;
				}
			}
			if($permanentlyClosed) {
				return sprintf('<span class="label label-rounded label-default	working-hours closed">%s</span>', esc_html__( "Closed", 'jvfrmtd' ));
			}
		}

		if( isset( $workingData[ $currentDateIndex ] ) ) {
			$currentData = $workingData[ $currentDateIndex ];
			if( $currentData !== null && $currentData->isActive ) {
				$currentHours = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
				if(!isset($currentData->isOpen24h) || (isset($currentData->isOpen24h) && false === $currentData->isOpen24h) ){
					$fromTimes = is_array($currentData->timeFrom) ? $currentData->timeFrom : Array($currentData->timeFrom);
					foreach($fromTimes as $fromTimeIndex => $fromTime) {
						$openHours = strtotime( $fromTime );
						$closeHours = is_array($currentData->timeTill) ? $currentData->timeTill[$fromTimeIndex] : $currentData->timeTill;
						$closeHours = strtotime($closeHours);
						if( $openHours < $currentHours && $currentHours < $closeHours ) {
							$isOpen = true;
							break;
						}
					}
				}else{
					return sprintf('<span class="label label-rounded label-default	working-hours open">%s</span>', esc_html__( "24 Hours open", 'jvfrmtd' ));
				}
			}else{
				return sprintf('<span class="label label-rounded label-default	working-hours closed">%s</span>', esc_html__( "Closed Now", 'jvfrmtd' ));
			}
		}
		return sprintf(
			'<span class="label label-rounded label-default	working-hours %1$s">%2$s</span>',
			( $isOpen ? 'open' : 'closed' ),
			( $isOpen ? esc_html__( "Open Now", 'jvfrmtd' ) : esc_html__( "Closed Now", 'jvfrmtd' ) )
		);
	}

	public function rating($type='image') {
		$output = false;
		if( function_exists( 'lv_directoryReview' ) ) {
			$rating = get_post_meta( $this->post_id, 'rating_average', true );
			switch( $type ){
				case 'number':
					$output = sprintf('<div class="rating-wrap">%1$.1f</div>', $rating);
					break;
				case 'star_number':
					$output = sprintf('<div class="rating-wrap"><i class="fa fa-star"></i> %1$.1f</div>', $rating);
					break;
				case 'icon':
					$rating = intVal($rating);
					$full_icon = '<i class="fa fa-star"></i>';
					$empty_icon = '<i class="fa fa-star-o"></i>';
					$output_icon = str_repeat($full_icon, $rating );
					$output_icon .= str_repeat($empty_icon, (5 - $rating) );
					$output = sprintf('<div class="rating-wrap">%1$s</div>', $output_icon);
					break;
				case 'image': default:
					$rating = intVal($rating);
					$output = sprintf( '<div class="module-rating-wrap"><div class="module-ratings" style="width:%1$s%%;"></div></div>', ( $rating / 5 * 100 ) );
			}
		}
		return $output;
	}

	public function favorite() {
		$output = false;
		if( class_exists( 'lvDirectoryFavorite_button' ) ) {
			$instance = new lvDirectoryFavorite_button( Array(
				'format' => '{text}',
				'post_id' => $this->post_id,
				'save' => __( "<i class='fa fa-heart-o'></i>", 'jvfrmtd' ),
				'unsave' => __( "<i class='fa fa-heart'></i>", 'jvfrmtd' ),
			) );
			$output = $instance->output( false );
		}
		return $output;
	}

	public function preview_map() {
		return sprintf(
			'<div class="jvbpd-preview-map" data-lat="%1$s" data-lng="%2$s"></div>',
			$this->getMetaData( Array( 'key' => 'lv_listing_lat' ) ),
			$this->getMetaData( Array( 'key' => 'lv_listing_lng' ) )
		);
	}

	public function favorite_count() {
		$count = intVal(get_post_meta($this->post_id, '_save_count', true));
		return $count;
	}

	public function post_view_count() {
		$count = 0;
		if(function_exists('pvc_get_post_views')) {
			$count = pvc_get_post_views($this->post_id);
		}
		return $count;
	}

	public function preview_detail() {
		return sprintf(
			'<div class="jvbpd-preview-detail" data-post-id="%1$s"><i class="fa fa-video-camera"></i><span></span></div>',
			$this->post_id
		);
	}

	public function slider_detail() {
		$output = false;
		$detailImages = $this->getMetaData( Array( 'key' => 'detail_images' ) );
		if(!empty($detailImages)) {
			$arrImageSrc = Array();
			foreach( $detailImages as $imageID ){
				$arrImageSrc[] = Array(
					'src' => wp_get_attachment_image_url( $imageID, 'full'),
					'thumb' => wp_get_attachment_image_url( $imageID, 'thumbnail')
				);
			}
			$strImages = wp_json_encode($arrImageSrc);
			$output = sprintf(
				'<div class="jvbpd-slider-detail" data-images="%1$s"><i class="fa fa-camera"></i><span></span></div>',
				esc_attr($strImages)
			);
		}
		return $output;
	}

	public function preview_video(){
		$output = false;
		$video_portal = $this->getMetaData( Array( 'key' => '_video_portal' ) );
		$video_id = $this->getMetaData( Array( 'key' => '_video_id' ) );
		if(!empty($video_portal)&&!empty($video_id)){
			$output = sprintf(
				'<div class="jvbpd-preview-video" data-video="%1$s"><i class="fa fa-video-camera"></i><span></span></div>',
				join(false, Array($video_portal, $video_id)				)
			);
		}
		return $output;

	}

	public function preview() {
		return sprintf(
			'<div class="preview">
				<a href="javascript:" class="javo-infow-brief" data-post-id="%1$s" data-toggle="tooltip" data-placement="top" title="%2$s"><i class="jvbpd-icon1-eyes"></i> <span class="brief-label"> %2$s</a>
			</div>',
			$this->post_id,
			esc_html__( "Preview", 'jvfrmtd' )
		);
	}

	public function custom_meta() { return ''; }

	public function taxonomy($attr) {
		return $this->getTermData(Array('taxonomy'=>$attr));
	}

	public function getTermLink($format, $term, $url) {
		$term_link = add_query_arg(
			// Array($term->taxonomy => $term->slug),
			Array($term->taxonomy => $term->name),
			$url
		);
		return sprintf($format, $term->name, esc_url($term_link));
	}

	public function more_tax($attr) {
		$output = Array();
		$permalink = '';
		$attr = explode('|', $attr);
		$taxonomy = isset($attr[0]) ? $attr[0] : '';
		$type = isset($attr[1]) ? $attr[1] : 'single';
		$link = isset($attr[2]) ? $attr[2] : '';
		if(!taxonomy_exists($taxonomy)){
			return '';
		}
		$terms = wp_get_object_terms($this->post_id, $taxonomy, Array('fields' => 'all'));
		$format = '<span>%1$s</span>';
		if('' != $link && false !== get_post_status($link)) {
			$permalink = get_permalink($link);
			$format = '<span><a href="%2$s" target="_self">%1$s</a></span>';
		}
		if('multiple' == $type){
			foreach($terms as $term){
				$output[] = $this->getTermLink($format, $term, $permalink);
			}
		}else{
			$term = isset($terms[0]) ? $terms[0] : false;
			if($term){
				$output[] = $this->getTermLink($format, $term, $permalink);
			}
		}
		return join(', ', $output);
	}

	public function distance() { return '<div class="jv-meta-distance hidden"></div>'; }

	public function term_taxonomy() { return $this->taxonomy; }

	public function term_name() { return $this->term->name; }

	public function term_icon() { return sprintf( '<i class="%s"></i>', $this->getTermMeta( 'icon' ) ); }

	public function term_featured_image( $attr ) {
		$attrs = explode('|', $attr);
		$imageSize = $attrs[0];
		if( 'taxonomy' != $this->object_type ) {
			return $this->listing_category_featured_image($attr);
		}
		$thumbnailID = $this->getTermMeta( 'featured' );
		$imageSize = apply_filters( 'jvbpd_core/elementor/relace/term_image/size', $imageSize );
		$output =  wp_get_attachment_image_url( $thumbnailID, $imageSize );
		if(!$output){
			$output = $attrs[1];
		}
		return $output;
	}

	public function term_permalink() { return $this->object_type == 'taxonomy' ? get_term_link( $this->term, $this->taxonomy ) : ''; }

	public function term_count() { return $this->term->count; }

	public function term_description() { return $this->spliceWord( $this->term->description, $this->defaults[ 'term_content' ][ 'length' ] ); }

	public function author_review($attr) {
		$attr = explode('|', $attr);
		$field = isset($attr[0]) ? $attr[0] : '';
		$onImageID = isset($attr[1]) ? $attr[1] : '';
		$offImageID = isset($attr[2]) ? $attr[2] : '';

		$output = '';

		$onRepeatCount = $this->getAuthorReviewData($field);
		$offRepeatCount = 5 - $onRepeatCount;

		$reviewLoop=0; while($reviewLoop < $onRepeatCount) {
			$output .= sprintf(
				'<img src="%s" class="author-review-icon lvar-on">',
				wp_get_attachment_image_url($onImageID)
			);
			$reviewLoop++;
		}
		$reviewLoop=0; while($reviewLoop < $offRepeatCount) {
			$output .= sprintf(
				'<img src="%s" class="author-review-icon lvar-off">',
				wp_get_attachment_image_url($offImageID)
			);
			$reviewLoop++;
		}
		return $output;
	}

	private function getAuthorReviewData($review) {
        $output = 0;
        $data = get_post_meta($this->post_id, '_lvar_review_key', true);
        if(is_array($data) && isset($data[$review])) {
            $output = $data[$review];
        }
        return intVal($output);
    }

	public function render() {
		preg_match_all( '/{(.*?)}/', $this->template, $findReplace );
		if( !empty( $findReplace[1] ) ) {
			foreach( $findReplace[1] as $replaceCallback ) {
				$replaceParam = false;
				$selector = sprintf( '{%1$s}', $replaceCallback );
				if( -1 < strpos( $replaceCallback, ':' ) ) {
					$splitReplace = explode( ':', $replaceCallback, 2 );
					$replaceCallback = $splitReplace[0];
					$replaceParam = $splitReplace[1];
				}
				if( method_exists( $this, $replaceCallback ) ) {
					$output = call_user_func( Array( $this, $replaceCallback ), $replaceParam );
					$this->template = str_replace( $selector, $output, $this->template );
				}
			}
		}
		return $this->template;
	}
}