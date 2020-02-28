<?php
add_action( 'after_setup_theme', 'register_jvbpd_category_box2' );
function register_jvbpd_category_box2() {
	$objInstance = new jvbpd_category_box2;
	add_shortcode( 'jvbpd_category_box2', Array( $objInstance, 'output' ) );
}
class jvbpd_category_box2 extends Jvbpd_Shortcode_Parse {

	public function output( $attr, $content='' ) {
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>

		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein">
			<div class="shortcode-header">
				<div class="shortcode-title">
					<?php echo $this->title; ?>
				</div>
				<div class="shortcode-nav"></div>
			</div>
			<div class="shortcode-output row" ><?php $this->rander(); ?> </div>
		</div>

		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function rander() {
		global $wp_query;

		$objTaxonomy = $wp_query->get_queried_object();
		$this->category_box2_cat	= !empty( $objTaxonomy->taxonomy ) ? $objTaxonomy->taxonomy : 'category';

		$objTerms	= get_terms( $this->category_box2_cat, Array( 'hide_empty' => false, 'fields' => 'id=>name' ) );
		if( ! empty( $objTerms ) ) : foreach( $objTerms as $term_id =>$term_name ) {
			$termFeaturedID		= get_option( sprintf( 'lava_%s_%s_featured', $this->category_box2_cat, $term_id ) );
			$termFeaturedMeta	= wp_get_attachment_image( $termFeaturedID, 'jvbpd-avatar', false, Array( 'class' => 'img-responsive' ) );
			$termHasPosts		= get_term( $term_id, $this->category_box2_cat )->count;

			echo join( false,
				Array(
					'<div class="col-md-3">',
						'<div class="module">',
							'<div class="jv-thumb thumb-wrap">',
								sprintf(
									"<a href=\"%s\">%s</a>",
									esc_url( get_term_link( $term_id, $this->category_box2_cat ) ),
									$termFeaturedMeta
								),
								sprintf(
									"<div class=\"meta-count admin-color-setting\">%s %s</div>",
									$termHasPosts,
									_n( "Post", "Posts", $termHasPosts, 'jvfrmtd' )
								),
							'</div>',
							'<div class="jv-grid-meta">',
								sprintf(
									"<h4 class=\"meta-title\"><a href=\"%s\">%s</a></h4>",
									'#',
									$term_name
								),
							'</div>',
						'</div>',
					'</div>',
				)
			);
		} endif;
	}
}