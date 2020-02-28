<div data-tab="settings" class="ui bottom attached tab active segment">
	<h2 class="wiloke-add-listing-fields__title">Quick Search Form Settings</h2>
	<form id="wiloke-quick-search-form-settings" method="post" action="#" :class="formClass">
		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="left right">
			<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
		</div>

        <div v-for="oField in aFields" class="ui segment">
            <div v-if="oField.type=='checkbox2'" class="field ui toggle checkbox">
                <input type="checkbox" v-model='oField.value' true-value="yes" false-value="no">
                <label>{{oField.label}}</label>
            </div>
            <div v-if="oField.type=='multiple-select'" class="field">
                <label>{{oField.label}}</label>
                <select class="ui dropdown" multiple v-model="oField.value">
                    <option v-for="value in oField.options" :value="value">{{value}}</option>
                </select>
                <p style="margin-top: 10px" v-if="oField.desc"><i>{{oField.desc}}</i></p>
            </div>
            <div v-else-if="oField.type=='select'" class="field">
                <label>{{oField.label}}</label>
                <select class="ui dropdown" v-model="oField.value">
                    <option v-for="(name, value) in oField.options" :value="value">{{name}}</option>
                </select>
                <p style="margin-top: 10px" v-if="oField.desc"><i>{{oField.desc}}</i></p>
            </div>
            <div v-else-if="oField.type=='text'" class="field">
                <label>{{oField.label}}</label>
                <input v-model="oField.value">
                <p style="margin-top: 10px" v-if="oField.desc"><i>{{oField.desc}}</i></p>
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
	</form>
</div>