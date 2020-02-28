<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\JsonSkeleton;
use WILCITY_APP\Controllers\ParsePost;
use WILCITY_APP\Controllers\VerifyToken;
use WILCITY_APP\Controllers\WooCommerceController;

class WooCommerceWishlistController extends WooCommerceController
{
    use ParsePost;
    use VerifyToken;
    use JsonSkeleton;

    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/wishlists', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getWishlists']
                ],
                [
                    'methods'  => \WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'addWishlist'],
                    'args'     => [
                        'productID'       => [
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => 'The Product ID id is required'
                        ],
                        'productQuantity' => [
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => 'The Product Quantity id is required'
                        ]
                    ]
                ],
                [
                    'methods'  => \WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'deleteWishlist'],
                    'args'     => [
                        'productID'     => [
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => esc_html__('The product id is an integer', 'wilcity-mobile-app')
                        ],
                        'wishlistToken' => [
                            'required'    => true,
                            'type'        => 'String',
                            'description' => esc_html__('The wishlist token is required', 'wilcity-mobile-app')
                        ],
                        'wishlistID'    => [
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => esc_html__('The wishlist id must be an integer', 'wilcity-mobile-app')
                        ]
                    ]
                ]
            ]);
        });

        add_filter('wilcity/wilcity-mobile-app/dashboard-navigator', [$this, 'addWishlistMenuItemToDashboard'],
            10, 1);
    }

    private function isInstalledWITHWCWL()
    {
        if (!function_exists('YITH_WCWL')) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('In order to use this feature, please go to Plugins -> Add New -> Install YITH WishList plugin',
                    'wilcity-mobile-app')
            ];
        }

        return true;
    }

    /**
     * Retrieve all the wishlist matching specified arguments
     *
     * @param $args mixed Array of valid arguments<br/>
     *              [<br/>
     *              'id'                  // Wishlist id to search, if any<br/>
     *              'user_id'             // User owner<br/>
     *              'wishlist_slug'       // Slug of the wishlist to search<br/>
     *              'wishlist_name'       // Name of the wishlist to search<br/>
     *              'wishlist_token'      // Token of the wishlist to search<br/>
     *              'wishlist_visibility' // Wishlist visibility: all, visible, public, shared, private<br/>
     *              'user_search'         // String to match against first name / last name or email of the wishlist
     *              owner<br/>
     *              'is_default'          // Whether wishlist should be default or not<br/>
     *              'orderby'             // Column used to sort final result (could be any wishlist lists column)<br/>
     *              'order'               // Sorting order<br/>
     *              'limit'               // Pagination param: maximum number of elements in the set. 0 to retrieve all
     *              elements<br/>
     *              'offset'              // Pagination param: offset for the current set. 0 to start from the first
     *              item<br/>
     *              'show_empty'          // Whether to show empty lists os not<br/>
     *              ]
     *
     * @return array
     * @since 2.0.0
     */
    public function getYITHWishlists($args = [])
    {
        global $wpdb;

        $default = [
            'id'                  => false,
            'user_id'             => '',
            'wishlist_slug'       => false,
            'wishlist_name'       => false,
            'wishlist_token'      => false,
            'wishlist_visibility' => apply_filters('yith_wcwl_wishlist_visibility_string_value', 'all'),
            // all, visible, public, shared, private
            'user_search'         => false,
            'is_default'          => false,
            'orderby'             => 'ID',
            'order'               => 'DESC',
            'limit'               => false,
            'offset'              => 0,
            'show_empty'          => true
        ];

        $args = wp_parse_args($args, $default);
        extract($args);

        $sql = "SELECT l.*";

        if (!empty($user_search)) {
            $sql .= ", u.user_email, umn.meta_value AS first_name, ums.meta_value AS last_name";
        }

        $sql .= " FROM `{$wpdb->yith_wcwl_wishlists}` AS l";

        if (!empty($user_search) || (!empty($orderby) && $orderby == 'user_login')) {
            $sql .= " LEFT JOIN `{$wpdb->users}` AS u ON l.`user_id` = u.ID";
        }

        if (!empty($user_search)) {
            $sql .= " LEFT JOIN `{$wpdb->usermeta}` AS umn ON umn.`user_id` = u.`ID`";
            $sql .= " LEFT JOIN `{$wpdb->usermeta}` AS ums ON ums.`user_id` = u.`ID`";
        }

        $sql .= " WHERE 1";

        if (!empty($user_id)) {
            $sql .= " AND l.`user_id` = %d";

            $sql_args = [
                $user_id
            ];
        }

        if (!empty($user_search)) {
            $sql        .= " AND ( umn.`meta_key` LIKE %s AND ums.`meta_key` LIKE %s AND ( u.`user_email` LIKE %s OR umn.`meta_value` LIKE %s OR ums.`meta_value` LIKE %s ) )";
            $sql_args[] = 'first_name';
            $sql_args[] = 'last_name';
            $sql_args[] = "%".esc_sql($user_search)."%";
            $sql_args[] = "%".esc_sql($user_search)."%";
            $sql_args[] = "%".esc_sql($user_search)."%";
        }

        if (!empty($is_default)) {
            $sql        .= " AND l.`is_default` = %d";
            $sql_args[] = $is_default;
        }

        if (!empty($id)) {
            $sql        .= " AND l.`ID` = %d";
            $sql_args[] = $id;
        }

        if (isset($wishlist_slug) && $wishlist_slug !== false) {
            $sql        .= " AND l.`wishlist_slug` = %s";
            $sql_args[] = sanitize_title_with_dashes($wishlist_slug);
        }

        if (!empty($wishlist_token)) {
            $sql        .= " AND l.`wishlist_token` = %s";
            $sql_args[] = $wishlist_token;
        }

        if (!empty($wishlist_name)) {
            $sql        .= " AND l.`wishlist_name` LIKE %s";
            $sql_args[] = "%".esc_sql($wishlist_name)."%";
        }

        if (!empty($wishlist_visibility) && $wishlist_visibility != 'all') {
            switch ($wishlist_visibility) {
                case 'visible':
                    $sql        .= " AND ( l.`wishlist_privacy` = %d OR l.`is_public` = %d )";
                    $sql_args[] = 0;
                    $sql_args[] = 1;
                    break;
                case 'public':
                    $sql        .= " AND l.`wishlist_privacy` = %d";
                    $sql_args[] = 0;
                    break;
                case 'shared':
                    $sql        .= " AND l.`wishlist_privacy` = %d";
                    $sql_args[] = 1;
                    break;
                case 'private':
                    $sql        .= " AND l.`wishlist_privacy` = %d";
                    $sql_args[] = 2;
                    break;
                default:
                    $sql        .= " AND l.`wishlist_privacy` = %d";
                    $sql_args[] = 0;
                    break;
            }
        }

        if (empty($show_empty)) {
            $sql .= " AND l.`ID` IN ( SELECT wishlist_id FROM {$wpdb->yith_wcwl_items} )";
        }

        if (!empty($orderby) && isset($order)) {
            $sql .= " ORDER BY ".esc_sql($orderby)." ".esc_sql($order);
        }

        if (!empty($limit) && isset($offset)) {
            $sql        .= " LIMIT %d, %d";
            $sql_args[] = $offset;
            $sql_args[] = $limit;
        }

        if (!empty($sql_args)) {
            $sql = $wpdb->prepare($sql, $sql_args);
        }

        $lists = $wpdb->get_results($sql, ARRAY_A);

        return $lists;
    }

    /**
     * Generate a token to visit wishlist
     *
     * @return string token
     * @since 2.0.0
     */
    public function generateYITHWishlistToken()
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM `{$wpdb->yith_wcwl_wishlists}` WHERE `wishlist_token` = %s";

        do {
            $dictionary = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $nchars     = 12;
            $token      = "";

            for ($i = 0; $i <= $nchars - 1; $i++) {
                $token .= $dictionary[mt_rand(0, strlen($dictionary) - 1)];
            }

            $count = $wpdb->get_var($wpdb->prepare($sql, $token));
        } while ($count != 0);

        return $token;
    }

    /**
     * Generate default wishlist for a specific user, adding all NULL items of the user to it
     *
     * @param $user_id int
     *
     * @return int Default wishlist id
     * @since 2.0.0
     */
    private function generateYITHWishlistID($userID)
    {
        global $wpdb;

        $wishlists = $this->getYITHWishlists([
            'user_id'    => $userID,
            'is_default' => 1
        ]);

        if (!empty($wishlists)) {
            $default_user_wishlist      = $wishlists[0]['ID'];
            $this->last_operation_token = $wishlists[0]['wishlist_token'];
            do_action('yith_wcwl_default_user_wishlist', $userID, $wishlists);
        } else {
            $token                      = $this->generateYITHWishlistToken();
            $this->last_operation_token = $token;

            $wpdb->insert($wpdb->yith_wcwl_wishlists, [
                'user_id'          => apply_filters('yith_wcwl_default_wishlist_user_id', $userID),
                'wishlist_slug'    => apply_filters('yith_wcwl_default_wishlist_slug', ''),
                'wishlist_token'   => $token,
                'wishlist_name'    => apply_filters('yith_wcwl_default_wishlist_name', ''),
                'wishlist_privacy' => apply_filters('yith_wcwl_default_wishlist_privacy', 0),
                'is_default'       => 1
            ]);

            $default_user_wishlist = $wpdb->insert_id;
        }

        $sql      = "UPDATE {$wpdb->yith_wcwl_items} SET wishlist_id = %d WHERE user_id = %d AND wishlist_id IS NULL";
        $sql_args = [
            $default_user_wishlist,
            $userID
        ];

        $wpdb->query($wpdb->prepare($sql, $sql_args));

        return $default_user_wishlist;
    }

    /**
     * @param      $productID
     * @param bool $wishlistID
     *
     * @return mixed
     */
    public function isYITHProductInWishlist($productID, $wishlistID, $userID)
    {
        global $wpdb, $sitepress;
        if (defined('ICL_SITEPRESS_VERSION')) {
            $productID = yit_wpml_object_id($productID, 'product', true, $sitepress->get_default_language());
        }

        $sql      = "SELECT COUNT(*) as `cnt` FROM `{$wpdb->yith_wcwl_items}` WHERE `prod_id` = %d AND `user_id` = %d";
        $sql_args = [
            $productID,
            $userID
        ];

        if ($wishlistID != false) {
            $sql        .= " AND `wishlist_id` = %d";
            $sql_args[] = $wishlistID;
        } elseif ($default_wishlist_id = $this->generateYITHWishlistID($userID)) {
            $sql        .= " AND `wishlist_id` = %d";
            $sql_args[] = $default_wishlist_id;
        } else {
            $sql .= " AND `wishlist_id` IS NULL";
        }

        $results = $wpdb->get_var($wpdb->prepare($sql, $sql_args));
        $exists  = (bool)($results > 0);

        return apply_filters('yith_wcwl_is_product_in_wishlist', $exists, $productID, $wishlistID);
    }

    private function addYITHWishList($productID, $userID, $quantity = 1)
    {
        global $wpdb, $sitepress;

        if (defined('ICL_SITEPRESS_VERSION')) {
            $productID = yit_wpml_object_id($productID, 'product', true, $sitepress->get_default_language());
        }

        if ($productID == false) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToWishlist')
            ];
        }

        $aInsertArgs = [
            'prod_id'   => $productID,
            'user_id'   => $userID,
            'quantity'  => $quantity,
            'dateadded' => date('Y-m-d H:i:s')
        ];

        $wishlistID                 = $this->generateYITHWishlistID($userID);
        $aInsertArgs['wishlist_id'] = $wishlistID;

        if ($this->isYITHProductInWishlist($productID, $wishlistID, $userID)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToWishlist')
            ];
        }

        $result = $wpdb->insert($wpdb->yith_wcwl_items, $aInsertArgs);

        if ($result) {
            if ($this->last_operation_token) {
                delete_transient('yith_wcwl_wishlist_count_'.$this->last_operation_token);
            }

            if ($userID) {
                delete_transient('yith_wcwl_user_default_count_'.$userID);
                delete_transient('yith_wcwl_user_total_count_'.$userID);
            }
        }

        if ($result) {
            do_action('yith_wcwl_added_to_wishlist', $productID, $wishlistID, $userID);

            return [
                'status' => 'success',
                'msg'    => sprintf(wilcityAppGetLanguageFiles('addedProductToWishlist'), get_the_title($productID)),
                'oInfo'  => [
                    'wishlistID'    => $wishlistID,
                    'wishlistToken' => $this->last_operation_token
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToWishlist')
            ];
        }
    }

    public function getWishlists(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }

        $oToken->getUserID();
        $userID = $oToken->userID;

        if ($status = $this->isInstalledWITHWCWL() !== true) {
            return $status;
        }

        $aArgs = [
            'pagination' => 'yes',
            'page'       => $oRequest->get_param('page') ? $oRequest->get_param('page') : 1
        ];

        if ($postsPerPage = $oRequest->get_param('postsPerPage')) {
            $aArgs['postsPerPage'] = $postsPerPage;
        }

        $aWistListItems = $this->getYITHWishListItems($userID, $aArgs);

        if (empty($aWistListItems)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noProductsInWishlist')
            ];
        }

        $aData          = $aWistListItems;
        $aData['title'] = get_option('yith_wcwl_wishlist_title');

        return [
            'status' => 'success',
            'data'   => $aData
        ];
    }

    public function addWishlist(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }

        if ($status = $this->isInstalledWITHWCWL() !== true) {
            return $status;
        }

        $oToken->getUserID();
        $userID = $oToken->userID;

        $productID = $oRequest->get_param('productID');
        if (get_post_type($productID) !== 'product' || get_post_status($productID) !== 'publish') {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('unPublishedProduct')
            ];
        }

        return $this->addYITHWishList(
            $productID,
            $userID,
            $oRequest->get_param('productQuantity')
        );
    }

    private function countYITHWishListProduct($wishlist_token)
    {
        global $wpdb;
        if (!empty($wishlist_token)) {
            $count = get_transient('yith_wcwl_wishlist_count_'.$wishlist_token);
        } else {
            $count = get_transient('yith_wcwl_user_default_count_'.get_current_user_id());
        }

        if (false === $count) {
            $hidden_products = yith_wcwl_get_hidden_products();

            $sql  = "SELECT i.`prod_id` AS `cnt`
                        FROM `{$wpdb->yith_wcwl_items}` AS i
                        LEFT JOIN `{$wpdb->yith_wcwl_wishlists}` AS l ON l.ID = i.wishlist_id
                        INNER JOIN `{$wpdb->posts}` AS p ON i.`prod_id` = p.`ID`
                        WHERE p.`post_type` = %s AND p.`post_status` = %s";
            $sql  .= $hidden_products ? " AND p.ID NOT IN ( ".implode(', ',
                    array_filter($hidden_products, 'esc_sql'))." )" : "";
            $args = [
                'product',
                'publish'
            ];

            if (!empty($wishlist_token)) {
                $sql    .= " AND l.`wishlist_token` = %s";
                $args[] = $wishlist_token;
            } else {
                $sql    .= " AND l.`is_default` = %d AND l.`user_id` = %d";
                $args[] = 1;
                $args[] = get_current_user_id();
            }

            $sql .= " GROUP BY i.prod_id, l.ID";

            $query = $wpdb->prepare($sql, $args);
            $count = count($wpdb->get_col($query));

            $transient_name = !empty($wishlist_token) ? ('yith_wcwl_wishlist_count_'.$wishlist_token) : ('yith_wcwl_user_default_count_'.get_current_user_id());
            set_transient($transient_name, $count, WEEK_IN_SECONDS);
        }

        return $count;
    }

    /**
     * Returns details of a wishlist, searching it by wishlist id
     *
     * @param $wishlistID int
     *
     * @return array
     * @since 2.0.0
     */
    private function getYITHWishlistDetail($wishlistID)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->yith_wcwl_wishlists} WHERE `ID` = %d";

        return $wpdb->get_row($wpdb->prepare($sql, $wishlistID), ARRAY_A);
    }

    private function deleteYITHWishListItem($productID, $wishlistID, $userID)
    {
        global $wpdb, $sitepress;
        do_action('yith_wcwl_removing_from_wishlist', $productID, $wishlistID, $userID);
        if (defined('ICL_SITEPRESS_VERSION')) {
            $productID = yit_wpml_object_id($productID, 'product', true, $sitepress->get_default_language());
        }

        if ($productID == false) {
            return false;
        }

        $sql        = "DELETE FROM {$wpdb->yith_wcwl_items} WHERE user_id = %d AND prod_id = %d";
        $aSqlParams = [
            $userID,
            $productID
        ];

        $aWishlist                  = $this->getYITHWishlistDetail($wishlistID);
        $this->last_operation_token = $aWishlist['wishlist_token'];

        $sql          .= " AND wishlist_id = %d";
        $aSqlParams[] = $wishlistID;

        $canDelete = $wpdb->query($wpdb->prepare($sql, $aSqlParams));
        if ($canDelete) {
            if ($this->last_operation_token) {
                delete_transient('yith_wcwl_wishlist_count_'.$this->last_operation_token);
            }

            delete_transient('yith_wcwl_user_default_count_'.$userID);
            delete_transient('yith_wcwl_user_total_count_'.$userID);

            $status = true;
        } else {
            $status = false;
        }

        if ($status) {
            do_action('yith_wcwl_removed_from_wishlist', $productID, $aWishlist, $userID);
        }

        return $status;
    }

    public function deleteWishlist(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }

        if ($status = $this->isInstalledWITHWCWL() !== true) {
            return $status;
        }

        $oToken->getUserID();

        $userID        = $oToken->userID;
        $wishListToken = $oRequest->get_param('wishlistToken');
        $productID     = $oRequest->get_param('productID');
        $wishListID    = $oRequest->get_param('wishlistID');

        $count = $this->countYITHWishListProduct($wishListToken);

        if ($count != 0) {
            if ($this->deleteYITHWishListItem($productID, $wishListID, $userID)) {
                return [
                    'status' => 'success',
                    'msg'    => sprintf(wilcityAppGetLanguageFiles('removedProductFromWishlist'), get_the_title($productID))
                ];
            } else {
                return [
                    'status' => 'error',
                    'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToWishlist')
                ];
            }
        } else {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noProductsInWishlist')
            ];
        }
    }

    public function addWishlistMenuItemToDashboard($aNavigation)
    {
        if (!function_exists('YITH_WCWL')) {
            return $aNavigation;
        }

        $aNavigation[] = [
            'name'     => wilcityAppGetLanguageFiles('productsWishlist'),
            'icon'     => 'la la-heart-o',
            'endpoint' => 'wc/products/wishlist'
        ];

        return $aNavigation;
    }

    private function countYITHTotalProductsInUserWishList($userID, $wishlist_token = '')
    {
        global $wpdb;
        if (!empty($wishlist_token)) {
            $count = get_transient('yith_wcwl_wishlist_count_'.$wishlist_token);
        } else {
            $count = get_transient('yith_wcwl_user_default_count_'.$userID);
        }

        if (false === $count) {
            $hidden_products = yith_wcwl_get_hidden_products();

            $sql  = "SELECT i.`prod_id` AS `cnt`
                        FROM `{$wpdb->yith_wcwl_items}` AS i
                        LEFT JOIN `{$wpdb->yith_wcwl_wishlists}` AS l ON l.ID = i.wishlist_id
                        INNER JOIN `{$wpdb->posts}` AS p ON i.`prod_id` = p.`ID`
                        WHERE p.`post_type` = %s AND p.`post_status` = %s";
            $sql  .= $hidden_products ? " AND p.ID NOT IN ( ".implode(', ',
                    array_filter($hidden_products, 'esc_sql'))." )" : "";
            $args = [
                'product',
                'publish'
            ];

            if (!empty($wishlist_token)) {
                $sql    .= " AND l.`wishlist_token` = %s";
                $args[] = $wishlist_token;
            } else {
                $sql    .= " AND l.`is_default` = %d AND l.`user_id` = %d";
                $args[] = 1;
                $args[] = $userID;
            }

            $sql .= " GROUP BY i.prod_id, l.ID";

            $query = $wpdb->prepare($sql, $args);
            $count = count($wpdb->get_col($query));

            $transient_name = !empty($wishlist_token) ? ('yith_wcwl_wishlist_count_'.$wishlist_token) : ('yith_wcwl_user_default_count_'.$userID);
            set_transient($transient_name, $count, WEEK_IN_SECONDS);
        }

        return abs($count);
    }

    private function getYITHWishListItems($userID, $aArgs = [])
    {
        global $yith_wcwl_wishlist_token;
        $aArgs                    = wp_parse_args(
            $aArgs,
            [
                'pagination'   => 'yes',
                'page'         => 1,
                'postsPerPage' => 5,
                'is_default'   => 1
            ]
        );
        $aQueryArgs               = [];
        $aQueryArgs['user_id']    = $userID;
        $aQueryArgs['is_default'] = 1;

        // counts number of elements in wishlist for the user
        $totalProducts = $this->countYITHTotalProductsInUserWishList($userID);

        if (empty($totalProducts)) {
            return [];
        }

        // sets current page, number of pages and element offset
        $currentPage = max(1, $aArgs['page']);
        // sets variables for pagination, if shortcode atts is set to yes
        $pages = abs(ceil($totalProducts / $aArgs['postsPerPage']));

        if ($currentPage > $pages) {
            $currentPage = $pages;
        }

        $offset               = ($currentPage - 1) * $aArgs['postsPerPage'];
        $aQueryArgs['limit']  = $aArgs['postsPerPage'];
        $aQueryArgs['offset'] = $offset;

        $wishlistToken = '';
        if (empty($wishlistID)) {
            $aWishlists = YITH_WCWL()->get_wishlists($aQueryArgs);
            if (!empty($aWishlists)) {
                $wishlistToken = $aWishlists[0]['wishlist_token'];
            }
        }

        $yith_wcwl_wishlist_token = $wishlistToken;

        // retrieve items to print
        $aWishListItems = YITH_WCWL()->get_products($aQueryArgs);
        if (empty($aWishListItems)) {
            return [];
        }

        $aProducts = [];
        foreach ($aWishListItems as $aItem) {
            $aProduct                  = $this->productSkeleton(wc_get_product($aItem['ID']), get_post($aItem['ID']));
            $aProduct['wishListID']    = abs($aItem['wishlist_id']);
            $aProduct['dateadded']     = $aItem['dateadded'];
            $aProduct['wishlistSlug']  = $aItem['wishlist_slug'];
            $aProduct['wishlistName']  = $aItem['wishlist_name'];
            $aProduct['wishlistToken'] = $aItem['wishlist_token'];
            $aProducts[]               = $aProduct;
        }

        return [
            'aProducts' => $aProducts,
            'pages'     => $pages,
            'totals'    => $totalProducts
        ];
    }
}
