<div data-tab="main-menu-settings" class="ui bottom attached tab active segment">
	<h2 class="wiloke-add-listing-fields__title">Bottom Tab Navigator</h2>
    <div class="ui segment message warning">
        <p>You can add maximum 5 menu items to the Main Menu</p>
    </div>
    <div id="wiloke-mobile-main-menu-settings" :class="formClass">
        <div v-show="errorMsg!=''" class="ui negative message">
            <p>{{errorMsg}}</p>
        </div>
        <div v-show="successMsg!=''" class="ui positive message">
            <p>{{successMsg}}</p>
        </div>

        <div class="drag">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <div class="drag__block">
                        <h3 class="drag__title">Available Fields</h3>
                        <draggable v-model="aAvailableMenuItems" class="dragArea drag__avai" :options="{group: {name: 'aBuildMenuItems'}}" @change="removeASectionInAvailableArea">
                            <div v-for="(oMenuItem, key) in aAvailableMenuItems" :key="key" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
                                <span class="dragArea__item-text">
                                    <span v-html="oMenuItem.oGeneral.heading"></span> <small>({{oMenuItem.oGeneral.key}})</small>
                                </span>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div class="sixteen wide column">
                    <h3 class="drag__title">Used Fields</h3>
                    <form action="#" id="wiloke-design-secondary-search-form" class="ui form wiloke-form-has-icon" @submit.prevent="saveValue">
                        <draggable class="dragArea drag__used" v-model="aUsedMenuItems" :options="{group:'aBuildMenuItems', handle: '.dragArea__form-title--icon'}" @change="addedNewSectionInUsedArea">
                            <div class="dragArea__block" v-for="(oMenuItem, index) in aUsedMenuItems" :key="index">
                                <div class="dragArea__form ui form field-wrapper segment">
                                    <div class="dragArea__form-title" @click.prevent="expandBlockSettings">
                                        <span class="dragArea__form-title--icon">
                                            <i class="la la-arrows-v"></i>
                                        </span>
                                        <span class="dragArea__form-title--text">
                                            <span v-html="oMenuItem.oGeneral.heading"></span> <small>({{oMenuItem.oGeneral.key}})</small>
                                        </span>
                                        <span class="dragArea__form-title--remove" @click.prevent="removeSection(index, oMenuItem)" title="Remove Menu">
                                            <i class="la la-times"></i>
                                        </span>
                                    </div>

                                    <div class="dragArea__form-content hidden">
                                        <div :class="wrapperClass(oMenuItem.oGeneral)">

                                            <div :class="['field', oField.component=='wiloke-icon' ? 'wil-icon-wrap' : '', oField.component=='wiloke-input-read-only' ? 'disabled' : '']" v-for="(oField, subIndex) in oMenuItem.aFields">
                                                <template v-if="oField.component=='wiloke-input-read-only'">
                                                    <label>{{oField.label}}</label>
                                                    <input type="text" class="wiloke-icon" v-model="oField.value">
                                                    <p v-if="oField.desc"><i>{{oField.desc}}</i></p>
                                                </template>
                                                <template v-else-if="oField.component=='wiloke-input'">
                                                    <label>{{oField.label}}</label>
                                                    <input type="text" v-model="oField.value">
                                                    <p v-if="oField.desc"><i>{{oField.desc}}</i></p>
                                                </template>
                                                <template v-else-if="oField.component=='wiloke-icon'">
                                                    <label>{{oField.label}}</label>
                                                    <div class="wil-icon-box">
                                                        <i :class="oField.value"></i>
                                                    </div>
                                                    <div class="ui right icon input">
                                                        <input type="text" v-model="oField.value" class="wiloke-icon" v-on:update-icon="oField.value = $event.target.value">
                                                    </div>
                                                </template>
                                                <template v-else-if="oField.component=='wiloke-select'">
                                                    <label>{{oField.label}}</label>
                                                    <select v-model="oField.value" class="ui fluid dropdown">
                                                        <option v-for="oOption in oField.aOptions" :value="oOption.value">{{oOption.name}}</option>
                                                    </select>
                                                </template>
                                                <wiloke-ajax-search-field v-else-if="oField.component=='wiloke-ajax-search-field'" v-model="oField.value" :action="oField.action" :label="oField.label" :std="oField.value" :action="oField.action" :desc="oField.desc"></wiloke-ajax-search-field>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </form>
                </div>
            </div>
        </div>

        <div v-show="errorMsg!=''" class="ui negative message">
            <p>{{errorMsg}}</p>
        </div>
        <div v-show="successMsg!=''" class="ui positive message">
            <p>{{successMsg}}</p>
        </div>

        <div class="field">
            <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
        </div>
    </div>
</div>