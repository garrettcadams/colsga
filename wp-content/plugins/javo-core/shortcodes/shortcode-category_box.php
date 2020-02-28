<?php
class jvbpd_category_box{

	public static $shortcode_loaded = false;

	public function __construct(){
		add_action( 'init', array( $this, 'register_shortcode' ) );
	}

	public function register_shortcode() {
		add_shortcode( 'jvbpd_category_box'	, Array( $this, 'parse_shortcode' ) );
		add_action( 'admin_footer', Array( $this, 'jvbpd_backend_scripts_func' ) );

	}


	public function parse_shortcode( $atts, $content='' ) {
		return $this->rander(
			shortcode_atts(
				Array(
					'column' => '1-3',
					'block_title' => '',
					'block_description' => '',
					'text_color' => '#fff',
					'text_sub_color' => '#fff',
					'overlay_color' => '#34495e',
					'jvbpd_featured_block_id' =>'',
					'jvbpd_featured_block_param'	=> '',
					'attachment_other_image' => '',
					'map_template'			=> '',
					'feataured_label' => ''
				), $atts
			)
		);
	}

	public static function jvbpd_backend_scripts_func() {
		if( ! self::$shortcode_loaded )
			return;

		ob_start(); ?>
		<script type="text/javascript">jQuery(function(e){e(document).on("change","select[name='jvbpd_featured_block_id']",function(){var t=e(this).closest(".wpb-edit-form").find('input[name="jvbpd_featured_block_title"]');t.val(e(this).find(":selected").text())})});</script>
		<?php
		ob_end_flush();
	}

	public function rander( $params ) {
		extract( $params );
		if($map_template){
			$output_link			= esc_url(
				apply_filters( 'jvbpd_wpml_link', $map_template ) . $jvbpd_featured_block_param
			);
		}else{
			$output_link = $jvbpd_featured_block_param;
		}

		$strImageSize		= 'full';
		$strClassName		= 'javo-image-full-size';
		$is_post				= '' == $attachment_other_image;

		if( $column == '1-3' ) {
			$strImageSize	= 'jvbpd-large';
			$strClassName	= 'javo-image-min-size';
		}elseif( $column == '2-3' ) {
			$strImageSize	= 'jvbpd-item-detail';
			$strClassName	= 'javo-image-middle-size';
		}

		if( $is_post ) {
			$jvbpd_this_attachment_meta = get_the_post_thumbnail( $jvbpd_featured_block_id, $strImageSize );
		}else{
			$jvbpd_this_attachment_meta = wp_get_attachment_image( $attachment_other_image, $strImageSize );
		}

		self::$shortcode_loaded = true;

		if(!$overlay_color) {
			$overlay_color = 'transparent';
		}

		ob_start();
		?>
		<div class="javo-featured-block <?php echo $strClassName; ?>">
			<a href="<?php echo $output_link; ?>">
				<?php echo $jvbpd_this_attachment_meta; ?>
				<div class="javo-image-overlay" style="background-color:<?php echo $overlay_color; ?>;"></div>
				<div class="javo-text-wrap">
					<h4 style="color:<?php echo $text_color; ?>"><?php echo $block_title; ?></h4>
					<div class="jvbpd_text_description-wrap">
						<span class="jvbpd_text_description" style="color:<?php echo $text_sub_color; ?>"><?php echo $block_description; ?></span>
					</div>
				</div> <!--javo-text-wrap -->
			</a>
		</div>

		<?php
		wp_reset_query();
		$content = ob_get_clean();
		return $content;
	}
}
new jvbpd_category_box;