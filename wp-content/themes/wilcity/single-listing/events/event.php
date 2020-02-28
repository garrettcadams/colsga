<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Controllers\EventController;
use WilokeListingTools\Framework\Helpers\General;

global $post;
?>
<div class="col-sm-6 col-md-4 wilcity-event-item" data-id="<?php echo esc_attr($post->ID); ?>">
    <!-- event_module__2zicF wil-shadow -->
    <div class="event_module__2zicF wil-shadow js-event">
        <?php
        $aEventSettings = GetSettings::getEventSettings($post->ID);
        if ( has_post_thumbnail($post->ID) ) :
            $thumbnailURL = get_the_post_thumbnail_url($post->ID, 'medium');
        ?>
        <header class="event_header__u3oXZ">
            <a href="<?php the_permalink(); ?>">
                <div class="event_img__1mVnG pos-a-full bg-cover" style="background-image: url(<?php echo esc_url($thumbnailURL); ?>);">
                    <img src="<?php echo esc_url($thumbnailURL); ?>" alt="<?php echo esc_attr($post->post_title); ?>">
                </div>
            </a>
        </header>
        <?php endif; ?>

        <div class="event_body__BfZIC">
            <?php EventController::eventStart($post->ID, false); ?>
            <div class="event_content__2fB-4">
                <h2 class="event_title__3C2PA"><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title($post->ID)); ?></a></h2>
                <ul class="event_meta__CFFPg list-none">
                    <li class="event_metaList__1bEBH text-ellipsis">
                        <?php $specifyDay = General::getDayOfWeek($aEventSettings['specifyDays']); ?>
                        <?php if ( !empty($specifyDay) ) : ?>
                        <span><?php echo General::getDayOfWeek($aEventSettings['specifyDays']); ?></span>
                        <?php endif; ?>
                        <span><?php echo esc_html($aEventSettings['address']); ?></span>
                    </li>
                    <li class="event_metaList__1bEBH text-ellipsis"><span>17,148 people interested</span></li>
                    <li class="event_metaList__1bEBH text-ellipsis"><span>100 Going</span></li>
                    <li class="event_metaList__1bEBH text-ellipsis"><span>19,302 Reach</span></li>
                    <?php do_action('wilcity/single-listing/events/event/event-info', $post); ?>
                </ul>
            </div>
        </div>
        <footer class="event_footer__1TsCF"><span class="event_by__23HUz">Hosted By <a href="#" class="color-dark-2">Game in the desert</a></span>
            <div class="event_right__drLk5 pos-a-center-right">

                <!-- dropdown_module__J_Zpj -->
                <div class="dropdown_module__J_Zpj">
                    <div class="dropdown_threeDots__3fa2o" data-toggle-button="dropdown" data-body-toggle="true"><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span></div>
                    <div class="dropdown_itemsWrap__2fuze" data-toggle-content="dropdown">

                        <!-- list_module__1eis9 list-none -->
                        <ul class="list_module__1eis9 list-none list_small__3fRoS list_abs__OP7Og arrow--top-right ">
                            <li class="list_item__3YghP">
                                <a class="list_link__2rDA1 text-ellipsis color-primary--hover js_edit_event" href="#" data-popup="createEvent" data-eventid="<?php echo esc_attr($post->ID); ?>"><span class="list_icon__2YpTp"><i class="la la-cc-paypal"></i></span><span class="list_text__35R07"><?php esc_html_e('Edit', 'wilcity'); ?></span></a>
                            </li>
                            <li class="list_item__3YghP">
                                <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#"><span class="list_icon__2YpTp"><i class="la la-forumbee"></i></span><span class="list_text__35R07"><?php esc_html_e('Report', 'wilcity'); ?></span></a>
                            </li>
                            <li class="list_item__3YghP">
                                <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#">
                                <span class="list_icon__2YpTp"><i class="la la-sliders"></i></span><span class="list_text__35R07"><?php esc_html_e('Create Event', 'wilcity'); ?></span></a>
                            </li>
                            <li class="list_item__3YghP">
                                <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#"><span class="list_icon__2YpTp"><i class="la la-commenting-o"></i></span><span class="list_text__35R07"><?php esc_html_e('Add Review', 'wilcity'); ?></span></a>
                            </li>
                            <li class="list_item__3YghP">
                                <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#"><span class="list_icon__2YpTp"><i class="la la-long-arrow-down"></i></span><span class="list_text__35R07"><?php esc_html_e('Contact Now', 'wilcity'); ?></span></a>
                            </li>
                        </ul><!-- End /  list_module__1eis9 list-none -->
                    </div>
                </div><!-- End / dropdown_module__J_Zpj -->
            </div>
        </footer>
    </div><!-- End / eveant_module__2zicF wil-shadow -->
</div>