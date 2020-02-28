<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="single-event-content" class="ui bottom attached tab segment">
	<div>
		<h2 class="wiloke-add-listing-fields__title">Single Event Content</h2>
        <div class="ui message info">
            <a href="https://documentation.wilcity.com/knowledgebase/how-to-build-custom-field-block-on-single-listing/" target="_blank">How to build Custom Field Block on Single Listing?</a>
        </div>
		<form id="wiloke-design-single-event-content" method="post" action="#" :class="formClass">
            <div class="field">
                <div class="right">
                    <button class="ui button violet" @click.prevent="addNewSection">Add New Section</button>
                    <button class="ui button green" @click.prevent="saveContent">Save Changes</button>
                </div>
            </div>
			<div v-show="errorMsg!=''" class="ui negative message">
				<p>{{errorMsg}}</p>
			</div>

			<div v-show="successMsg!=''" class="ui positive message">
				<p>{{successMsg}}</p>
			</div>

			<div class="field">
				<h3 class="ui heading">Click and drag a tab name to rearrange the order.</h3>

				<draggable class="dragArea drag__used" v-model="oContent" :options="{handle: '.dragArea__form-title--icon'}">
					<div v-for="(oSection, index) in oContent" class="dragArea__block">
						<div class="dragArea__form ui form field-wrapper segment">
							<div class="dragArea__form-title">
                                <span class="dragArea__form-title--icon">
                                    <i class="la la-arrows-v"></i>
                                </span>
								<span class="dragArea__form-title--text">
                                    {{oSection.name}}
                                </span>
								<span class="dragArea__form-title--remove" @click.prevent="removeItem(index, oContent)" title="Remove Section">
                                    <i class="la la-times"></i>
                                </span>
							</div>
							<div class="dragArea__form-content">
								<div class="two fields">
									<div class="field">
										<label>Name</label>
										<input type="text" v-model="oSection.name">
									</div>
									<div class="setting-field field">
                                        <label>Icon</label>
                                        <div class="wil-icon-wrap">
                                            <div class="wil-icon-box">
                                                <i :class="oSection.icon"></i>
                                            </div>
                                            <div class="ui right icon input">
                                                <input type="text" v-model='oSection.icon' class="wiloke-icon" v-on:update-icon="oContent.icon=$event.target.value">
                                            </div>
                                        </div>
									</div>

									<div class="field">
                                        <label>Field Key</label>
                                        <ajax-search-field v-model="oSection.key" :value="oSection.key" action="wlt_search_event_key"></ajax-search-field>
                                    </div>

                                    <div class="field">
                                        <label>Field Content</label>
                                        <textarea v-model="oSection.content"></textarea>
                                        <p>It's useful for Custom Field and Group Field.</p>
                                    </div>
								</div>
							</div>
						</div>
					</div>
				</draggable>

			</div>

			<div v-show="errorMsg!=''" class="ui negative message">
				<p>{{errorMsg}}</p>
			</div>

			<div v-show="successMsg!=''" class="ui positive message">
				<p>{{successMsg}}</p>
			</div>

			<div class="field">
				<button class="ui button green" @click.prevent="saveContent">Save Changes</button>
                <button class="ui button violet" @click.prevent="addNewSection">Add New Section</button>
			</div>
		</form>
	</div>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>