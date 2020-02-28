<div data-tab="admin-push-notification-settings" class="ui bottom attached tab segment">
	<h2 class="wiloke-add-listing-fields__title">Admin Settings</h2>
	<form id="wiloke-push-notification-settings" method="post" action="#" :class="formClass">
		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': toggleAdminReceiveNotifications=='enable'}">
				<input type="checkbox" v-model='toggleAdminReceiveNotifications' true-value="enable" false-value="disable">
				<label>Toggle Push Notifications</label>
			</div>
		</div>

        <div class="field" v-if="toggleAdminReceiveNotifications=='enable'">
            <div class="ui segment" v-for="(oSetting, notificationKey) in oAdminNotifications">
                <h3 class="ui">{{oSetting.title}}</h3>
                <p v-if="oSetting.settingDesc">{{oSetting.settingDesc}}</p>

                <div class="field">
                    <label>Status</label>
                    <select class="dropdown ui" v-model="oSetting.status">
                        <option value="on">On</option>
                        <option value="off">Off</option>
                    </select>
                </div>
                <div class="field">
                    <label>Title</label>
                    <input type="text" v-model="oSetting.title">
                </div>
                <div class="field">
                    <label>Description</label>
                    <input type="text" v-model="oSetting.desc">
                </div>
                <div class="field">
                    <label>Message</label>
                    <input type="text" v-model="oSetting.msg">
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
			<button class="ui button green" @click.prevent="saveAdminSettings">Save Changes</button>
		</div>
	</form>
</div>