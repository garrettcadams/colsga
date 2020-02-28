<?php
use WilokeListingTools\Framework\Helpers\General;
?>

<div class="ui bottom attached active tab segment" data-tab="addlisting">
	<h2 class="wiloke-add-listing-fields__title"><?php esc_html_e('Design Add Listing Fields', 'wiloke-design-addlisting'); ?></h2>
    <div id="wiloke-design-fields">
        <div class="ui info message">
            <p>Refer to <a href="https://documentation.wilcity.com/" target="_blank">Documentation -> Add Listing</a> or Open a topic on <a href="https://wilcity.ticksy.com/" target="_blank">wilcity.ticksy.com</a></p>
        </div>

        <div v-show="errorMsg.length" class="ui negative message">
            <p>{{errorMsg}}</p>
        </div>

        <div v-show="successMsg.length" class="ui positive message">
            <p>{{successMsg}}</p>
        </div>

        <div class="drag">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <div class="drag__block">
                        <h3 class="drag__title">Available Fields</h3>
                        <draggable v-model="oAvailableSections" class="dragArea drag__avai" :options="{group: {name: 'addListingFields'}}" @change="addedNewSectionInAvailableArea">
                            <div v-for="(oSection, index) in oAvailableSections" :key="index" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
                                <span class="dragArea__item-text">
                                    <span v-html="oSection.heading"></span> <small>{{sectionName(oSection)}}</small>
                                </span>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div class="sixteen wide column">
                    <h3 class="drag__title">Used Fields</h3>
                    <form action="#" id="wiloke-design-listing-form" class="ui form wiloke-form-has-icon" @submit.prevent="saveValue">
                        <div v-show="errorMsg!=''" class="ui negative message">
                            <p>{{errorMsg}}</p>
                        </div>

                        <div v-show="successMsg!=''" class="ui positive message">
                            <p>{{successMsg}}</p>
                        </div>

                        <div class="drag__btn-wrap">
                            <div class="drag__btn-group right">
                                <button class="ui button red" @click.prevent="resetDefault">Reset Defaults</button>
                                <button type="submit" class="ui green button"><i class="la la-save"></i> <?php esc_html_e('Save Changes', 'wiloke-design-addlisting'); ?></button>
                            </div>
                        </div>

                        <draggable class="dragArea drag__used" v-model="usedSections" @change="addedNewSectionInUsedArea" :options="{group:'addListingFields', handle: '.dragArea__form-title--icon'}">
                            <div class="dragArea__block" v-for="(oUsedSection, index) in usedSections" :key="index">
                                <div class="dragArea__form ui form field-wrapper segment">
                                    <div class="dragArea__form-title" @click.prevent="expandBlockSettings">
                                        <span class="dragArea__form-title--icon">
                                            <i class="la la-arrows-v"></i>
                                        </span>
                                        <span class="dragArea__form-title--text">
                                            <span v-html="oUsedSection.heading"></span> <small>({{oUsedSection.type}})</small>
                                            <input type="hidden" v-model='oUsedSection.type'>
                                        </span>
                                        <span class="dragArea__form-title--remove" @click.prevent="removeSection(index, oUsedSection)" title="<?php esc_html_e('Remove Section', 'wiloke-design-addlisting'); ?>">
                                            <i class="la la-times"></i>
                                        </span>
                                    </div>

                                    <div class="dragArea__form-content hidden">
                                        <div class="ui message info" v-if="oUsedSection.desc" v-html="oUsedSection.desc"></div>
                                        <div class="ui setting-field field">
                                            <label><?php esc_html_e('Section Icon', 'wiloke-listing-tools'); ?></label>
                                            <div class="wil-icon-wrap">
                                                <div class="wil-icon-box">
                                                    <i :class="oUsedSection.icon"></i>
                                                </div>
                                                <div class="ui right icon input">
                                                  <input type="text" v-model='oUsedSection.icon' class="wiloke-icon" v-on:update-icon="oUsedSection.icon=$event.target.value">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ui setting-field field">
                                            <label><?php esc_html_e('Section Name', 'wiloke-listing-tools'); ?></label>
                                            <input type="text" v-model='oUsedSection.heading' :data-sectionid="index" :class="{'isCustomSection': oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'}" @keyup="changedSectionName">
                                        </div>

                                        <div v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'" class="ui setting-field field">
                                            <label><?php esc_html_e('Section Key', 'wiloke-listing-tools'); ?> <small>(It must be unique. Please do not use special character like ? & | and space in the section key too)</small></label>
                                            <input type="text" v-model='oUsedSection.key'>
                                        </div>

                                        <div v-if="oUsedSection.type == 'group'">
                                            <wiloke-new-input :std="oUsedSection.key" label="Group Key" :std="oUsedSection.key" v-model="oUsedSection.key"></wiloke-new-input>
                                            <wiloke-group :a-fields="oUsedSection.fields"></wiloke-group>
                                        </div>
                                        <div v-else-if="typeof oAllSections[oUsedSection.type].fields !== 'undefined'" class="settings ui segment" v-for="(oField, fieldKey) in oAllSections[oUsedSection.type].fields">
                                            <div v-if="typeof oUsedSection['fields'][oField.key] !== 'undefined'">
                                                <h3 class="ui heading">{{oField.heading}}</h3>
                                                <input type="hidden" v-model='oUsedSection["fields"][oField.key].type'>
                                                <div v-if="oField.toggle" class="ui toggle checkbox" :class="{checked: oUsedSection['fields'][oField.key].toggle=='enable'}">
                                                    <input type="checkbox" v-model='oUsedSection["fields"][oField.key].toggle' true-value="enable" false-value="disable">
                                                    <label>Toggle</label>
                                                </div>
                                                <div v-show="!oField.toggle || oField.toggle == 'enable'" :class="oField.wrapperClass">
                                                    <component v-for="(oSubField, subFieldKey) in oField.fields" v-bind:is="oSubField.component ? oSubField.component :'wiloke-'+oSubField.type" :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></component>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </form>

                    <div v-show="errorMsg!=''" class="ui negative message">
                        <p>{{errorMsg}}</p>
                    </div>

                    <div v-show="successMsg!=''" class="ui positive message">
                        <p>{{successMsg}}</p>
                    </div>

                    <div class="mb-15">
                        <button class="ui button green" @click.prevent="saveValue">Save Changes</button>
                        <button class="ui button red" @click.prevent="resetDefault">Reset Defaults</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), 'addlisting'); ?>
</div>


