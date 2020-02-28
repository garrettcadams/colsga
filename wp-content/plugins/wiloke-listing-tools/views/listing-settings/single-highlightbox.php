<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="single-highlightboxes-tab" class="ui bottom attached tab segment">
	<div>
		<h2 class="wiloke-add-listing-fields__title">Highlight Box Settings</h2>
        <div class="field">
            <p class="ui message info">Only the owner of listing can see these boxes.</p>
        </div>

        <div class="ui info message">
            <p>Refer to <a href="https://documentation.wilcity.com/knowledgebase/how-to-customize-single-highlight-box/" target="_blank">Documentation</a> or Open a topic on <a href="https://wilcity.ticksy.com/" target="_blank">wilcity.ticksy.com</a></p>
        </div>

		<form id="wiloke-design-highlight-boxes" method="post" action="#" class="ui form wiloke-form-has-icon wiloke-review-claim-settings">
			<div v-show="errorMsg!=''" class="ui negative message">
				<p>{{errorMsg}}</p>
			</div>

			<div v-show="successMsg!=''" class="ui positive message">
				<p>{{successMsg}}</p>
			</div>

            <div class="field" style="margin-bottom: 20px; margin-top: 20px; min-height: 50px;">
                <div class="right">
                    <button v-show="aBoxes.isEnable=='yes'" class="ui button violet" @click.prevent="addNewItem">Add New Section</button>
                    <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                </div>
            </div>

            <div class="field">
			    <div class="ui segment">
				<h3 class="ui heading">General Settings</h3>
				<div class="fields two">
					<div class="field">
						<label>Is Enable Highlight Box</label>
						<select v-model="aBoxes.isEnable" class="ui dropdown">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</div>
					<div v-show="aBoxes.isEnable=='yes'" class="field">
						<label>Number of boxes / row</label>
						<select v-model="aBoxes.itemsPerRow" class="ui dropdown">
							<option value="col-md-12 col-lg-12">1 Item</option>
							<option value="col-md-6 col-lg-6">2 Items</option>
							<option value="col-md-4 col-lg-4">3 Items</option>
							<option value="col-md-3 col-lg-3">4 Items</option>
						</select>
					</div>
				</div>
			</div>
            </div>

            <div class="field">
			    <div v-show="aBoxes.isEnable=='yes'">
				<h3 class="ui heading">Click and drag a tab name to rearrange the order.</h3>
				<draggable class="dragArea drag__used" v-model="aBoxes.aItems" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oBox, index) in aBoxes.aItems" class="dragArea__block">
						<div class="dragArea__form field-wrapper ui segment">
							<div class="dragArea__form-title">
	                            <span class="dragArea__form-title--icon">
	                                <i class="la la-arrows-v"></i>
	                            </span>
								<span class="dragArea__form-title--text">
	                                {{oBox.name}}
	                            </span>
								<span v-if="oBox.isCustomSection" class="dragArea__form-title--remove" @click.prevent="removeItem(index, aBoxes)" title="Remove Item">
	                                <i class="la la-times"></i>
	                            </span>
							</div>
							<div class="dragArea__form-content">
								<div class="two fields">
								<div class="field">
									<label>Name</label>
									<input type="text" v-model="oBox.name" @keyup="nameChanged(index)">
								</div>
								<div class="field">
									<label>Key</label>
									<input v-if="oBox.isPopup=='yes'" readonly="" type="text" v-model="oBox.key">
									<input v-else type="text" v-model="oBox.key">
								</div>
								<div class="field">
									<label>Box Type</label>
									<select v-model="oBox.type" @change="changeBoxType(index)" class="ui dropdown">
										<option value="add-photos-videos-popup">Add Photo Video</option>
										<option value="message-popup">Message</option>
										<option value="event">Create Event</option>
										<option value="custom-link">Custom Link</option>
									</select>
								</div>
								<div v-show="oBox.type=='custom-link'" class="field">
									<label>Open Link Type</label>
									<select class="ui dropdown" v-model="oBox.linkTargetType">
										<option value="self">Refresh the page</option>
										<option value="blank">Open a new window</option>
									</select>
								</div>
								<div v-show="oBox.type=='custom-link'" class="field">
									<label>Link To</label>
									<input v-model="oBox.linkTo">
								</div>

                                <div class="ui setting-field field">
                                    <label>Icon</label>
                                    <div class="wil-icon-wrap">
                                        <div class="wil-icon-box">
                                            <i :class="oBox.icon"></i>
                                        </div>
                                        <div class="ui right icon input">
                                            <input type="text" v-model='oBox.icon' class="wiloke-icon" v-on:update-icon="oBox.icon=$event.target.value">
                                        </div>
                                    </div>
                                </div>

								<div class="field">
									<label>Background Color</label>
                                    <wiloke-color-picker v-model="oBox.bgColor"></wiloke-color-picker>
								</div>
								<input v-if="oBox.isCustomSection" type="hidden" value="yes">
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
				<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                <button v-show="aBoxes.isEnable=='yes'" class="ui button violet" @click.prevent="addNewItem">Add New Section</button>
			</div>
		</form>

		<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
	</div>
</div>
