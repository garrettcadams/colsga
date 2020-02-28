<div data-tab="secondary-menu-settings" class="ui bottom attached tab segment">
	<h2 class="wiloke-add-listing-fields__title">Secondary Navigator</h2>
	<div id="wiloke-mobile-secondary-menu-settings" :class="formClass">
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
						<draggable v-model="aAvailableMenuItems" class="dragArea drag__avai" :options="{group: {name: 'aBuildSecondaryMenuItems'}}">
							<div v-for="(oMenuItem, key) in aAvailableMenuItems" :key="key" class="dragArea__item">
                                <span class="dragArea__item-icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__item-text">
                                    <span v-html="oMenuItem.oGeneral.heading"></span> <small>({{oMenuItem.oGeneral.screen}})</small>
                                </span>
							</div>
						</draggable>
					</div>
				</div>
				<div class="sixteen wide column">
					<h3 class="drag__title">Used Fields</h3>
					<form action="#" id="wiloke-design-secondary-search-form" class="ui form wiloke-form-has-icon" @submit.prevent="saveValue">
                        <draggable class="dragArea drag__used" v-model="aUsedMenuItems" :options="{group:'aBuildSecondaryMenuItems', handle: '.dragArea__form-title--icon'}" @change="addedNewSectionInUsedArea">
                            <div class="dragArea__block" v-for="(oMenuItem, index) in aUsedMenuItems" :key="index">
                                <div class="dragArea__form ui form field-wrapper segment">
                                    <div class="dragArea__form-title" @click.prevent="expandBlockSettings">
                                        <span class="dragArea__form-title--icon">
                                            <i class="la la-arrows-v"></i>
                                        </span>
                                        <span class="dragArea__form-title--text">
                                            <span v-html="oMenuItem.oGeneral.heading"></span> <small>({{oMenuItem.oGeneral.screen}})</small>
                                        </span>
                                        <span class="dragArea__form-title--remove" @click.prevent="removeSection(index, oMenuItem)" title="Remove Menu">
                                            <i class="la la-times"></i>
                                        </span>
                                    </div>

                                    <div class="dragArea__form-content hidden">
                                        <component v-for="oField in oMenuItem.aFields" v-model="oField.value" :is="oField.component" :label="oField.label ? oField.label : oField.value" :std="oField.value" :desc="oField.desc" :a-options="oField.aOptions" :action="oField.action"></component>
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