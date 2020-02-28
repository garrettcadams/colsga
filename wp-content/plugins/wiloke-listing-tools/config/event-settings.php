<?php
return [
	'default-fields' => '{"settings":[{"isDefault":"true","type":"listing_title","key":"listing_title","icon":"la la-file-text","heading":"Event Title","fields":{"listing_title":{"heading":"Settings","type":"text","desc":"","key":"listing_title","fields":[{"heading":"Label","type":"text","key":"label","label":"Listing Title"}],"label":"Event Title"}}},{"isDefault":"true","type":"featured_image","icon":"la la-img","key":"featured_image","heading":"Featured Image","fields":{"featured_image":{"heading":"Settings","type":"single_image","desc":"","key":"featured_image","fields":[{"heading":"Label name","type":"text","desc":"","key":"label","label":"Featured Image"},{"heading":"Is Required?","type":"checkbox","desc":"","key":"isRequired","isRequired":"yes"}],"label":"Featured Image"}}},{"isCustomSection":"yes","type":"text","key":"content","icon":"la la-magic","heading":"Content","fields":{"settings":{"heading":"Settings","type":"text","desc":"","key":"settings","fields":[{"heading":"Is Required?","type":"checkbox","desc":"","key":"isRequired","isRequired":"no"}]}}},{"isDefault":"true","type":"listing_address","key":"listing_address","icon":"la la-globe","heading":"Listing Address","fields":{"address":{"heading":"Google Address","type":"map","desc":"","key":"address","fields":[{"heading":"Label","type":"text","key":"label","label":"Google Address"},{"heading":"Set Default Starting Location","type":"text","desc":"Leave empty to use visitor&#039;s location as the default","key":"defaultLocation","defaultLocation":""},{"heading":"Default Zoom","type":"text","key":"defaultZoom","defaultZoom":"8"},{"heading":"Is Required?","type":"checkbox","desc":"","key":"isRequired","isRequired":"yes"}],"label":"Enter google address","isRequired":"yes"},"listing_location":{"heading":"Region","type":"select2","desc":"","key":"listing_location","fields":[{"heading":"Is Enable?","type":"checkbox","desc":"","key":"isEnable","isEnable":"yes"},{"heading":"Label","type":"text","key":"label","label":"Region"},{"heading":"Is Required?","type":"checkbox","desc":"","key":"isRequired","isRequired":"yes"},{"heading":"Order by","desc":"Get all tags ordered by","type":"select","key":"orderBy","orderBy":"term_id","options":{"name":"name","count":"count","slug":"slug","term_id":"term_id"}}],"isEnable":"yes","label":"Region","orderBy":""}},"isNotDeleteAble":"true"},{"isDefault":"true","type":"event_calendar","key":"event_calendar","icon":"la la-certificate","heading":"Event Calendar","fields":{"event_calendar":{"heading":"Settings","type":"event_calendar","desc":"","key":"event_calendar","fields":[{"heading":"Label Name","type":"text","desc":"","key":"label","label":"Frequency"},{"heading":"Is Required?","type":"checkbox","desc":"","key":"isRequired","isRequired":"yes"}],"label":"Calendar","isRequired":"yes"}},"isNotDeleteAble":"true"},{"type":"event_belongs_to_listing","key":"video","heading":"Event Belongs To","desc":"","icon":"la la-certificate","fields":{"event_belongs_to_listing":{"type":"select2","value":"","label":"Listing Parent","isRequired":"no","ajaxAction":"wilcity_fetch_post","ajaxArgs":{"postType":"listing"},"isAjax":"yes"},"videos":{"type":"video","value":"","addMoreBtnName":"Add More","placeholder":"Video Link","isRequired":"yes"}}}],"postType":"event","settingType":"fields"}',
	'general' => array(
		'toggle_event' => 'enable',
		'toggle' => 'enable',
		'toggle_comment_discussion' => 'enable',
		'immediately_approved' => 'enable'
	),
	'keys' => array(
		'general' => 'event_general_settings'
	),
	'designFields' => array(
		'usedSectionKey' => 'add_event_sections'
	),
	'aFrequencies' => array(
		'occurs_once' => esc_html__('Occurs Once', 'wiloke-listing-tools'),
		'daily' => esc_html__('Daily', 'wiloke-listing-tools'),
		'weekly' => esc_html__('Weekly', 'wiloke-listing-tools'),
	)
];