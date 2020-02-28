<?php
class CMB2_Search_Get_Extra_Post_Data {
	protected static $single_instance = null;
	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return CMB2_Search_Get_Extra_Post_Data A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}
		return self::$single_instance;
	}
	protected function __construct() {
		add_action( 'cmb2_init', array( $this, 'register_repeatable_group_field_metabox' ), 9999 );
		add_action( 'wp_ajax_cmb2_get_post_data_from_search', array( $this, 'get_post_data' ) );
	}
	public function register_repeatable_group_field_metabox() {
		$prefix = 'cmb2_link_group_';
		$cmb_group = new_cmb2_box( array(
			'id'           => $prefix . 'metabox_links',
			'title'        => __( 'Related Links', 'cmb2' ),
			'object_types' => array( 'post', ),
		) );
		$group_field_id = $cmb_group->add_field( array(
			'id'          => $prefix . 'metabox_links_group',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => __( 'Link {#}', 'cmb2' ), // {#} gets replaced by row number
				'add_button'    => __( 'Add Another Link', 'cmb2' ),
				'remove_button' => '<span class="dashicons dashicons-no-alt"></span>',
				'sortable'      => true, // beta
			),
		) );
		$cmb_group->add_group_field( $group_field_id, array(
			'name'       => __( 'Title', 'cmb2' ),
			'id'         => 'title',
			'type'       => 'text',
		) );
		$cmb_group->add_group_field( $group_field_id, array(
			'name'        => __( 'URL', 'cmb2' ),
			'description' => __( 'Add the related URL here, or search for a related article.', 'cmb2' ),
			'id'          => 'url',
			'type'        => 'post_search_text',
// 'post_type'   => 'post_type',
			'select_type' => 'radio',
			'options'     => array(
				'find_text' => __( 'Find/Select a wiki post', 'cmb2' ),
			),
			'attributes' => array(
				'class'=> 'regular-text post-search-data',
			),
		) );
// Probably better to put in a dedicated JS file, and enqueue it in the override_post_search_field_callback
		add_action( "cmb2_after_post_form_{$prefix}metabox_links", array( $this, 'override_post_search_field_callback' ) );
	}
	/**
	 * Override the post-search-fields default handling for selected posts and ajax-fetch post-data instead
	 */
	public function override_post_search_field_callback() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				function get_post_data( post_id, $object ) {
					$.post( ajaxurl, {
						'action' : 'cmb2_get_post_data_from_search',
						ajaxurl : ajaxurl,
						post_id : post_id
					}, function( response ) {
						if ( response.success && response.data.url ) {
							// update the url w/ the post permalink
							$object.val( response.data.url );
							// update the title w/ the post title
							var id = $object.attr( 'id' ).replace( '_url', '_title' );
							$( document.getElementById( id ) ).val( response.data.title );
						}
					})
				}
				// Make sure window.cmb2_post_search is around
				setTimeout( function() {
					if ( window.cmb2_post_search ) {
						// once a post is selected...
						window.cmb2_post_search.handleSelected = function( checked ) {
							if ( this.$idInput.hasClass( 'post-search-data' ) ) {
								// ajax-grab the data we need
								get_post_data( checked[0], this.$idInput );
							} else {
								var existing = this.$idInput.val();
								existing = existing ? existing + ', ' : '';
								this.$idInput.val( existing + checked.join( ', ' ) );
							}
							this.close();
						};
					}
				}, 500 );
			});
		</script>
		<?php
	}

	public function get_post_data() {
		if ( isset( $_POST['post_id'] ) ) {
			$post = get_post( absint( $_POST['post_id'] ) );
			if ( $post && 'post' == $post->post_type ) {
				wp_send_json_success( array( 'url' => get_permalink( $post->ID ), 'title' => get_the_title( $post->ID ) ) );
			}
		}
		wp_send_json_error( 'Missing required data.' );
	}
}

if ( !class_exists('CMB2_Search_Get_Extra_Post_Data') ){
	add_action( 'plugins_loaded', array( 'CMB2_Search_Get_Extra_Post_Data', 'get_instance' ) );
}