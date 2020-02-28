<?php

namespace WilokeListingTools\Framework\Helpers;


class HTML {
    private static function mergeClass($aAtts){
	    $aAtts['wrapperClass'] = !empty($aAtts['marginClass']) ? $aAtts['wrapperClass'] . ' ' . $aAtts['marginClass'] : $aAtts['wrapperClass'];
	    return $aAtts;
    }

	public static function renderCheckboxField($aAtts){
		$aAtts = wp_parse_args(
			$aAtts,
			array(
				'wrapperClass' => 'checkbox_module__1K5IS mb-15 d-inline-block js-checkbox',
				'inputClass'   => 'field_field__3U_Rt',
				'label'        => '',
				'marginClass'  => '',
				'name'         => '',
				'value'        => 1,
				'type'         => ''
			)
		);

		$aAtts = self::mergeClass($aAtts);

		?>
        <div class="<?php echo esc_attr($aAtts['wrapperClass']); ?>">
            <label class="checkbox_label__3cO9k">
                <input class="checkbox_inputcheck__1_X9Z" type="checkbox" name="<?php echo esc_attr($aAtts['name']); ?>" value="<?php echo esc_attr($aAtts['value']); ?>" />
                <span class="checkbox_icon__28tFk bg-color-primary--checked-after bd-color-primary--checked">
                    <i class="la la-check"></i>
                    <span class="checkbox-iconBg"></span>
                </span>
                <?php if ( !in_array($aAtts['name'], array('isAgreeToTermsAndConditionals', 'isAgreeToPrivacyPolicy')) ) : ?>
                    <span class="checkbox_text__3Go1u text-ellipsis"><?php \Wiloke::ksesHTML($aAtts['label']); ?><span class="checkbox-border"></span></span>
                <?php endif; ?>
            </label>
	        <?php if ( in_array($aAtts['name'], array('isAgreeToTermsAndConditionals', 'isAgreeToPrivacyPolicy')) ) : ?>
                <span class="checkbox_text__3Go1u text-ellipsis"><?php \Wiloke::ksesHTML($aAtts['label']); ?><span class="checkbox-border"></span></span>
	        <?php endif; ?>
        </div><!-- End / checkbox_module__1K5IS -->
		<?php
	}

    public static function renderHiddenField($aAtts){
	    $aAtts = wp_parse_args(
		    $aAtts,
		    array(
			    'name'         => '',
			    'value'        => ''
		    )
	    );
        ?>
        <input type="hidden" name="<?php echo esc_attr($aAtts['name']); ?>" value="<?php echo esc_attr($aAtts['value']); ?>" />
        <?php
    }

    public static function renderInputField($aAtts){
	    $aAtts = wp_parse_args(
		    $aAtts,
	        array(
                'wrapperClass' => 'field_module__1H6kT field_style5__3OR3T mb-15 js-field',
                'innerClass'   => 'field_wrap__Gv92k',
                'inputClass'   => 'field_field__3U_Rt',
                'margin'       => '',
                'label'        => '',
                'name'         => '',
                'value'        => '',
                'type'         => 'text'
            )
        );
	    if ( !in_array($aAtts['type'], array('email', 'password', 'text')) ){
	        return '';
        }
	    $aAtts = self::mergeClass($aAtts);
        ?>
        <div class="<?php echo esc_attr($aAtts['wrapperClass']); ?>">
            <div class="<?php echo esc_attr($aAtts['innerClass']); ?>">
                <input name="<?php echo esc_attr($aAtts['name']); ?>" class="<?php echo esc_attr($aAtts['inputClass']); ?>" value="<?php echo esc_attr($aAtts['value']); ?>" type="<?php echo esc_attr($aAtts['type']); ?>">
                <span class="field_label__2eCP7 text-ellipsis"><?php echo esc_html($aAtts['label']); ?></span>
                <span class="bg-color-primary"></span>
            </div>
        </div>
        <?php
    }

