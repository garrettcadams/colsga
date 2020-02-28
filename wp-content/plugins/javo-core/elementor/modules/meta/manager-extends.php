<?php
namespace jvbpdelement\Modules\Meta\Widgets;

if( class_exists( 'Lava_Directory_Manager_Field' ) ) {
	class listing_field extends \Lava_Directory_Manager_Field {

		public $post_id = 0;
		public $post = null;

		public function __construct( $field, $args=Array() ) {

			$this->post_id = intVal( get_query_var( 'edit' ) );
			if( false !== get_post_status( $this->post_id ) && lava_directory()->core->slug == get_post_type( $this->post_id ) ) {
				$this->post = get_post( $this->post_id );
			}else{
				$this->post = (object) Array(
					'ID' => 0,
					'post_title' => false,
					'post_content' => false,
					'post_author' => false,
				);
			}
			$this->argsFilter( $field );
			parent::__construct( $field, $args );
		}

		public function argsFilter( $field ) {
			$value = false;
			$field = str_replace( '][', '', $field );
			switch( $field ) {
				case 'txt_title' :
					$value = $this->post->post_title;
					break;
				case 'txt_content' :
					$value = $this->post->post_content;
					break;
				case 'listing_category' :
				case 'listing_location' :
					$value = wp_get_object_terms( $this->post->ID, $field, Array( 'fields' => 'ids' ) );
					break;
				case 'listing_keyword' :
					$value = wp_get_object_terms( $this->post->ID, $field, Array( 'fields' => 'names' ) );
					break;
				default:
					$value = get_post_meta( $this->post->ID, $field, true );
			}
			$this->value = $value;
		}

		public function register_hooks() {
			parent::register_hooks();
			add_filter( $this->hook_prefix . 'user_join_form', array( __CLASS__, 'user_join_form' ), 10, 3 );
			add_filter( $this->hook_prefix . 'featured_image', array( __CLASS__, 'featured_image' ), 10, 3 );
			add_filter( $this->hook_prefix . 'detail_image', array( __CLASS__, 'detail_image' ), 10, 3 );
			add_filter( $this->hook_prefix . 'map', array( __CLASS__, 'map' ), 10, 3 );
			add_filter( $this->hook_prefix . 'custom_field', array( __CLASS__, 'custom_field' ), 10, 3 );
		}

		public static function user_join_form() {
			if(is_user_logged_in() && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			$loginURL = apply_filters( "lava_lv_listing_login_url", wp_login_url() );
			ob_start();
			?>
			<div class="form-inner">
				<label class="field-title"><?php _e("Login", "Lavacode"); ?></label>
				<?php _e( "If you have an account?", 'Lavacode'); ?>&nbsp;
				<a href="<?php echo esc_url($loginURL); ?>"> <?php _e( "Please Login", 'Lavacode' ); ?> </a>
			</div>

			<div class="form-inner">
				<label class="field-title"><?php _e("User Email", "Lavacode"); ?></label>
				<input name="user_email" type="email" value="" placeholder="<?php _e( "Email Address",'Lavacode' ); ?>">
			</div>

			<div class="form-inner">
				<label class="field-title"><?php _e("User Password", "Lavacode"); ?></label>
				<input name="user_pass" type="password" value="" placeholder="<?php _e( "Password",'Lavacode' ); ?>">
			</div>
			<?php
			return ob_get_clean();
		}

		public static function featured_image( $output=null, $field=false, $obj=false ) {
			ob_start();
			if( ! is_user_logged_in() ) :
				if( has_post_thumbnail( $obj->post ) ) :
					echo get_the_post_thumbnail( $obj->post ); ?>
					<div>
						<label>
							<input type="checkbox" name="lava_remove_featured_file" value="1" style="width:auto;">
							<span><?php esc_html_e( "Remove featured image", 'Lavacode' ); ?></span>
						</label>
					</div>
				<?php endif; ?>
				<input type="file" name="lava_featured_file">
			<?php
			else:
				$intFeaturedID = get_post_thumbnail_id( $obj->post->ID );
				$strFeatured = wp_get_attachment_url( $intFeaturedID );
				?>
				<div class="lava-listing-wp-media" data-field="featured_id" data-multiple="false" data-modal-title="<?php esc_html_e( "Featured Image", 'Lavacode' ); ?>" data-button-select="<?php esc_html_e( "Select", 'Lavacode' ); ?>">
					<input type="hidden" name="featured_id" value="<?php echo $intFeaturedID; ?>">
					<div class="upload-preview" style="background-image:url(<?php echo $strFeatured; ?>);"></div>
					<div class="upload-action">
						<button type="button" class="action-add-item button">
							<i class="fa fa-plus"></i>
							<?php esc_html_e( "Select", 'Lavacode' ); ?>
						</button>
						<button type="button" class="item-clear button">
							<i class="fa fa-remove"></i>
							<?php esc_html_e( "Clear", 'Lavacode' ); ?>
						</button>
					</div>
				</div>
				<?php
			endif;
			return ob_get_clean();
		}

		public static function detail_image( $output=null, $field=false, $obj=false ) {
			ob_start();
			$intDetailImageLimit = lava_directory()->submit->getLimitDetailImages();
			$arrDetailImages = get_post_meta( $obj->post->ID, 'detail_images', true );
			$arrDetailImageOutput = $arrAttachmentOutput = Array();
			if( is_arraY( $arrDetailImages ) ) {
				foreach( $arrDetailImages as $intImage ) {
					$arrDetailImageOutput[] = Array(
						'val' => $intImage,
						'img' => wp_get_attachment_image( $intImage ),
					);
				}
			}
			if( ! is_user_logged_in() ) :
				?>
				<div class="lava-upload-wrap" data-field="lava_additem_meta[detail_images][]" data-limit="<?php echo $intDetailImageLimit; ?>" data-value="<?php echo htmlspecialchars( json_encode( $arrDetailImageOutput ) ); ?>">
					<div class="upload-item-group"></div>
				</div>
			<?php
			else:
				?>
				<div class="lava-listing-wp-media" data-field="lava_additem_meta[detail_images][]" data-multiple="true" data-value="<?php echo htmlspecialchars( json_encode( $arrDetailImageOutput ) ); ?>" data-modal-title="<?php esc_html_e( "Detail Images", 'Lavacode' ); ?>" data-button-select="<?php esc_html_e( "Select", 'Lavacode' ); ?>" data-button-remove="<?php esc_html_e( "Remove", 'Lavacode' ); ?>" data-limit="<?php echo $intDetailImageLimit; ?>">
					<input type="hidden" name="lava_additem_meta[detail_images]">
					<div class="upload-item-group"></div>
					<div class="upload-action">
						<button type="button" class="action-add-item button">
							<i class="fa fa-plus"></i>
							<?php esc_html_e( "Select", 'Lavacode' ); ?>
						</button>
					</div>
				</div>
				<?php
			endif;
			return ob_get_clean();
		}

		public static function map( $output=null, $field=false, $obj=false ) {
			ob_start();
			?>
			<div class="address-group">
				<input class="lava-add-item-map-search" placeholder="<?php _e("Type an Address","Lavacode");?>">
				<input type="button" value="<?php _e('Find','Lavacode'); ?>" class="lava-add-item-map-search-find">
			</div>
			<div class="lava-field-item map_area field_map"></div>
			<?php
			/*
			<input type="hidden" name="lava_location[country]" value="<?php echo $obj->post->country; ?>">
			<input type="hidden" name="lava_location[locality]" value="<?php echo $obj->post->locality; ?>">
			<input type="hidden" name="lava_location[political]" value="<?php echo $obj->post->political; ?>">
			<input type="hidden" name="lava_location[political2]" value="<?php echo $obj->post->political2; ?>">
			<input type="hidden" name="lava_location[lat]" class="only-number" value="<?php echo $obj->post->lat; ?>">
			<input type="hidden" name="lava_location[lng]" class="only-number" value="<?php echo $obj->post->lng; ?>">
			*/ ?>
			<div class="lava_map_advanced hidden">
			<div class="lava-field-item map_area_streetview field_streeview"></div>
				<?php
				foreach( Array(
					'country' => Array( 'label' => esc_html__( "Country", 'Lavacode' ), ),
					'locality' => Array( 'label' => esc_html__( "Locality", 'Lavacode' ), ),
					'political' => Array( 'label' => esc_html__( "Political", 'Lavacode' ), ),
					'political2' => Array( 'label' => esc_html__( "Plitical2", 'Lavacode' ), ),
					'lat' => Array( 'label' => esc_html__( "Lat", 'Lavacode' ), ),
					'lng' => Array( 'label' => esc_html__( "Lng", 'Lavacode' ), ),
					'street_lat' => Array( 'label' => esc_html__( "Streetview Lat", 'Lavacode' ), ),
					'street_lng' => Array( 'label' => esc_html__( "Streetview Lng", 'Lavacode' ), ),
					'street_heading' => Array( 'label' => esc_html__( "POV: Heading", 'Lavacode' ), ),
					'street_pitch' => Array( 'label' => esc_html__( "POV: Pitch", 'Lavacode' ), ),
					'street_zoom' => Array( 'label' => esc_html__( "POV : Zoom", 'Lavacode' ), ),
				) as $fID => $meta ) {
					$meta = wp_parse_args( Array(
						'element' => 'input',
						'type' => 'text',
						'class' => 'all-options',
					), $meta );
					$objField = new self( $fID, $meta );
					$objField->fieldGroup = 'lava_location';
					$objField->fieldClassPrefix = 'field_';
					$objField->value = floatVal( get_post_meta( intVal( get_query_var( 'edit' ) ), 'lv_listing_' . $fID, true ) );
					echo $objField->output();
				} ?>
			</div>
			<?php
			return ob_get_clean();
		}

		public static function custom_field( $output=null, $field=false, $obj=false ) {
			global $edit;
			if(!function_exists('lv_directory_customfield')) {
				return;
			}
			ob_start();
			lv_directory_customfield()->template->append_to_write_form($edit);
			return ob_get_clean();
		}
	}
}