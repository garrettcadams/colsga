<?php
/**
Widget Name: Single more taxonomies widget
Author: Javo
Version: 1.0.0.0
*/


namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_more_taxo extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-more-taxo';
	}

	public function get_title() {
		return 'More Taxo (Group)';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-checkbox';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'More Taxonomies', 'jvfrmtd' ),
			)
		);

		$this->add_control(
		'Des',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);
		$this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-more-taxonomies.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
		?>

	<div class="more-tax-wrap" id="javo-item-condition-section" data-jv-detail-nav>
			<!--<h3 class="page-header"><?php esc_html_e( "Item detail", 'listopia' ); ?></h3>-->
		<div class="panel panel-default">
			<div class="panel-body">

			<?php
			if( function_exists( 'javo_moreTax' ) ) {
				$arrTaxonomies = javo_moreTax()->admin->getMoreTaxonomies();
				foreach( array_filter( $arrTaxonomies ) as $taxonomy ) {
					if( taxonomy_exists( $taxonomy[ 'name' ] ) ) {
						$terms = wp_get_object_terms( get_the_ID(), $taxonomy[ 'name' ], array( 'fields' => 'names' ) );
						?>
						<div class="row <?php echo esc_attr( $taxonomy[ 'name' ] ); ?>">
							<span class="contact-icons"><?php echo esc_html( $taxonomy[ 'label' ] ); ?> : </span>
							<span><?php echo join( ', ', $terms ); ?></span>
						</div>
						<?php
					}
				}
			}
      ?>
			</div>
		</div>
	</div>
	<?php
	}
}
