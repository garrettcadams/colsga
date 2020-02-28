<?php
return [
	'configuration' => array(
		'fields' => array(
			array(
				'type' => 'open_segment'
			),
			array(
				'text'    => 'General Settings',
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select',
				'heading' => 'Toggle Wiloke Submission',
				'desc'    => 'If the Become an author feature is enabled, please set the Role Default to Subscriber. Otherwise, please set to Contributor. To set the Role Default, from the admin sidebar click on Settings -> General.',
				'name'    => 'wilcity_submission[toggle]',
				'id'      => 'wilcity_submission_toggle',
				'options' => array(
					'disable' => 'Disable',
					'enable'  => 'Enable'
				),
				'default'     => 'enable'
			),
			array(
				'type'    => 'select',
				'heading' =>  'Toggle Become an author?',
				'desc'    => 'If the feature is enabled, they have to confirm your terms and conditions on "Become an author" page',
				'name'    => 'wilcity_submission[toggle_become_an_author]',
				'id'      => 'toggle_become_an_author',
				'options' => array(
					'disable' => 'Disable',
					'enable'  => 'Enable',
				),
				'default'     => 'disable'
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Become an author page',
				'desc'    => 'Go to Pages -> Add new -> Assign this page to Become an author template -> Writing your terms and conditionals in this page, then assign this page to this field.',
				'name'    => 'wilcity_submission[become_an_author_page]',
				'required'=> array('toggle_become_an_author', '=', 'enable'),
				'id'      => 'become_an_author_page',
				'post_type' => 'page',
				'default' => ''
			),
			array(
				'type'    => 'select',
				'heading' => 'Toggle Debug',
				'name'    => 'wilcity_submission[toggle_debug]',
				'id'      => 'wilcity_submission_toggle_debug',
				'options' => array(
					'disable' => 'Disable',
					'enable'  => 'Enable',
				),
				'default'     => 'disable'
			),
			array(
				'type'    => 'text',
				'heading' => 'Your Brand (*)',
				'description' =>'The brand name will be shown on the PayPal payment page and it also used as Email Subject.',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[brandname]',
				'id'      => 'branding',
				'default' => 'Wiloke'
			),
			array(
				'type'    => 'select',
				'heading' => 'Approved Method',
				'name'    => 'wilcity_submission[approved_method]',
				'desc'    => 'What method is used to review?',
				'desc_status'=>'info',
				'id'      => 'approved_method',
				'default' => 'manual_review',
				'options' => array(
					'manual_review' => 'Manual Review',
					'auto_approved_after_payment' => 'Automatically Approval After Payment Success',
				)
			),
			array(
				'type'    => 'text',
				'heading' => 'Automatically Delete Unpaid Listing',
				'desc'    => 'A robot will automatically check all unpaid listings each day, and if a listing has been submitted more than x hours, it will be deleted. Leave empty to disable this feature.',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[delete_listing_conditional]',
				'id'      => 'delete_listing_conditional',
				'default' => 60
			),
			array(
				'type'    => 'select',
				'heading' => 'Edit Published Listing Type',
				'name'    => 'wilcity_submission[published_listing_editable]',
				'desc'    => 'Allow / Not allow editing a listing that has been published',
				'desc_status'=>'info',
				'id'      => 'published_listing_editable',
				'default' => 'disable',
				'options' => array(
					'allow_need_review'     => 'Editable and Need to review before re-publishing',
					'allow_trust_approved'  => 'Editable and immediately approved',
					'not_allow'             => 'Not allow editing'
				)
			),
			array(
				'text'    => 'Listings Expired Management',
				'type'    => 'header',
				'class'   => 'dividing',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'text',
				'heading' => 'Move listing to expired store after x days',
				'desc'    => 'When a listing is expired, It will be moved to expired store. It means this article will temporary removed from the front-end until the post author do a renew for it. Leave empty or 0 mean do it immediately.',
				'desc_status' => 'info',
				'name'    => 'wilcity_submission[move_listing_to_expired_store_after]',
				'id'      => 'wilcity_submission_move_listing_to_expired_store_after',
				'default' => 2
			),
			array(
				'type' => 'close_segment'
			),
			array(
				'type'    => 'open_segment',
				'class'   => ''
			),
			array(
				'text'    => 'Package Settings',
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'addlisting-plans',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Add Listing Plans',
				'desc'    => 'Deciding what plans will be shown on the Package page.',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[listing_plans]',
				'id'      => '',
				'multiple'=>true,
				'post_type'=>'listing_plan',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Default Plan For Free Listing Claim',
				'desc' => 'If you are using Free Claim, this setting is required. Once a listing claim is approved, this plan will be assigned to this listing',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[free_claim_listing_plan]',
				'id'      => '',
				'multiple'=>true,
				'post_type'=>'listing_plan',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Event Plans',
				'desc'    => 'Set the package leads to Pricing table. Please click on Wiloke Guide -> FAQs -> Setup Package page to know more.',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[event_plans]',
				'id'      => 'event_plans',
				'post_type'=>'listing_plan',
				'multiple' =>true,
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Default Plan For Free Event Claim',
				'desc' => 'If you are using Free Claim, this setting is required. Once a listing claim is approved, this plan will be assigned to this listing',
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[free_claim_event_plan]',
				'id'      => '',
				'multiple'=>true,
				'post_type'=>'listing_plan',
				'default' => ''
			),
			array(
				'type' => 'close_segment'
			),
			array(
				'type'    => 'open_segment',
				'class'   => ''
			),
			array(
				'text'    => 'Payment General Settings (*)',
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Mode', 'wiloke'),
				'name'    => 'wilcity_submission[mode]',
				'id'      => 'payment_mode',
				'options' => array(
					'sandbox' => esc_html__('Sandbox', 'wiloke'),
					'live'    => esc_html__('Live', 'wiloke')
				),
				'default' => 'sandbox'
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Currency Code', 'wiloke'),
				'name'    => 'wilcity_submission[currency_code]',
				'id'      => 'paypal_mode',
				'options' => array(
					'AUD' => 'AUD',
					'ARS' => 'ARS',
					'BRL' => 'BRL',
					'USD' => 'USD',
					'CAD' => 'CAD',
					'CHF' => 'CHF',
					'CZK' => 'CZK',
					'CNY' => 'CNY',
					'DKK' => 'DKK',
					'DZD' => 'DZD',
					'EGP' => 'EGP',
					'EUR' => 'EUR',
					'GTQ' => 'GTQ',
					'HKD' => 'HKD',
					'HUF' => 'HUF',
					'ILS' => 'ILS',
					'JPY' => 'JPY',
					'MYR' => 'MYR',
					'MXN' => 'MXN',
					'NOK' => 'NOK',
					'NZD' => 'NZD',
					'NPR' => 'NPR',
					'PHP' => 'PHP',
					'PLN' => 'PLN',
					'GBP' => 'GBP',
					'RUB' => 'RUB',
					'SGD' => 'SGD',
					'SEK' => 'SEK',
					'CHF' => 'CHF',
					'TWD' => 'TWD',
					'THB' => 'THB',
					'INR' => 'INR',
					'KWD' => 'KWD',
					'KRW' => 'KRW',
					'KZT' => 'KZT',
					'PEN' => 'PEN',
					'PKR' => 'PKR',
					'SAR' => 'SAR',
					'ZAR' => 'ZAR',
					'TRY' => 'TRY',
					'AED' => 'AED',
					'LKR' => 'LKR',
					'LEI' => 'LEI',
					'IDR' => 'IDR',
					'NGN' => 'NGN',
					'GH'  => 'GH',
					'VND' => 'VND'
				),
				'default' => 'USD'
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Currency position', 'wiloke'),
				'name'    => 'wilcity_submission[currency_position]',
				'id'      => 'currency_position',
				'options' => array(
					'left'          => esc_html__('Left $99.99', 'wiloke'),
					'right'         => esc_html__('Right 99.99$', 'wiloke'),
					'left_space'    => esc_html__('Left With Space $ 99.99', 'wiloke'),
					'right_space'   => esc_html__('Right With Space 99.99 $', 'wiloke'),
				),
				'default' => 'left'
			),
			array(
				'text'    => esc_html__('Payment gateways', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select_ui',
				'heading' => esc_html__('Accept Payment Via', 'wiloke'),
				'name'    => 'wilcity_submission[payment_gateways]',
				'id'      => 'payment_gateways',
				'multiple'=>true,
				'desc'    => esc_html__('Notice: Check Payment is not available for Recurring Payments'),
				'desc_status'=>'info',
				'default' => 'paypal,stripe',
				'options' => array(
					array(
						'value' => 'paypal',
						'text'  => esc_html__('PayPal', 'wiloke'),
						'img'   => plugin_dir_url(dirname(__FILE__)) . 'admin/img/paypal.png'
					),
					array(
						'value' => 'stripe',
						'text'  => esc_html__('Stripe', 'wiloke'),
						'img'   => plugin_dir_url(dirname(__FILE__)) . 'admin/img/checkpayment.png'
					),
					array(
						'value' => 'banktransfer',
						'text'  => esc_html__('Direct Bank Tranfer', 'wiloke'),
						'img'   => plugin_dir_url(dirname(__FILE__)) . 'admin/img/bank-transfer.png'
					),
					array(
						'value' => 'woocommerce',
						'text'  => 'WooCommerce (We can\'t not use the other gateways)',
						'img'   => plugin_dir_url(dirname(__FILE__)) . 'admin/img/woocommerce.png'
					)
				)
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Billing Type *', 'wiloke'),
				'name'    => 'wilcity_submission[billing_type]',
				'id'      => 'billing_type',
				'options' => array(
					'NonRecurringPayment' => 'Non-Recurring Payment',
					'RecurringPayment'    => 'Recurring Payment (Subscription)'
				),
				'default'     => 'default'
			),
			array(
				'type'    => 'close_segment'
			),
			array(
				'type'    => 'open_segment'
			),
			array(
				'text'    => esc_html__('Payment Pages (*)', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select_post',
				'heading' => esc_html__('Dashboard page', 'wiloke'),
				'name'    => 'wilcity_submission[dashboard_page]',
				'desc'    => 'Go to Pages -> Add New -> Enter in the page title, then set this page to Wilcity Dashboard Template -> Click Publish. Finally, assigning this page to the field.',
				'desc_status'=>'info',
				'id'      => 'dashboard_page',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Add Listing Mode', 'wiloke'),
				'desc'    => sprintf(__('<a href="%s" target="_blank">Tutorial: Setup Free Add Listing Mode</a>', 'wiloke'), 'https://blog.wiloke.com/enabling-free-add-listing-mode-listgo-1-5-x/'),
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[add_listing_mode]',
				'id'      => 'add_listing_mode',
				'options' => array(
					'free_add_listing'=> esc_html__('Free Add Listing', 'wiloke'),
					'select_plans'    => esc_html__('Select a plan before adding', 'wiloke'),
				),
				'default' => 'select_plans'
			),
			array(
				'type'    => 'select_post',
				'heading' => esc_html__('Package Page', 'wiloke'),
				'desc_status'=>'red',
				'desc'=>'To setup a Package page, please follow this instruction: From the admin sidebar, click on Pages -> Add New -> Set this page to Wilcity Package Page template. Next, click on Add New shortcode button -> Navigate to Wilcity tab -> Select Pricing Table shortcode and set Post Type to Depends on Listing Type Request',
				'name'    => 'wilcity_submission[package]',
				'id'      => 'package',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => esc_html__('Add Listing Page', 'wiloke'),
				'desc'    => esc_html__('This page tell Wiloke Submission where to add a listing on the front-end. To create an Add Listing Page, please click on Pages -> Add New -> Create a new page -> Assing that page to Add Listing Template', 'wiloke'),
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[addlisting]',
				'id'      => 'addlisting',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => 'Checkout Page',
				'desc'    => 'To create an Checkout Page, please click on Pages -> Add New -> Create a new page -> Assing that page to Add Checkout Template. Please read <a href="https://documentation.wilcity.com/knowledgebase/setting-up-checkout-page/" target="_blank">Setting Up Checkout page</a> to know more.',
				'name'    => 'wilcity_submission[checkout]',
				'desc_status'=>'info',
				'id'      => 'checkoutpage',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => esc_html__('Thank you Page', 'wiloke'),
				'desc'    => esc_html__('Once your customer paid to you, the browser will redirect to this page.', 'wiloke'),
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[thankyou]',
				'id'      => 'thankyou',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type'    => 'select_post',
				'heading' => esc_html__('Cancel Page', 'wiloke'),
				'desc'    => esc_html__('For example: When your customer click on Proceed to PayPal button but then he/she decided to cancel that session, then the browser will redirect to this page.', 'wiloke'),
				'desc_status'=>'info',
				'name'    => 'wilcity_submission[cancel]',
				'id'      => 'cancel',
				'post_type'=>'page',
				'default' => ''
			),
			array(
				'type' => 'close_segment'
			),
			array(
				'name' => '',
				'type' => 'open_segment'
			),
			array(
				'text'    => esc_html__('PayPal Settings', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'tag'     => 'h3'
			),
			array(
				'type'          =>  'desc',
				'desc_status'   =>  'info',
				'desc'          => __('<a href="https://blog.wiloke.com/setup-paypal-gateway-listgo-1-5-x/" target="_blank">How to Setup PayPal gateway In Listgo?</a>', 'wiloke'),
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Maximum Failed Payments (PayPal)', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_maximum_failed]',
				'id'      => 'paypal_maximum_failed',
				'default' => 3,
				'desc'    => esc_html__('Number of scheduled payments that can fail before the profile is automatically suspended.', 'wiloke'),
				'desc_status'=>'info'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Agreement Text (*)', 'wiloke'),
				'desc'    => esc_html__('This setting is required if you want to use PayPal Gateway', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_agreement_text]',
				'id'      => 'paypal_agreement_text',
				'default' => 'Agreement'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Initial Fee', 'wiloke'),
				'desc'    => esc_html__('This setting is available for Recurring Billing Type only', 'wiloke'),
				'name'    => 'wilcity_submission[initial_fee]',
				'id'      => 'paypal_initial_fee',
				'default' => ''
			),
			array(
				'text'    => esc_html__('PayPal Sandbox', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Client ID', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_sandbox_client_id]',
				'id'      => 'paypal_sandbox_client_id',
				'default' => ''
			),
			array(
				'type'    => 'password',
				'heading' => esc_html__('Secret Token', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_sandbox_secret]',
				'id'      => 'paypal_sandbox_secret',
				'default' => ''
			),
			array(
				'text'    => esc_html__('PayPal Live', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Client ID', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_live_client_id]',
				'id'      => 'paypal_live_client_id',
				'default' => ''
			),
			array(
				'type'    => 'password',
				'heading' => esc_html__('Secret Token', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_live_secret]',
				'id'      => 'paypal_live_secret',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Log File Name', 'wiloke'),
				'name'    => 'wilcity_submission[paypal_logfilename]',
				'id'      => 'paypal_logfilename',
				'default' => 'paypallog.txt'
			),
			array(
				'name' => '',
				'type' => 'close_segment'
			),

			array(
				'type'    => 'open_segment'
			),
			array(
				'type'    => 'open_segment'
			),
			array(
				'text'    => esc_html__('Stripe Settings', 'wiloke'),
				'type'    => 'header',
				'desc'    => __('<a href="https://blog.wiloke.com/learn-configure-stripe-gateway-listgo/" target="_blank">LEARN HOW TO CONFIGURE STRIPE GATEWAY IN LISTGO</a>', 'wiloke'),
				'desc_status'=>'info',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'tag'     => 'h3'
			),
			array(
				'heading' => esc_html__('Zero-decimal Currency *', 'wiloke'),
				'type'    => 'text',
				'name'    => 'wilcity_submission[stripe_zero_decimal]',
				'id'      => 'stripe_sandbox_secret_key',
				'desc'    => esc_html__('Stripe expects amounts to be provided in a currency’s smallest unit. For example, Plan A\'s cost $10 USD, We need to provide an amount value of 10*100 = 1000 (i.e, 1000 cents), so you should enter 100 in this setting. But in case, you are using JPY currency, because there is no decimal for JPY so ¥1 is the smallest currency unit, since you should enter 1 in this setting.', 'wiloke'),
				'desc_status'=>'info',
				'default' => 100
			),
			array(
				'text'    => esc_html__('Stripe API', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing',
				'tag'     => 'h4'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Publishable Key (*)', 'wiloke'),
				'name'    => 'wilcity_submission[stripe_publishable_key]',
				'id'      => 'stripe_publishable_key',
				'default' => ''
			),
			array(
				'type'    => 'password',
				'heading' => esc_html__('Secret Key (*)', 'wiloke'),
				'name'    => 'wilcity_submission[stripe_secret_key]',
				'id'      => 'stripe_secret_key',
				'default' => ''
			),
			array(
				'type'    => 'close_segment'
			),
			array(
				'type'    => 'open_segment'
			),
			array(
				'text'    => esc_html__('Direct Bank Transfer', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'desc' => __('<a href="https://blog.wiloke.com/learn-configure-direct-bank-transfer/" target="_blank">LEARN HOW TO CONFIGURE DIRECT BANK TRANSFER</a>', 'wiloke'),
				'desc_status'=>'info',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'open_fields_group',
				'class'   => 'six'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Name 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_name_1]',
				'id'      => 'bank_transfer_account_name_1',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Number 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_number_1]',
				'id'      => 'bank_transfer_account_number_1',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Bank Name 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_name_1]',
				'id'      => 'bank_transfer_name_1',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Short code 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_short_code_1]',
				'id'      => 'bank_transfer_short_code_1',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('IBAN 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_iban_1]',
				'id'      => 'bank_transfer_iban_1',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('BIC / Swift 1', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_swift_1]',
				'id'      => 'bank_transfer_swift_1',
				'default' => ''
			),
			array(
				'type'    => 'close'
			),
			array(
				'type'    => 'open_fields_group',
				'class'   => 'six'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Name 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_name_2]',
				'id'      => 'bank_transfer_account_name_2',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Number 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_number_2]',
				'id'      => 'bank_transfer_account_number_2',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Bank Name 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_name_2]',
				'id'      => 'bank_transfer_name_2',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Short code 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_short_code_2]',
				'id'      => 'bank_transfer_short_code_2',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('IBAN 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_iban_2]',
				'id'      => 'bank_transfer_iban_2',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('BIC / Swift 2', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_swift_2]',
				'id'      => 'bank_transfer_swift_2',
				'default' => ''
			),
			array(
				'type'    => 'close'
			),
			array(
				'type'    => 'open_fields_group',
				'class'   => 'six'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Name 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_name_3]',
				'id'      => 'bank_transfer_account_name_3',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Number 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_number_3]',
				'id'      => 'bank_transfer_account_number_3',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Bank Name 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_name_3]',
				'id'      => 'bank_transfer_name_3',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Short code 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_short_code_3]',
				'id'      => 'bank_transfer_short_code_3',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('IBAN 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_iban_3]',
				'id'      => 'bank_transfer_iban_3',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('BIC / Swift 3', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_swift_3]',
				'id'      => 'bank_transfer_swift_3',
				'default' => ''
			),
			array(
				'type'    => 'close'
			),
			array(
				'type'    => 'open_fields_group',
				'class'   => 'six'
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Name 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_name_4]',
				'id'      => 'bank_transfer_account_name_4',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Account Number 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_account_number_4]',
				'id'      => 'bank_transfer_account_number_4',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Bank Name 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_name_4]',
				'id'      => 'bank_transfer_name_4',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('Short code 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_short_code_4]',
				'id'      => 'bank_transfer_short_code_4',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('IBAN 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_iban_4]',
				'id'      => 'bank_transfer_iban_4',
				'default' => ''
			),
			array(
				'type'    => 'text',
				'heading' => esc_html__('BIC / Swift 4', 'wiloke'),
				'name'    => 'wilcity_submission[bank_transfer_swift_4]',
				'id'      => 'bank_transfer_swift_4',
				'default' => ''
			),
			array(
				'type'    => 'close'
			),
			array(
				'type'    => 'close_segment'
			),
			array(
				'type'    => 'open_segment'
			),
			array(
				'type' => 'submit',
				'name' => esc_html__('Submit', 'wiloke')
			),
			array(
				'type'    => 'close_segment'
			)
		)
	),
	'currencySymbol' => array(
		'AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&fnof;',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '₹',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
		'ISK' => 'kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x441;&#x43e;&#x43c;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'LEI' => 'LEI',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'MDL',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'Fr',
		'XCD' => '&#36;',
		'XOF' => 'Fr',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
		'GH' => 'ZK',
	),
	'can_u'
];