<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;
use WilokeListingTools\Frontend\User;

class WooCommerceRatingController extends WooCommerceController
{
    public function __construct()
    {
        // Rating
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/products/(?P<id>\d+)/ratings', [
                'methods'  => 'GET',
                'callback' => [$this, 'getProductRatings'],
                'args'     => [
                    'id'    => [
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => 'The product id is required'
                    ],
                    'page'  => [
                        'required'    => false,
                        'type'        => 'integer',
                        'description' => 'The page number must be interger type'
                    ],
                    'count' => [
                        'required'    => false,
                        'type'        => 'integer',
                        'description' => 'The count number must be interger type'
                    ]
                ]
            ]);
        });

        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/products/(?P<id>\d+)/ratings-statistic', [
                'methods'  => 'GET',
                'callback' => [$this, 'getProductRatingsStatistic'],
                'args'     => [
                    'id' => [
                        'required'    => true,
                        'type'        => 'integer',
                        'description' => 'The product id is required'
                    ]
                ]
            ]);
        });

        add_filter('wilcity/wilcity-mobile-app/filter/product-rating', [$this, 'filterProductRating'], 10, 2);
    }

    public function filterProductRating($aRatings, $oRequest) {
        $aRatings = $this->getProductRatings($oRequest);
        return $aRatings;
    }

    /**
     * @param $oComment
     *
     * @return array
     */
    private function ratingSkeleton($oComment)
    {
        return [
            'ID'           => abs($oComment->comment_ID),
            'rating'       => abs(get_comment_meta($oComment->comment_ID, 'rating', true)),
            'author'       => $oComment->comment_author,
            'authorAvatar' => User::getAvatar($oComment->user_id),
            'date'         => $oComment->comment_date,
            'content'      => $oComment->comment_content
        ];
    }

    protected function isEnablingRating()
    {
        if (!wc_review_ratings_enabled()) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('ratingIsDisabling')
            ];
        }

        return true;
    }

    public function getProductRatingsStatistic(\WP_REST_Request $oRequest)
    {
        if ($aMsg = $this->isEnablingRating() !== true) {
            return $aMsg;
        }

        $productID = $oRequest->get_param('id');
        if ($aMsg = $this->isProduct($productID) !== true) {
            return $aMsg;
        }

        $ratingCount = $this->oProduct->get_rating_count();
        if (empty($ratingCount)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('There are no rating yet', 'wilcity-mobile-app')
            ];
        }

        $average = $this->oProduct->get_average_rating();

        return [
            'status' => 'success',
            'data'   => [
                'average_rating'    => floatval($average),
                'rating_count'      => $ratingCount,
                'oDetailStatistics' => [
                    5 => $this->oProduct->get_rating_count(5),
                    4 => $this->oProduct->get_rating_count(4),
                    3 => $this->oProduct->get_rating_count(3),
                    2 => $this->oProduct->get_rating_count(2),
                    1 => $this->oProduct->get_rating_count(1)
                ]
            ]
        ];
    }

    /**
     * @param \WP_REST_Request $oRequest
     *
     * @return array|bool
     */
    public function getProductRatings(\WP_REST_Request $oRequest, $totalRatings = '')
    {
        if ($aMsg = $this->isEnablingRating() !== true) {
            return $aMsg;
        }

        $productID = $oRequest->get_param('id');
        $paged     = $oRequest->get_param('page');
        $number    = $oRequest->get_param('count');
        $paged     = empty($paged) ? 1 : $paged;

        if (!empty($totalRatings)) {
            $number = $totalRatings;
        } else {
            $number = empty($number) ? 5 : $number;
        }

        if ($aMsg = $this->isProduct($productID) !== true) {
            return $aMsg;
        }

        $ratingCount = $this->oProduct->get_rating_count();

        if (empty($ratingCount)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noRating')
            ];
        }

        $aArgs = [
            'comment_post_ID' => abs($productID),
            'status'          => 'approve',
            'paged'           => $paged,
            'number'          => $number
        ];

        if ($ratingFilter = $oRequest->get_param('filter_rating')) {
            $aArgs['meta_key']   = 'rating';
            $aArgs['meta_value'] = $ratingFilter;
        }

        $oQuery    = new \WP_Comment_Query();
        $aComments = $oQuery->query($aArgs);

        if (empty($aComments)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('allCommentsLoaded')
            ];
        }

        $aResponse = [];
        foreach ($aComments as $oComment) {
            $aResponse[] = $this->ratingSkeleton($oComment);
        }

        return [
            'status' => 'success',
            'data'   => [
                'aItems' => $aResponse,
                'total'  => $ratingCount,
                'pages'  => abs(ceil($ratingCount / $number))
            ]
        ];
    }
}