    public static function renderUploadField($aAtts){
        $aAtts = wp_parse_args(
            $aAtts,
            array(
                'wrapperClass' => 'field_module__1H6kT field_style5__3OR3T mb-15 js-field',
                'innerClass'   => 'field_wrap__Gv92k',
                'inputClass'   => 'field_field__3U_Rt',
                'margin'       => '',
                'label'        => '',
                'name'         => '',
                'value'        => '',
                'type'         => 'upload'
            )
        );
        $aAtts = self::mergeClass($aAtts);
        ?>
        <div class="<?php echo esc_attr($aAtts['wrapperClass']); ?>">
            <div class="<?php echo esc_attr($aAtts['innerClass']); ?>">
                <input name="<?php echo esc_attr($aAtts['name']); ?>" class="<?php echo esc_attr($aAtts['inputClass']); ?>" value="<?php echo esc_attr($aAtts['value']); ?>" type="<?php echo esc_attr($aAtts['type']); ?>">
                <input name="<?php echo esc_attr($aAtts['name'].'_id'); ?>" class="img-id" value="<?php echo esc_attr
                ($aAtts['img_id']); ?>" type="hidden">
                <span class="field_label__2eCP7 text-ellipsis"><?php echo esc_html($aAtts['label']); ?></span>
                <span class="bg-color-primary"></span>
                <div class="field_right__2qM90 pos-a-center-right">
                    <a href="#" class="wil-btn wil-btn--primary wil-btn--round wil-btn--xxs js-upload-single-image">
                        <?php esc_html_e('Upload Image', 'wiloke-listing-tools'); ?>
                        <div class="pill-loading_module__3LZ6v" style="display: none;"><div class="pill-loading_loader__3LOnT"></div></div>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    public static function renderPurgeUploadField($aAtts){
        $aAtts = wp_parse_args(
            $aAtts,
            array(
                'wrapperClass' => 'field_module__1H6kT field_style5__3OR3T mb-15 js-field',
                'innerClass'   => 'field_wrap__Gv92k',
                'inputClass'   => 'field_field__3U_Rt',
                'margin'       => '',
                'label'        => '',
                'name'         => '',
                'value'        => '',
                'type'         => 'upload'
            )
        );
        $aAtts = self::mergeClass($aAtts);
        ?>
        <div class="<?php echo esc_attr($aAtts['wrapperClass']); ?>">
            <div class="<?php echo esc_attr($aAtts['innerClass']); ?>">
                <input name="<?php echo esc_attr($aAtts['name']); ?>" class="<?php echo esc_attr
                ($aAtts['inputClass']); ?>" value="<?php echo esc_attr($aAtts['value']); ?>" type="file">
                <span data-text="" class="input-filename"><span class="input-fileimg"></span></span>
                <span class="field_label__2eCP7 text-ellipsis"><?php echo esc_html($aAtts['label']); ?></span>
                <span class="bg-color-primary"></span>
                <div class="field_right__2qM90 pos-a-center-right">
                    <a href="#" class="wil-btn wil-btn--primary wil-btn--round wil-btn--xxs js-upload-single-image">
                        <?php esc_html_e('Upload Image', 'wiloke-listing-tools'); ?>
                        <div class="pill-loading_module__3LZ6v" style="display: none;"><div class="pill-loading_loader__3LOnT"></div></div>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    public static function renderSelectField($aAtts){
	    $aAtts = wp_parse_args(
		    $aAtts,
	        array(
                'wrapperClass' => 'field_module__1H6kT field_style5__3OR3T mb-15 js-field',
                'innerClass'   => 'field_wrap__Gv92k',
                'inputClass'   => 'field_field__3U_Rt',
                'margin'       => '',
                'label'        => '',
                'name'         => '',
                'value'        => '',
                'options'       => array()
            )
        );

        $aAtts = self::mergeClass($aAtts);

        ?>
        <div class="<?php echo esc_attr($aAtts['wrapperClass']); ?>">
            <div class="<?php echo esc_attr($aAtts['innerClass']); ?>">
                <select name="<?php echo esc_attr($aAtts['name']); ?>"  class="<?php echo esc_attr($aAtts['inputClass']); ?>">
                    <?php if( !empty($aAtts['options']) && is_array($aAtts['options']) ) : ?>
                        <?php foreach ($aAtts['options'] as $val => $name) : ?>
                            <option value="<?php echo esc_attr($val)?>" <?php selected( $aAtts['value'], $val); ?>><?php echo esc_html($name)?></option>
                        <?php endforeach; ?>
                    <?php endif;?>
                </select>

                <span class="field_label__2eCP7 text-ellipsis"><?php echo esc_html($aAtts['label']); ?></span>
                <span class="bg-color-primary"></span>
            </div>
        </div>
        <?php
    }

    public static function renderSiteLogo(){
        global $wiloke;
        $logo = '';
        $logo2x = '';
        if ( is_page() ){
            global $post;
            $logo = GetSettings::getPostMeta($post->ID, 'logo');
            $logo2x = GetSettings::getPostMeta($post->ID, 'retina_logo');
        }

        if ( empty($logo) ){
            $logo = isset($wiloke->aThemeOptions['general_logo']['url']) ? $wiloke->aThemeOptions['general_logo']['url'] : '';
        }

        if( empty($logo2x) ) {
            $logo2x = isset($wiloke->aThemeOptions['general_retina_logo']['url']) ? $wiloke->aThemeOptions['general_retina_logo']['url'] : '';
        }
        ?>
        <a class="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-site-logo')); ?>" href="<?php echo esc_url(home_url('/')); ?>">
	        <?php if ( empty($logo2x) ) : ?>
                <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>"/>
	        <?php else: ?>
                <img src="<?php echo esc_url($logo); ?>" srcset="<?php echo esc_url($logo2x) ?> 2x" alt="<?php bloginfo('name'); ?>"/>
	        <?php endif; ?>
        </a>
        <?php
    }

	public static function renderLink($class='', $name='', $link='', $icon='', $linkID='', $includePreloader=false){
	    $class = 'wil-btn ' . $class;
	    $linkID = empty($linkID) ? uniqid('link_id') : $linkID;
		?>
		<a id="<?php echo esc_attr($linkID); ?>" class="<?php echo esc_attr(trim($class)); ?>" href="<?php echo esc_url($link); ?>"><?php if(!empty($icon)):?><i class="<?php echo esc_attr($icon); ?>"></i><?php endif; ?><?php echo esc_html($name); ?> <?php if ($includePreloader): ?> <div class="pill-loading_module__3LZ6v hidden"><div class="pill-loading_loader__3LOnT"></div></div><?php endif; ?></a>
		<?php
	}

	public static function reStyleText($number){
		$input = number_format($number);
		$input_count = substr_count($input, ',');

		if($input_count != '0'){
			if($input_count == '1'){
				$timeK = 1000;
				$beforeK = substr($input, 0, -4).'k';
			} else if($input_count == '2'){
				$timeK = 1000000;
				$beforeK = substr($input, 0, -8).'mil';
			} else if($input_count == '3'){
				$timeK = 100000000000;
				$beforeK = substr($input, 0,  -12).'bil';
			} else {
				return $input;
			}

			$afterK = floatval(($number-($beforeK*$timeK))/100);

			return empty($afterK) ? $beforeK : $beforeK . $afterK;
		} else {
			return $input;
		}
    }

	public static function renderTable($aColumnTitles, $aColumnValues, $tblClass=''){
        $tblClass = 'table-module__table wil-table-responsive-lg ' . $tblClass;
        $tblClass = trim($tblClass);
        ?>
        <table class="<?php echo esc_attr($tblClass); ?>">
            <thead>
                <tr>
                    <?php foreach ($aColumnTitles as $aColunm) :  ?>
                        <th><?php echo esc_html($aColunm['name']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
	                <?php foreach ($aColumnValues as $key => $value) :  ?>
                        <td data-th="<?php echo esc_attr($aColumnTitles[$key]['name']); ?>" class="<?php echo esc_attr($aColumnTitles[$key]['class']); ?>"><?php echo esc_html($value); ?></td>
	                <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
        <?php
    }

    public static function renderPaymentButtons(){
	    $aGateways = GetWilokeSubmission::getAllGateways();
        $total = count($aGateways);

        $aPaymentData = apply_filters('wilcity/wiloke-listing-tools/renderPaymentButtons', array(
	        'paypal' => array(
		        'icon' => 'la la-cc-paypal',
		        'bg'   => 'bg-color-paypal',
		        'name' => esc_html__('PayPal', 'wiloke-listing-tools')
	        ),
	        'stripe' => array(
		        'icon' => 'la la-cc-stripe',
		        'bg'   => 'bg-color-stripe',
		        'name' => esc_html__('Stripe', 'wiloke-listing-tools')
	        ),
	        'banktransfer' => array(
		        'icon' => 'la la-money',
		        'bg'   => 'bg-color-banktransfer',
		        'name' => esc_html__('Direct Bank Transfer', 'wiloke-listing-tools')
	        )
        ));

        switch ($total){
            case 1:
                $itemClass = 'col-md-12 col-lg-12';
                break;
            case 2:
	            $itemClass = 'col-md-6 col-lg-6';
                break;
            default:
	            $itemClass = 'col-md-4 col-lg-4';
                break;
        }

        foreach ($aGateways as $gateway):
            ?>
            <div class="<?php echo esc_attr($itemClass); ?>">
                <!-- icon-box-2_module__AWd3Y wil-text-center bg-color-primary -->
                <div class="icon-box-2_module__AWd3Y wil-text-center <?php echo esc_attr($aPaymentData[$gateway]['bg']); ?>">
                    <a id="wilcity-proceed-with-<?php echo esc_attr($gateway); ?>" class="disable wilcity-gateway-box" href="#">
                        <div class="icon-box-2_icon__ZqobK">
                            <i class="<?php echo esc_attr($aPaymentData[$gateway]['icon']); ?>"></i>
                        </div>
                        <p class="icon-box-2_content__1J1Eb"><?php echo esc_html($aPaymentData[$gateway]['name']); ?></p>
                    </a>
                </div><!-- End / icon-box-2_module__AWd3Y wil-text-center bg-color-primary -->
            </div>
            <?php
        endforeach;
    }
}
