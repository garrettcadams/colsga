<?php

class Jvbpd_Core_Ajax {

	public static $instance = null;

	public $list = Array();

	public function __construct() {

		$this->add( 'jvbpd_map_info_window_content', 'mapInfoWinContent' );
		$this->add( 'jvbpd_map_list', 'mapBlockContents' );
		$this->add( 'jvbpd_search_get_keywords', 'searchGetKeywords' );
		$this->add( 'jvbpd_map_brief', 'previewModalContent' );
		$this->add( 'jvbpd_page_block_content', 'pageBlockContent' );
		$this->add( 'jvbpd_detail_images', 'previewModalSliderContent' );
		$this->add( 'jvbpd_get_canvas', 'getCanvasRender' );
		$this->add( 'jvbpd_get_listing_category_featured_iamge', 'getListingCategoryFeaturedImage' );
		$this->add( 'jvbpd_get_LoginForm', 'getLoginFormRender' );
		$this->add( 'jvbpd_get_SignUpForm', 'getSignupFormRender' );

		$this->flush();
	}

	public function add( $hook='', $cb='' ) {
		$this->list[ $hook ] = apply_filters( 'jvbpd_core/ajax/add/list', Array( $this, $cb ) );
	}

	public function flush() {
		foreach( $this->list as $hook => $cb ) {
			add_action( 'wp_ajax_' . $hook, $cb );
			add_action( 'wp_ajax_nopriv_' . $hook, $cb );
		}
	}

	public function mapInfoWinContent() {
		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		$jvbpd_result = Array( "state" => "fail" );

		if( false !== get_post_status( $post_id ) ) {

			$post = get_post( $post_id );
			if( false == ( $jvbpd_this_author = get_userdata( $post->post_author ) ) ) {
				$jvbpd_this_author = new stdClass();
				$jvbpd_this_author->display_name = '';
				$jvbpd_this_author->user_login = '';
				$jvbpd_this_author->avatar = 0;
			}

			// Post Thumbnail
			if( '' !== ( $jvbpd_this_thumb_id = get_post_thumbnail_id( $post->ID ) ) ) {
				$jvbpd_this_thumb_url = wp_get_attachment_image_src( $jvbpd_this_thumb_id , 'jvfrm-spot-box-v' );
				if( isset( $jvbpd_this_thumb_url[0] ) ) {
					$jvbpd_this_thumb					= $jvbpd_this_thumb_url[0];
				}
			}

			// If not found this post a thaumbnail
			if( empty( $jvbpd_this_thumb ) ) {
				$jvbpd_this_thumb		=	jvbpd_tso()->get( 'no_image', jvbpd_IMG_DIR . '/no-image.png' );
			}
			$jvbpd_this_thumb			= apply_filters( 'jvbpd_map_list_thumbnail', $jvbpd_this_thumb, $post );
			$jvbpd_this_thumb			= "<div class=\"javo-thb\" style=\"background-image:url({$jvbpd_this_thumb});\"></div>";

			$strAddition_meta			= '';
			if( class_exists( 'jvbpd_Module' ) && class_exists( 'jvbpd_Directory_Shortcode') ) {
				add_filter( 'jvbpd_jvbpd_Module_additional_meta', Array( jvbpd_Directory_Shortcode::$scdInstance, 'additional_meta' ), 10, 2 );
				$objShortcode			= new jvbpd_Module( $post );
				$strAddition_meta		= "<i class='fa fa-map-marker'></i><span class='jvfrm_info_location'>".$objShortcode->c( 'listing_location', __( "Not Set", 'jvfrmtd' ) )." </span><i class='fa fa-bookmark'></i><span class='jvfrm_info_category'> ".$objShortcode->c( 'listing_category', __( "Not Set", 'jvfrmtd' ) )."</span>";
			}

			$meta_rating = '';
			if(class_exists( 'Lava_Directory_Review' )){
				$strTemplate			= '';
				$ratingScore			= floatVal( get_post_meta( $post->ID, 'rating_average', true ) );
				$ratingPercentage	= floatVal( ( $ratingScore / 5 ) * 100 ) . '%';
				$rating2x					= intVal( $ratingScore ) * 2;
				$meta_rating = "<div class='meta-rating-wrap'><div class='meta-rating' style=\"width:" . esc_html( $ratingPercentage ) .";\"></div></div>";
			}

			// Other Informations
			$jvbpd_result					= Array(
				'state'					=> 'success'
				, 'meta'				=> $strAddition_meta
				, 'post_id'				=> $post->ID
				, 'post_title'			=> $post->post_title
				, 'permalink'			=> get_permalink( $post->ID )
				, 'thumbnail'			=> $jvbpd_this_thumb
				, 'author_name'			=> $jvbpd_this_author->display_name
				, 'rating' => $meta_rating
			);
		}
		wp_send_json( $jvbpd_result );
	}

