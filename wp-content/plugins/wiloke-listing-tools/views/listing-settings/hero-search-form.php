<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="hero-search-form" class="ui bottom attached tab segment">
	<h2 class="wiloke-add-listing-fields__title">Design Sidebar Sections</h2>
    <div class="ui info message">You can add maximum 3 fields to the Hero Search Fields.</div>

    <div class="ui info message">
        <p>Refer to <a href="https://documentation.wilcity.com/knowledgebase/setting-up-hero-search-form/" target="_blank">Documentation</a> or Open a topic on <a href="https://support.wilcity.com/" target="_blank">wilcity.ticksy.com</a></p>
    </div>

    <div id="wiloke-design-hero-search-form-wrapper">
		<div class="drag">
			<div class="ui grid">
				<div class="sixteen wide column">
					<!-- drag__block -->
					<div class="drag__block">
						<h3 class="drag__title">Available Fields</h3>
						<draggable v-model="oAvailableFields" class="dragArea drag__avai" :options="{group: {name: 'addSidebarItems'}}" @change="addedNewSectionInAvailableArea">
							<div v-for="(oField, index) in oAvailableFields" :key="index" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__item-text">
                                    {{oField.label}} <small>({{oField.type}})</small>
                                </span>
							</div>
						</draggable>

					</div>
					<!-- /drag__block -->
				</div>
				<div class="sixteen wide column">
					<h3 class="drag__title">Used Fields</h3>
					<form action="#" id="wiloke-design-hero-search-form" class="ui form" @submit.prevent="saveChanges">

						<div v-show="successMsg!=''" class="ui positive message"><i class="la la-certificate"></i> {{successMsg}}</div>
						<div v-show="errorMsg!=''" class="ui negative message"><i class="la la-certificate"></i> {{errorMsg}}</div>

						<div class="drag__btn-wrap">
							<div class="drag__btn-group right">
								<button class="ui button violet" @click.prevent="resetDefaults">Reset Settings</button>
								<button type="submit" class="ui green button"><i class="la la-save"></i> Save Changes</button>
							</div>
						</div>

						<draggable class="dragArea drag__used" v-model="oUsedFields" @change="addedNewSectionInUsedArea" :options="{group:'addSidebarItems', handle: '.dragArea__form-title--icon'}">
							<div class="dragArea__block" v-for="(oUsedField, index) in oUsedFields" :key="index">
								<div :class="dragFormClass(oUsedField)">

									<div class="dragArea__form-title" @click.prevent="expandBlockSettings">
                                        <span class="dragArea__form-title--icon">
                                            <i class="la la-arrows-v"></i>
                                        </span>
										<span class="dragArea__form-title--text">
                                            {{oUsedField.label}} <small v-if="oUsedField.isCustomField && oUsedField.isCustomField=='yes'">(Custom Section)</small>
                                            <small>({{oUsedField.type}})</small>
                                            <input type="hidden" v-model='oUsedField.type'>
                                        </span>
										<span class="dragArea__form-title--remove" @click.prevent="removeSection(index, oUsedField)" title="Remove Section">
                                            <i class="la la-times"></i>
                                        </span>
									</div>

									<div class="dragArea__form-content hidden">
										<p v-if="oUsedField.desc"><i>{{oUsedField.desc}}</i></p>

										<div class="ui setting-field field">
											<label>Label</label>
											<input type="text" v-model='oUsedField.label'>
										</div>

										<div v-if="oUsedField.fieldType" class="ui setting-field field">
											<label>Type</label>
											<select class="ui dropdown" v-model="oUsedField.fieldType">
												<option v-for="type in oUsedField.types" :value="type">{{type}}</option>
											</select>
										</div>

                                        <div v-if="oUsedField.group=='term'">
                                            <div class="ui setting-field field">
                                                <label>Is Ajax?</label>
                                                <p><i>Highly recommend using this feature if you have more than 100 items</i></p>
                                                <select class="ui dropdown" v-model="oUsedField.isAjax">
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>

                                            <div v-show="oUsedField.isAjax=='no'" class="ui setting-field field">
                                                <label>Is Show Parent Only?</label>
                                                <select class="ui dropdown" v-model="oUsedField.isShowParentOnly">
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>

                                            <div class="ui setting-field field">
                                                <label>Order By</label>
                                                <select class="ui dropdown" v-model="oUsedField.orderBy">
                                                    <option value="count">Count</option>
                                                    <option value="id">ID</option>
                                                    <option value="slug">Slug</option>
                                                    <option value="name">Name</option>
                                                    <option value="meta_value_num">Taxonomy Position</option>
                                                </select>
                                            </div>
                                            <div class="ui setting-field field">
                                                <label>Order</label>
                                                <select class="ui dropdown" v-model="oUsedField.order">
                                                    <option value="DESC">DESC</option>
                                                    <option value="ASC">ASC</option>
                                                </select>
                                            </div>
                                            <div class="ui setting-field field">
                                                <label>Hide Empty</label>
                                                <select class="ui dropdown" v-model="oUsedField.isHideEmpty">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div v-if="oUsedField.key=='listing_cat'" class="ui setting-field field">
                                                <label>Is Multiple?</label>
                                                <select class="ui dropdown" v-model="oUsedField.isMultiple">
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>
                                        </div>
										<div v-else>
                                            <div v-if="oUsedField.key=='date_range'">
                                                <div class="ui setting-field field">
                                                    <label>From Label</label>
                                                    <input type="text" v-model="oUsedField.fromLabel">
                                                </div>

                                                <div class="ui setting-field field">
                                                    <label>To Label</label>
                                                    <input type="text" v-model="oUsedField.toLabel">
                                                </div>
                                            </div>
                                        </div>

										<div class="ui setting-field field">
											<label>Section Key <small>(It must be unique)</small></label>
											<input v-if="oUsedField.isCustomField && oUsedField.isCustomField == 'yes'" type="text" v-model='oUsedField.key'>
											<input v-else type="text" readonly v-model='oUsedField.key'>
										</div>

									</div>
								</div>
							</div>
						</draggable>

						<div v-show="successMsg!=''" class="ui positive message"><i class="la la-certificate"></i> {{successMsg}}</div>
						<div v-show="errorMsg!=''" class="ui negative message"><i class="la la-certificate"></i> {{errorMsg}}</div>
						<div class="mb-15">
							<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
							<button class="ui button violet" @click.prevent="resetDefaults">Reset Settings</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>