<?php
/**
Widget Name: Single contact info widget
Author: Javo
Version: 1.0.0.1
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_contact_info extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-contact-info';
	}

	public function get_title() {
		return 'Contact Info (Single Listing)';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-mail';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

  protected function _register_controls() {

    $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Contact Info(Group)', 'jvfrmtd' ),
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

  $this->start_controls_section(
			'section_block_setting',
			[
				'label' => esc_html__( 'Contact Info Setting', 'jvfrmtd' ),   //section name for controler view
			]
    );
    

    $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'callback',
			[
        'label' => __( 'Select One', 'jvfrmtd' ),
        'type' => Controls_Manager::SELECT,
        'options' => Array(
          'email' => __('Email', 'jvfrmtd'),
          'website' => __('Website', 'jvfrmtd'),
          'phone1' => __('Phone1', 'jvfrmtd'),
          'phone2' => __('Phone2', 'jvfrmtd'),
          'address' => __('Address', 'jvfrmtd'),
          'keywords' => __('Keywords', 'jvfrmtd'),
          'social' => __('Social', 'jvfrmtd'),
          'claim' => __('Claim', 'jvfrmtd'),
        )
      ]    
		);

		$this->add_control(
			'callback_list',
			[
				'label' => __( 'Button List', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ callback }}}',
			]
		);
  
		$this->end_controls_section();
		
		$this->start_controls_section(
			'contact_info_style',
			[
				'label' => __( 'Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'contact_info_text_typography',
				'label' => __('Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #javo-listings-contact-section .panel-body > .contact-info-meta span:not(.contact-icons)',
			]
		);

		$this->add_control(
			'contact_info_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-listings-contact-section .panel-body > .contact-info-meta span:not(.contact-icons)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'contact_info_items_padding',
			[
				'label'      => esc_html__( 'Items Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 15,
					'right' => 30,
					'bottom' => 15,
					'left' => 30,
				],					
				'selectors'  => [
					'{{WRAPPER}} #javo-listings-contact-section .panel-body > .contact-info-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'contact_info_items_border',
				'label' => __('Contact Info Item Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} #javo-listings-contact-section .panel-body > .contact-info-meta',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'contact_info_icon_style',
			[
				'label' => __( 'Icon Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->end_controls_section();
    }

    protected function render() {

  		$settings = $this->get_settings();
  		wp_reset_postdata();
  		  $this->getContent( $settings, get_post() );
    }

  public function email( $settings=Array() ) {?>
            <?php
            if( is_admin()) { ?>
              <div class="contact-info-meta meta-email">
              <span class="contact-icons"><i class=" jvbpd-icon3-envelop"></i></span>
              <span><?php echo "sample@email.com"; ?></span>
              </div><!-- /.contact-info-meta *email -->

            <?php }else{
              if(''!=(get_post_meta( get_the_ID(), '_email', true ))){?>
                <div class="contact-info-meta meta-email">
                <span class="contact-icons"><i class=" jvbpd-icon3-envelop"></i></span>
                <span>
                <?php $listing_email = esc_html(get_post_meta( get_the_ID(), '_email', true ));
                printf('<a href="mailto:%s">%s</a>', $listing_email, $listing_email); ?>
                </span>
                </div><!-- /.contact-info-meta *email -->
              <?php }
            }
            ?>
    <?php
  }

  public function website( $settings=Array() ) {?>
            <?php
            if( is_admin()) {?>
              <div class="contact-info-meta meta-website">
              <span class="contact-icons"><i class=" jvbpd-icon3-globe"></i></span>
              <span><?php echo "sample-site.com";?></span>
              </div>
            <?php }else{
              if(''!=(get_post_meta( get_the_ID(), '_website', true ))){ ?>
                <div class="contact-info-meta meta-website">
                <span class="contact-icons"><i class=" jvbpd-icon3-globe"></i></span>
                <span><a href="<?php echo esc_url(esc_attr(get_post_meta( get_the_ID(), '_website', true )));?>" target="_blank"><?php echo esc_html(get_post_meta( get_the_ID(), '_website', true ));?></a></span>
                </div>
                <?php
              }
            }
            ?>
    <?php
  }
  public function address( $settings=Array() ) {?>
      <?php if(is_admin()){?>
        <div class="contact-info-meta meta-address">
        <span class="contact-icons"><i class="jvbpd-icon2-location3"></i></span>
        <span><?php esc_html_e('A sample address here.', 'listopia');?></span>
        </div>
      <?php }else{?>
        <?php if(''!=(get_post_meta( get_the_ID(), '_address', true ))){?>
        <div class="contact-info-meta meta-address">
        <span class="contact-icons"><i class="jvbpd-icon2-location3"></i></span>
        <span><?php echo esc_html(get_post_meta( get_the_ID(), '_address', true ));?></span>
        </div>
        <?php } ?>
      <?php } ?>
  <?php }


  public function phone1( $settings=Array() ) {?>
      <?php if(is_admin()){?>
        <div class="contact-info-meta meta-phone1">
          <span class="contact-icons"><i class="jvbpd-icon2-tell"></i></span>
          <span> 1-601-1111-1111</span>
        </div>
      <?php }else{?>
        <?php if(''!=(get_post_meta( get_the_ID(), '_phone1', true ))){?>
        <div class="contact-info-meta meta-phone1">
          <span class="contact-icons"><i class="jvbpd-icon2-tell"></i></span>
          <span><?php echo esc_html(get_post_meta( get_the_ID(), '_phone1', true ));?></span>
        </div><!-- /.contact-info-meta *address -->
        <?php } ?>
      <?php } ?>

  <?php }

  public function phone2( $settings=Array() ) {?>
      <?php if(is_admin()){?>
        <div class="contact-info-meta meta-phone2">
          <span class="contact-icons"><i class="jvbpd-icon3-print"></i></span>
          <span>1-601-2222-2222</span>
        </div>
      <?php }else{?>
        <?php if(''!=(get_post_meta( get_the_ID(), '_phone2', true ))){?>
        <div class="contact-info-meta meta-phone2">
          <span class="contact-icons"><i class="jvbpd-icon3-print"></i></span>
          <span><?php echo esc_html(get_post_meta( get_the_ID(), '_phone2', true ));?></span>
        </div><!-- /.contact-info-meta *address -->
        <?php } ?>
      <?php } ?>
  <?php }

  public function keywords( $settings=Array() ) {?>
        <?php if(is_admin()){?>
          <div class="contact-info-meta meta-keyword">
            <span class="contact-icons"><i class="jvbpd-icon2-bookmark2"></i></span>
            <span><i><?php esc_html_e('Keyword1, Keyword2, Keyword3', 'listopia'); ?></i></span>
          </div>
        <?php }else{?>
          <?php if($listing_keyword = esc_html(lava_directory_terms( get_the_ID(), 'listing_keyword' ))){?>
          <div class="contact-info-meta meta-keyword">
            <span class="contact-icons"><i class="jvbpd-icon2-bookmark2"></i></span>
            <span><i><?php echo $listing_keyword; ?></i></span>
          </div>
          <?php } ?>
        <?php } ?>
  <?php }

  public function social( $settings=Array() ) {?>
    <?php
    if (is_admin()){?>
      <div class="contact-info-meta jvbpd_single_listing_social-wrap">
        <a href="#" target="_blank" class="jvbpd_single_listing_facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
        <a href="#" target="_blank" class="jvbpd_single_listing_twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
        <a href="#" target="_blank" class="jvbpd_single_listing_instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
        <a href="#" target="_blank" class="jvbpd_single_listing_google"><i class="fab fa-google" aria-hidden="true"></i></a>
        <a href="#" target="_blank" class="jvbpd_single_listing_youtube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
        <a href="#" target="_blank" class="jvbpd_single_listing_linkedin"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
      </div>
    <?php
    }else{
      $jvbpd_facebook_link = esc_html(get_post_meta( get_the_ID(), '_facebook_link', true ));
      $jvbpd_twitter_link = esc_html(get_post_meta( get_the_ID(), '_twitter_link', true ));
      $jvbpd_instagram_link = esc_html(get_post_meta( get_the_ID(), '_instagram_link', true ));
      $jvbpd_google_link = esc_html(get_post_meta( get_the_ID(), '_google_link', true ));
      $jvbpd_youtube_link = esc_html(get_post_meta( get_the_ID(), '_youtube_link', true ));
      $jvbpd_linkedin_link = esc_html(get_post_meta( get_the_ID(), '_linkedin_link', true ));

      if(!($jvbpd_facebook_link =='' && $jvbpd_twitter_link=='' && $jvbpd_instagram_link=='' && $jvbpd_google_link=='' && $jvbpd_youtube_link=='' && $jvbpd_linkedin_link=='' )){
        ?>
      <div class="jvbpd_single_listing_social-wrap">
        <?php if ($jvbpd_facebook_link!=''){ ?>
          <a href="<?php echo $jvbpd_facebook_link;?>" target="_blank" class="jvbpd_single_listing_facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
        <?php }
        if ($jvbpd_twitter_link!=''){ ?>
          <a href="<?php echo $jvbpd_twitter_link;?>" target="_blank" class="jvbpd_single_listing_twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
        <?php }
        if ($jvbpd_instagram_link!=''){ ?>
          <a href="<?php echo $jvbpd_instagram_link;?>" target="_blank" class="jvbpd_single_listing_instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
        <?php }
        if ($jvbpd_google_link!=''){ ?>
          <a href="<?php echo $jvbpd_google_link;?>" target="_blank" class="jvbpd_single_listing_google"><i class="fab fa-google" aria-hidden="true"></i></a>
        <?php }
        if ($jvbpd_youtube_link!=''){ ?>
          <a href="<?php echo $jvbpd_youtube_link;?>" target="_blank" class="jvbpd_single_listing_youtube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
        <?php }
		if ($jvbpd_linkedin_link!=''){ ?>
          <a href="<?php echo $jvbpd_linkedin_link;?>" target="_blank" class="jvbpd_single_listing_linkedin"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
        <?php } ?>
      </div>
      <?php } ?>
    <?php }
  }

	public function claim( $settings=Array() ) {

		if( function_exists( 'lava_directory_claim_button' ) ){ ?>
			<div class="jvbpd_single_claim_wrap">
				<?php
				lava_directory_claim_button( Array(
				'class'	=> 'btn btn-block admin-color-setting-hover',
				'label' => esc_html__( "Claim", 'jvbpd' ),
				'icon' => false
				) ); ?>
			</div>
			<?php
		}
	}

	public function getContent( $settings, $obj ) {
		?>
    <div class="contact-info-group" id="javo-listings-contact-section" data-jv-detail-nav>
      <div class="panel panel-default">
        <div class="panel-body">
  			<?php
  			$arrCallBack = $settings[ 'callback_list' ];
  			if( !empty( $arrCallBack ) && is_array( $arrCallBack ) ) {
  				foreach( $arrCallBack as $strCallBack ) {
  					if( method_exists( $this, $strCallBack[ 'callback' ] ) ) {
  						call_user_func( Array( $this, $strCallBack[ 'callback' ] ), $strCallBack );
  					}
  				}
  			}?>
      </div>
    </div>
	</div>
	<?php
	}
}