	public function mapBlockContents() {
		if( ! defined( 'JVBPD_MAP_AJAX' ) ) {
			define( 'JVBPD_MAP_AJAX', true );
		}

		$argsPosts		= isset( $_REQUEST[ 'post_ids' ] ) ? (Array) $_REQUEST[ 'post_ids' ] : Array();
		$argsTemplate	= isset( $_REQUEST[ 'template' ] ) ? $_REQUEST[ 'template' ] : 0;

		// $clsMapName		= 'module15';
		/*

		if( !class_exists( jvbpdCore()->var->getCoreName( 'Map' ) ) )
			$clsMapName					= 'module12';

		*/
		$clsMapName = isset( $_REQUEST[ 'mapModule' ] ) && 'false' != $_REQUEST[ 'mapModule' ] ? $_REQUEST[ 'mapModule' ] : 'module15';
		$clsListName = isset( $_REQUEST[ 'listModule' ] ) && 'false' != $_REQUEST[ 'listModule' ] ? $_REQUEST[ 'listModule' ] : 'module15';

		/*
		$clsMapName		= apply_filters( 'jvbpd_template_map_module', $clsMapName, $argsTemplate );
		$clsListName	= apply_filters( 'jvbpd_template_list_module', $clsListName, $argsTemplate );
		$strBasicTemplate = $strMapTemplate = "<div class=\"col-md-6\">%s</div>";
		*/

		$mapColumns = isset( $_REQUEST[ 'mapColumns' ] ) ? intVal( $_REQUEST[ 'mapColumns' ] ) : 1;
		$listColumns = isset( $_REQUEST[ 'listColumns' ] ) ? intVal( $_REQUEST[ 'listColumns' ] ) : 1;
		$outputColumns = Array();
		foreach( Array( 'map' => $mapColumns, 'list' => $listColumns ) as $type => $typeColumn ) {
			switch( $typeColumn ) {
				case 2: $outputColumns[ $type ] = '<li class="col-md-6 jvbpd-module" data-post-id="%s">%s</li>'; break;
				case 3: $outputColumns[ $type ] = '<li class="col-md-4 jvbpd-module" data-post-id="%s">%s</li>'; break;
				case 4: $outputColumns[ $type ] = '<li class="col-md-3 jvbpd-module" data-post-id="%s">%s</li>'; break;
				default: $outputColumns[ $type ] = '<li class="col-md-12 jvbpd-module one-row" data-post-id="%s">%s</li>';
			}
		}



		/*
		if( !class_exists( jvbpdCore()->var->getCoreName( 'Map' ) ) )
			$strMapTemplate	= "<div class=\"col-md-6\">%s</div>";

		$arrBasicModuleOption = Array(
			'length_content' => 12,
			'length_title' => 10,
		);

		$strMapColumn = apply_filters( 'jvbpd_template_map_module_loop', $strMapTemplate, $clsMapName, $argsTemplate );
		$strListColumn = apply_filters( 'jvbpd_template_list_module_loop', $strListTemplate, $clsListName, $argsTemplate );
		$arrMapModuleOption = apply_filters( 'jvbpd_template_map_module_options', $arrBasicModuleOption, $argsTemplate );
		$arrListModuleOption = apply_filters( 'jvbpd_template_list_module_options', $arrBasicModuleOption, $argsTemplate );
		*/

		if( empty( $argsPosts ) )
			die( json_encode( array( 'state' => 'fail' ) ) );

		$arrOutput			= Array( 'map' => Array(), 'list' => Array() );
		//$arrPosts				= get_posts( Array( 'post_type' => jvbpd_core()->slug, 'include' => $argsPosts ) );

		// do_action( 'jvbpd_template_all_module_loop_before', $argsTemplate );

		$notContent = '<div class="alert alert-warning" role="alert">';
		$notContent .= esc_html__("Please select a module", 'jvfrmtd');
		$notContent .= '</div>';

		$map_template = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $clsMapName );
		if('' == $map_template ) {
			$map_template = $notContent;
		}
		$list_template = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $clsListName );
		if('' == $map_template ) {
			$map_template = $list_template;
		}
		if( !empty( $argsPosts ) ) : foreach( $argsPosts as $post_id ) {

			if( false == get_post_status( $post_id ) ) {
				continue;
			}

			$mapInstance = new Jvbpd_Replace_Content( $post_id, $map_template );
			$listInstance = new Jvbpd_Replace_Content( $post_id, $list_template );
			$arrOutput['map'][] = sprintf( $outputColumns['map'], $post_id, $mapInstance->render() );
			$arrOutput['list'][] = sprintf( $outputColumns['list'], $post_id, $listInstance->render() );

			/*
			if( class_exists( $clsMapName ) && class_exists( $clsListName ) ) {
				$objModuleMap		= new $clsMapName( $post, $arrMapModuleOption );
				$objModuleList			= new $clsListName( $post, $arrListModuleOption );
				$arrOutput['map'][]	= sprintf( $strMapColumn, $objModuleMap->output() );
				$arrOutput['list'][]		= sprintf( $strListColumn, $objModuleList->output() );
			}else{
				$arrOutput['map'][] = $arrOutput['list'][] = join( '',
					Array(
						'<div class="alert alert-warning text-center">',
							sprintf( esc_html__( "Missing - %s or %s : ", 'jvfrmtd' ), $clsMapName, $clsListName ),
							esc_html__( "You must activate Javo Core Pluign (required plugin) to work properly. please activate the plugin.", 'jvfrmtd' ),
						'</div>',
					)
				);
			} */
		} endif;
		// do_action( 'jvbpd_template_all_module_loop_after', $argsTemplate );
		wp_send_json( Array( 'list' => join( '', $arrOutput['list'] ), 'map' => join( '', $arrOutput['map'] ) ) );
	}

	public function searchGetKeywords() {
		global $wpdb;
		$strQuery = isset( $_POST[ 'query' ] ) ? $_POST[ 'query' ] : '';
		$keywords = get_terms( array( 'taxonomy' => 'listing_keyword', 'hide_empty' => false, 'name__like' => $strQuery ) );
		$queryPostTitles = $wpdb->prepare( "SELECT post_title FROM {$wpdb->posts} WHERE post_type=%s and post_title like %s;", 'lv_listing', '%'.$strQuery.'%' );

		foreach( $wpdb->get_results( $queryPostTitles ) as $post ) {
			$keywords[] = (object) Array(
				'term_id' => $post->post_title,
				'name' => $post->post_title,
			);
		}
		wp_send_json( $keywords );
	}

	public function previewModalContent(){
		$post = get_post( intVal( $_POST[ 'post_id' ] ) );
		$arrReturn = Array( 'html' => '' );
			$arrHTML = Array();
			$arrHTML[] = sprintf(
				'<div class="row">
					<div class="col-md-12">
						<a href="%1$s"><h1>%2$s</h1></a>
					</div>
					<div class="col-md-12">

					</div>
				</div>',
				get_permalink( $post ),
				$post->post_title
			);

			$arrHTML[] = sprintf(
				'<div class="row">
					<div class="col-md-6"><a href="%1$s">%2$s</a></div>
					<div class="col-md-6 alert alert-light-gray">
						<div class="">%3$s</div>
						%4$s
					</div>
				</div>',
				get_permalink( $post ),
				get_the_post_thumbnail( $post, 'thumbnail', Array( 'class' => 'img-circle img-inner-shadow' ) ),
				esc_html__('Description','jvfrmtd'),
				get_the_excerpt( $post )
			);

			$arrHTML[] = sprintf(
				'<div class="row"><div class="col-md-6"><ul class="list-unstyled"><li>%1$s : %2$s</li><li>%3$s : %4$s</li><li>%5$s : %6$s</li></ul></div></div>',
				esc_html__( 'Phone1', 'jvfrmtd'), get_post_meta( $post->ID, '_phone1', true ),
				//esc_html__( 'Email', 'jvfrmtd'), get_post_meta( $post->ID, '_email', true ),
				esc_html__( 'Website', 'jvfrmtd'), get_post_meta( $post->ID, '_website', true ),
				esc_html__( 'Website', 'jvfrmtd'), esc_html__( 'Website', 'jvfrmtd'),
				esc_html__( 'Website12', 'jvfrmtd'), esc_html__( 'Website12', 'jvfrmtd')
			);

			$arrHTML = apply_filters( 'jvbpd_core/ajax/brief/get_html', $arrHTML, $post );

		$arrReturn[ 'html' ] = join( false, $arrHTML );
		wp_send_json( $arrReturn );
	}

	public function pageBlockContent() {
		$post = get_post( intVal( $_POST[ 'post_id' ] ) );
		$arrReturn[ 'html' ] = '<p>' . $post->post_content . '</p>';
		wp_send_json( $arrReturn );
	}

	public function previewModalSliderContent() {
		$post = get_post( intVal( $_POST[ 'post_id' ] ) );
		$images = (Array) get_post_meta( $post->ID, 'detail_images', true );
		ob_start();
		?>
		<div class="swiper-container" style="max-width:1024px;">
			<div class="swiper-wrapper">
				<?php
				foreach( array_filter($images) as $imageID ) {
					echo wp_get_attachment_image( $imageID, 'large', false, Array( 'class' => 'swiper-slide' ) );
				} ?>
			</div>

			<div class="swiper-pagination"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
			<div class="swiper-scrollbar"></div>
		</div>
		<?php
		$arrReturn = Array( 'post_id'=> $post->ID, 'html' => ob_get_clean() );
		wp_send_json( $arrReturn );
	}

	public function getCanvasRender() {
		$templateID = isset( $_POST[ 'template' ] ) ? $_POST[ 'template' ] : 0;
		$render = null;
		$render = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $templateID );
		wp_send_json( Array( 'render' => $render ) );
	}

	public function getLoginFormRender() {
		ob_start();
		function_exists( 'jvbpd_login_content') && jvbpd_login_content();
		wp_send_json( Array( 'render' => ob_get_clean() ) );
	}

	public function getSignupFormRender() {
		ob_start();
		function_exists( 'jvbpd_signup_content') && jvbpd_signup_content();
		wp_send_json( Array( 'render' => ob_get_clean() ) );
	}

	public function getListingCategoryFeaturedImage() {
		$taxonomy = 'listing_category';
		$term_id = isset( $_POST[ 'term_id' ] ) ? intVal( $_POST[ 'term_id' ] ) : 0;
		$output = Array( 'term_id' => $term_id );
		if( function_exists( 'lava_directory' ) ){
			$output[ 'attach_id' ] = lava_directory()->admin->getTermOption( $term_id, 'featured', $taxonomy );
			foreach( Array( 'thumbnail', 'medium', 'large', 'full' ) as $imageSize ) {
				$output[ $imageSize ] = wp_get_attachment_image_url( $output[ 'attach_id' ], $imageSize );
			}
		}
		wp_send_json( $output );
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

if( ! function_exists( 'jvbpd_coreAjax' ) ) {
	function jvbpd_coreAjax() {
		return Jvbpd_Core_Ajax::getInstance();
	}
	jvbpd_coreAjax();
}