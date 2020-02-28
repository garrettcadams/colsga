<?php
namespace WILCITY_SC;


use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Frontend\BusinessHours;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\MetaBoxes\Review;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\UserModel;

class SCHelpers {
	public static $isApp = false;
	private static $listingID = '';
	public static $post;

	public static function getCustomSCClass($sc){
	    if ( strpos($sc, 'wilcity_render') !== false ){
            preg_match('/(wilcity_render_)([^\s]*)/', $sc, $aMatches);
            if ( isset($aMatches[2]) ){
                return $aMatches[2];
            }
        }
        return '';
    }

    public static function renderLazyLoad($imgUrl, $aAtts=array(), $isFocusRender=false){
	    $aAtts = wp_parse_args(
		    $aAtts,
            array(
                'divClass' => '',
                'divInfo'  => array(),
                'imgClass' => '',
                'isNotRenderImg' => false,
                'alt'      => ''
            )
        );

	    if ( !$isFocusRender && \WilokeThemeOptions::isEnable('general_toggle_lazyload') ) :
	    ?>
            <div class="<?php echo esc_attr($aAtts['divClass'] . apply_filters('wilcity/filter/class-prefix', ' wilcity-lazyload')); ?>" data-src="<?php echo esc_url($imgUrl); ?>">
                <?php if ( !$aAtts['isNotRenderImg'] ) : ?>
                <img class="<?php echo esc_attr($aAtts['imgClass'] . apply_filters('wilcity/filter/class-prefix', ' wilcity-lazyload')); ?>" data-src="<?php echo esc_url($imgUrl); ?>" alt="<?php echo esc_attr($aAtts['alt']); ?>">
                <?php endif; ?>
            </div>
        <?php
        else:
        ?>
            <div class="<?php echo esc_attr($aAtts['divClass']); ?>" data-info='<?php echo json_encode($aAtts['divInfo']); ?>' style="background-image: url(<?php echo esc_url($imgUrl); ?>);">
	            <?php if ( !$aAtts['isNotRenderImg'] ) : ?>
                <img class="<?php echo esc_attr($aAtts['imgClass']); ?>" src="<?php echo esc_url($imgUrl); ?>" alt="<?php echo esc_attr($aAtts['alt']); ?>">
	            <?php endif; ?>
            </div>
        <?php
        endif;
    }

	public static function removeUnnecessaryParamOnApp($aData){
	    unset($aData['isApp']);
	    unset($aData['extra_class']);
	    unset($aData['bg_color']);
	    unset($aData['alignment']);
	    unset($aData['blur_mark']);
	    unset($aData['blur_mark_color']);

	    return $aData;
    }

    /*
     * This function helps to resolve Video Popup doesn't works if customer put &feature as query string
     */
    public static function cleanYoutubeUrl($url){

		if(!strpos($url, 'facebook.com')){
			$aParseVideo = explode('?v=', $url);
			$aSplitQueryString = explode('&', $aParseVideo[1]);

			return $aParseVideo[0] . '?v=' . $aSplitQueryString[0];
		}else {
			return $url;
		}
    }

	public static function renderSidebarRating($post){
		$averageRating = GetSettings::getAverageRating($post->ID, '', true, true);
	    ?>
        <div class="starRating_module__w77sS d-inline-block">
            <div class="starRating_point__12mp0"><?php echo GetSettings::getAverageRating($post->ID, true, true); ?></div>
            <div class="starRating_data__xAaEP" data-rating="<?php echo $averageRating; ?>"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
            </div>
            <div class="starRating_text__3_CO9"></div>
        </div>
        <?php
    }

	public static function renderSidebarAddress($post){
		$address = GetSettings::getAddress($post->ID, false);
		if ( !empty($address) ) :
			?>
            <div class="widget-listing2_excerpt__3wHpJ"><?php echo esc_html($address); ?></div>
			<?php
		endif;
	}

	public static function renderSidebarMetaData($post, $aAtts){
		\WILCITY_SC\SCHelpers::renderAds($post, 'LISTING_SIDEBAR');
		do_action('wilcity/wilcity-shortcodes/wilcity-sidebar-'.$aAtts['style'].'/start-meta-data', $post, $aAtts);
		if ( !empty($aAtts['aMetaData']) ){
			foreach ($aAtts['aMetaData'] as $meta) {
				switch ( $meta ) {
					case 'rating':
						\WILCITY_SC\SCHelpers::renderSidebarRating( $post );
						break;
					case 'address':
						\WILCITY_SC\SCHelpers::renderSidebarAddress( $post );
						break;
					default:
						do_action( 'wilcity/wilcity-shortcodes/wilcity-sidebar-'.$aAtts['style'].'/new-meta-data', $post, $meta, $aAtts );
						break;
				}
			};
		}
		do_action('wilcity/wilcity-shortcodes/wilcity-sidebar-'.$aAtts['style'].'/end-meta-data', $post, $aAtts);
    }

