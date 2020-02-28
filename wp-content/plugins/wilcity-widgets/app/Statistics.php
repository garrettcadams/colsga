<?php
namespace WilcityWidgets\App;

use WilokeListingTools\Models\PostModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\SharesStatistic;
use WilokeListingTools\Models\ViewStatistic;

class Statistics extends \WP_Widget {
    public $aDef = array('logo'=>'', 'total_places'=>'%total% places', 'total_places_desc'=>'worldwide', 'total_visitors'=>'%total% people', 'total_visitors_desc'=>'unique visitors', 'total_reviews'=>'%total% reviews', 'total_reviews_desc'=>'shared over');

	public function __construct() {
		// Instantiate the parent object
		parent::__construct( 'wilcity_statistics', WILCITY_WIDGET . ' Statistics');
	}

	public function widget( $aArgs, $aInstance ) {
	    $aInstance = wp_parse_args($aInstance, $this->aDef);
	    echo $aArgs['before_widget'];
	    $totalPosts = PostModel::countAllPosts();

	    $totalViews = ViewStatistic::countAllViews();
	    $totalReviews = ReviewModel::countAllReviewed();

		?>
        <!-- widget-statictis -->
        <div class="widget-statictis">
            <?php if ( !empty($aInstance['logo']) ) : ?>
            <div class="widget-statictis__logo"><img src="<?php echo esc_url($aInstance['logo']); ?>" alt="<?php echo esc_attr(get_option('name')); ?>"/></div>
            <?php endif; ?>
            <?php if ( !empty($totalViews) || !empty($totalReviews) || !empty($totalPosts) ) : ?>
            <div class="widget-stattictis__body">
                <?php if ( !empty($totalPosts) ) : ?>
                <div class="widget_statictis__item"><i class="la la-map-marker"></i>
                    <div class="widget_statictis__item-text">
                        <span class="widget-stattictis__textLg"><?php echo esc_html(str_replace('%total%', $totalPosts, $aInstance['total_places'])); ?></span>
                        <?php if ( !empty($aInstance['total_places_desc']) ) : ?>
                        <span class="widget-stattictis__textSm"><?php echo esc_html($aInstance['total_places_desc']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( !empty($totalViews) ) : ?>
                <div class="widget_statictis__item"><i class="la la-user-plus"></i>
                    <div class="widget_statictis__item-text">
                        <span class="widget-stattictis__textLg"><?php echo esc_html(str_replace('%total%', $totalViews, $aInstance['total_visitors'])); ?></span>
	                    <?php if ( !empty($aInstance['total_visitors_desc']) ) : ?>
                            <span class="widget-stattictis__textSm"><?php echo esc_html($aInstance['total_visitors_desc']); ?></span>
	                    <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( !empty($totalReviews) ) : ?>
                <div class="widget_statictis__item"><i class="la la-star-o"></i>
                    <div class="widget_statictis__item-text">
                        <span class="widget-stattictis__textLg"><?php echo esc_html(str_replace('%total%', $totalReviews, $aInstance['total_reviews'])); ?></span>
	                    <?php if ( !empty($aInstance['total_reviews_desc']) ) : ?>
                            <span class="widget-stattictis__textSm"><?php echo esc_html($aInstance['total_reviews_desc']); ?></span>
	                    <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div><!-- End / widget-statictis -->
        <?php
		echo $aArgs['after_widget'];
	}

	public function update( $aNewInstance, $aOldInstance ) {
        $aInstance = $aOldInstance;
        foreach ($aNewInstance as $key => $val){
            $aInstance[$key] = sanitize_text_field($val);
        }
        return $aInstance;
	}

	public function form( $aInstance ) {
	    $aInstance = wp_parse_args($aInstance, $this->aDef);
		$get = 'url';
		?>
        <div class="media-widget-control wiloke-widget-control" style="margin-top: 20px;">
            <div>
                <input class="widefat wiloke_image" type="hidden" name="<?php echo $this->get_field_name('logo'); ?>" id="<?php echo $this->get_field_id('logo'); ?>" value="<?php echo esc_url($aInstance['logo']); ?>">
            </div>
            <div class="media-widget-preview media_image">
                <?php  if ( empty($aInstance['logo']) ) :  ?>
                    <div class="attachment-media-view">
                        <div class="placeholder">No image selected</div>
                    </div>
                <?php else: ?>
                    <?php
                    if ( $get == 'url' ){
                        echo '<img src="'.esc_url($aInstance['logo']).'" style="max-width: 100%;">';
                    }else{
	                    echo wp_get_attachment_image($aInstance['logo'], 'thumbnail');
                    }
                    ?>
                <?php endif; ?>
            </div>
            <div class="media-widget-buttons" style="margin-bottom: 20px;">
                <button class="button wiloke_upload_image widefat" data-get="<?php echo esc_attr($get); ?>">Upload Image</button>
            </div>
        </div>
        <div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_places'); ?>">Total Places</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_places'); ?>" id="<?php echo $this->get_field_id('total_places'); ?>" value="<?php echo esc_attr($aInstance['total_places']); ?>">
            </div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_places_desc'); ?>">Total Places Description</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_places_desc'); ?>" id="<?php echo $this->get_field_id('total_places_desc'); ?>" value="<?php echo esc_attr($aInstance['total_places_desc']); ?>">
            </div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_visitors'); ?>">Total Visitors</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_visitors'); ?>" id="<?php echo $this->get_field_id('total_visitors'); ?>" value="<?php echo esc_attr($aInstance['total_visitors']); ?>">
            </div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_visitors_desc'); ?>">Total Visitors Description</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_visitors_desc'); ?>" id="<?php echo $this->get_field_id('total_visitors_desc'); ?>" value="<?php echo esc_attr($aInstance['total_visitors_desc']); ?>">
            </div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_reviews'); ?>">Total Reviews</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_reviews'); ?>" id="<?php echo $this->get_field_id('total_reviews'); ?>" value="<?php echo esc_attr($aInstance['total_reviews']); ?>">
            </div>
            <div class="field-item">
                <label for="<?php echo $this->get_field_id('total_reviews_desc'); ?>">Total Reviews Description</label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('total_reviews_desc'); ?>" id="<?php echo $this->get_field_id('total_reviews_desc'); ?>" value="<?php echo esc_attr($aInstance['total_reviews_desc']); ?>">
            </div>
        </div>
		<?php
	}
}