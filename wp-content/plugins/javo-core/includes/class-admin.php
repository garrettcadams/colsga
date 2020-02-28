<?php
class Jvbpd_Core_Admin extends Jvbpd_Core {

	const PAGE_SETTINGS_KEY = 'jvbpd_page_setting';

	private $modules = Array();

	private $module_option_format = 'lynk_term_%s_module';

	public function __construct() {
		add_action( 'jvbpd_modukles_loaded', Array( $this, 'get_modules' ) );
		add_filter( 'manage_edit-category_columns' , Array( $this, 'custom_category_column' ) );
		add_filter( 'manage_category_custom_column' , Array( $this, 'custom_category_contents' ), 10, 3 );
		add_action( 'category_add_form_fields', Array( $this, 'append_module_selector' ) );
		add_action( 'category_edit_form_fields', Array( $this, 'append_module_selector' ) );
		add_action( 'created_category', Array( $this, 'update_module_name' ), 10, 2 );
		add_action( 'edited_category', Array( $this, 'update_module_name' ), 10, 2 );
		add_action( 'deleted_term_taxonomy', Array( $this, 'trash_module_name' ) );

		add_action( 'jvbpd_contact_send_mail', Array( $this, 'sendsMsnage' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_menu' ), 999 );

		add_filter( 'jvbpd_wizard_addons', array( $this, 'setAddonsListInWizard' ) );
		add_filter( 'jvbpd_wizard_footer', array( $this, 'wizardFooterBanner' ) );

		// add_action('add_meta_boxes', Array($this, 'custom_meta_boxes'));
		// add_action('save_post', Array($this, 'save_custom_meta_boxes'));

		add_action( 'add_meta_boxes', Array( $this, 'reigster_meta_box' ) );
		add_action( 'save_post', array( $this, 'custom_elementor_header_single_save' ) );

		$this->adminPostColumn();


	}

	public function get_term_module( $term_id, $default=false ) {
		return get_option( sprintf( $this->module_option_format, $term_id ), $default );
	}

	public function get_term_module_post_length( $term_id, $default=false ) {
		return get_option( sprintf( $this->module_option_format . '_length' , $term_id ), $default );
	}

	public function get_term_module_columns( $term_id, $default=false ) {
		return get_option( sprintf( $this->module_option_format . '_columns' , $term_id ), $default );
	}

	public function get_modules( $modules ) {
		$this->modules	= Array_diff(
			Array_keys( $modules ),
			Array(
				'module2'
			)
		);
	}

	public function custom_category_column( $columns ) {
		return wp_parse_args(
			$columns,
			Array(
				'cb'				=> '<input type="checkbox">',
				'name'			=>__( "Name", 'jvfrmtd' ),
				'description'	=>__( "Description", 'jvfrmtd' ),
				'jvbpd_modukle'	=> __( "Module Name", 'jvfrmtd' ),
			)
		);
	}

	public function custom_category_contents( $dep, $column_name, $term_id ) {
		switch( $column_name ) {
			case 'jvbpd_modukle' :
				if(  ! $module_name = $this->get_term_module( $term_id ) )
					$module_name	 = __( "None", 'jvfrmtd' );
				echo $module_name;
			break;
		}
	}

	public function append_module_selector( $objTaxonomy ) {

		if( ! is_Array( $this->modules ) )
			return;

		$term_id	= 0;
		if( is_object( $objTaxonomy ) )
			$term_id = $objTaxonomy->term_id;

		$arrOptionModules					=
		$arrOptionModules_columns	= Array();
		foreach( $this->modules as $module_name )
			$arrOptionModules[]	= sprintf(
				"<option value=\"{$module_name}\"%s>{$module_name}</option>",
				selected( $module_name == $this->get_term_module( $term_id ), true, false )
			);

		for( $intColumn=1; $intColumn <= 3; $intColumn++ )
			$arrOptionModules_columns[]	= sprintf(
				"<option value=\"{$intColumn}\"%s>{$intColumn} %s</option>",
				selected( $intColumn == $this->get_term_module_columns( $term_id ), true, false ),
				_n( "Column", "Columns", $intColumn, 'jvfrmtd' )
			);

		echo join( "\n",
			Array(
				'<tr>',
					'<th>',
						__( "Archive Module", 'jvfrmtd' ),
					'</th>',
					'<td>',
						sprintf( '<h4>%s</h4>', __( "Module Name", 'jvfrmtd' ) ),
						'<fieldset class="inner">',
							'<select name="lynk_category[_module]">',
							'<option value="">' . __( "Default Template", 'jvfrmtd' ) . '</option>',
							join( "\n", $arrOptionModules ),
							'</select>',
						'</fieldset>',
						sprintf( '<h4>%s</h4>', __( "Module Columns", 'jvfrmtd' ) ),
						'<fieldset class="inner">',
							'<select name="lynk_category[_module_columns]">',
							join( "\n", $arrOptionModules_columns ),
							'</select>',
						'</fieldset>',
						sprintf( '<h4>%s</h4>', __( "Post Content Length", 'jvfrmtd' ) ),
						'<fieldset class="inner">',
							sprintf( '<input type="number" name="lynk_category[_module_length]" value="%s">', $this->get_term_module_post_length( $term_id ) ),
						'</fieldset>',
					'</td>',
				'</tr>',
			)
		);
	}

	public function update_module_name( $term_id, $taxonomy_id ){

		if( empty( $term_id ) || empty( $_POST[ 'lynk_category' ] ) )
			return;

		foreach( $_POST[ 'lynk_category'] as $key_name => $value )
			update_option( 'lynk_term_' . $term_id . $key_name, $value );
	}

	public function trash_module_name( $term_id ) {
		delete_option( $this->module_option_format );
		delete_option( $this->module_option_format . '_length' );
		delete_option( $this->module_option_format . '_columns' );
	}

	static function send_mail_content_type(){ return 'text/html';	}
	public function sendsMsnage() {
		$jvbpd_query = new jvbpd_array( $_POST );
		$jvbpd_this_return = Array();
		$jvbpd_this_return['result'] = false;
		$meta = Array(
			'to' => $jvbpd_query->get('to', NULL),
			'subject' => $jvbpd_query->get('subject', esc_html__('Untitled Mail', 'jvfrmtd')).' : '.get_bloginfo('name'),
			'from' => sprintf("From: %s<%s>\r\n", get_bloginfo('name'),			$jvbpd_query->get('from', get_option('admin_email') )
			),
			'content' => $jvbpd_query->get( 'content', NULL ),
		);

		if(
			$jvbpd_query->get('to', NULL) != null &&
			$jvbpd_query->get('from', NULL) != null
		){
			add_filter( 'wp_mail_content_type', Array(__CLASS__, 'send_mail_content_type') );
			$mailer = wp_mail(
				$meta['to']
				, $meta['subject']
				, $meta['content']
				, $meta['from']
			);
			$jvbpd_this_return['result'] = $mailer;
			remove_filter( 'wp_mail_content_type', Array(__CLASS__, 'send_mail_content_type'));
		};

		die( $jvbpd_this_return );
	}

	public function add_admin_menu( $wp_admin_bar ) {

		if( ! function_exists( 'jvbpd_admin_helper_init' ) ) {
			return false;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$setting_link = add_query_arg(
			Array(
				'page' => jvbpd_admin_helper_init()->parent_slug . '_settings',
			), admin_url( 'admin.php' )
		);

		$wp_admin_bar->add_node( Array(
			'id' => get_class( $this ) . '-ts',
			'title' => esc_html__( "Theme Settings", 'jvfrmtd' ),
			'href' => esc_url( $setting_link ),
		) );
	}

	public function setAddonsListInWizard( $addons=Array() ) {
		$addons = Array(
			Array(
				'name' => 'Lava 3D Viewer',
				'slug' => sanitize_title( 'lava-directory-3dViewer' ),
				'version' => '1.0.3',
				'source' => jvbpdCore()->addons_url . 'lava-directory-3dviewer.zip',
			),
			Array(
				'name' => 'Lava post review',
				'slug' => sanitize_title( 'lava-post-review' ),
				'version' => '1.0.2.1',
				'source' => jvbpdCore()->addons_url . 'lava-post-review.zip',
			),
			Array(
				'name' => 'Lava More Taxonomies',
				'slug' => sanitize_title( 'lava-more-taxonomies' ),
				'version' => '1.0.3',
				'source' => jvbpdCore()->addons_url . 'lava-more-taxonomies.zip',
			),
			Array(
				'name' => 'Lava FaQ',
				'slug' => sanitize_title( 'lava-faq' ),
				'version' => '1.0.1.1',
				'source' => jvbpdCore()->addons_url . 'lava-faq.zip',
			),
			Array(
				'name' => 'Lava Favorite',
				'slug' => sanitize_title( 'lava-directory-favorite' ),
				'version' => '1.0.3',
				'source' => jvbpdCore()->addons_url . 'lava-directory-favorite.zip',
			),
			Array(
				'name' => 'Lava Open Hours',
				'slug' => sanitize_title( 'lava-open-hours' ),
				'version' => '1.0.2',
				'source' => jvbpdCore()->addons_url . 'lava-open-hours.zip',
			),
			Array(
				'name' => 'Lava Video',
				'slug' => sanitize_title( 'lava-directory-video' ),
				'version' => '1.0.3',
				'source' => jvbpdCore()->addons_url . 'lava-directory-video.zip',
			),
		);
		return $addons;
	}

	public function wizardFooterBanner( $instance=null ) {
		if( is_null( $instance ) ) {
			return false;
		}
		jvbpdCore()->template_instance->load_template( 'part-admin-wizard-freeinstall-banner' );
	}

	public function getElementorLoginID() {
		$headers = Array( '' => esc_html__( "Select a login template", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'custom_login' ), $headers );
	}

	public function getElementorSignupID() {
		$headers = Array( '' => esc_html__( "Select a sign-up template", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'custom_signup' ), $headers );
	}

	public function getElementorHeaderID() {
		$headers = Array( '' => esc_html__( "Select a header", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'custom_header' ), $headers );
	}

	public function getElementorFooterID() {
		$headers = Array( '' => esc_html__( "Select a footer", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'custom_footer' ), $headers );
	}

	public function getElementorSingleID() {
		$headers = Array( '' => esc_html__( "Select a single listing", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'single_listing_page' ), $headers );
	}

	public function getElementorSingleProductID(){
        $headers = Array( '' => esc_html__( "Select a single product template", 'jvfrmtd' ) );
        if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
            return $headers;
        }
        $instance = Jvbpd_Listing_Elementor::getInstance();
        return wp_parse_args( $instance->get_template_id( 'single_product_page' ), $headers );
    }

	public function getElementorSinglePostID(){
        $headers = Array( '' => esc_html__( "Select a single post template", 'jvfrmtd' ) );
        if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
            return $headers;
        }
        $instance = Jvbpd_Listing_Elementor::getInstance();
        return wp_parse_args( $instance->get_template_id( 'single_post_page' ), $headers );
    }

	public function getElementorArchiveID() {
		$headers = Array( '' => esc_html__( "Select a archive listing", 'jvfrmtd' ) );
		if( ! class_exists( 'Jvbpd_Listing_Elementor' ) ) {
			return $headers;
		}
		$instance = Jvbpd_Listing_Elementor::getInstance();
		return wp_parse_args( $instance->get_template_id( 'listing_archive' ), $headers );
	}

	public function reigster_meta_box( $post_type ) {
		if( ! in_array( $post_type, array( 'page', 'lv_listing', 'post', 'product' ) ) ) {
			return;
		}

		add_meta_box( 'jvbpd_custom_elementor_custom_template', __( "Custom Template for this post / page", 'jvfrmtd' ),
			Array( $this, 'custom_elementor_custom_template' ), $post_type, 'advanced', 'high'
		);
	}

	public function custom_meta_boxes() {
		$metaBoxes = Array(
			'custom-sidebars' => Array(
				'title' => esc_html__("Custom sidebars", 'jvbpd'),
				'cb' => Array($this, 'meta_box_custom_sidebar'),
				'post_type' => 'page',
			),
		);
		foreach($metaBoxes as $boxID => $boxMeta){
			add_meta_box('jvbpd-'.$boxID, $boxMeta['title'], $boxMeta['cb'], $boxMeta['post_type'], 'normal', 'high');
		}
	}

	public function meta_box_custom_sidebar() {
		$metaSidebars = get_post_meta(get_the_ID(), self::PAGE_SETTINGS_KEY, true);
		foreach(
			Array(
				'sidebar_left' => Array(
					'label' => esc_html__( "Left Sidebar", 'playo' ),
					'note' => esc_html__( "It shows when there is at least one menu. otherwise, it doesn't show.", 'playo' ),
				),
				'sidebar_member' => Array(
					'label' => esc_html__( "Member Sidebar", 'playo' ),
					'note' => esc_html__( "It works when required plugins ('Core', 'BuddyPress') are actived. For groups, group component in BuddyPress needs to be actived.", 'playo' ),
				),
			) as $strOptionName => $strOptionMeta
		) {
			$thisValue = isset($metaSidebars[$strOptionName]) ? $metaSidebars[$strOptionName] : false; ?>
			<h4 class="pull-left"><?php echo esc_html( $strOptionMeta[ 'label' ] ); ?></h4>
			<fieldset class="inner margin-20-0 <?php if($strOptionMeta[ 'label' ]=='Member Sidebar') echo 'margin-custom-28-0'; ?>">
				<select name="jvbpd_mb[<?php echo esc_attr( $strOptionName ); ?>]">
					<?php
					foreach(
						Array(
							'' => esc_html__( "Default as theme settings", 'playo' ),
							'disabled' => esc_html__( "Disable", 'playo' ),
							'enabled' => esc_html__( "Enable", 'playo' ),
						) as $strOption => $strOptionLabel
					) {
						printf(
							'<option value="%1$s"%3$s>%2$s</option>',
							$strOption, $strOptionLabel, selected( $strOption == $thisValue, true, false )
						);
					} ?>
				</select>
				<?php printf( '<div class="description">%1$s : %2$s</div>', esc_html__( "Note", 'playo' ), $strOptionMeta[ 'note' ] ); ?>
			</fieldset>
			<?php
		}
	}

	public function save_custom_meta_boxes($post_id) {
		if( !is_admin() ) {
			return;
		}
		if(isset($_POST['jvbpd_mb'])) {
			update_post_meta($post_id, self::PAGE_SETTINGS_KEY, $_POST['jvbpd_mb']);
		}
	}

	public function custom_elementor_custom_template( $post ) {
		?>
		<div class="">
			<div class="">
				<?php
				$elementor_headerKey = 'elementor_header_id';
				$elementor_headerID = get_post_meta( $post->ID, $elementor_headerKey, true ); ?>
				<p><?php esc_html_e( "Header template for this page", 'jvfrmtd' ); ?></p>
				<select name="jvbpd_header_option[<?php echo $elementor_headerKey; ?>]">
					<option value=''><?php esc_html_e( "Default", 'jvfrmtd' ); ?></option>
					<option value='-1'<?php selected(-1 == $elementor_headerID ); ?>><?php esc_html_e( "Disabled", 'jvfrmtd' ); ?></option>
					<?php
					foreach( $this->getElementorHeaderID() as $headerID  ) {
						if( false === get_post_status( $headerID ) ) {
							continue;
						}
						printf(
							'<option value="%1$s"%3$s>%2$s</option>', $headerID, get_the_title( $headerID ),
							selected( $headerID == $elementor_headerID, true, false )
						);
					} ?>
				</select>
			</div>
			<div class="">
				<?php
				$elementor_footerKey = 'elementor_footer_id';
				$elementor_footerID = get_post_meta( $post->ID, $elementor_footerKey, true ); ?>
				<p><?php esc_html_e( "Footer template for this page", 'jvfrmtd' ); ?></p>
				<select name="jvbpd_header_option[<?php echo $elementor_footerKey; ?>]">
					<option value=''><?php esc_html_e( "Default", 'jvfrmtd' ); ?></option>
					<option value='-1'<?php selected(-1 == $elementor_footerID ); ?>><?php esc_html_e( "Disabled", 'jvfrmtd' ); ?></option>
					<?php
					foreach( $this->getElementorFooterID() as $footerID  ) {
						if( false === get_post_status( $footerID ) ) {
							continue;
						}
						printf(
							'<option value="%1$s"%3$s>%2$s</option>', $footerID, get_the_title( $footerID ),
							selected( $footerID == $elementor_footerID, true, false )
						);
					} ?>
				</select>
			</div>
			<?php if($post->post_type == 'lv_listing' ) { ?>
				<div class="">
					<?php
					$elementor_singleKey = 'elementor_single_lv_listing_id';
					$elementor_singleID = get_post_meta( $post->ID, $elementor_singleKey, true ); ?>
					<p><?php esc_html_e( "Single listing template for this page", 'jvfrmtd' ); ?></p>
					<select name="jvbpd_header_option[<?php echo $elementor_singleKey; ?>]">
						<option value=''><?php esc_html_e( "Default", 'jvfrmtd' ); ?></option>
						<option value='-1'<?php selected(-1 == $elementor_singleID ); ?>><?php esc_html_e( "Disabled", 'jvfrmtd' ); ?></option>
						<?php
						foreach( $this->getElementorSingleID() as $templateID  ) {
							if( false === get_post_status( $templateID ) ) {
								continue;
							}
							printf(
								'<option value="%1$s"%3$s>%2$s</option>', $templateID, get_the_title( $templateID ),
								selected( $templateID == $elementor_singleID, true, false )
							);
						} ?>
					</select>
				</div>
			<?php
			}
			if($post->post_type == 'product' ) { ?>
				<div class="">
					<?php
					$elementor_singleKey = 'elementor_single_product_id';
					$elementor_singleID = get_post_meta( $post->ID, $elementor_singleKey, true ); ?>
					<p><?php esc_html_e( "Single product template for this page", 'jvfrmtd' ); ?></p>
					<select name="jvbpd_header_option[<?php echo $elementor_singleKey; ?>]">
						<option value=''><?php esc_html_e( "Default", 'jvfrmtd' ); ?></option>
						<option value='-1'<?php selected(-1 == $elementor_singleID ); ?>><?php esc_html_e( "Disabled", 'jvfrmtd' ); ?></option>
						<?php
						foreach( $this->getElementorSingleProductID() as $templateID  ) {
							if( false === get_post_status( $templateID ) ) {
								continue;
							}
							printf(
								'<option value="%1$s"%3$s>%2$s</option>', $templateID, get_the_title( $templateID ),
								selected( $templateID == $elementor_singleID, true, false )
							);
						} ?>
					</select>
				</div>
			<?php
			}
			if($post->post_type == 'post' ) { ?>
				<div class="">
					<?php
					$elementor_singlePostKey = 'elementor_single_post_id';
					$elementor_singlePostID = get_post_meta( $post->ID, $elementor_singlePostKey, true ); ?>
					<p><?php esc_html_e( "Single post template for this page", 'jvfrmtd' ); ?></p>
					<select name="jvbpd_header_option[<?php echo $elementor_singlePostKey; ?>]">
						<option value=''><?php esc_html_e( "Default", 'jvfrmtd' ); ?></option>
						<option value='-1'<?php selected(-1 == $elementor_singlePostID ); ?>><?php esc_html_e( "Disabled", 'jvfrmtd' ); ?></option>
						<?php
						foreach( $this->getElementorSinglePostID() as $templateID  ) {
							if( false === get_post_status( $templateID ) ) {
								continue;
							}
							printf(
								'<option value="%1$s"%3$s>%2$s</option>', $templateID, get_the_title( $templateID ),
								selected( $templateID == $elementor_singlePostID, true, false )
							);
						} ?>
					</select>
				</div>
			<?php } ?>
			<p><span class="description"><?php printf( 'Default header can be set in Theme settings Header ( <a href="%1$s" target="_blank">Theme Settings > Header</a> )', esc_url( add_query_arg( Array( 'page' => 'jvbpd_admin_settings', ), admin_url( 'admin.php' ) ) ) ); ?></span></p>
		</div>
		<?php
	}

	public function custom_elementor_header_single_save( $post_id=0 ) {
		if( ! is_admin() ) {
			return false;
		}
		if( isset( $_POST[ 'jvbpd_header_option' ] ) && is_array( $_POST[ 'jvbpd_header_option' ] ) ) {
			foreach( $_POST[ 'jvbpd_header_option' ] as $meta_key => $meta_value ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}
	}

	public function adminPostColumn() {
		add_filter( 'manage_post_posts_columns', Array( $this, 'postColumns' ) );
		add_action( 'manage_post_posts_custom_column', Array( $this, 'postColumnContent' ), 10, 2 );

	}

	public function postColumns( array $columns=Array() ) { return array_merge( Array( 'cb' => '', 'thumbnail' => '' ), $columns ); }
	public function postColumnContent( $column, $post_id=0 ) {
		if( 'thumbnail' == $column && !function_exists( 'lava_bpp' ) ) {
			echo get_the_post_thumbnail( $post_id );
		}
	}

}