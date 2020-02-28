<div id="wiloke-add-listing-fields">
	<h2 class="wiloke-add-listing-fields__title">Adding Directory Type</h2>
    <div class="message ui info">
        You can drag to re-order to Directory Type.
        The first Directory Type will be used as the default directory type.
    </div>
	<form id="wiloke-add-custom-posttypes" method="post" action="#" class="ui form wiloke-form-has-icon">
		<div v-show="errorMessage!=''" class="ui negative message">
			<p>{{errorMessage}}</p>
		</div>

		<div v-show="successMessage!=''" class="ui positive message">
			<p>{{successMessage}}</p>
		</div>

        <draggable v-model="oFields">
            <div v-for="(oField, index) in oFields" class="ui segment">
                <h4 class="ui dividing">Directory Type Settings</h4>
                <div class="fields six">
                    <div class="field">
                        <label>Key</label>
                        <input v-if="oField.keyEditAble=='no'" type="text" v-model="oField.key" readonly>
                        <input v-else type="text" v-model="oField.key" required>
                        <p><i>The key must be unique. The uppercase, space and special characters like &, $ are not allowed</i></p>
                    </div>
                    <div class="field">
                        <label>Slug</label>
                        <input type="text" v-model="oField.slug" required>
                        <p><i>The slug must be unique. The uppercase, space and special characters like &, $ are not allowed. You should use - to replace for space. Eg: wilcity-listing</i></p>
                    </div>
                    <div class="field">
                        <label>Singular Name</label>
                        <input type="text" v-model="oField.singular_name" required>
                    </div>
                    <div class="field">
                        <label>Name</label>
                        <input type="text" v-model="oField.name" required>
                    </div>
                    <div class="field">
                        <label>Add Listing Label</label>
                        <input type="text" v-model="oField.addListingLabel" required>
                        <div><i>This setting will be used on the Pricing Page</i></div>
                    </div>
                    <div class="field">
                        <label>Background Color</label>
                        <wiloke-color-picker v-model="oField.addListingLabelBg"></wiloke-color-picker>
                    </div>
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <input type="checkbox" v-model='oField.isDisableOnFrontend' true-value="yes" false-value="no">
                            <label>Disable on AddListing Front-end</label>
                        </div>
                        <div><i>Do not show this directory type on the Add Listing page.</i></div>
                    </div>
                    <div class="ui setting-field field">
                        <label>Icon</label>
                        <div class="wil-icon-wrap">
                            <div class="wil-icon-box">
                                <i :class="oField.icon"></i>
                            </div>
                            <div class="ui right icon input">
                                <input type="text" v-model='oField.icon' class="wiloke-icon" v-on:update-icon="oField.icon=$event.target.value">
                            </div>
                        </div>
                    </div>
                    <span v-if="!oField.deleteAble || oField.deleteAble=='yes'" class="dragArea__form-title--remove" @click.prevent="removePostType(index)" title="Remove Post Type" style="font-size: 20px; position: absolute; top: -1px; right: 1px;">
                        <i class="la la-times"></i>
                    </span>
                </div>
            </div>
        </draggable>

		<div v-show="errorMessage!=''" class="ui negative message">
			<p>{{errorMessage}}</p>
		</div>

		<div v-show="successMessage!=''" class="ui positive message">
			<p>{{successMessage}}</p>
		</div>

		<div class="field">
			<button class="ui button basic green" @click.prevent="saveAddCustomPostTypes">Save Changes</button>
            <button class="ui button basic purple" @click.prevent="addCustomPostType">Add Directory Type</button>
		</div>
	</form>
</div>