<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\EventModel;
use WilokeListingTools\Models\MessageModel;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\NotificationsModel;

class DashboardController extends Controller
{
    private static $aEndpoint = [
        'favorites'     => 'get-my-favorites',
        'profile'       => 'get-profile',
        'messages'      => 'get-my-messages',
        'listings'      => 'get-my-listings',
        'events'        => 'get-my-events',
        'notifications' => 'get-my-notifications',
        'dokan'         => 'dokan/sub-menus'
    ];
    
    public function __construct()
    {
        add_action('wilcity/header/after-menu', [$this, 'printProfileNavigation'], 30);
        add_action('wilcity/footer/vue-popup-wrapper', [$this, 'askForDeletingAuthorMessage'], 30);
        add_action('wp_ajax_wilcity_fetch_general_status_statistics', [$this, 'fetchGeneralStatusStatistics']);
    }
    
    public function askForDeletingAuthorMessage()
    {
        if (!is_user_logged_in()) {
            return '';
        }
        
        $dashboardPage = GetWilokeSubmission::getField('dashboard_page');
        global $post;
        
        if (empty($post->ID) || $post->ID != $dashboardPage) {
            return '';
        }
        
        ?>
        <ask-for-delete-author-message-popup
                title="<?php echo esc_html_e('Delete Author Messages', 'wiloke-listing-tools'); ?>"
                body="<?php esc_html_e('Do you want to delete all message from this author?',
                    'wiloke-listing-tools'); ?>" yes="<?php esc_html_e('Yes', 'wiloke-listing-tools'); ?>"
                cancel="<?php esc_html_e('Cancel', 'wiloke-listing-tools'); ?>"></ask-for-delete-author-message-popup>
        <ask-for-delete-single-message-popup title="<?php echo esc_html_e('Delete Message', 'wiloke-listing-tools'); ?>"
                                             body="<?php esc_html_e('Do you want to delete this message?',
                                                 'wiloke-listing-tools'); ?>"
                                             yes="<?php esc_html_e('Yes', 'wiloke-listing-tools'); ?>"
                                             cancel="<?php esc_html_e('Cancel',
                                                 'wiloke-listing-tools'); ?>"></ask-for-delete-single-message-popup>
        <?php
    }
    
    /*
     * 1. All
     * 2. Event Only
     * 3. Excepet Event
     */
    public static function countPostStatus($postStatus, $postTypeType = 3)
    {
        switch ($postTypeType) {
            case 1:
                $aPostTypes = GetSettings::getFrontendPostTypes(true, true);
                break;
            case 2:
                $aPostTypes = ['event'];
                break;
            case 3:
                $aPostTypes = GetSettings::getFrontendPostTypes(true, false);
                break;
            default:
                $aPostTypes = GetSettings::getFrontendPostTypes(true, true);
                break;
        }
        
        switch ($postStatus) {
            case 'up_coming_events':
                $count = EventModel::countUpcomingEventsOfAuthor(User::getCurrentUserID());
                
                return empty($count) ? 0 : abs($count);
                break;
            case 'on_going_events':
                $count = EventModel::countOnGoingEventsOfAuthor(User::getCurrentUserID());
                
                return empty($count) ? 0 : abs($count);
                break;
            case 'expired_events':
                $count = EventModel::countExpiredEventsOfAuthor(User::getCurrentUserID());
                
                return empty($count) ? 0 : abs($count);
                break;
        }
        
        $postTypeKeys = '("'.implode('","', $aPostTypes).'")';
        
        global $wpdb;
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($wpdb->posts.ID) FROM $wpdb->posts WHERE $wpdb->posts.post_type IN $postTypeKeys AND $wpdb->posts.post_status=%s AND $wpdb->posts.post_author=%d",
                $postStatus, User::getCurrentUserID()
            )
        );
        
        return empty($total) ? 0 : abs($total);
    }
    
    public static function getPostStatusInfo()
    {
        $aData = [
            [
                'status'      => esc_html__('Published', 'wiloke-listing-tools'),
                'icon'        => 'la la-share-alt',
                'bgColor'     => 'bg-gradient-1',
                'post_status' => 'publish',
                'total'       => 0
            ],
            [
                'status'      => esc_html__('In Review', 'wiloke-listing-tools'),
                'icon'        => 'la la-refresh',
                'bgColor'     => 'bg-gradient-2',
                'post_status' => 'pending',
                'total'       => 0
            ],
            [
                'status'      => esc_html__('Unpaid', 'wiloke-listing-tools'),
                'icon'        => 'la la-money',
                'bgColor'     => 'bg-gradient-3',
                'post_status' => 'unpaid',
                'total'       => 0
            ],
            [
                'status'      => esc_html__('Expired', 'wiloke-listing-tools'),
                'icon'        => 'la la-exclamation-triangle',
                'bgColor'     => 'bg-gradient-4',
                'post_status' => 'expired',
                'total'       => 0
            ]
        ];
        
        $aData           = apply_filters('wilcity/dashboard/general-listing-status-statistic', $aData);
        $totalPostStatus = count($aData);
        
        foreach ($aData as $order => $aInfo) {
            $aData[$order]['total'] = self::countPostStatus($aInfo['post_status'], 3);
            
            if ($totalPostStatus == 3) {
                $aData[$order]['wrapperClass'] = 'col-sm-6 col-md-4 col-lg-4';
            } else {
                $aData[$order]['wrapperClass'] = 'col-sm-6 col-md-3 col-lg-3';
            }
        }
        
        return $aData;
    }
    
    public function fetchGeneralStatusStatistics()
    {
        $this->middleware(['canSubmissionListing'], []);
        $aData = self::getPostStatusInfo();
        wp_send_json_success($aData);
    }
    
    public static function getNavigation($userID = null)
    {
        $aNavigation = wilokeListingToolsRepository()->get('dashboard:aNavigation');
        
        if (!\WilokeThemeOptions::isEnable('listing_toggle_favorite')) {
            unset($aNavigation['favorites']);
        }
        
        $aDokanDashboardPage = GetSettings::getDokanPages(true);
        
        if ($aDokanDashboardPage) {
            $aNavigation['dokan'] = [
                'name'     => $aDokanDashboardPage['title'],
                'icon'     => 'la la-shopping-cart',
                'redirect' => $aDokanDashboardPage['permalink']
            ];
        }
        
        if (empty($aNavigation)) {
            return false;
        }
        $userID = empty($userID) ? User::getCurrentUserID() : $userID;
        
        if (!User::canSubmitListing($userID)) {
            $aNavigation = array_filter($aNavigation, function ($aItem, $key) {
                if (in_array($key, ['favorites', 'messages', 'profile'])) {
                    return true;
                }
            }, ARRAY_FILTER_USE_BOTH);
        }
        
        $aNavigation = apply_filters('wilcity/dashboard/navigation', $aNavigation, $userID);
        
        foreach ($aNavigation as $key => $aItem) {
            if (isset(self::$aEndpoint[$key])) {
                $aNavigation[$key]['endpoint'] = self::$aEndpoint[$key];
            } else {
                $aNavigation[$key]['endpoint'] = '';
            }
            
            switch ($key) {
                case 'favorites':
                    $aNavigation[$key]['count'] = absint(FavoriteStatistic::countMyFavorites($userID));
                    break;
                case 'messages':
                    $aNavigation[$key]['count'] = absint(MessageModel::countUnReadMessages($userID));
                    break;
                case 'listings':
                    $aNavigation[$key]['count'] = absint(User::countUserPostsByPostTypes($userID, true));
                    break;
                case 'events':
                    $aNavigation[$key]['count'] = absint(User::countUserPostsByPostTypes($userID, false));
                    break;
                case 'notifications':
                    $aNavigation[$key]['count'] =
                        absint(GetSettings::getUserMeta($userID, NotificationsModel::$countNewKey));
                    break;
            }
        }
        
        return $aNavigation;
    }
    
    public static function printMobileDashboard()
    {
        $aNavigation = self::getNavigation();
        ?>
        <div class="dashboard-content_navMobile__2NsOn">
            <div class="field_module__1H6kT field_style2__2Znhe js-field">
                <div class="field_wrap__Gv92k">
                    <select id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix',
                        'wilcity-dashboard-nav-mobile')); ?>" class="select-2 wilcity-select2-changed mt-15">
                        <?php foreach ($aNavigation as $route => $aItem): ?>
                            <option value="<?php echo esc_attr($route); ?>"><?php echo esc_html($aItem['name']); ?></option>
                        <?php endforeach; ?>
                    </select><span class="field_label__2eCP7 text-ellipsis"><?php esc_html_e('Dashboard',
                            'wiloke-listing-tools'); ?></span><span class="bg-color-primary"></span>
                </div>
            </div>
        </div>
        <?php
    }
    
    public static function printDashboardNavigation($isPrintRouter)
    {
        $dashboardUrl = GetWilokeSubmission::getField('dashboard_page', true);
        ?>
        <ul>
            <?php
            $aNavigation = self::getNavigation();
            foreach ($aNavigation as $route => $aItem) : ?>
                <li :class="navWrapperClass('<?php echo esc_attr($route); ?>')">
                    <?php if ($isPrintRouter) : ?>
                        <?php if (isset($aItem['redirect'])) : ?>
                            <router-link
                                class="dashboard-nav_link__2BmK9 text-ellipsis color-primary--hover <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                                    'wilcity-dashboard-item-'.$route)); ?>"
                                :to="{path: '<?php echo esc_attr($route); ?>', query: {redirectTo: '<?php echo esc_attr($aItem['redirect']); ?>'}}">
                        <?php else: ?>
                            <router-link
                                class="dashboard-nav_link__2BmK9 text-ellipsis color-primary--hover <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                                    'wilcity-dashboard-item-'.$route)); ?>" to="/<?php echo esc_attr($route); ?>">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($aItem['redirect'])) : ?>
                            <a class="dashboard-nav_link__2BmK9 text-ellipsis color-primary--hover <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                                'wilcity-dashboard-item-'.$route)); ?>"
                               href="<?php echo esc_url($aItem['redirect']); ?>">
                                <?php else: ?>
                                <a class="dashboard-nav_link__2BmK9 text-ellipsis color-primary--hover <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                                    'wilcity-dashboard-item-'.$route)); ?>"
                                   href="<?php echo esc_url($dashboardUrl).'#/'.esc_attr($route); ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                            <span class="dashboard-nav_icon__2gZV4">
                                <i class="<?php echo esc_attr($aItem['icon']); ?>"></i>
                            </span>
                            <span class="dashboard-nav_text__x-_IZ"><?php echo esc_html($aItem['name']); ?></span>
                            <?php if ($route == 'messages' && Firebase::isFirebaseEnable() && $isPrintRouter) : ?>
                                <span class="dashboard-nav_number__5N1Ch color-primary"
                                      v-html="countMessages"><?php echo esc_html($aItem['count']); ?></span>
                            <?php else: ?>
                                <?php if (isset($aItem['count']) && !empty($aItem['count'])) : ?>
                                    <span class="dashboard-nav_number__5N1Ch color-primary"><?php echo esc_html($aItem['count']); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php if ($isPrintRouter) : ?>
                            </router-link>
                        <?php else: ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <li class="dashboard-nav_item__2798B <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                'wilcity-dashboard-item-lougout')); ?>">
                <a class="dashboard-nav_link__2BmK9 text-ellipsis color-primary--hover"
                   href="<?php echo wp_logout_url(); ?>">
                    <span class="dashboard-nav_icon__2gZV4"><i class="la la-sign-out"></i></span>
                    <span class="dashboard-nav_text__x-_IZ"><?php esc_html_e('Logout',
                            'wiloke-listing-tools'); ?></span>
                    <span class="dashboard-nav_number__5N1Ch color-primary"></span>
                </a>
            </li>
        </ul>
        <?php
    }
    
    public function printProfileNavigation()
    {
        if (!is_user_logged_in() || !GetWilokeSubmission::isSystemEnable()) {
            return '';
        }
        $userID = get_current_user_id();
        ?>
        <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-profile-nav-menu')); ?>"
             style="display: none" :class="aCssClass">
            <a class="header_loginHead__3HoVP" href="#" @click.prevent="toggleProfileMenu">
                <div class="header_avatar__3lw1r bg-cover"
                     style="background-image: url('<?php echo User::getAvatar($userID); ?>')"><img
                            src="<?php echo User::getAvatar($userID); ?>"
                            alt="<?php echo User::getField('display_name', $userID); ?>"/></div>
            </a>
            <div class="header_loginBody__2hz2g" :class="wrapperClass">
                <div class="dashboard-nav_module__3c0Pb list-none dashboard-nav_abs__2IGwx arrow--top-right">
                    <?php self::printDashboardNavigation(false); ?>
                </div>
            </div>
        </div>
        <?php
    }
}
