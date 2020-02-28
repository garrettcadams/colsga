<div class="ui bottom attached tab segment active" data-tab="claimsettings">
	<h2 class="wiloke-add-listing-fields__title">Claim Popup Fields</h2>
	<form id="wiloke-claim-settings" method="post" action="#" class="ui form wiloke-review-claim-settings">
        <div class="field">
            <div class="right">
                <button class="ui button violet" @click.prevent="addClaimField">Add Field</button>
                <button class="ui button green" @click.prevent="saveClaimFieldsSettings">Save Changes</button>
            </div>
        </div>

		<div v-show="errorMsg.length" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg.length" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div>
			<div class="field">
				<h3 class="ui heading">Claim Information</h3>
				<p>When a user click on Claim Listing button, a Claim Information form will be shown. Here is the place where you decide what fields will be displayed on the Claim Information form.</p>
				<draggable class="dragArea drag__used" v-model="oClaimFields" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oField, index) in oClaimFields" class="dragArea__block">
						<div class="dragArea__form ui form field-wrapper segment">
							<div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__form-title--text">
                                    {{oField.label}}
                                </span>
								<span class="dragArea__form-title--remove" @click.prevent="removeClaimField(index, oField)" title="Remove Category">
                                    <i class="la la-times"></i>
                                </span>
							</div>

							<div id="wilcity-claim-fields-wrapper" class="dragArea__form-content">
								<div v-if="oField.type=='claimPackage'" class="ui message info">
									The claim packages are your listing plans. Go to Wiloke Submission -> Settings -> AddListing Plans to setup it.
								</div>

								<div class="five fields">
									<div class="field">
										<label>Field Label</label>
										<input type="text" v-model="oField.label">
									</div>
									<div class="field" v-if="oField.type!='claimPackage'">
										<label>Field Key</label>
										<input type="text" v-model="oField.key">
										<i>This key must be unique</i>
									</div>
									<div class="field" v-else>
										<label>Field Key</label>
										<input type="text" v-model="oField.key" value="claimPackage" readonly>
									</div>
									<div class="field">
										<label>Field Type</label>
										<select class="ui dropdown" v-model="oField.type" @change="changeClaimFieldType(oField, index)">
											<option value="textarea">Textarea</option>
											<option value="text">Text</option>
											<option value="radio">Radio</option>
											<option value="checkbox">Checkbox</option>
											<option value="claimPackage">Packages</option>
										</select>
									</div>
									<div v-show="oField.type == 'checkbox' || oField.type == 'radio'" class="field">
										<label>Options</label>
										<textarea v-model="oField.options"></textarea>
										<p><i>Each option is separated by a comma. For example: Option A, Option B</i></p>
									</div>
									<div class="field ui toggle checkbox" :class="{'checked': oField.isRequired=='yes'}">
										<input type="checkbox" v-model='oField.isRequired' true-value="yes" false-value="no">
										<label>Is Required?</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</draggable>
			</div>
		</div>

		<div v-show="errorMsg.length" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg.length" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="field">
			<button class="ui button green" @click.prevent="saveClaimFieldsSettings">Save Changes</button>
            <button class="ui button violet" @click.prevent="addClaimField">Add Field</button>
		</div>
	</form>
</div>