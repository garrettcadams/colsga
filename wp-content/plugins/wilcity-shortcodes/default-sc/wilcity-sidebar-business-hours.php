<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time as WilcityTimeHelper;
use WilokeListingTools\Frontend\BusinessHours;

add_shortcode('wilcity_sidebar_business_hours', 'wicitySidebarBusinessHours');
function wicitySidebarBusinessHours($aArgs){
	global $post;
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'  => '',
			'icon'  => 'la la-clock-o',
			'desc'  => ''
		)
	);
	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_business_hours') ){
	    return '';
    }

    if ( isset($aAtts['isMobile']) ){
	    return apply_filters('wilcity/mobile/sidebar/business_hours', $post, $aAtts);
    }

	$hourMode = GetSettings::getPostMeta($post->ID, 'hourMode');

	if ( $hourMode == 'no_hours_available' ){
	    return '';
    }else if ( $hourMode == 'always_open' ){
	    ob_start();
	    ?>
        <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-business-hours listing-hours_module__38Iy5')); ?>">
            <header class="content-box_header__xPnGx clearfix">
                <div class="wil-float-left">
                    <h4 class="content-box_title__1gBHS">
                        <i class="<?php echo esc_attr($aAtts['icon']); ?>"></i>
                        <span><?php echo esc_html($aAtts['name']); ?></span>
                    </h4>
                </div>
            </header>
            <div class="content-box_body__3tSRB">
                <h3  class="alway-open wil-btn wil-btn--block wil-btn--secondary wil-btn--round"><?php  esc_html_e('Open 24/7', 'wilcity-shortcodes'); ?></h3>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

	$aBusinessHours = GetSettings::getBusinessHours($post->ID);

	if ( empty($aBusinessHours) ){
		return '';
	}

	$timeFormat = GetSettings::getPostMeta($post->ID, 'timeFormat');

	$aDefineDaysOfWeek = wilcityShortcodesRepository()->get('config:aDaysOfWeek');
    $aTodayBusinessHours = BusinessHours::getTodayBusinessHours($post);
    $isInvalidFirstHour  = BusinessHours::invalidFirstHours($aTodayBusinessHours);
    $todayKey = BusinessHours::getTodayKey($post);
	ob_start();
	?>
	<div class="listing-hours_module__38Iy5">
        <?php if ( $isInvalidFirstHour ) : ?>
        <header class="listing-hours_header__1uH8N clearfix active close" data-toggle-button="hour" data-toggle-effect="slide">
            <div class="wil-float-left">
                <h4 class="listing-hours_title__sRfK7">
                    <i class="<?php echo esc_attr($aAtts['icon']); ?>"></i>
                    <span><?php esc_html_e('Today', 'wilcity-shortcodes'); ?></span>
                    <span class="listing-hours_status__dQ6Z9 close"><?php echo esc_html__('Closed', 'wilcity-shortcodes'); ?></span>
                    <span class="listing-hours_timezone__3-xfl color-quaternary"><?php echo BusinessHours::getListingUTC($post); ?></span>
                </h4>
            </div>
        </header>
        <?php else:
            $aBusinessStatus = BusinessHours::getCurrentBusinessHourStatus($post, $aTodayBusinessHours);
        ?>
            <header class="listing-hours_header__1uH8N clearfix active <?php echo esc_attr($aBusinessStatus['status']); ?>" data-toggle-button="hour" data-toggle-effect="slide">
                <div class="wil-float-left">
                    <h4 class="listing-hours_title__sRfK7">
                        <i class="<?php echo esc_attr($aAtts['icon']); ?>"></i>
                        <span><?php esc_html_e('Today', 'wilcity-shortcodes'); ?></span>
                        <span class="listing-hours_status__dQ6Z9 <?php echo esc_attr($aBusinessStatus['status']); ?>"><?php echo esc_html($aBusinessStatus['text']); ?></span>
                        <span class="listing-hours_timezone__3-xfl color-quaternary"><?php echo BusinessHours::getListingUTC($post); ?></span>
                    </h4>
                </div>
                <div class="wil-float-right">
                    <span class="listing-hours_todayhour__1iK4_">
                        <span class="listing-hours_todayhouritem__2WBlQ"><?php echo WilcityTimeHelper::renderTime($aTodayBusinessHours['firstOpenHour'], $timeFormat); ?> - <?php echo WilcityTimeHelper::renderTime($aTodayBusinessHours['firstCloseHour'], $timeFormat); ?></span>
                        <?php if ( BusinessHours::isSecondHourExists($aTodayBusinessHours) ) : ?>
                        <span class="listing-hours_todayhouritem__2WBlQ"><?php echo WilcityTimeHelper::renderTime($aTodayBusinessHours['secondOpenHour'], $timeFormat); ?> - <?php echo WilcityTimeHelper::renderTime($aTodayBusinessHours['secondCloseHour'], $timeFormat); ?></span>
                        <?php endif; ?>
                    </span>
                    <div class="listing-hours_threeDots__27IFT" data-toggle-button="dropdown">
                        <span class="listing-hours_dot__3xWqn bg-color-primary"></span>
                        <span class="listing-hours_dot__3xWqn bg-color-primary"></span>
                        <span class="listing-hours_dot__3xWqn bg-color-primary"></span>
                    </div>
                </div>
            </header>
        <?php endif; ?>
		<div class="listing-hours_body__3StC3 active" data-toggle-content="hour" style="display: block">
			<ul class="listing-hours_listhours__270Nz list-none">
				<?php
                foreach ($aBusinessHours as $aDayInfo) :
                    if ( $aDayInfo['isOpen'] == 'no' ):
                        $wrapperClass = $aDayInfo['dayOfWeek'] == $todayKey ? 'listing-hours_item__1B8Vv close' : 'listing-hours_item__1B8Vv';
                        ?>
                        <li class="<?php echo esc_attr($wrapperClass); ?>">
                            <span class="listing-hours_day__2Opo_"><?php echo esc_html($aDefineDaysOfWeek[$aDayInfo['dayOfWeek']]); ?></span>
                            <div class="listing-hours_hour__1OuD9">
                                <span class="listing-hours_houritem__3cCSp"><?php esc_html_e('Day Off', 'wilcity-shortcodes'); ?></span>
                            </div>
                        </li>
                        <?php
                    else:
	                    if ( $aDayInfo['dayOfWeek'] == $todayKey && isset($aBusinessStatus) ){
		                    $wrapperClass = 'listing-hours_item__1B8Vv ' . $aBusinessStatus['status'];
	                    }else{
		                    $wrapperClass = 'listing-hours_item__1B8Vv';
	                    }
	                    ?>
                        <li class="<?php echo esc_attr($wrapperClass); ?>"><span class="listing-hours_day__2Opo_"><?php echo esc_html($aDefineDaysOfWeek[$aDayInfo['dayOfWeek']]); ?></span>
                            <div class="listing-hours_hour__1OuD9">
                                <span class="listing-hours_houritem__3cCSp">
                                    <?php 
                                        if( !empty($aDayInfo['firstOpenHour']) ) {
                                            echo WilcityTimeHelper::renderTime($aDayInfo['firstOpenHour'], $timeFormat); 
                                        }
                                        
                                        if( !empty($aDayInfo['firstCloseHour']) ) {
                                            echo ' - ';
                                            echo WilcityTimeHelper::renderTime($aDayInfo['firstCloseHour'], $timeFormat); 
                                        }
                                    ?>
                                </span>
			                    <?php if ( BusinessHours::isSecondHourExists($aDayInfo) ) : ?>
                                    <span class="listing-hours_houritem__3cCSp">
                                        <?php 
                                            if( !empty($aDayInfo['secondOpenHour']) ) {
                                                echo WilcityTimeHelper::renderTime($aDayInfo['secondOpenHour'], $timeFormat); 
                                            }
                                            
                                            if( !empty($aDayInfo['secondCloseHour']) ) {
                                                echo ' - ';
                                                echo WilcityTimeHelper::renderTime($aDayInfo['secondCloseHour'], $timeFormat); 
                                            }
                                        ?>
                                    </span>
			                    <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}