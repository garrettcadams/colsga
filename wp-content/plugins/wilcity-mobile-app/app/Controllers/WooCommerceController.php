<?php
/*
 * This file will handle everything relates to WooCommerce App
 *
 * @category App
 * @package  Wilcity
 * @author   Wiloke
 * @since    1.5
 */

namespace WILCITY_APP\Controllers;

use Automattic\WooCommerce\Client;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

/*
 * Initialize class
 */

class WooCommerceController
{
    use ParsePost;
    use VerifyToken;
    use JsonSkeleton;
    protected $oWooCommerce;
    protected $oUserMeta;
    protected $aRequiredCheckoutFields;
    protected $aAllowedCountries;
    protected $productID;
    public $errAuthMsg = '';
    /*
     * \WC_Product
     */
    protected $oProduct;
    /**
     * Details array
     *
     * @var array
     * @since 1.0.0
     */
    public $details;
    public $last_operation_token;
    
    protected function dokanIsOrderByMyCustomer($myID, \WC_Order $oOrder)
    {
        if (!class_exists('WeDevs_Dokan')) {
            return false;
        }
        
        $aProductIDs = array_map(function ($oItem) {
            return get_post_field('post_author', $oItem->get_product_id());
        }, $oOrder->get_items());
        
        return in_array($myID, $aProductIDs);
    }
    
    protected function retrieveOrdersFormat($aOrders, $total, $aOtherRetrieveInfo = [])
    {
        $aData = $aOtherRetrieveInfo;
        $aData = array_merge([
            'aOrders' => $aOrders,
            'total'   => $total
        ], $aData);
        
        return [
            'status' => 'success',
            'data'   => $aData
        ];
    }
    
    protected function retrieveProductsFormat($aProducts, $total)
    {
        return [
            'status' => 'success',
            'items'  => $aProducts,
            'totals' => $total
        ];
    }
    
