<?php
use WilokeListingTools\Framework\Helpers\General;
?>
<div data-tab="wiloke-schema-markup" class="ui bottom attached tab segment">
    <ul class="ui list" style="display: inline-block">
        <li>{{postTitle}}</li>
        <li>{{featuredImg}}</li>
        <li>{{logo}}</li>
        <li>{{postExcerpt}}</li>
        <li>{{eventStartDate}}</li>
        <li>{{eventEndDate}}</li>
        <li>{{bestRating}}</li>
        <li>{{worstRating}}</li>
        <li>{{worstRating}}</li>
        <li>{{averageRating}}</li>
        <li>{{totalRatings}}</li>
        <li>{{reviewCount}}</li>
        <li>{{reviewDetails}}</li>
        <li>{{streetAddress}}: It's Google Address value</li>
        <li>{{telephone}}</li>
        <li>{{website}}</li>
        <li>{{listingURL}}</li>
        <li>{{email}}</li>
        <li>{{priceRange}}</li>
        <li>{{singlePrice}}</li>
        <li>{{latitude}}</li>
        <li>{{longitude}}</li>
        <li>{{photos}}: It's Gallery setting</li>
        <li>{{listing_location}}</li>
        <li>{{socialNetworks}}</li>
        <li>{{openingHours}}</li>
        <li>{{author}}</li>
    </ul>

    <div id="wiloke-schema-markup">
        <h2 class="wiloke-add-listing-fields__title">Schema Markup</h2>

        <h3>Place Holders</h3>
        <div :class="wrapperClass">
            <div class="field" style="margin-bottom: 20px; margin-top: 20px; min-height: 50px;">
                <div class="right">
                    <button class="ui button red" @click.prevent="resetSetting">Reset</button>
                    <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                </div>
            </div>

            <div v-show="errorMsg!=''" class="ui negative message">
                <p>{{errorMsg}}</p>
            </div>

            <div v-show="successMsg!=''" class="ui positive message">
                <p>{{successMsg}}</p>
            </div>

            <json-editor :obj-data="oSchemaMarkup" v-model="oSchemaMarkup" ></json-editor>

            <div v-show="errorMsg!=''" class="ui negative message">
                <p>{{errorMsg}}</p>
            </div>

            <div v-show="successMsg!=''" class="ui positive message">
                <p>{{successMsg}}</p>
            </div>

            <div class="field" style="margin-bottom: 20px; margin-top: 20px; min-height: 50px;">
                <div class="right">
                    <button class="ui button green" @click.prevent="saveChanges">Save Changes</button>
                    <button class="ui button red" @click.prevent="resetSetting">Reset</button>
                </div>
            </div>
        </div>
	</div>

	<?php do_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', General::detectPostType() == 'wiloke-listing-settings' ? 'listing' : General::detectPostType(), str_replace('.php', '', basename(__FILE__))); ?>
</div>