	public static function getViewAllUrl($atts){
	    global $wiloke;
	    $aQuery = array('type' => $atts['post_type']);
	    if ( isset($atts['order']) && !empty($atts['order']) ){
		    $aQuery['order'] = $atts['order'];
        }

		if ( isset($atts['orderby']) && !empty($atts['orderby']) ){
			$aQuery['order_by'] = $atts['orderby'];
		}

		if ( is_tax() ){
		    $oTax = get_queried_object();
			$aQuery[$oTax->taxonomy] = $oTax->slug;
            if ( $atts['post_type'] == 'depends_on_belongs_to' ){
				$aDirectoryTypes = GetSettings::getTermMeta($oTax->term_id, 'belongs_to');
				if ( empty($aDirectoryTypes) ) {
					$aQuery['type'] = GetSettings::getDefaultPostType(true);
				}else{
					$aQuery['type'] = $aDirectoryTypes[0];
				}
			}
		}else{
		    if ( isset($atts['listing_cats']) && !empty($atts['listing_cats']) ){
		        $aListingCatIDs = self::getAutoCompleteVal($atts['listing_cats']);
		        $aListingCatSlugs = array_map(function($catID){
                    $oTerm = get_term($catID, 'listing_cat');
                    if ( !empty($oTerm) && !empty($oTerm) ){
		                return $oTerm->slug;
			        }
                }, $aListingCatIDs);
		        if ( !empty($aListingCatSlugs) ){
			        $aQuery['listing_cat'] = implode(',', $aListingCatSlugs);
                }
            }

			if ( isset($atts['listing_tags']) && !empty($atts['listing_tags']) ){
				$aListingTagIDs = self::getAutoCompleteVal($atts['listing_tags']);
				$aListingTagSlugs = array_map(function($catID){
					$oTerm = get_term($catID, 'listing_tag');
					if ( !empty($oTerm) && !empty($oTerm) ){
						return $oTerm->slug;
					}
                }, $aListingTagIDs);
				if ( !empty($aListingTagIDs) ){
					$aQuery['listing_tag'] = implode(',', $aListingTagIDs);
				}
			}

			if ( isset($atts['listing_locations']) && !empty($atts['listing_locations']) ){
				$aListingLocations = self::getAutoCompleteVal($atts['listing_locations']);
				$aListingLocations = array_map(function($catID){
					$oTerm = get_term($catID, 'listing_location');
					if ( !empty($oTerm) && !empty($oTerm) ){
						return $oTerm->slug;
					}
				}, $aListingLocations);

				if ( !empty($aListingLocations) ){
					$aQuery['listing_location'] = implode(',', $aListingLocations);
				}
			}
        }

		$url = esc_url(add_query_arg(
			$aQuery,
			get_permalink($wiloke->aThemeOptions['search_page'])
		));
		return apply_filters('wilcity-shortcode/button-view-more/url', $url, get_permalink($wiloke->aThemeOptions['search_page']), $aQuery, $atts);
    }

