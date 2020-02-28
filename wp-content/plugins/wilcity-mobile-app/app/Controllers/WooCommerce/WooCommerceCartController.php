<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class WooCommerceCartController extends WooCommerceController
{
    private $aVariations;
    private $aAttributes;
    private $quantity;
    private $variationID = 0;
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/my-cart', [
                'methods'  => 'GET',
                'callback' => [$this, 'getMyCart']
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/add-to-cart', [
                'methods'  => 'POST',
                'callback' => [$this, 'addProductsToCart']
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/remove-cart', [
                'methods'  => 'POST',
                'callback' => [$this, 'removeCartItem']
            ]);
        });
    }
    
    public function removeCartItem(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $cartKey = $oRequest->get_param('key');
        WC()->cart->remove_cart_item($cartKey);
        
        return [
            'status' => 'success',
            'msg'    => wilcityAppGetLanguageFiles('itemHasBeenRemovedFromCart')
        ];
    }
    
    private function addToCart()
    {
        global $current_user;
        $current_user    = new \WP_User($this->userID);
        $oSessionHandler = new \WC_Session_Handler();
        $oSessionHandler->init_session_cookie();
        try {
            // Adding product to cart through home page or shop page
            $aSessions  = $oSessionHandler->get_session($this->userID);
            $aCartItems = isset($aSessions['cart']) ? maybe_unserialize($aSessions['cart']) : [];
            
            $status = false;
            if (empty($aCartItems)) {
                $status = WC()->cart->add_to_cart($this->productID, $this->quantity, $this->variationID,
                    $this->aVariations);
            } else {
                $aProductsCart = array_filter($aCartItems, function ($aItem) {
                    $aItem['product_id'] = $this->productID;
                });
                
                if (!empty($aProductsCart)) {
                    if ($this->quantity === 1) {
                        $status = WC()->cart->add_to_cart($this->productID, $this->quantity, $this->variationID,
                            $this->aVariations);
                    } else {
                        foreach ($aProductsCart as $aProductCart) {
                            $status = WC()->cart->set_quantity($aProductCart['key'], $this->quantity);
                        }
                    }
                } else {
                    $status = WC()->cart->add_to_cart($this->productID, $this->quantity, $this->variationID,
                        $this->aVariations);
                }
            }
            
            if ($status === false) {
                return [
                    'status' => 'error',
                    'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToCart')
                ];
            }
            
            return [
                'status' => 'success',
                'msg'    => wilcityAppGetLanguageFiles('itemHasBeenRemovedFromCart')
            ];
            
        } catch (\Exception $oException) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('couldNotAddProductToCart')
            ];
        }
    }
    
    /**
     * @return array
     */
    private function addToCardHandleVariation()
    {
        $variationId        = absint(wp_unslash($this->variationID));
        $quantity           = wc_stock_amount(wp_unslash($this->quantity));
        $aMissingAttributes = [];
        $aVariations        = [];
        $aPostedAttributes  = [];
        foreach ($this->oProduct->get_attributes() as $aAttribute) {
            if (!$aAttribute['is_variation']) {
                continue;
            }
            $attributeKey = sanitize_title($aAttribute['name']);
            
            if (isset($this->aAttributes[$attributeKey])) {
                if ($aAttribute['is_taxonomy']) {
                    // Don't use wc_clean as it destroys sanitized characters.
                    $value = sanitize_title(wp_unslash($this->aAttributes[$attributeKey]));
                } else {
                    return [
                        'status' => 'error',
                        'msg'    => wilcityAppGetLanguageFiles('productAttributeMustATerm')
                    ];
                }
                $aPostedAttributes[$attributeKey] = $value;
            }
        }
        
        // If no variation ID is set, attempt to get a variation ID from posted attributes.
        if (empty($variationId)) {
            /**
             * $data_store \WC_Data_Store
             */
            $data_store  = \WC_Data_Store::load('product');
            $variationId = $data_store->find_matching_product_variation($this->oProduct, $aPostedAttributes);
        }
        
        // Do we have a variation ID?
        if (empty($variationId)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('mustChooseProductOptions')
            ];
        }
        
        // Check the data we have is valid.
        $aVariationData = wc_get_product_variation_attributes($variationId);
        
        foreach ($this->oProduct->get_attributes() as $aAttribute) {
            if (!$aAttribute['is_variation']) {
                continue;
            }
            
            // Get valid value from variation data.
            $attributeKey = sanitize_title($aAttribute['name']);
            $valid_value  = isset($aVariationData[$attributeKey]) ? $aVariationData[$attributeKey] : '';
            
            /**
             * If the attribute value was posted, check if it's valid.
             *
             * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
             */
            if (isset($aPostedAttributes[$attributeKey])) {
                $value = $aPostedAttributes[$attributeKey];
                
                // Allow if valid or show error.
                if ($valid_value === $value) {
                    $aVariations[$attributeKey] = $value;
                } elseif ('' === $valid_value && in_array($value, $aAttribute->get_slugs(), true)) {
                    // If valid values are empty, this is an 'any' variation so get all possible values.
                    $aVariations[$attributeKey] = $value;
                } else {
                    /* translators: %s: Attribute name. */
                    return [
                        'status' => 'error',
                        'msg'    => sprintf(
                            wilcityAppGetLanguageFiles('invalidProductAttribute'), wc_attribute_label
                            ($aAttribute['name'])
                        )
                    ];
                }
            } elseif ('' === $valid_value) {
                $aMissingAttributes[] = wc_attribute_label($aAttribute['name']);
            }
        }
        
        if (!empty($aMissingAttributes)) {
            /* translators: %s: Attribute name. */
            return [
                'status' => 'error',
                'msg'    => sprintf(_n(wilcityAppGetLanguageFiles('requiredField'),
                    wilcityAppGetLanguageFiles('requiredFields'),
                    count($aMissingAttributes), 'woocommerce'), wc_format_list_of_items($aMissingAttributes))
            ];
        }
        
        $this->aVariations = $aVariations;
        $passedValidation  = apply_filters('woocommerce_add_to_cart_validation', true, $this->oProduct->get_id(),
            $quantity,
            $variationId, $this->aVariations);
        
        if ($passedValidation) {
            $aStatus = $this->addToCart();
            
            return $aStatus;
        }
        
        return [
            'status' => 'error',
            'msg'    => wilcityAppGetLanguageFiles('invalidVariation')
        ];
    }
    
    public function addProductsToCart(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->userID    = $oToken->userID;
        $this->productID = $oRequest->get_param('id');
        $this->quantity  = $oRequest->get_param('quantity');
        
        if (get_post_type($this->productID) !== 'product') {
            return [
                'status' => 'error',
                'msg'    => esc_html__('This product does not exists', 'wilcity-mobile-app')
            ];
        }
        
        $this->oProduct = wc_get_product($this->productID);
        
        switch ($this->oProduct->get_type()) {
            case 'variation':
            case 'variable':
                $this->variationID = $oRequest->get_param('variationID');
                if (empty($this->variationID)) {
                    return [
                        'status' => 'error',
                        'msg'    => wilcityAppGetLanguageFiles('variationIDRequired')
                    ];
                }
                
                $this->aAttributes = $oRequest->get_param('attributes');
                
                $aAttributes = [];
                if (is_string($this->aAttributes)) {
                    $aParse = explode('|', $this->aAttributes);
                    foreach ($aParse as $attribute) {
                        $aAttribute                  = explode(':', $attribute);
                        $aAttributes[$aAttribute[0]] = $aAttribute[1];
                    }
                    $this->aAttributes = $aAttributes;
                }
                $aStatus = $this->addToCardHandleVariation();
                break;
            case 'simple':
                $aStatus = $this->addToCart();
                break;
            default:
                $aStatus = [
                    'status' => 'error',
                    'msg'    => sprintf(
                        wilcityAppGetLanguageFiles('noSupportedProductType'),
                        $this->oProduct->get_type()
                    )
                ];
                break;
        }
        
        return $aStatus;
    }
    
    public function getMyCart()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        $aCartItems = $this->getMyCartSkeleton($oToken->userID);
        
        if (empty($aCartItems)) {
            return [
                'status'     => 'success',
                'msg'        => 'emptyCart',
                'oCartItems' => []
            ];
        }
        
        return [
            'status'     => 'success',
            'oCartItems' => $aCartItems
        ];
    }
}
