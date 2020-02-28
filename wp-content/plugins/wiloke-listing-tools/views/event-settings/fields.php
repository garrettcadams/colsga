<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="fieldsettings" class="ui bottom attached tab segment">
    <h2 class="wiloke-add-listing-fields__title"><?php esc_html_e('Design Add Event Fields', 'wiloke-design-addlisting'); ?></h2>

    <div class="ui info message">
        Warning: The Listing Address is required by Event Directory Type
    </div>

    <!-- drag -->
    <div id="wiloke-design-fields">
        <div class="drag">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <!-- drag__block -->
                    <div class="drag__block">
                        <h3 class="drag__title"><?php esc_html_e('Available Fields', 'wiloke-design-addlisting'); ?></h3>
                        <draggable v-model="oAvailableSections" class="dragArea drag__avai" :options="{group: {name: 'addListingFields'}}" @change="addedNewSectionInAvailableArea">

                            <div v-for="(oSection, index) in oAvailableSections" :key="index" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
                                <span class="dragArea__item-text">
                                    {{oSection.heading}} <small>{{sectionName(oSection)}}</small>
                                </span>
                            </div>
                        </draggable>

                    </div>
                    <!-- /drag__block -->
                </div>
                <div class="sixteen wide column">
                    <h3 class="drag__title"><?php esc_html_e('Used Fields', 'wiloke-design-addlisting'); ?></h3>
                    <form action="#" id="wiloke-design-listing-form wiloke-form-has-icon" class="ui form" @submit.prevent="saveValue">

                        <div v-if="successMsg!=''" class="print-msg wil-message success"><i class="la la-certificate"></i> {{successMsg}}</div>

                        <div class="drag__btn-wrap">
                            <div class="drag__btn-group right">
                                <button type="submit" class="ui green button"><i class="la la-save"></i> <?php esc_html_e('Save Changes', 'wiloke-design-addlisting'); ?></button>
                                <button class="ui button red" @click.prevent="resetDefault">Reset Defaults</button>
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
                                            {{oUsedSection.heading}} <small>({{oUsedSection.type}})</small>
                                            <input type="hidden" v-model='oUsedSection.type'>
                                        </span>
                                        <span v-if="!oUsedSection.isNotDeleteAble" class="dragArea__form-title--remove" @click.prevent="removeSection(index, oUsedSection)" title="<?php esc_html_e('Remove Section', 'wiloke-design-addlisting'); ?>">
                                            <i class="la la-times"></i>
                                        </span>
                                    </div>

                                    <div class="dragArea__form-content hidden">

                                        <div class="ui setting-field field">
                                            <label><?php esc_html_e('Section Icon', 'wiloke-listing-tools'); ?></label>
                                            <div class="wil-icon-wrap">
                                                <div class="wil-icon-box">
                                                    <i :class="oUsedSection.icon"></i>
                                                </div>
                                                <div class="ui right icon input loading">
                                                    <input type="text" v-model='oUsedSection.icon' class="wiloke-icon" v-on:update-icon="oUsedSection.icon=$event.target.value">
                                                    <i class="search icon"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ui setting-field field">
                                            <label><?php esc_html_e('Section Name', 'wiloke-listing-tools'); ?></label>
                                            <input type="text" v-model='oUsedSection.heading' :data-sectionid="index" :class="{'isCustomSection': oUsedSection.isCustomSection && oUsedSection.isCustomSection == 'yes'}" @keyup="changedSectionName">
                                        </div>

                                        <div v-if="oUsedSection.isCustomSection && oUsedSection.isCustomSection =='yes'" class="ui setting-field field">
                                            <label><?php esc_html_e('Section Key', 'wiloke-listing-tools'); ?> <small>(It must be unique. Please do not use special character like ? & | and space in the section key too)</small></label>
                                            <input type="text" v-model='oUsedSection.key'>
                                        </div>

                                        <div v-if="oUsedSection.type == 'group'">
                                            <wiloke-new-input :std="oUsedSection.key" label="Group Key" :std="oUsedSection.key" v-model="oUsedSection.key"></wiloke-new-input>
                                            <wiloke-group :a-fields="oUsedSection.fields"></wiloke-group>
                                        </div>
                                        <div v-else-if="typeof oAllSections[oUsedSection.type] !== 'undefined'" class="settings ui segment" v-for="(oField, fieldKey) in oAllSections[oUsedSection.type].fields">
                                            <h3 class="ui heading">{{oField.heading}}</h3>
                                            <input type="hidden" v-model='oUsedSection["fields"][oField.key].type'>

                                            <div v-if="oField.toggle" class="ui toggle checkbox" :class="{'checked': oField.toggle=='enable'}">
                                                <input type="checkbox" v-model='oField.toggle' true-value="enable" false-value="disable">
                                                <label>Toggle</label>
                                            </div>
                                            <div v-for="(oSubField, subFieldKey) in oField.fields" v-show="!oField.toggle || oField.toggle == 'enable'">
                                                <wiloke-text :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></wiloke-text>
                                                <wiloke-checkbox :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></wiloke-checkbox>
                                                <wiloke-textarea :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></wiloke-textarea>
                                                <wiloke-hidden :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></wiloke-hidden>
                                                <wiloke-select :settings="oSubField" :parent-key="oField.key" :value="oUsedSection"></wiloke-select>
                                                <div v-if="oSubField.desc && oSubField.desc!==''" class="ui blue message" v-text="oSubField.desc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </draggable>

                        <button class="ui button green" @click.prevent="saveValue">Save Changes</button>
                        <button class="ui button red" @click.prevent="resetDefault">Reset Defaults</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>