    public static function renderTermsOnCard($post, $taxonomy){
	    $aTerms = wp_get_post_terms($post->ID, $taxonomy);
	    if ( empty($aTerms) || is_wp_error($aTerms) ){
	        return '';
        }
        ?>
        <div>
            <?php
            foreach ($aTerms as $oTerm):
                $aIcon = \WilokeHelpers::getTermOriginalIcon($oTerm);
                ?>
                <div class="icon-box-1_module__uyg5F mb-10">
                    <div class="icon-box-1_block1__bJ25J">
                        <a href="<?php echo esc_url(get_term_link($oTerm)); ?>">
	                        <?php
                            if ( $aIcon ) :
                                if ( $aIcon['type'] == 'icon' ) : ?>
                                    <?php if ( $aIcon['color'] ) : ?>
                                        <div class="icon-box-1_icon__3V5c0 rounded-circle" style="background-color: <?php echo esc_attr($aIcon['color']); ?>">
                                    <?php else: ?>
                                        <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <?php endif; ?>
                                            <i class="<?php echo esc_attr($aIcon['icon']); ?>"></i>
                                    </div>
                            <?php elseif  ( $aIcon['type']  == 'image' ) : ?>
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <img src="<?php echo esc_url($aIcon['url']); ?>" alt="<?php echo esc_attr($oTerm->name); ?>">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                            <div class="icon-box-1_text__3R39g"><?php echo esc_html($oTerm->name); ?></div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

	public static function getPostMetaData($post, $metaKey){
		switch ($metaKey){
			case 'date':
				return date_i18n(get_option('date_format'), strtotime($post->post_date));
				break;
			case 'category':
				$aCategories = get_the_category($post->ID);
				if ( empty($aCategories) ){
					return false;
				}
				$aCatNames = array_map(function($oTerm){
					return $oTerm->name;
				}, $aCategories);
				return implode(', ', $aCatNames);
				break;
			case 'comment':
				$comments = get_comments_number($post->ID);
				if ( empty($comments) ){
					return esc_html__('No Comment', 'wilcity-shortcodes');
				}else if ( $comments  == 1 ){
					return esc_html__('1 Comment', 'wilcity-shortcodes');
				}else if ( $comments  == 2 ){
					return esc_html__('2 Comments', 'wilcity-shortcodes');
				}else{
					return sprintf(esc_html__('%d Comments', 'wilcity-shortcodes'), $comments);
				}
				break;
			default:
				return apply_filters('wilcity/post/meta-data/item/'.$metaKey, '', $post);
				break;
		}
	}

	public static function decodeAtts($atts){
		return $atts ? json_decode(utf8_decode($atts), true) : array();
	}

	public static function parseImgSize($size){
		if ( empty($size) ){
			return '';
		}

		if ( strpos($size, 'wilcity_') !== false ){
			return $size;
		}else if ( strpos($size, ',') !== false ){
			return explode(',', $size);
		}else if ( strpos($size, 'x') !== false ){
		    return explode('x', $size);
		}

		return $size;
	}

	public static function prepareCustomSC($customSC, $postID='', $isApp=false){
		$customSC = str_replace(array('{{', '}}'), array('"', '"'), $customSC);
		if ( strpos($customSC, 'post_id') === false ){
			if ( !empty($postID) ){
				self::$listingID = $postID;
				$customSC = preg_replace_callback('/\[[^\s]*\s/', function($aMatched){
					return $aMatched[0] . ' post_id="'.self::$listingID.'" is_grid="yes" ';
				}, $customSC, 1);
			}
		}

		if ( $isApp ){
			$customSC = preg_replace_callback('/\[[^\s]*\s/', function($aMatched){
				return $aMatched[0] . ' is_mobile="yes" ';
			}, $customSC, 1);
		}

		return $customSC;
	}

	public static function getFeaturedImage($postID, $size=''){
		$thumbnailURL = get_the_post_thumbnail_url($postID, $size);

		if ( empty($thumbnailURL) ){
			global $wiloke;
			if ( isset($wiloke->aThemeOptions['listing_featured_image']['id']) ){
				$thumbnailURL = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_featured_image']['id'], $size);
			}

			if ( empty($thumbnailURL) ){
				$thumbnailURL = $wiloke->aThemeOptions['listing_featured_image']['url'];
			}
		}

		return $thumbnailURL;
	}

	public static function getPost(){
		if ( wp_doing_ajax() ){
			$post = get_post($_POST['postID']);
		}else{
			global $post;
		}
		return $post;
	}

	public static function parseTermQuery($atts){
        $aArgs = array(
            'taxonomy'      => $atts['taxonomy'],
            'number'        => isset($atts['number']) ? $atts['number'] : 6,
            'hide_empty'    => $atts['is_hide_empty'] == 'yes'
        );

        if ( $atts['is_show_parent_only'] == 'yes' ){
            $aArgs['parent'] = 0;
        }else if ( isset($atts['parent']) && !empty($atts['parent']) ){
            $aArgs['orderby'] = $atts['orderby'];
            $aArgs['order'] = $atts['order'];
            $aArgs['parent'] = $atts['parent'];
        }else{
            $aRawTermIDs = $atts[$atts['taxonomy'].'s'];

            if ( !empty($aRawTermIDs) ){
                $aRawTermIDs = is_array($aRawTermIDs) ? $aRawTermIDs : explode(',', $aRawTermIDs);
                $aTerms = array();

                foreach ($aRawTermIDs as $rawTerm){
                    $aParse = explode(':', $rawTerm);
                    $aTerms[] = $aParse[0];
                }

                $aArgs['include'] = $aTerms;
                $aArgs['orderby'] = 'include';
                $aArgs['number'] = count($aTerms);
            }else{
                $aArgs['orderby'] = $atts['orderby'];
                $aArgs['order'] = $atts['order'];
            }
        }

        return $aArgs;
	}

	public static function mergeIsAppRenderingAttr($aAtts){
		if ( isset($_POST['post_ID']) ){
			$pageTemplate = get_page_template_slug($_POST['post_ID']);
			if ( $pageTemplate == 'templates/mobile-app-homepage.php' ){
				self::$isApp = true;
			}
		}
		$aAtts['isApp'] = self::$isApp;

		if ( isset($aAtts['taxonomy']) && $aAtts['taxonomy'] == '_self' ){
			$aAtts['taxonomy'] = get_query_var( 'taxonomy' );
			$aAtts[$aAtts['taxonomy'].'s'] = get_queried_object_id();
			$aAtts['is_show_parent_only'] = 'no';
			$aAtts['parent'] = get_queried_object_id();
        }

		return $aAtts;
	}

	public static function isApp($aAtts){
		if ( isset($aAtts['isApp']) && $aAtts['isApp'] ){
			return true;
		}
		return false;
	}

	public static function renderPlanPrice($price, $aPriceSettings=array(), $productID=null){
	    if ( !empty($productID) ){
	        $oProduct = new \WC_Product($productID);
	        return '<span class="pricing_price__2vtrC color-primary">' . $oProduct->get_price_html() . '</span>';
        }

		$currencyPosition   = GetWilokeSubmission::getField('currency_position');
		$currencyCode       = GetWilokeSubmission::getField('currency_code');
		$currencySymbol     = GetWilokeSubmission::getSymbol($currencyCode);

		$price = apply_filters('wilcity/filter/pricing-price', $price, $aPriceSettings);
		ob_start();
		switch ($currencyPosition){
			case 'left':
				?>
                <span class="pricing_price__2vtrC color-primary"><sup class="pricing_currency__2bkpj"><?php echo esc_html($currencySymbol); ?></sup><span class="pricing_amount__34e-B"><?php echo esc_html($price); ?></span></span>
				<?php
				break;
			case 'right':
				?>
                <span class="pricing_price__2vtrC color-primary"><span class="pricing_amount__34e-B"><?php echo esc_html($price); ?></span><sup class="pricing_currency__2bkpj"><?php echo esc_html($currencySymbol); ?></sup></span>
				<?php
				break;
			case 'left_space':
				?>
                <span class="pricing_price__2vtrC color-primary"><sup class="pricing_currency__2bkpj"><?php echo esc_html($currencySymbol); ?></sup> <span class="pricing_amount__34e-B"><?php echo esc_html($price); ?></span></span>
				<?php
				break;
			case 'right_space':
				?>
                <span class="pricing_price__2vtrC color-primary"><span class="pricing_amount__34e-B"><?php echo esc_html($price); ?></span> <sup class="pricing_currency__2bkpj"><?php echo esc_html($currencySymbol); ?></sup></span>
				<?php
				break;
		}
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters('wilcity/wilcity-shortcodes/filter/listing-plan-price', $content, $price, $aPriceSettings);
	}

	public static function renderIconAndLink($link, $icon, $content, $aArgs=array()){
		$wrapperClass = isset($aArgs['wrapperClass']) ? 'icon-box-1_module__uyg5F one-text-ellipsis ' . $aArgs['wrapperClass'] : 'icon-box-1_module__uyg5F one-text-ellipsis';
		$style = isset($aArgs['style']) ? $aArgs['style'] : 'icon-box-1_block1__bJ25J';
		$iconWrapperClass = isset($aArgs['iconWrapperClass']) ? 'icon-box-1_icon__3V5c0 ' . $aArgs['iconWrapperClass'] : 'icon-box-1_icon__3V5c0';
		$rel = isset( $aArgs['rel'] ) ? 'rel="'. $aArgs['rel'] .'"' : '';
		?>
        <div class="<?php echo esc_attr($wrapperClass); ?>">
            <div class="<?php echo esc_attr($style); ?>">
				<?php if ( is_email($link) ): ?>
                    <a href="mailto:<?php echo esc_attr($link); ?>">
                <?php elseif(isset($aArgs['isPhone'])): ?>
                    <a href="tel:<?php echo esc_attr($link); ?>">
                <?php elseif(isset($aArgs['isGoogle'])):
                    $link = str_replace('/', '%2F', $link);
                ?>
                    <a target="_blank" href="<?php echo esc_url('https://www.google.com/maps/search/'.esc_attr($link)); ?>">
                <?php else: ?>
                    <a target="_blank" href="<?php echo esc_url($link); ?>" rel="nofollow">
                <?php endif;?>
                    <div class="<?php echo esc_attr($iconWrapperClass); ?>"><i class="<?php echo esc_attr($icon); ?>"></i></div>
                    <div class="icon-box-1_text__3R39g"><?php echo esc_html(stripslashes($content)); ?></div>
                </a>
            </div>
        </div>
		<?php
	}

	public static function parseAutoComplete($val){
		if ( empty($val) ){
			return false;
		}
		if ( is_array($val) ){
			$aTerms = array_filter($val, function($val){
				return !empty($val);
			});
			return $aTerms;
		}
		return explode(',', $val);
	}

	public static function getPostTypeKeys($isExcludeEvents=true, $isIncludeDefaultPostTypes = true){
		if ( !class_exists('WilokeListingTools\Framework\Helpers\General') ){
			return array('listing'=>'listing');
		}
		$aRawPostTypes  = General::getPostTypeKeys($isIncludeDefaultPostTypes, $isExcludeEvents);

		$aPostTypes  = array();
		foreach ($aRawPostTypes as $postType){
			$aPostTypes[$postType] = $postType;
		}

		return $aPostTypes;
	}

	public static function getPostTypeOptions(){
	    $aPostTypes = self::getPostTypeKeys();
		return array_merge(array('depends_on_belongs_to'=>'Using Belongs To Setting'), $aPostTypes);
	}

	public static function getAutoCompleteVal($val){
		if ( empty($val) ){
			return false;
		}
		$aParse = self::parseAutoComplete($val);

		if ( !$aParse ){
			return false;
		}

		$aValues = array();
		foreach ($aParse as $val){
			$aInfo = explode(':', $val);
			$aValues[] = $aInfo[0];
		}
		return $aValues;
	}

	public static function parseArgs($atts){
		$aArgs = array(
			'post_type'         => $atts['post_type'],
			'post_status'       => 'publish'
		);

		$aTaxQuery = array();

		if ( is_tax() ){
		    $termID = get_queried_object_id();
			$aTaxQuery[] = array(
				'taxonomy' => get_query_var('taxonomy'),
				'field'    => 'term_id',
				'terms'    => array($termID)
			);
			if ( $aArgs['post_type'] == 'depends_on_belongs_to' ){
			    $aDirectoryTypes = GetSettings::getTermMeta($termID, 'belongs_to');
			    if ( empty($aDirectoryTypes) ) {
				    $aArgs['post_type'] = GetSettings::getDefaultPostType(true);
			    }else{
				    $aArgs['post_type'] = $aDirectoryTypes;
			    }
			}
        }else{
			if ( isset($atts['listing_locations']) && !empty($atts['listing_locations']) ){
				$aTaxQuery[] = array(
					'taxonomy' => 'listing_location',
					'field'    => 'term_id',
					'terms'    => self::getAutoCompleteVal($atts['listing_locations'])
				);
			}

			if ( isset($atts['listing_cats']) && !empty($atts['listing_cats']) ){
				$aTaxQuery[] = array(
					'taxonomy' => 'listing_cat',
					'field'    => 'term_id',
					'terms'    => self::getAutoCompleteVal($atts['listing_cats'])
				);
			}

			if ( isset($atts['listing_tags']) && !empty($atts['listing_tags']) ){
				$aTaxQuery[] = array(
					'taxonomy' => 'listing_tag',
					'field'    => 'term_id',
					'terms'    => self::getAutoCompleteVal($atts['listing_tags'])
				);
			}

			if ( isset($atts['custom_taxonomy_key']) && !empty($atts['custom_taxonomy_key']) ){
			    $customTaxIds = $atts['custom_taxonomies_id'];
			    if ( !empty($customTaxIds) ){
				    $aParseCustomTaxIDs = explode(',', $customTaxIds);
				    foreach ($aParseCustomTaxIDs as $key => $val){
					    $aParseCustomTaxIDs[$key] = trim($val);
				    }

				    $aTaxQuery[] = array(
					    'taxonomy' => $atts['custom_taxonomy_key'],
					    'field'    => 'term_id',
					    'terms'    => $aParseCustomTaxIDs
				    );
                }
			}
        }

		if ( isset($atts['listing_ids']) && !empty($atts['listing_ids']) ){
			$aArgs['post__in'] = self::getAutoCompleteVal($atts['listing_ids']);
			$aArgs['posts_per_page'] = count($aArgs['post__in']);
		}else{
			$aArgs['posts_per_page'] = isset($atts['posts_per_page']) ? $atts['posts_per_page'] : $atts['maximum_posts'];
		}

		if ( !empty($aTaxQuery) ){
			$aArgs['tax_query'] = array($aTaxQuery);
		}

		if ( count($aTaxQuery) > 1 ){
			$aArgs['tax_query']['relation'] = apply_filters('wilcity/wilcity-shortcodes/query/taxonomy-relationship', 'AND');
		}

		switch ($atts['orderby']){
			case 'best_rated':
				$aArgs['orderby']   = 'meta_value_num post_date';
				$aArgs['meta_key']  = 'wilcity_average_reviews';
				$aArgs['order']     = 'DESC';
				break;
			case 'best_viewed':
				$aArgs['orderby']   = 'meta_value_num post_date';
				$aArgs['meta_key']  = 'wilcity_count_viewed';
				$aArgs['order']     = 'DESC';
				break;
			case 'best_shared':
				$aArgs['orderby']   = 'meta_value_num post_date';
				$aArgs['meta_key']  = 'wilcity_count_shared';
				$aArgs['order']     = 'DESC';
				break;
			case 'premium_listings':
				$aArgs['order']     = 'DESC';
				$aArgs['orderby']   = 'rand post_date';
				if ( $atts['TYPE'] == 'LISTINGS_SLIDER' ){
					$aMetaKey = GetSettings::getPromotionKeyByPosition('listing_slider_sc', true);
				}else{
					$aMetaKey = GetSettings::getPromotionKeyByPosition('listing_grid_sc', true);
				}

				if( !empty($aMetaKey) ) {
					$aArgs['meta_key']  = $aMetaKey[0];
				}

				break;
			case 'open_now':
				$aArgs['open_now'] = 'yes';
				break;
			default:
				$aArgs['orderby'] = $atts['orderby'];
				break;
		}

		if ( isset($atts['orderby']) && isset($atts['order']) ){
			$aArgs['order'] = $atts['order'];
		}else  if ( isset($atts['orderby']) && in_array($atts['orderby'], array('upcoming_event', 'happening_event')) ){
			$aArgs['order'] = 'ASC';
		}

		return $aArgs;
	}

	public static function renderClaimedBadge($postID, $nothing=''){
        if ( SingleListing::isClaimedListing($postID, true) ){
	        ?>
            <span class="wil-verified-badge wil-verified"><?php esc_html_e('Verified', 'wilcity-shortcodes'); ?></span>
	        <?php
        }
	}

	public static function renderAds($post, $type='', $isReturn = false){
		$isAds = false;
		switch ($type){
			case 'LISTINGS_SLIDER':
				$promo_key = GetSettings::getPromotionKeyByPosition('listing_slider_sc', false);
				if( empty($promo_key) || !is_array($promo_key) ) {
					break;
				}
				$val = GetSettings::getPostMeta($post->ID, $promo_key[0]);
				if ( !empty($val) ){
					$isAds = true;
				}
				break;
			case 'GRID':
				$promo_key = GetSettings::getPromotionKeyByPosition('listing_grid_sc', false);
				if( empty($promo_key) || !is_array($promo_key) ) {
					break;
				}
				$val = GetSettings::getPostMeta($post->ID, $promo_key[0]);
				if ( !empty($val) ){
					$isAds = true;
				}
				break;
			case 'TOP_SEARCH':
				$promo_key = GetSettings::getPromotionKeyByPosition('top_of_search', false);
				if( empty($promo_key) || !is_array($promo_key) ) {
					break;
				}
				$val = GetSettings::getPostMeta($post->ID, $promo_key[0]);
				if ( !empty($val) ){
					$isAds = true;
				}
				break;
			case 'LISTING_SIDEBAR':
				$promo_key = GetSettings::getPromotionKeyByPosition('listing_sidebar', false);
				if( empty($promo_key) || !is_array($promo_key) ) {
					break;
				}
				$val = GetSettings::getPostMeta($post->ID, $promo_key[0]);
				if ( !empty($val) ){
					$isAds = true;
				}
				break;
		}

		if ($isReturn) {
		    return $isAds;
        }
		
		if ( $isAds ){
			?>
            <span class="wil-ads"><?php esc_html_e('Ads', 'wilcity-shortcodes'); ?></span>
			<?php
		}
	}

	public static function renderInterested($post, $aAtts=array(), $isReturn=false){
		$total = FavoriteStatistic::countFavorites($post->ID);
		if ( empty($total) ){
			return '';
		}

		$total = abs($total);
		if ( $isReturn ){
			return array(
				'type'    => 'interested',
				'value'   => sprintf( _n('%d person interested', '%d people interested', $total, 'wilcity-shortcodes'), $total ),
				'icon'    => 'la la-star'
			);
		}
		?>
        <li class="event_metaList__1bEBH text-ellipsis">
            <span><?php echo sprintf( _n('%d person interested', '%d people interested', $total, 'wilcity-shortcodes'), $total ); ?></span>
        </li>
		<?php
	}

	public static function renderEventStartsOn($post, $aAtts=array(), $isReturn=false){
		$aEventCalendarSettings = GetSettings::getEventSettings($post->ID);
		if ( $isReturn ){
			return array(
				'type'    => 'event_starts_on',
				'value'   =>  array(
					'day' =>  date_i18n(get_option('date_format'), strtotime($aEventCalendarSettings['startsOn'])),
					'hour' => Time::renderTimeFormat(strtotime($aEventCalendarSettings['startsOn']), $post->ID)
				),
				'icon'    => 'la la-clock-o'
			);
		}
		?>
        <span class="event_month__S8D_o color-primary"><?php echo date_i18n('M', strtotime($aEventCalendarSettings['startsOn'])); ?></span>
        <span class="event_date__2Z7TH"><?php echo date_i18n('d', strtotime($aEventCalendarSettings['startsOn'])); ?></span>
		<?php
	}

	public static function renderFavorite($post, $aAtts=array(), $isReturn=false){
		if ( self::isApp($aAtts) ){
			return UserModel::isMyFavorite($post->ID)  ? 'yes' : 'no';
		}
		if ( $post->post_type == 'event' ){
			$favoriteIconClass = UserModel::isMyFavorite($post->ID) ? 'la la-star color-primary' : 'la la-star-o';
		}else{
			$favoriteIconClass = UserModel::isMyFavorite($post->ID) ? 'la la-heart color-primary' : 'la la-heart-o';
		}

		?>
        <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-favorite')); ?>" data-post-id="<?php echo esc_attr($post->ID); ?>" href="#" data-tooltip="<?php esc_html_e('Save to my favorites', 'wilcity-shortcodes'); ?>" data-tooltip-placement="top"><i class="<?php echo esc_attr($favoriteIconClass); ?>"></i></a>
		<?php
	}

	public static function renderCardHeaderButtonAction($post){
	    $aHeader = GetSettings::getOptions(General::getSingleListingSettingKey('header_card', $post->post_type));
        $type = isset($aHeader['btnAction']) ? $aHeader['btnAction'] : 'total_views';
        switch ($type):
            case 'call_us':
                $phone = GetSettings::getListingPhone($post->ID);
                if ( empty($phone) ){
                    return '';
                }
            ?>
                <a class="utility-meta_module__mfOnV utility-meta_light__2EzdO utility-meta_border__3O9g6  mb-10 mr-5 wilcity-listing-card-header-phone" href="tel:<?php echo esc_attr($phone); ?>"><i class="la la-phone"></i><?php esc_html_e('Call us', 'wilcity-shortcodes'); ?></a>
            <?php
                break;
            case 'email_us':
                $email = GetSettings::getListingEmail($post->ID);
                if ( empty($email) ){
                    return '';
                }
            ?>
                <a class="utility-meta_module__mfOnV utility-meta_light__2EzdO utility-meta_border__3O9g6  mb-10 mr-5" href="mailto:<?php echo esc_attr($email); ?>"><i class="la la-envelope"></i><?php esc_html_e('Mail us', 'wilcity-shortcodes'); ?></a>
            <?php
                break;
            default:
				$totalViews = GetSettings::getListingTotalViews($post->ID);

                if ( empty($totalViews) ){
                    return '';
                }
                ?>
                <a class="utility-meta_module__mfOnV utility-meta_light__2EzdO utility-meta_border__3O9g6  mb-10 mr-5" href="<?php echo get_permalink($post->ID); ?>"><i class="la la-eye"></i><?php echo sprintf( _n( '%d View', '%d Views', $totalViews, 'wilcity-shortcodes' ), $totalViews ); ?></a>
                <?php
                break;
        endswitch;
	}

	public static function renderFavoriteStyle2($post, $aAtts=array(), $isReturn=false){
		if ( self::isApp($aAtts) ){
			return UserModel::isMyFavorite($post->ID)  ? 'yes' : 'no';
		}

        if ( $post->post_type == 'event' ){
			$favoriteIconClass = UserModel::isMyFavorite($post->ID) ? 'la la-star color-primary' : 'la la-star-o';
		}else{
			$favoriteIconClass = UserModel::isMyFavorite($post->ID) ? 'la la-heart color-primary' : 'la la-heart-o';
		}

		?>
        <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-favorite')); ?> utility-meta_module__mfOnV utility-meta_primary__2xTvX utility-meta_border__3O9g6  mb-10 mr-5" data-post-id="<?php echo esc_attr($post->ID); ?>" href="#" data-tooltip="<?php esc_html_e('Save to my favorites', 'wilcity-shortcodes'); ?>" data-tooltip-placement="top"><i class="<?php echo esc_attr($favoriteIconClass); ?>"></i></a>
		<?php
	}

	public static function renderGallery($post, $aAttts=array()){
		$aImagesID = GetSettings::getPostMeta($post->ID, 'gallery');
		if ( !empty($aImagesID) ) :
			$aImagesSrc = array();
			$gallery_size = apply_filters('wiloke-listing-tools/listing-card/gallery-size', 'large');
			foreach ($aImagesID as $id => $imgSrc){
				$largeImg = wp_get_attachment_image_url($id, $gallery_size);
				if ( !$largeImg ){
					$aImagesSrc[] = $imgSrc;
				}else{
					$aImagesSrc[] = $largeImg;
				}
			}

			if ( self::isApp($aAttts) ){
				return $aImagesSrc;
			}
			?>
            <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-preview-gallery')); ?>" href="#" data-tooltip="<?php esc_html_e('Gallery', 'wilcity-shortcodes'); ?>" data-tooltip-placement="top" data-gallery="<?php echo esc_attr(implode(',', $aImagesSrc)); ?>"><i class="la la-search-plus"></i></a>
		<?php endif;
		return '';
	}

	public static function renderFooterTaxonomy($post, $aAtts=array()){
	    $aFooterSettings = GetSettings::getOptions(General::getSingleListingSettingKey('footer_card', $post->post_type));
	    $taxonomy = isset($aFooterSettings['taxonomy']) ? $aFooterSettings['taxonomy'] : 'listing_cat';

		if ( is_tax($taxonomy) ){
			$aTerm = get_term_by( 'slug', get_query_var('term'), $taxonomy);
		}else{
			$aTerm = \WilokeHelpers::getTermByPostID($post->ID, $taxonomy);
		}
	    if ( $aTerm ){
			if ( self::isApp($aAtts) ){
				return array(
					'oTerm' => $aTerm,
					'oIcon' => \WilokeHelpers::getTermOriginalIcon($aTerm)
				);
			}
			echo '<div class="icon-box-1_block1__bJ25J">'.\WilokeHelpers::getTermIcon($aTerm, 'icon-box-1_icon__3V5c0 rounded-circle', true). '</div>';
		}
		return '';
	}

	public static function renderListingCat($post, $aAtts=array()){
		return self::renderFooterTaxonomy($post, 'listing_card');
	}

	public static function renderBusinessStatus($post, $aAtts=array(), $isGridItem=false){
		if ( BusinessHours::isEnableBusinessHour($post) ) :
			$aBusinessHours = BusinessHours::getCurrentBusinessHourStatus($post);

			if ( self::isApp($aAtts) ){
				return $aBusinessHours['text'];
			}

			if ( $isGridItem ){
				if ( $aBusinessHours['status'] == 'day_off' ){
					$aBusinessHours['class'] = ' color-quaternary';
				}
			}
			?>
            <div class="icon-box-1_block2__1y3h0 <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-listing-hours')); ?>"><span class="<?php echo esc_attr($aBusinessHours['class']); ?>"><?php echo esc_html($aBusinessHours['text']); ?></span></div>
		<?php endif;
		return '';
	}


	public static function renderTitle($post){
		?>
        <h2 class="listing_title__2920A text-ellipsis">
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a>
        </h2>
		<?php
	}

	public static function renderPhone($post, $aAtts=array(), $isReturn=false){
		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-phone';
		}

		$phone = GetSettings::getPostMeta($post->ID, 'phone');
		if ( self::isApp($aAtts) || $isReturn ){
			return array(
				'value' => $phone,
				'icon'  => $aAtts['icon'],
				'type'  => 'phone'
			);
		}

		if ( !empty($phone) ) :
			?>
            <a class="text-ellipsis phone-number" href="tel:<?php echo esc_attr($phone); ?>">
                <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo esc_html($phone); ?>
            </a>
		<?php endif;
	}

