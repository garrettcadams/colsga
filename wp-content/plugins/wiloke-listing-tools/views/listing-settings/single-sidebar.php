<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="single-sidebar" class="ui bottom attached tab segment">
	<h2 class="wiloke-add-listing-fields__title">Design Sidebar Sections</h2>

    <div class="ui info message">
        <p>Refer to <a href="https://documentation.wilcity.com/knowledgebase/how-to-customize-single-directory-sidebar/" target="_blank">Documentation</a> or Open a topic on <a href="https://wilcity.ticksy.com/" target="_blank">wilcity.ticksy.com</a></p>
    </div>

	<div id="wiloke-design-sidebar" class="wiloke-form-has-icon">
		<div class="drag">
			<div class="ui grid">
				<div class="sixteen wide column">
					<!-- drag__block -->
					<div class="drag__block">
						<h3 class="drag__title">Available Fields</h3>
						<draggable v-model="oAvailableSections" class="dragArea drag__avai" :options="{group: {name: 'addSidebarItems'}}" @change="addedNewSectionInAvailableArea">
							<div v-for="(oSection, index) in oAvailableSections" :key="index" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__item-text">
                                    {{oSection.name}}
                                </span>
							</div>
						</draggable>

					</div>
					<!-- /drag__block -->
				</div>
				<div class="sixteen wide column">
					<h3 class="drag__title">Used Fields</h3>
					<form action="#" id="wiloke-design-single-sidebar-form" class="ui form" @submit.prevent="saveChanges">

						<div v-show="successMsg!=''" class="ui positive message"><i class="la la-certificate"></i> {{successMsg}}</div>
                        <div v-show="errorMsg!=''" class="ui negative message"><i class="la la-certificate"></i> {{errorMsg}}</div>

						<div class="drag__btn-wrap">
							<div class="drag__btn-group right">
                                <button class="ui button red" @click.prevent="resetDefaults">Reset Settings</button>
                                <button class="ui button violet" @click.prevent="addNewSection">Add New Section</button>
								<button type="submit" class="ui green button"><i class="la la-save"></i> Save Changes</button>
							</div>
						</div>

						<draggable class="dragArea drag__used" v-model="aUsedSections" @change="addedNewSectionInUsedArea" :options="{group:'addSidebarItems', handle: '.dragArea__form-title--icon'}">
							<div class="dragArea__block" v-for="(oUsedSection, index) in aUsedSections" :key="index">
								<div :class="dragFormClass(oUsedSection)">

									<div class="dragArea__form-title" @click.prevent="expandBlockSettings">
                                        <span class="dragArea__form-title--icon">
                                            <i class="la la-arrows-v"></i>
                                        </span>
										<span class="dragArea__form-title--text">
                                            {{oUsedSection.name}} <small v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection=='yes'">(Custom Section)</small>
                                            <input type="hidden" v-model='oUsedSection.type'>
                                        </span>
										<span v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'" class="dragArea__form-title--remove" @click.prevent="removeSection(index, oUsedSection)" title="Remove Section">
                                            <i class="la la-times"></i>
                                        </span>
									</div>

									<div class="dragArea__form-content hidden">
                                        <div class="ui setting-field field">
                                            <label>Icon</label>
                                            <div class="wil-icon-wrap">
                                                <div class="wil-icon-box">
                                                    <i :class="oUsedSection.icon"></i>
                                                </div>
                                                <div class="ui right icon input">
                                                    <input type="text" v-model='oUsedSection.icon' class="wiloke-icon" v-on:update-icon="oUsedSection.icon=$event.target.value">
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="oUsedSection.key == 'promotion'" class="ui setting-field field">
                                            <label>Promotion ID</label>
                                            <input type="text" v-model='oUsedSection.promotionID'>
                                        </div>

										<div class="ui setting-field field">
											<label>Name</label>
											<input type="text" v-model='oUsedSection.name' :data-sectionid="index" :class="{'isCustomSection': oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'}" @keyup="changedSectionName">
										</div>

                                        <div v-show="oUsedSection.key=='relatedListings'" class="field">
                                            <label>Get Related Listings</label>
                                            <select class="ui dropdown" v-model="oUsedSection.conditional">
                                                <option v-for="(name, type) in oRelatedBy" :value="type">{{name}}</option>
                                            </select>
                                        </div>

                                        <div v-show="oUsedSection.key=='relatedListings' && oUsedSection.conditional=='google_address'" class="ui setting-field field">
                                            <label>Radius (Showing up Related Listings within X KM) </label>
                                            <input type="text" v-model='oUsedSection.radius' :data-sectionid="index" :class="{'isCustomSection': oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'}" @keyup="changedSectionName">
                                            <p><i>Enter number only</i></p>
                                        </div>

                                        <div v-show="oUsedSection.key=='promotion' || oUsedSection.key=='relatedListings'" class="field">
                                            <label>Style</label>
                                            <select class="ui dropdown" v-model="oUsedSection.style">
                                                <option v-for="(name, style) in oStyles" :value="style">{{name}}</option>
                                            </select>
                                        </div>

                                        <div v-show="oUsedSection.key=='relatedListings'" class="field">
                                            <label>Order By</label>
                                            <select class="ui dropdown" v-model="oUsedSection.orderby">
                                                <option v-for="(name, orderby) in oOrderBy" :value="orderby">{{name}}</option>
                                            </select>
                                        </div>

                                        <div v-show="oUsedSection.key=='relatedListings' && oUsedSection.orderby=='menu_order'" class="field">
                                            <label>Order By Fallback</label>
                                            <select class="ui dropdown" v-model="oUsedSection.oOrderFallbackBy">
                                                <option v-for="(name, orderby) in oOrderFallbackBy" :value="orderby">{{name}}</option>
                                            </select>
                                        </div>

                                        <div v-show="oUsedSection.key=='relatedListings' && (oUsedSection.orderby != 'best_rated' && oUsedSection.orderby != 'best_viewed')" class="field">
                                            <label>Order</label>
                                            <select class="ui dropdown" v-model="oUsedSection.order">
                                                <option v-for="(name, order) in {DESC:'DESC', ASC: 'ASC'}" :value="order">{{name}}</option>
                                            </select>
                                        </div>

                                        <div v-show="oUsedSection.key=='promotion' || oUsedSection.key=='relatedListings'" class="field">
                                            <label>Maximum Listings Can Be Shown</label>
                                            <input type="text" v-model='oUsedSection.postsPerPage' :data-sectionid="index">
                                        </div>

                                        <div v-if="oUsedSection.key == 'taxonomy'" class="ui setting-field field">
                                            <label>Taxonomy Key</label>
                                            <input type="text" v-model='oUsedSection.taxonomy' :data-sectionid="index">
                                        </div>

										<div v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'" class="ui setting-field field">
											<label>Section Key <small>(It must be unique)</small></label>
											<input type="text" v-model='oUsedSection.key'>
										</div>

                                        <div v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'" class="ui setting-field field">
                                            <label>Content</label>
                                            <textarea v-model='oUsedSection.content'></textarea>
                                        </div>

                                        <div v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'" class="message ui">
                                            <p>If you want to print the Custom Field that has been added on AddListing setting, the key should follow this structure: <i style="color:red">wilcity_single_sidebar_[fieldKey]</i>. Eg: wilcity_single_sidebar_my_select_field</p>
                                        </div>

                                        <div v-if="oUsedSection.isDefaultSidebar && oUsedSection.isDefaultSidebar == 'yes'" class="ui setting-field field">
                                            <label>Sidebar Key</label>
                                            <input type="text" v-model='oUsedSection.key'>
                                        </div>

                                        <div class="ui setting-field field">
                                            <label>Enable This Sidebar Item</label>
                                            <p v-if="oUsedSection.key!=='google_adsense' && oUsedSection.key!=='promotion'">The listing owner can disable it on his/her listing.</p>
                                            <select v-model="oUsedSection.status" class="ui dropdown">
                                                <option value="no">No</option>
                                                <option value="yes">Yes</option>
                                            </select>
                                        </div>

									</div>
								</div>
							</div>
						</draggable>

                        <div v-show="successMsg!=''" class="ui positive message"><i class="la la-certificate"></i> {{successMsg}}</div>
                        <div v-show="errorMsg!=''" class="ui negative message"><i class="la la-certificate"></i> {{errorMsg}}</div>
						<div class="mb-15">
							<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                            <button class="ui button violet" @click.prevent="addNewSection">Add New Section</button>
                            <button class="ui button red" @click.prevent="resetDefaults">Reset Settings</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>