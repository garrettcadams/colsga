<div data-tab="settings" class="ui bottom attached tab active segment">
	<h2 class="wiloke-add-listing-fields__title">Report Settings</h2>
	<form id="wiloke-report-settings" method="post" action="#" :class="formClass">
		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="left right">
			<button class="ui button violet" @click.prevent="addField">Add Field</button>
			<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
		</div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': toggle=='enable'}">
				<input type="checkbox" v-model='toggle' true-value="enable" false-value="disable">
				<label>Toggle Report</label>
			</div>
		</div>

        <div class="field" v-show="toggle=='enable'">
            <label>Description</label>
            <textarea v-model="description" cols="30" rows="10"></textarea>
        </div>

        <div class="field" v-show="toggle=='enable'">
            <label>Thank you message</label>
            <textarea v-model="thankyou" cols="30" rows="10"></textarea>
        </div>

		<div v-show="toggle=='enable'">
			<div class="field">
				<h3 class="ui heading">Field Setting</h3>

				<draggable class="dragArea drag__used" v-model="aFields" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oField, index) in aFields" class="dragArea__block">
						<div class="dragArea__form ui form field-wrapper segment">
							<div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__form-title--text">
                                    {{oField.name}}
                                </span>
								<span class="dragArea__form-title--remove" @click.prevent="removeField(index, aFields)" title="Remove Field">
                                    <i class="la la-times"></i>
                                </span>
							</div>
							<div class="dragArea__form-content">
								<div class="two fields">
									<div class="field">
										<label>Field Label</label>
										<input type="text" v-model="oField.label">
									</div>
									<div class="field">
										<label>Field Key</label>
										<input type="text" v-model="oField.key">
									</div>
									<div class="field">
										<label>Field Type</label>
										<select class="ui dropdown" v-model="oField.type">
											<option value="text">Text</option>
											<option value="textarea">Textarea</option>
											<option value="select">Select</option>
										</select>
									</div>
									<div v-show="oField.type=='select'" class="field">
										<label>Options</label>
										<input v-model="oField.options" cols="30" rows="10">
                                        <p><i>Each option separated by a comma. For example: Option A, Option B</i></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</draggable>
			</div>
		</div>

		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="field">
			<button class="ui button violet" @click.prevent="addField">Add Field</button>
			<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
		</div>
	</form>
</div>