	public static function renderSingleSocialNetworks($post, $aAtts=array(), $isReturn=false){
        $aSocialNetworks = \WilokeSocialNetworks::getUsedSocialNetworks();
    }

	public static function renderSinglePrice($post, $aAtts=array(), $isReturn=false){
		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-money';
		}

		$price = GetSettings::getPostMeta($post->ID, 'single_price');
		if ( self::isApp($aAtts) || $isReturn ){
			return array(
				'value' => $price,
				'icon'  => $aAtts['icon'],
				'type'  => 'single_price'
			);
		}

		if ( !empty($price) ) :
			?>
            <a class="text-ellipsis single-price" href="<?php echo get_permalink($post->ID); ?>">
                <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo GetWilokeSubmission::renderPrice($price); ?>
            </a>
		<?php endif;
	}

	public static function renderWebsite($post, $aAtts=array(), $isWebsite=false){
		$website = GetSettings::getPostMeta($post->ID, 'website');
		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-link';
		}

		if ( self::isApp($aAtts) || $isWebsite){
			return array(
				'value' => $website,
				'icon'  => $aAtts['icon'],
				'type'  => 'website'
			);
		}

		if ( !empty($website) ) :
			?>
            <a class="text-ellipsis website" href="<?php echo esc_url($website); ?>" target="_blank">
                <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo esc_html($website); ?>
            </a>
		<?php endif;
	}

	public static function renderEmail($post, $aAtts=array(), $isReturn=false){
		$email = GetSettings::getPostMeta($post->ID, 'email');
		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-envelope';
		}

		if ( self::isApp($aAtts) || $isReturn){
			return array(
				'value' => $email,
				'icon'  => $aAtts['icon'],
				'type'  => 'email'
			);
		}

		if ( !empty($email) && is_email($email) ) :
			?>
            <a class="text-ellipsis mail-address" href="mailto:<?php echo esc_attr($email); ?>">
                <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo esc_html($email); ?>
            </a>
		<?php endif;
	}

	public static function scNotHasIcon($aContent) {
		foreach ($aContent as $aSc){
			if ( !isset($aSc['oIcon']) || !empty($aSc['oIcon']['icon']) ){
				return true;
			}
		}
		return false;
	}

	public static function renderCustomField($post, $aAtts=array(), $isReturn=false){
		if ( empty($aAtts['content']) ){
			return '';
		}
		$sc = self::prepareCustomSC($aAtts['content'], $post->ID, $isReturn);
		if ( $isReturn ){
			return do_shortcode($sc);
		}

		$parsedSc = do_shortcode($sc);

		if ( empty($parsedSc) ){
			return '';
		}

		if ( isJson($parsedSc) ){
			if ( strpos($aAtts['content'], 'select') !== false || strpos($aAtts['content'], 'checkbox') !== false ){
				$aParsedSc = json_decode($parsedSc, true);
				$aScHasIcon = array();
				$aScName  = array();

				foreach ($aParsedSc as $aSc){
					if ( !empty($aSc['oIcon']['icon']) ){
						$sc = do_shortcode("[wilcity_render_box_icon1 icon='".$aSc['oIcon']['icon']."' name='".$aSc['name']."' color='".$aSc['oIcon']['color']."']");
						if ( !empty($sc) ){
							$aScHasIcon[] = $sc;
						}
					}else{
						$aScName[] = $aSc['name'];
					}
				}

				if ( empty($aScHasIcon) ){
					if ( !empty($aScName) ) {
						$aScName = apply_filters('wilcity/wilcity-shortcodes/filter/listing-grid/aScName', $aScName, $aAtts, $post);
						?>
                        <a class="text-ellipsis custom-content"
                           href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                            <i class="<?php echo esc_attr( $aAtts['icon'] ); ?> color-primary"></i> <?php echo implode( ', ', $aScName ); ?>
                        </a>
						<?php
					}
				}else{
					$aScHasIcon = apply_filters('wilcity/wilcity-shortcodes/filter/listing-grid/aScHasIcon', $aScHasIcon, $aAtts, $post);
					echo implode("\r\n", $aScHasIcon);
				}
			}
		}else{
			if ( empty($parsedSc) ){
				return '';
			}

			$class = preg_replace_callback('/\s+/', function(){
				return '';
			}, $parsedSc);

			$class = strip_tags(strtolower(trim($class)));

			$checkLink = strpos(strval($parsedSc),"href");

			if ($checkLink): ?>
				<div class="text-ellipsis custom-content <?php echo esc_attr($class); ?>">
					<i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i> <?php echo $parsedSc; ?>
				</div>
			<?php else:?>
				<a class="text-ellipsis custom-content <?php echo esc_attr($class); ?>" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
					<i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i> <?php echo $parsedSc; ?>
				</a>
			<?php endif;
		}
	}

	public static function renderPriceRange($post, $aAtts=array(), $isReturn=false){
		$aPriceRange  = GetSettings::getPriceRange($post->ID);
		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-money';
		}

		$symbol = !empty($aPriceRange) ? GetWilokeSubmission::getSymbol($aPriceRange['currency']) : '';
		$symbol = apply_filters('wilcity/price-range/currencySymbol', $symbol);

		if ( self::isApp($aAtts) || $isReturn){
			if ( empty($aPriceRange) || ($aPriceRange['mode'] == 'nottosay') || ($aPriceRange['minimumPrice'] == $aPriceRange['maximumPrice']) ){
				return array(
					'value' => $aPriceRange,
					'icon'  => $aAtts['icon'],
					'type'  => 'price_range',
					'symbol'=> $symbol
				);
			}else{
				$aPriceRange['minimumPrice'] = GetWilokeSubmission::renderPrice($aPriceRange['minimumPrice'], '', false, $symbol);
				$aPriceRange['maximumPrice'] = GetWilokeSubmission::renderPrice($aPriceRange['maximumPrice'], '', false, $symbol);
				return array(
					'value' => $aPriceRange,
					'icon'  => $aAtts['icon'],
					'type'  => 'price_range',
					'symbol'=> $symbol
				);
			}
		}

		if ( empty($aPriceRange) || ($aPriceRange['mode'] == 'nottosay') || ($aPriceRange['minimumPrice'] == $aPriceRange['maximumPrice']) ){
			return '';
		}

		?>
        <a class="text-ellipsis price-range" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
            <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo GetWilokeSubmission::renderPrice($aPriceRange['minimumPrice'], '', false, $symbol) . ' - ' . GetWilokeSubmission::renderPrice($aPriceRange['maximumPrice'], '', false, $symbol); ?>
        </a>
		<?php
	}

	public static function renderInterestedPeople($post, $aAtts=array(), $isReturn=false){
		$favoriteIconClass = UserModel::isMyFavorite($post->ID) ? 'la la-star color-primary' : 'la la-star-o';
		if ( $isReturn ){
			$aAtts['icon'] = 'la la-user';
			return array(
				'type'  => 'text',
				'postID' => $post->ID,
				'value' => GetSettings::getEventHostedByName($post),
				'icon'  => $favoriteIconClass,
				'isFavorite' => UserModel::isMyFavorite($post->ID) ? 'yes' : 'no'
			);
		}
		?>
        <span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-favorite')); ?> event_interested__2RxI- is-event" data-tooltip="<?php esc_html_e('Interested', 'wilcity-shortcodes'); ?>" data-post-id="<?php echo esc_attr($post->ID); ?>" data-tooltip-placement="top"><i class="<?php echo esc_attr($favoriteIconClass); ?>"></i></span>
		<?php
	}

	public static function renderHostedBy($post, $aAtts = array(), $isReturn=false){
		if ( $isReturn ){
			$aAtts['icon'] = 'la la-user';
			return array(
				'type'  => 'text',
				'value' => array(
					'name' => GetSettings::getEventHostedByName($post),
					'url' => GetSettings::getEventHostedByUrl($post)
				),
				'icon'  => $aAtts['icon']
			);
		}

		$hostedByURL = GetSettings::getEventHostedByUrl($post);

		$target = GetSettings::getEventHostedByTarget($hostedByURL);
		?>
        <span class="event_by__23HUz">
            <?php esc_html_e('Hosted By', 'wilcity-shortcodes'); ?> <a href="<?php echo esc_url(GetSettings::getEventHostedByUrl($post)); ?>" target="<?php echo esc_url($target); ?>" class="color-dark-2"><?php echo GetSettings::getEventHostedByName($post); ?></a>
        </span>
		<?php
	}

	public static function renderTextType($post, $key, $aAtts){
		$val = GetSettings::getPostMeta($post->ID, $key);

		if ( !isset($aAtts['icon']) ){
			$aAtts['icon'] = 'la la-refresh';
		}

		if ( self::isApp($aAtts) ){
			return array(
				'type'  => 'text',
				'value' => $val,
				'icon'  => $aAtts['icon']
			);
		}

		if ( empty($val) ){
			return '';
		}

		?>
        <a class="text-ellipsis text-type" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
            <i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo esc_html($val); ?>
        </a>
		<?php
	}

	public static function renderSelectType($post, $aAtts=array()){

	}

	public static function renderAddress($post, $aAtts=array(), $isReturn = false){
		$aThemeOptions = \Wiloke::getThemeOptions();
		$aListingAddress = GetSettings::getListingMapInfo($post->ID);

		if ( !empty($aListingAddress) && !empty($aListingAddress['lat']) && ($aListingAddress['lat'] != $aListingAddress['lng']) ) :
			$mapPageUrl = add_query_arg(
				array(
					'title' => urlencode($post->post_title),
					'lat'   => $aListingAddress['lat'],
					'lng'   => $aListingAddress['lng'],
                    'type'  => $post->post_type
				),
				get_permalink($aThemeOptions['map_page'])
			);

			if ( !isset($aAtts['icon']) ){
				$aAtts['icon'] = 'la la-map-marker';
			}

			if ( self::isApp($aAtts) || $isReturn ){
				return array(
					'type'    => 'google_address',
					'value'   => array(
						'address' => stripslashes($aListingAddress['address']),
						'mapUrl'  => $mapPageUrl,
						'googleMapAddress' => 'https://www.google.com/maps/search/' . urlencode($aListingAddress['address'])
					),
					'icon'    => $aAtts['icon']
				);
			}

            if ( isset($aAtts['isSearchNearByMe']) && $aAtts['isSearchNearByMe'] ){
	            $aLatLng = array('lat'=>$aListingAddress['lat'], 'lng'=>$aListingAddress['lng']);
	            $wrapperClass = 'text-ellipsis google-address js-print-distance';
            }else{
                $wrapperClass = 'text-ellipsis google-address';
	            $aLatLng = '';
            }
			?>
            <a style="position: relative; padding-right: 61px" class="<?php echo esc_attr($wrapperClass); ?>" data-latlng="<?php echo esc_attr(json_encode($aLatLng)); ?>" href="<?php echo esc_url($mapPageUrl); ?>" data-tooltip="<?php echo esc_html(stripslashes($aListingAddress['address'])); ?>">
                <span><i class="<?php echo esc_attr($aAtts['icon']); ?> color-primary"></i><?php echo esc_html(stripslashes($aListingAddress['address'])); ?></span>
            </a>
			<?php
		endif;

		if ( self::isApp($aAtts) ){
			return '';
		}
	}

	public static function renderExcerpt($post, $aAtts=array(), $isReturn = false){
		$tagLine = GetSettings::getTagLine($post->ID);
		if ( (isset($aAtts['isApp']) && $aAtts['isApp']) || $isReturn ){
			return $tagLine;
		}
		if ( !empty($tagLine) ): ?>
            <div class="listing_tagline__1cOB3 text-ellipsis"><?php \Wiloke::ksesHTML($tagLine); ?></div>
		<?php endif;
	}

	public static function renderAverageReview($post, $aAtts=array(), $isReturn = false){
		if ( ReviewController::isEnableRating() ) :
			$averageReview = GetSettings::getPostMeta($post->ID, 'average_reviews');
			$mode = ReviewController::getMode($post->post_type);
			if ( self::isApp($aAtts) || $isReturn ){
				return array(
					'mode' => $mode,
					'average' => $averageReview
				);
			}
			if ( !empty($averageReview) ) :
				?>
                <div class="listing_rated__1y7qV">
                    <div class="rated-small_module__1vw2B rated-small_style-2__3lb7d">
                        <div class="rated-small_wrap__2Eetz" data-rated="<?php echo esc_html($averageReview); ?>" data-tenmode="<?php echo ReviewController::toTenMode($averageReview, $mode); ?>">
                            <div class="rated-small_overallRating__oFmKR"><?php echo esc_html(number_format($averageReview, 1)); ?></div>
                            <div class="rated-small_ratingWrap__3lzhB">
                                <div class="rated-small_maxRating__2D9mI"><?php echo ReviewController::getMode($post->post_type); ?></div>
                                <div class="rated-small_ratingOverview__2kCI_"><?php echo esc_html(ReviewMetaModel::getReviewQualityString($averageReview, $post->post_type)); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif;
		endif;
	}
}
