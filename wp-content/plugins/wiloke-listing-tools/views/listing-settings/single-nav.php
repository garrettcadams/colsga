<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="single-nav" class="ui bottom attached tab segment">
    <div>
        <h2 class="wiloke-add-listing-fields__title">Navigation Settings</h2>

        <div class="ui message info">
            <a href="https://documentation.wilcity.com/knowledgebase/how-to-build-custom-field-block-on-single-listing/" target="_blank">How to build Custom Field Block on Single Listing?</a>
        </div>

        <form id="wiloke-design-single-nav" method="post" action="#" class="ui form wiloke-review-claim-settings wiloke-form-has-icon">
            <div class="ui info message">
                <p>Refer to <a href="https://documentation.wilcity.com/knowledgebase/how-to-customize-single-directory-page/" target="_blank">Documentation</a> or Open a topic on <a href="https://wilcity.ticksy.com/" target="_blank">wilcity.ticksy.com</a></p>
            </div>

            <div v-show="errorMsg!=''" class="ui negative message">
                <p>{{errorMsg}}</p>
            </div>

            <div v-show="successMsg!=''" class="ui positive message">
                <p>{{successMsg}}</p>
            </div>

            <div class="field">
                <div class="right">
                    <button class="ui button red" @click.prevent="reset">Reset</button>
                    <button class="ui button violet" @click.prevent="addNewSectionToTop">Add New Section</button>
                    <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                </div>
            </div>

            <div class="field">
                <h3 class="ui heading">Click and drag a tab name to rearrange the order.</h3>

                <draggable class="dragArea drag__used" v-model="aSingleNav" :options="{handle: '.dragArea__form-title--icon'}" :move="checkMove">
                    <div v-for="(oNav, index) in aSingleNav" class="dragArea__block">
                        <div class="dragArea__form ui form field-wrapper segment">
                            <div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
                                <span class="dragArea__form-title--text">
                                    {{oNav.name}}
                                </span>
                                <span v-if="oNav.isCustomSection" class="dragArea__form-title--remove" @click.prevent="removeItem(index, aSingleNav)" title="Remove Category">
                                    <i class="la la-times"></i>
                                </span>
                            </div>
                            <div :class="['dragArea__form-content', oNav.isCustomSection ? 'dragArea__form-custom-section-content' : '']">
                                <div class="two fields">
                                    <div class="field">
                                        <label>Name</label>
                                        <input type="text" v-model="oNav.name">
                                    </div>
                                    <div class="field">
                                        <label>Key</label>
                                        <input v-if="!oNav.isCustomSection" readonly="" type="text" v-model="oNav.key">
                                        <input v-else type="text" v-model="oNav.key">
                                    </div>
                                    <div v-if="oNav.key == 'taxonomy'" class="field">
                                        <label>Taxonomy Key</label>
                                        <input type="text" v-model="oNav.taxonomy">
                                    </div>

                                    <div class="ui setting-field field">
                                        <label>Icon</label>
                                        <div class="wil-icon-wrap">
                                            <div class="wil-icon-box">
                                                <i :class="oNav.icon"></i>
                                            </div>
                                            <div class="ui right icon input">
                                                <input type="text" v-model='oNav.icon' class="wiloke-icon" v-on:update-icon="oNav.icon=$event.target.value">
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="oNav.maximumItemsOnHome != undefined" class="field">
                                        <label>Maximum Items On Home</label>
                                        <input type="text" v-model="oNav.maximumItemsOnHome">
                                    </div>
                                    <div class="field" v-if="oNav.key!='google_adsense_1' && oNav.key!='google_adsense_2' && oNav.key!='taxonomy' && oNav.key != 'coupon'">
                                        <label>Show On Navigation?</label>
                                        <select v-model="oNav.status" class="ui dropdown no-js">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                    <div v-if="oNav.isShowOnHome" class="field">
                                        <label>Show On Home?</label>
                                        <select v-model="oNav.isShowOnHome" class="ui dropdown no-js">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                    </div>
                                    <div class="field" v-if="oNav.key=='google_adsense_1' || oNav.key=='google_adsense_2'">
                                        <label>Show Box Title?</label>
                                        <select v-model="oNav.isShowBoxTitle" class="ui dropdown no-js">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                    </div>
                                    <input v-if="oNav.isCustomSection" type="hidden" value="yes">
                                </div>
                                <div v-if="oNav.isCustomSection" class="field">
                                    <label>Content</label>
                                    <textarea v-model="oNav.content"></textarea>
                                </div>
                                <div v-if="oNav.isCustomSection" class="message ui">
                                    <p>If you want to print the Custom Field that has been added on AddListing setting, the key should follow this structure: <i style="color:red">wilcity_single_navigation_[fieldKey]</i>. Eg: wilcity_single_navigation_my_select_field</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </draggable>

            </div>

            <div v-show="errorMsg!=''" class="ui negative message">
                <p>{{errorMsg}}</p>
            </div>

            <div v-show="successMsg!=''" class="ui positive message">
                <p>{{successMsg}}</p>
            </div>

            <div class="field">
                <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                <button class="ui button violet" @click.prevent="addNewSection">Add New Section</button>
                <button class="ui button red" @click.prevent="reset">Reset</button>
            </div>
        </form>

	    <?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
    </div>
</div>