    /*
     * @param \WC_Order $oOrder
     */
    protected function getShortOrderSkeleton($oOrder)
    {
        $itemCount = 0;
        foreach ($oOrder->line_items as $oItem) {
            $itemCount = $itemCount + $oItem->quantity;
        }
        
        return [
            'id'        => $oOrder->id,
            'createdAt' => date_i18n(wc_date_format(), strtotime($oOrder->date_created)),
            'status'    => $oOrder->status,
            'total'     => sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $itemCount, 'wilcity-app'),
                wc_price($oOrder->total, ['currency' => $oOrder->currency]), $itemCount)
        ];
    }
    
    protected function calculateProductSaleOff($product)
    {
        if ($product->is_type('variable')) {
            $aPrices = $product->get_variation_prices();
            
            $saleOff = '';
            
            foreach ($aPrices['sale_price'] as $attributeOrder => $salePrice) {
                $regularPrice = $aPrices['regular_price'][$attributeOrder];
                if ($salePrice == $regularPrice) {
                    continue;
                }
                
                $currentSaleOff = ceil((abs($salePrice - $regularPrice) / $regularPrice) * 100);
                
                if ($currentSaleOff > $saleOff) {
                    $saleOff = $currentSaleOff;
                }
            }
            
            return $saleOff;
        } else {
            $regularPrice = $product->get_regular_price();
            $salePrice    = $product->get_sale_price();
            
            if (empty($regularPrice)) {
                return '';
            }
            
            if (empty($salePrice)) {
                return '';
            } else {
                return ceil((abs($salePrice - $regularPrice) / $regularPrice) * 100);
            }
        }
    }
    
    /**
     * @param object $product \WC_Product
     *
     * @return array
     */
    protected function getProductPrices($product)
    {
        $aResponse = [];
        if ($product->is_type('variable')) {
            $minPrice        = $product->get_variation_price();
            $maxPrice        = $product->get_variation_price('max');
            $regularPriceMin = $product->get_variation_regular_price();
            $regularPriceMax = $product->get_variation_regular_price('max');
            
            $aResponse['price']     = ($minPrice).' - '.floatval($maxPrice);
            $aResponse['priceHtml'] = wc_price($minPrice).' - '.wc_price($maxPrice);
            
            $salePriceMin = $product->get_variation_sale_price('min', false);
            $salePriceMax = $product->get_variation_sale_price('max', false);
            
            if ($salePriceMin == $regularPriceMin && $salePriceMax == $regularPriceMax) {
                $aResponse['salePrice']     = '';
                $aResponse['salePriceHtml'] = '';
            } else {
                $aResponse['salePrice']     = floatval($salePriceMin).' - '.floatval($salePriceMax);
                $aResponse['salePriceHtml'] = wc_price($salePriceMin).' - '.wc_price($salePriceMax);
            }
            
            $aResponse['regularPrice']     = floatval($regularPriceMin).' - '.floatval($regularPriceMax);
            $aResponse['regularPriceHtml'] = wc_price($regularPriceMin).' - '.wc_price($regularPriceMax);
        } elseif ($product->is_type('booking')) {
            $aResponse['price']     = floatval(wc_booking_calculated_base_cost(new \WC_Product_Booking
            ($product->get_id())));
            $aResponse['priceHtml'] = sprintf(__('From %s', 'wilcity-mobile-app'), wc_price($aResponse['price']));
            
            $aResponse['regularPrice']     = $aResponse['price'];
            $aResponse['regularPriceHtml'] = $aResponse['priceHtml'];
            $aResponse['salePrice']     = '';
            $aResponse['salePriceHtml'] = '';
        } else {
            $aResponse['price']            = floatval($product->get_price());
            $aResponse['priceHtml']        = wc_price($product->get_price());
            $aResponse['salePrice']        = $product->get_sale_price() === '' ? '' : floatval
            ($product->get_sale_price());
            $aResponse['salePriceHtml']    = $aResponse['salePrice'] == '' ? '' : wc_price($aResponse['salePrice']);
            $aResponse['regularPrice']     = floatval($product->get_regular_price());
            $aResponse['regularPriceHtml'] = wc_price($product->get_regular_price());
        }
        
        $aResponse['saleOff'] = $this->calculateProductSaleOff($product);
        
        return $aResponse;
    }
    
    /**
     * @param $productID
     *
     * @return bool
     */
    protected function isProductInWishlist($productID)
    {
        if (!class_exists('\YITH_WCWL')) {
            $isAddedToWishlist = false;
        } else {
            $isAddedToWishlist = YITH_WCWL()->is_product_in_wishlist($productID);
        }
        
        return $isAddedToWishlist;
    }
    
    /**
     * @param       object $product WC_Product
     * @param              $post
     * @param array        $aExcludes
     *
     * @return array
     */
    public function productSkeleton($product, $post, $aExcludes = [])
    {
        $productID    = $product->get_ID();
        $aFeaturedImg = $this->getFeaturedImg($productID);
        
        $aResponse = [
            'id'                => abs($productID),
            'type'              => $product->get_type(),
            'cartKey'           => '',
            'name'              => get_the_title($productID),
            'link'              => get_permalink($productID),
            'style'             => 'classic',
            'averageRating'     => floatval($product->get_average_rating()),
            'oFeaturedImg'      => $aFeaturedImg,
            'oAuthor'           => [
                'ID'          => $post->post_author,
                'displayName' => User::getField('display_name', $post->post_author),
                'avatar'      => User::getAvatar($post->post_author)
            ],
            'isAddedToWishlist' => $this->isProductInWishlist($productID)
        ];
        
        $aPrices = $this->getProductPrices($product);
        
        $aResponse = array_merge($aPrices, $aResponse);
        
        $aCategoryIDs = $product->get_category_ids();
        if (!empty($aCategoryIDs)) {
            foreach ($aCategoryIDs as $catID) {
                $oCat                       = get_term($catID, 'product_cat');
                $aResponse['oCategories'][] = $oCat->name;
            }
        } else {
            $aResponse['oCategories'] = [];
        }
        
        if (!empty($aExcludes)) {
            foreach ($aExcludes as $key) {
                if (isset($aResponse[$key])) {
                    unset($aResponse[$key]);
                }
            }
        }
        return $aResponse;
    }
    
    /**
     * @param $cart_item
     *
     * @return array|mixed|void
     */
    protected function getCartItemData($cart_item)
    {
        $item_data = [];
        // Variation values are shown only if they are not found in the title as of 3.0.
        // This is because variation titles display the attributes.
        if ($cart_item['data']->is_type('variation') && is_array($cart_item['variation'])) {
            foreach ($cart_item['variation'] as $name => $value) {
                $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));
                
                if (taxonomy_exists($taxonomy)) {
                    // If this is a term slug, get the term's nice name.
                    $term = get_term_by('slug', $value, $taxonomy);
                    if (!is_wp_error($term) && $term && $term->name) {
                        $value = $term->name;
                    }
                    $label = wc_attribute_label($taxonomy);
                } else {
                    // If this is a custom option slug, get the options name.
                    $value = apply_filters('woocommerce_variation_option_name', $value, null, $taxonomy,
                        $cart_item['data']);
                    $label = wc_attribute_label(str_replace('attribute_', '', $name), $cart_item['data']);
                }
                
                // Check the nicename against the title.
                if ('' === $value || wc_is_attribute_in_product_name($value, $cart_item['data']->get_name())) {
                    continue;
                }
                
                $item_data[] = [
                    'key'   => $label,
                    'value' => $value,
                ];
            }
        }
        
        // Filter item data to allow 3rd parties to add more to the array.
        $item_data = apply_filters('woocommerce_get_item_data', $item_data, $cart_item);
        
        // Format item data ready to display.
        foreach ($item_data as $key => $data) {
            // Set hidden to true to not display meta on cart.
            if (!empty($data['hidden'])) {
                unset($item_data[$key]);
                continue;
            }
            $item_data[$key]['key']     = !empty($data['key']) ? $data['key'] : $data['name'];
            $item_data[$key]['display'] = !empty($data['display']) ? $data['display'] : $data['value'];
        }
        
        // Output flat or in list format.
        if (count($item_data) > 0) {
            return $item_data;
        }
        
        return [];
    }
    
    /**
     * @param $product
     * @param $aCartData
     *
     * @return array
     */
    public function productCartItemSkeleton($product, $aCartData)
    {
        $productID     = $product->get_ID();
        $aCartItem     = WC()->cart->get_cart_item($aCartData['key']);
        $featuredImgID =
            isset($aCartItem['data']->variation_id) && has_post_thumbnail($aCartItem['data']->variation_id) ?
                $aCartItem['data']->variation_id :
                $productID;
        
        $aFeaturedImg = $this->getFeaturedImg($featuredImgID);
        $isVariation  = isset($aCartItem['data']) && $aCartItem['data']->is_type('variation');
        
        return [
            'id'           => abs($productID),
            'cartKey'      => $aCartData['key'],
            'name'         => get_the_title($productID),
            'postLink'     => get_permalink($productID),
            'quantity'     => $aCartData['quantity'],
            'price'        => floatval($product->get_price()),
            'priceHTML'    => $product->get_price_html(),
            'oFeaturedImg' => $aFeaturedImg,
            'aVariations'  => $isVariation ? $this->getCartItemData($aCartItem) : []
        ];
    }
    
    /**
     * @param $userID
     *
     * @return array|mixed
     */
    protected function getMyCartSkeleton($userID)
    {
        global $current_user;
        if (empty($current_user) || !is_wp_error($current_user) || empty($current_user->user_login)) {
            $current_user = get_user_by('ID', $userID);
        }
        
        $oSessionHandler = new \WC_Session_Handler();
        $session         = $oSessionHandler->get_session($userID);
        $aCartItems      = maybe_unserialize($session['cart']);
        if (empty($aCartItems)) {
            return $aCartItems;
        }
        
        $aProducts  = [];
        $totalPrice = 0;
        $totalItems = 0;
        foreach ($aCartItems as $item => $aValue) {
            $aProducts['items'][] = $this->productCartItemSkeleton(wc_get_product($aValue['product_id']),
                $aValue);
            $totalPrice           = $totalPrice + floatval($aValue['line_total']);
            $totalItems           = $totalItems + $aValue['quantity'];
        }
        
        $aProducts['totalPrice']     = $totalPrice;
        $aProducts['totalPriceHTML'] = wc_price($totalPrice);
        $aProducts['totalItems']     = abs($totalItems);
        
        return $aProducts;
    }
    
    /**
     * @param $productID
     *
     * @return array|bool
     */
    protected function isProduct($productID)
    {
        $this->oProduct = wc_get_product($productID);
        if (empty($this->oProduct)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('This product does not exist', 'wilcity-mobile-app')
            ];
        }
        
        return true;
    }
    
    /**
     * @param $fieldKey
     *
     * @return string
     */
    protected function getBillingField($fieldKey)
    {
        if (empty($this->userID)) {
            return '';
        }
        
        if (empty($this->oUserMeta)) {
            $this->oUserMeta = get_user_meta($this->userID);
        }
        
        if (isset($this->oUserMeta[$fieldKey]) && !empty($this->oUserMeta[$fieldKey][0])) {
            return $this->oUserMeta[$fieldKey][0];
        } else {
            if (strpos($fieldKey, 'billing_') !== false) {
                $newFieldKey = str_replace('billing_', '', $fieldKey);
                
                $wilcityFieldKey = 'wilcity_'.$newFieldKey;
                if (isset($this->oUserMeta[$wilcityFieldKey]) && !empty($this->oUserMeta[$wilcityFieldKey][0])) {
                    return $this->oUserMeta[$wilcityFieldKey][0];
                }
                
                if (isset($this->oUserMeta[$newFieldKey]) && !empty($this->oUserMeta[$newFieldKey][0])) {
                    return $this->oUserMeta[$newFieldKey][0];
                }
            }
        }
        
        return '';
    }
    
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getAllowedSupportedCountries($key = 'billing_country')
    {
        if (!empty($this->aAllowedCountries)) {
            return $this->aAllowedCountries;
        }
        
        $oCountries    = new \WC_Countries();
        $aRawCountries = $oCountries->get_allowed_countries();
        foreach ($aRawCountries as $countryCode => $country) {
            $this->aAllowedCountries[] = [
                'id'       => $countryCode,
                'name'     => $country,
                'selected' => $this->getBillingField($key) == $countryCode
            ];
        }
        
        return $this->aAllowedCountries;
    }
    
    /**
     * @return array
     */
    protected function getRequiredCheckoutFields()
    {
        if (empty($this->aRequiredCheckoutFields)) {
            $aBillingFields                = WC()->countries->get_default_address_fields();
            $this->aRequiredCheckoutFields = array_filter($aBillingFields, function ($aField) {
                return isset($aField['required']) && $aField['required'];
            });
        }
        
        return $this->aRequiredCheckoutFields;
    }
    
    /**
     * @param string $prefix
     *
     * @return mixed
     */
    protected function getCheckoutFormField($prefix = 'billing_')
    {
        $aFields = [
            [
                'type'           => 'text',
                'name'           => $prefix.'first_name',
                'label'          => 'firstName',
                'required'       => isset($this->aRequiredCheckoutFields['first_name']) ?
                    $this->aRequiredCheckoutFields['first_name']['required']
                    : false,
                'validationType' => 'firstName',
                'value'          => $this->getBillingField($prefix.'first_name')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'last_name',
                'label'          => 'lastName',
                'required'       => isset($this->aRequiredCheckoutFields['last_name']) ?
                    $this->aRequiredCheckoutFields['last_name']['required']
                    : false,
                'validationType' => 'lastName',
                'value'          => $this->getBillingField($prefix.'last_name')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'address_1',
                'label'          => 'address1',
                'required'       => isset($this->aRequiredCheckoutFields['address_1']) ?
                    $this->aRequiredCheckoutFields['address_1']['required']
                    : false,
                'validationType' => 'address',
                'value'          => $this->getBillingField($prefix.'address_1')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'address_2',
                'label'          => 'address2',
                'required'       => isset($this->aRequiredCheckoutFields['address_2']) ?
                    $this->aRequiredCheckoutFields['address_2']['required']
                    : false,
                'validationType' => 'address',
                'value'          => $this->getBillingField($prefix.'address_2')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'city',
                'label'          => 'city',
                'required'       => isset($this->aRequiredCheckoutFields['city']) ?
                    $this->aRequiredCheckoutFields['city']['required']
                    : false,
                'validationType' => 'city',
                'value'          => $this->getBillingField($prefix.'city')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'state',
                'label'          => 'state',
                'required'       => isset($this->aRequiredCheckoutFields['state']) ?
                    $this->aRequiredCheckoutFields['state']['required']
                    : false,
                'validationType' => 'State',
                'value'          => $this->getBillingField($prefix.'state')
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'postcode',
                'label'          => 'postcode',
                'required'       => isset($this->aRequiredCheckoutFields['postcode']) ?
                    $this->aRequiredCheckoutFields['postcode']['required']
                    : false,
                'validationType' => 'postcode',
                'value'          => $this->getBillingField($prefix.'postcode')
            ],
            [
                'type'           => 'select',
                'name'           => $prefix.'country',
                'label'          => 'country',
                'required'       => isset($this->aRequiredCheckoutFields['country']) ?
                    $this->aRequiredCheckoutFields['country']['required']
                    : false,
                'validationType' => 'country',
                'value'          => $this->getBillingField($prefix.'country'),
                'options'        => $this->getAllowedSupportedCountries()
            ],
            [
                'type'           => 'text',
                'name'           => $prefix.'email',
                'label'          => 'email',
                'required'       => isset($this->aRequiredCheckoutFields['email']) ?
                    $this->aRequiredCheckoutFields['email']['required']
                    : false,
                'validationType' => 'email',
                'value'          => $this->getBillingField($prefix.'email')
            ],
            [
                'type'           => 'phone',
                'name'           => $prefix.'phone',
                'label'          => 'phone',
                'required'       => isset($this->aRequiredCheckoutFields['phone']) ?
                    $this->aRequiredCheckoutFields['phone']['required']
                    : false,
                'validationType' => 'phone',
                'value'          => $this->getBillingField($prefix.'phone')
            ]
        ];
        
        if ($prefix === 'shipping_') {
            $shippingToDifferentAddress = $this->getBillingField($prefix.'to_different_address');
            $aFields                    = array_merge([
                [
                    'type'           => 'checkbox',
                    'name'           => $prefix.'to_different_address',
                    'label'          => esc_html__('Ship to different address?', 'wilcity-mobile-app'),
                    'required'       => false,
                    'validationType' => $prefix.'to_different_address',
                    'value'          => $shippingToDifferentAddress === 'true' || $shippingToDifferentAddress == 1 ?
                        true :
                        false
                ]
            ], $aFields);
        }
        
        return apply_filters('wilcity/wilcity-mobile-app/filter/checkout-form-fields', $aFields);
    }
    
    /**
     * @return array
     */
    public function getBillingFields()
    {
        $oToken = $this->verifyPermanentToken();
        
        if ($oToken) {
            $this->getUserID();
            $this->userID = $oToken->userID;
        }
        
        $this->getRequiredCheckoutFields();
        
        return [
            'status'  => 'success',
            'oFields' => $this->getCheckoutFormField()
        ];
    }
    
    /**
     * @return array
     */
    public function getShippingFields()
    {
        $oToken = $this->verifyPermanentToken();
        
        if ($oToken) {
            $this->getUserID();
            $this->userID = $oToken->userID;
        }
        
        $this->getRequiredCheckoutFields();
        
        return [
            'status'  => 'success',
            'oFields' => apply_filters('wilcity/wilcity-mobile-app/filter/shipping-fields', [
                'heading' => 'shippingAddress',
                'key'     => 'shipping',
                'fields'  => $this->getCheckoutFormField('shipping_')
            ])
        ];
    }
    
    protected function auth()
    {
        try {
            $this->oWooCommerce = new Client(
                home_url('/'),
                trim(\WilokeThemeOptions::getOptionDetail('app_woocommerce_consumer_key')),
                trim(\WilokeThemeOptions::getOptionDetail('app_woocommerce_consumer_secret')),
                [
                    'wp_api'            => true,
                    'version'           => 'wc/v3',
                    'verify_ssl'        => true,
                    'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
                ]
            );
            
            return true;
        } catch (\Exception $oException) {
            $this->errAuthMsg = esc_html__('Missing WooCommerce Authentication Configuration', 'wilcity-mobile-app');
            
            return false;
        }
        
    }
    
    public function testPayPal(\WP_REST_Request $oData)
    {
        $id    = $oData->get_param(('id'));
        $order = wc_get_order($id);
        $order->calculate_totals();
        
        $oPayPalGateway = new \WC_Gateway_Paypal();
        $aStatus        = $oPayPalGateway->process_payment($id, 148);
        
        if ($aStatus['result'] == 'success') {
            return $aStatus;
        } else {
            return ['status' => 'error'];
        }
    }
}
