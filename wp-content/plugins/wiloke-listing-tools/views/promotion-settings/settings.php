<div data-tab="settings" class="ui bottom attached tab active segment">
	<h2 class="wiloke-add-listing-fields__title">Promotion Settings</h2>
    <p class="ui message info"><a href="https://documentation.wilcity.com/knowledgebase/setting-up-promotion-in-wilcity/" target="_blank">Setting Up Promotion in Wilcity</a></p>
	<form id="wiloke-promotion-settings" method="post" action="#" :class="formClass">
		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

		<div class="left right">
			<button class="ui button violet" @click.prevent="addPlan">Add Plan</button>
			<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
		</div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': toggle=='enable'}">
				<input type="checkbox" v-model='toggle' true-value="enable" false-value="disable">
				<label>Toggle Promotion</label>
			</div>
		</div>

        <div class="field">
            <label>Popup Description</label>
            <textarea type="text" v-model="description"></textarea>
        </div>

		<div v-show="toggle=='enable'">
			<div class="field">
				<h3 class="ui heading">Plans Detail</h3>

				<draggable class="dragArea drag__used" v-model="aPlans" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oPromotion, index) in aPlans" class="dragArea__block">
						<div class="dragArea__form ui form field-wrapper segment">
							<div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__form-title--text">
                                    {{oPromotion.name}}
                                </span>
								<span class="dragArea__form-title--remove" @click.prevent="removePlan(index, aPlans)" title="Remove Package">
                                    <i class="la la-times"></i>
                                </span>
							</div>
							<div class="dragArea__form-content">
								<div class="two fields">
                                    <div class="field">
                                        <label>Promotion ID (Unique)</label>
                                        <input type="text" v-model="oPromotion.id">
                                        <p><i style="color: #ff6657;">It's required If you are using listing_sidebar position. This Promotion ID and Promotion ID under [Your directory Type] Settings -> Single Sidebar -> Promotion must be the same.</i></p>
                                    </div>
									<div class="field">
										<label>Package Name</label>
										<input type="text" v-model="oPromotion.name">
									</div>
									<div class="field">
										<label>Available Days</label>
										<input type="text" v-model="oPromotion.duration">
									</div>

									<div class="field">
										<label>Package Price</label>
										<input type="text" v-model="oPromotion.price">
									</div>
                                    <div class="field">
                                        <label>Preview URL</label>
                                        <input type="text" v-model="oPromotion.preview">
                                    </div>
									<div class="field">
										<label>Position</label>
										<select class="ui dropdown" v-model="oPromotion.position">
											<option v-for="position in oPositions">{{position}}</option>
										</select>
									</div>
                                    <div v-show="oPromotion.position=='listing_sidebar'" class="field">
                                        <label>Conditional</label>
                                        <select class="ui dropdown" v-model="oPromotion.conditional">
                                            <option v-for="(name, conditional) in aSingleListingConditionals" :value="conditional">{{name}}</option>
                                        </select>
                                    </div>
                                    <div v-show="oPromotion.position=='listing_sidebar' && oPromotion.conditional=='google_address'" class="field">
                                        <label>Radius (*)</label>
                                        <input type="text" v-model="oPromotion.radius">
                                        <p>Show up all Listing within X KM</p>
                                    </div>
                                    <div v-show="oPromotion.position=='top_of_search'" class="field">
                                        <label>Menu Order</label>
                                        <input type="text" v-model="oPromotion.menu_order">
                                    </div>
								</div>

                                <select-from-post-types @changed="updateWooCommerceAssociate" :value="oPromotion.productAssociation" post-types="product" :index="index" is-multiple="no" label="Associate with WooCommerce"></select-from-post-types>
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
			<button class="ui button violet" @click.prevent="addPlan">Add Plan</button>
			<button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
		</div>
	</form>
</div>
