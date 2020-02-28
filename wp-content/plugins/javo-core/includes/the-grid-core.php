<?php

if( ! function_exists( 'jvfrm_spot_thegrid_custom_css' ) ) {
	function jvfrm_spot_thegrid_custom_css( $rows ) {
		$rows[] = sprintf(
			'.tg-jv-meta-rating{ background-image:url(%s) !important; }',
			JVFRM_SPOT_IMG_DIR . '/star-all.png'
		);
		return $rows;
	}
	add_filter( 'jvfrm_spot_custom_css_rows', 'jvfrm_spot_thegrid_custom_css' );
}


/**
 * Detect plugin. For use in Admin area only.
 */

function check_lava_plugin() {
	if( function_exists( 'lava_directory' ) ) {
		add_filter('tg_register_item_skin', function( $skins ) {
		// just push your skin slugs (file name) inside the registered skin array
		array_push($skins,
			// JV Grid
			'javo-grid-skin1',
			'jv-alofi',
			'jv-apia',
			'jv-bogota',
			'jv-brasilia',
			'jv-camberra',
			'jv-caracas',
			'jv-dacca',
			'jv-honiara',
			'jv-lisboa',
			'jv-lome',
			'jv-malabo',
			'jv-male',
			'jv-maputo',
			'jv-oslo',
			'jv-podgorica',
			'jv-pracia',
			'jv-roma',
			'jv-sofia',
			'jv-suva',
			'jv-flip',
			// JV Masonry
			'jv-doha',
			'jv-kampala',
			'jv-lima',
			'jv-lusaka',
			'jv-maren',
			'jv-panama',
			'jv-praia',
			'jv-quito',
			'jv-riga',
			'jv-sanaa',
			'jv-vaduz',
			'jv-victoria',
			// Custom
			'javo-masonry-skin1',
			'javo-skin2');
		return $skins;
	});


	add_filter('tg_register_item_skin', function($skins){

		$skins = array_merge($skins,
			array(
				'javo-grid-skin1'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-alofi'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-apia'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-bogota'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-brasilia'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-camberra'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-caracas'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-dacca'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-honiara'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-lisboa'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-lome'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-malabo'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-male'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-maputo'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-oslo'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-podgorica'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-pracia'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-roma'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-sofia'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-suva'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-filp'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				// JV Masonry
				'jv-doha'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-kampala'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-lima'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-lusaka'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-maren'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-panama'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-praia'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-quito'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-riga'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				/*'jv-sanaa'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),*/
				'jv-vaduz'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'jv-victoria'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				// Custom
				'javo-masonry-skin1'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				),
				'javo-skin2'=> array(
					'filter' => 'Javo', // filter button name
					'col'    => 1, // col number in preview skin mode
					'row'    => 1  // row number in preview skin mode
				)
			)
		);
		return $skins;
	});

	class JV_The_Grid_Elements {
		public $post_ID;
		public $jv_category;
		public $jv_tax;
		public $jv_type;
		public $lv_rating_average;

		static private $instance = null;
		static public function getInstance() {
			if(self::$instance == null) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		* To initialize a The_Grid_Elements object
		* @since 1.0.0
		*/
		public function init() {
			// set the grid base helper class
			//$this->base = new The_Grid_Base();
			// retrieve grid data
			//$this->grid_data = tg_get_grid_data();
			// retrieve grid item data
			//$this->grid_item = tg_get_grid_item();
			// retrieve item colors
			//$this->item_colors = $this->get_colors();
		}


		public function get_jv_category() {
			return "<span class='tg-jv-category'><i class='glyphicon glyphicon-bookmark'></i>". $this->getTermName( 'listing_category' ) ."</span>";
		}

		public function get_jv_location() {
			return "<span class='tg-jv-location'><i class='glyphicon glyphicon-map-marker'></i>". $this->getTermName( 'listing_location' ) ."</span>";
		}

		public function get_jv_rating_ave() {
			if( function_exists( 'get_lava_directory_review' ) )
				return "<div class='tg-jv-meta-rating-wrap'><div class='tg-jv-meta-rating' style='width:" . floatVal( ( $this->getPostMeta('rating_average') / 5 ) * 100 )."%;'></div></div>";
		}



		public function getTermName( $strTaxonomy='' ){
			//echo "aaa=".$strTaxonomy."bbb";
			//$post_ID=8953;
			//echo "post-id=". $post_ID;
			//echo "post-id=". $item_ID;
			$the_item_id=The_Grid_Elements()->get_item_ID();
			//echo "item_id=".$the_item_id;


			$arrTerms = wp_get_object_terms( $the_item_id, $strTaxonomy, array( 'fields' => 'names', 'orderby'=>'parent' ));
			$strOutput = join( ', ', $arrTerms );
			//return $strOutput;
			//var_dump($strOutput);
			//echo "ok";
			return !empty( $arrTerms[0] ) ? $arrTerms[0] : false;
		}

		public function getPostMeta($listing_postmeta){
			$the_item_id=The_Grid_Elements()->get_item_ID();
			return get_post_meta( $the_item_id, $listing_postmeta, true );
		}

	}


	if(!function_exists('JV_The_Grid_Elements')) {

		/**
		* Tiny wrapper function
		* @since 1.0.0
		*/
		function JV_The_Grid_Elements() {
			$to_Item_Content = JV_The_Grid_Elements::getInstance();
			$to_Item_Content->init();
			return $to_Item_Content;
		}

	}


	//Video Site => _video_portal
	//Video ID => _video_id

	if( !class_exists('jvfrm_spot_listing_meta')){
		class jvfrm_spot_listing_meta
		{
			public $lv_tax;
			public $lv_category;
			public $lv_category_tag;
			public $lv_location;
			public $lv_rating_average;
			public $lv_featured_listing;

			public static $instance = null;

			public static function getInstance() {
				if(self::$instance === null) {
					self::$instance = new self;
				}
				return self::$instance;
			}

			public function __construct() {}

			public function process($options) {
				// set main item/grid vars
				$this->set_var($options);

				$jv_content['lv_category'] = !empty($this->lv_category) ? "<span class='tg-jv-category'><i class='glyphicon glyphicon-bookmark'></i>". $this->lv_category ."</span>":false;
				$jv_content['lv_category_tag'] = !empty($this->lv_category) ? "<div class='tg-jv-category-tag'>". $this->lv_category ."</div>":false;
				$jv_content['lv_location'] = !empty($this->lv_location) ? "<span class='tg-jv-location'><i class='glyphicon glyphicon-map-marker'></i>". $this->lv_location ."</span>":false;
				
				//echo "aaaaa=". $this->lv_rating_average;

				if(is_numeric($this->lv_rating_average) && is_numeric($this->lv_rating_average)){
					$jv_content['lv_rating_average'] = "<div class='tg-jv-meta-rating-wrap'><div class='tg-jv-meta-rating' style='width:" . floatVal( ( $this->lv_rating_average / 5 ) * 100 )."%;'></div></div>";
				}else{
					$jv_content['lv_rating_average'] = "";
				}
				$jv_content['lv_featured_listing'] = intVal( $this->lv_featured_listing ) > 0 ? "<div class='tg-jv-featured-listing-wrap triangle1'><div class='tg-jv-featured-listing-inner'><span class='featured'>HOT</span></div></div>":false ;
				//$jv_content['lv_featured_listing'] = intVal( $this->lv_featured_listing ) > 0 ? "<div class='featured-listing-wrap triangle2'><div class='featured-listing-inner'><span class='featured'><i class='glyphicon glyphicon-heart'></i></span></div></div>":false ;




				return $jv_content;
			}

			public function set_var($option)
			{
				$this->lv_category = $this->getTermName( 'listing_category' );
				$this->lv_location = $this->getTermName( 'listing_location' );
				$this->lv_rating_average = $this->getPostMeta( 'rating_average' );
				$this->lv_featured_listing = $this->getPostMeta( '_featured_item' );
			}

			public function getTermName( $strTaxonomy='' ){
				$the_item_id=The_Grid_Elements()->get_item_ID();
				$arrTerms = wp_get_object_terms( $the_item_id, $strTaxonomy, array( 'fields' => 'names', 'orderby'=>'parent' ));
				$strOutput = join( ', ', $arrTerms );
				//return $strOutput;
				return !empty( $arrTerms[0] ) ? $arrTerms[0] : false;
			}

			public function getPostMeta($listing_postmeta){
				return get_post_meta( get_the_ID(), $listing_postmeta, true );
			}
		}

	}

	if(!function_exists('jvfrm_spot_listing_meta_output')) {
		/**
		* Tiny wrapper function
		* @since 1.0.0
		*/
		function jvfrm_spot_listing_meta_output($jv_options = '') {
			$jv_to_Item_Content = jvfrm_spot_listing_meta::getInstance();
			return $jv_to_Item_Content->process($jv_options);
		}

	}

	}// checking lava plugin is activated
}
add_action( 'init', 'check_lava_plugin' );