<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="reviewsetting" class="ui bottom attached tab segment">
	<h2 class="wiloke-add-listing-fields__title">Reviews Settings</h2>
	<form id="wiloke-review-settings" method="post" action="#" class="ui form wiloke-review-claim-settings">
        <div class="ui info message">
            <p>Refer to <a href="https://documentation.wilcity.com/knowledgebase/how-to-set-up-review/" target="_blank">Documentation</a> or Open a topic on <a href="https://wilcity.ticksy.com/" target="_blank">wilcity.ticksy.com</a></p>
        </div>

		<div v-show="errorMsg!=''" class="ui negative message">
			<p>{{errorMsg}}</p>
		</div>

		<div v-show="successMsg!=''" class="ui positive message">
			<p>{{successMsg}}</p>
		</div>

        <div class="right">
            <button class="ui button violet" @click.prevent="addReviewCategory">Add Category</button>
            <button class="ui button green" @click.prevent="saveReviewSettings">Save Changes</button>
        </div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': oReviewSettings.toggle=='enable'}">
				<input type="checkbox" v-model='oReviewSettings.toggle' true-value="enable" false-value="disable">
				<label>Toggle Review</label>
			</div>
		</div>

        <div class="field">
            <div class="ui toggle checkbox" :class="{'checked': oReviewSettings.is_immediately_approved=='enable'}">
                <input type="checkbox" v-model='oReviewSettings.is_immediately_approved' true-value="yes" false-value="no">
                <label>Is Immediately Approved</label>
            </div>
        </div>

		<div class="field">
			<div class="ui toggle checkbox" :class="{'checked': oReviewSettings.toggle=='enable'}">
				<input type="checkbox" v-model='oReviewSettings.toggle_review_discussion' true-value="enable" false-value="disable">
				<label>Toggle Review Discussion</label>
			</div>
		</div>

		<div v-show="oReviewSettings.toggle=='enable'">
			<div class="field">
				<div class="ui toggle checkbox" :class="{'checked': oReviewSettings.toggle=='enable'}">
					<input type="checkbox" v-model='oReviewSettings.toggle_gallery' true-value="enable" false-value="disable">
					<label>Toggle Gallery Upload</label>
				</div>
			</div>

			<div class="field">
				<h3 class="ui heading">Score Scale</h3>
				<div class="field">
					<div class="ui radio checkbox">
						<input type="radio" v-model="oReviewSettings.mode" value="5" class="hidden">
						<label>Scale of 1 to 5</label>
					</div>
				</div>
				<div class="field">
					<div class="ui radio checkbox">
						<input type="radio" v-model="oReviewSettings.mode" value="10" class="hidden">
						<label>Scale of 1 to 10</label>
					</div>
				</div>
			</div>

			<div class="field">
				<h3 class="ui heading">Rating Details</h3>

				<draggable class="dragArea drag__used" v-model="oReviewSettings.details" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oReview, index) in oReviewSettings.details" class="dragArea__block">
						<div class="dragArea__form ui form field-wrapper segment">
							<div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__form-title--text">
                                    {{oReview.name}}
                                </span>
								<span class="dragArea__form-title--remove" @click.prevent="removeReviewCategory(index, oReviewSettings.details)" title="Remove Category">
                                    <i class="la la-times"></i>
                                </span>
							</div>
							<div class="dragArea__form-content">
								<div class="two fields">
									<div class="field">
										<label>Category name</label>
										<input type="text" v-model="oReview.name">
									</div>
									<div class="field">
										<label>Category key</label>
										<input v-if="oReview.isEditable!='enable'" readonly="" type="text" v-model="oReview.key">
										<input v-if="oReview.isEditable!='enable'" type="hidden" v-model="oReview.isEditable" value="disable">
										<input v-if="oReview.isEditable=='enable'" type="text" v-model="oReview.key">
										<i>This key must be unique</i>
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
            <button class="ui button green" @click.prevent="saveReviewSettings">Save Changes</button>
            <button class="ui button violet" @click.prevent="addReviewCategory">Add Category</button>
		</div>
	</form>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>