<div data-tab="generalsettings" class="ui bottom attached active tab segment">
	<h2 class="wiloke-add-listing-fields__title">Event Settings</h2>

	<form id="wiloke-event-settings" method="post" action="#" :class="formClass">
		<div v-show="errMsg!=''" class="ui negative message">
			<p>{{errMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

        <div class="field">
            <div class="ui toggle checkbox" :class="{'checked': oEventGenerals.toggle_event=='enable'}">
                <input type="checkbox" v-model='oEventGenerals.toggle_event' true-value="enable" false-value="disable">
                <label>Toggle Event Directory Type</label>
            </div>
        </div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': oEventGenerals.toggle=='enable'}">
				<input type="checkbox" v-model='oEventGenerals.toggle' true-value="enable" false-value="disable">
				<label>Toggle Comment</label>
			</div>
		</div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': oEventGenerals.immediately_approved=='enable'}">
				<input type="checkbox" v-model='oEventGenerals.immediately_approved' true-value="enable" false-value="disable">
				<label>Is Immediately Approved?</label>
			</div>
		</div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': oEventGenerals.toggle_comment_discussion=='enable'}">
				<input type="checkbox" v-model='oEventGenerals.toggle_comment_discussion' true-value="enable" false-value="disable">
				<label>Toggle Comment Discussion</label>
				<div class="ui message info">The visitors can discus in a comment</div>
			</div>
		</div>

		<div class="field">
			<button class="ui button green" @click.prevent="saveGeneralSettings">Save Changes</button>
		</div>
	</form>
</div>