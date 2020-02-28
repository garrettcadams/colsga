<?php

namespace WilokeListingTools\Controllers;


trait PrintAddListingFields {
	public function printAddListingFields($post){
		?>
		<form v-cloak id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-addlisting-form')); ?>" type="POST" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
            <div v-for="oSection in oUsedSections" :id="sectionID(oSection.key)" class="content-box_module__333d9 content-box_lg__3v3a-">
				<header class="content-box_header__xPnGx clearfix">
					<div class="wil-float-left">
						<h4 class="content-box_title__1gBHS"><i :class="oSection.icon"></i>
							<span>{{oSection.heading}}</span>
						</h4>
					</div>
				</header>
				<div class="content-box_body__3tSRB">
					<div v-for="(oField, fieldKey) in oSection.fields" v-if="oField.isEnable!='no'" :class="{'group-required': (oSection.fields[fieldKey].isRequired && oSection.fields[fieldKey].isRequired == 'yes') || (oField.key && oSection.fields[oField.key] && oSection.fields[oField.key].isRequired && oSection.fields[oField.key].isRequired=='yes'), 'disable': groupDisabled(oSection)}">
                        <wil-group v-if="oSection.type=='group'" :a-fields="oField"></wil-group>
                        <wil-input v-else-if="oField.type=='text'" :settings="oField"></wil-input>
                        <wil-icon v-else-if="oField.type=='icon'" :settings="oField" :value="oField.value"></wil-icon>
                        <wil-checkbox-two v-else-if="oField.type=='checkbox2'" :settings="oField"></wil-checkbox-two>
                        <wil-radio v-else-if="oField.type=='radio'" :settings="oField"></wil-radio>
                        <wil-textarea v-else-if="oField.type=='textarea'" :settings="oField"></wil-textarea>
                        <wil-upload-img v-else-if="oField.type=='single_image'" :settings="{value: oField.value, isRequired:oField.isRequired, labelName: oField.label,isLinkTo:oField.isLinkTo, isMultiple: false, paramName:fieldKey, oPlanSettings: oPlanSettings}" :field="oField"></wil-upload-img>
                        <wil-upload-img v-else-if="oField.type=='gallery'" :settings="{value: oField.value, isRequired:oField.isRequired, labelName: oField.label, isMultiple: true, paramName:fieldKey, toggle: oPlanSettings.toggle_gallery, maximumImages: oPlanSettings.maximumGalleryImages}" :field="oField"></wil-upload-img>
                        <wil-select-two v-else-if="oField.type=='select2' || oField.type=='select'" :settings="oField" :target="fieldKey"></wil-select-two>
                        <wil-email v-else-if="oField.type=='email'" :settings="oField" :target="fieldKey"></wil-email>
                        <wil-url v-else-if="oField.type=='url'" :settings="oField" :target="fieldKey"></wil-url>
                        <wil-color-picker v-else-if="oField.type=='colorpicker'" :settings="oField" :target="fieldKey"></wil-color-picker>
                        <wil-social-networks v-else-if="oField.type=='social_networks'" :settings="oField" :target="fieldKey"></wil-social-networks>
                        <wil-video v-else-if="oField.type=='video'" :settings="oField" :toggle="oPlanSettings.toggle_videos" :maximum-videos="maximumVideos"></wil-video>
                        <wil-price-range v-else-if="oField.type=='price_range'" :settings="oField"></wil-price-range>
                        <wil-business-hours v-else-if="oField.type=='business_hours'" :settings="oField"></wil-business-hours>
                        <wil-map v-else-if="oField.type=='map'" :settings="oField" :is-showing-map="true"></wil-map>
                        <wil-date-time v-else-if="oField.type=='date_time'" :settings="oField"></wil-date-time>
                        <wil-date-picker v-else-if="oField.type=='date_picker'" :settings="oField"></wil-date-picker>
                        <wil-tags v-else-if="oField.type=='listing_tag'" :settings="oField"></wil-tags>
                        <wil-category v-else-if="oField.type=='category'" :settings="oField" target="listing_cat" :listing-type="listingType"></wil-category>
                        <wil-event-calendar v-else-if="oField.type=='event_calendar'" :settings="oField"></wil-event-calendar>
                        <wil-restaurant-menu v-else-if="oField.type=='restaurant_menu'" :settings="oField" :a-values="oSection.fields[fieldKey].value"></wil-restaurant-menu>
					</div>
				</div>
			</div>

            <ul v-cloak v-show="aErrors.length" class="list-none mt-20 mb-20" style="padding: 0;">
                <li v-for="errorMsg in aErrors" style="color: #d61313;" class="alert_content__1ntU3" v-html="errorMsg"></li>
            </ul>

			<button type="submit" class="wil-btn wil-btn--primary wil-btn--round wil-btn--lg wil-btn--block" :class="submitBtnClass" @click.prevent="handlePreview"><?php echo !\WilokeThemeOptions::isEnable('addlisting_skip_preview_step') ? esc_html__('Save &amp; Preview', 'wiloke-listing-tools') : esc_html__('Submit', 'wiloke-listing-tools'); ?> <div class="pill-loading_module__3LZ6v" v-show="isSubmitting"><div :class="pillLoadingClass"></div></div></button>
		</form>
		<?php
	}
}
