<?php do_action('wiloke-listing-tools/wiloke-tools/general/before-installing'); ?>
<!--<div class="ui segment">-->
<!--    <h2 class="wiloke-add-listing-fields__title">Verify Purchase Code</h2>-->
<!--    <div id="wilcity-verify-purchase-code-form">-->
<!--        --><?php //if ( !\WilokeListingTools\Controllers\VerifyPurchaseCode::isActivating() ) : ?>
<!--            <form method="POST" :class="formClass">-->
<!--                <div class="ui message info">-->
<!--                    <p>To get your purchase code, please read and follow this tutorial <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Where is My Purchase Code?</a></p>-->
<!--                </div>-->
<!---->
<!--                <div class="ui red message">-->
<!--                    <p>As Envato Policy, You can not use 1 Regular License on more than 1 domain. If you want to migrate your website to another hosting or switch to another domain, You have to <strong>Revoke Verify Purchase Code</strong> on the currently domain first. </p>-->
<!--                </div>-->
<!---->
<!--                <div class="field">-->
<!--                    <label for="wilcity-purchase-code">Purchase Code</label>-->
<!--                    <input type="text" id="wilcity-purchase-code" value="" v-model="purchasedCode">-->
<!--                </div>-->
<!---->
<!--                <div class="mb-15">-->
<!--                    <button class="ui button green" @click.prevent="verifyPurchaseCode">Verify Now</button>-->
<!--                </div>-->
<!--            </form>-->
<!--        --><?php //else: ?>
<!--            <div class="ui info message">-->
<!--                <p>You are activating --><?php //echo \WilokeListingTools\Controllers\VerifyPurchaseCode::showLessPurchaseCode(); ?><!--</p>-->
<!--            </div>-->
<!--            <div class="form ui">-->
<!--                <button :class="revokeBtnClass" @click.prevent="revokeLicense">Revoke License</button>-->
<!--            </div>-->
<!--        --><?php //endif; ?>
<!--    </div>-->
<!--</div>-->

<div class="ui segment" data-tab="addlisting">
    <h2 class="wiloke-add-listing-fields__title"><?php esc_html_e('Install Wiloke Submission Pages', 'wiloke-design-addlisting'); ?></h2>
    <div id="wiloke-listing-tools-general" v-cloak>
        <form action="#" class="form ui" :class="additionalClass">
            <div class="ui info message">
                <p>This tool will install all the missing Wiloke Submission pages. Pages already defined and set up will not be replaced.</p>
            </div>

            <div v-show="aResponse.length" class="ui positive message">
                <ul class="ui list">
                    <li v-for="oItem in aResponse" :style="printStyle(oItem)">{{oItem.msg}}</li>
                </ul>
            </div>

            <div class="mb-15">
                <button class="ui button green" @click.prevent="installPages">Install</button>
            </div>
        </form>
    </div>
</div>
<?php do_action('wiloke-listing-tools/wiloke-tools/general/after-installing'); ?>


