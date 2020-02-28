<?php
return [
	'aMainMenu' => array(
		'homeStack'    => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'canClone'  => 'no',
				'isDefault' => 'yes',
				'key'       => 'homeStack',
				'heading'   => 'Home'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'label'     => 'Key',
					'desc'      => 'This key is fixed.',
					'value'     => 'home'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Home'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'home'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'accountStack' => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'canClone'  => 'no',
				'isDefault' => 'yes',
				'key'       => 'accountStack',
				'heading'   => 'Account'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'label'     => 'Key',
					'desc'      => 'This key is fixed.',
					'value'     => 'account'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Account'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'user'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'listingStack' => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'canClone'  => 'yes',
				'key'       => 'listingStack',
				'heading'   => 'Listing'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-ajax-search-field',
					'name'      => 'key',
					'value'     => 'listing',
					'label'     => 'Directory Key',
					'action'    => 'wilcity_get_directory_key'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Listing'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'map-pin'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'eventStack'   => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'canClone'  => 'no',
				'key'       => 'eventStack',
				'heading'   => 'Event'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-ajax-search-field',
					'name'      => 'key',
					'value'     => 'event',
					'label'     => 'Directory Key',
					'action'    => 'wilcity_get_listing_directory_key'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Event'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'calendar'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'blogStack'    => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'blogStack',
				'key'       => 'blogStack',
				'heading'   => 'Blog Stack'
			),
			'aFields'  => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'value'     => 'posts',
					'label'     => 'Page Name',
					'action'    => 'wilcity_get_page_id'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Blog Stack'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'map-pin'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
//		'pageStack'    => array(
//			'oGeneral' => array(
//				'class'     => 'fields five',
//				'key'       => 'pageStack',
//				'screen'    => 'pageStack',
//				'heading'   => 'Page Stack'
//			),
//			'aFields'  => array(
//				array(
//					'component' => 'wiloke-ajax-search-field',
//					'name'      => 'key',
//					'value'     => '',
//					'label'     => 'Page Name',
//					'action'    => 'wilcity_get_page_id'
//				),
//				array(
//					'component' => 'wiloke-input',
//					'name'      => 'name',
//					'value'     => 'Page Stack'
//				),
//				array(
//					'component' => 'wiloke-icon',
//					'name'      => 'iconName',
//					'label'     => 'Icon',
//					'value'     => 'map-pin'
//				),
//				array(
//					'component' => 'wiloke-select',
//					'name'      => 'status',
//					'value'     => 'enable',
//					'label'     => 'Toggle Menu',
//					'aOptions'   => array(
//						array(
//							'name'  => 'Disable',
//							'value' => 'disable'
//						),
//						array(
//							'name' => 'Enable',
//							'value'=> 'enable'
//						)
//					)
//				)
//			)
//		),
		'menuStack' => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'canClone'  => 'no',
				'key'       => 'menuStack',
				'heading'   => 'Secondary Menu Stack'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'label'     => 'Key',
					'desc'      => 'This key is fixed.',
					'value'     => 'menu'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'label'     => 'Name',
					'value'     => 'Menu'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'la la-bars'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		)
	),
	'aSecondaryMenu' => array(
		'homeStack'     => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'homeStack',
				'heading'   => 'Home Stack'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'label'     => 'Key',
					'desc'      => 'This key is fixed.',
					'value'     => 'key'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'value'     => 'Home'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'home'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'listingStack' => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'listingStack',
				'heading'   => 'Listing Stack'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-ajax-search-field',
					'name'      => 'key',
					'value'     => 'listing',
					'label'     => 'Directory Key',
					'action'    => 'wilcity_get_listing_directory_key'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'value'     => 'Listing'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'map-pin'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'eventStack'   => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'eventStack',
				'heading'   => 'Event Stack'
			),
			'aFields' => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'value'     => 'event',
					'label'     => 'Event Key',
					'desc'      => 'This key is fixed'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'value'     => 'Event'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'calendar'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'pageStack'    => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'pageStack',
				'heading'   => 'Page Stack'
			),
			'aFields'  => array(
				array(
					'component' => 'wiloke-ajax-search-field',
					'name'      => 'key',
					'value'     => '',
					'label'     => 'Page Name',
					'action'    => 'wilcity_get_page_id'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'value'     => 'Page Stack'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'map-pin'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
		'blogStack'    => array(
			'oGeneral' => array(
				'class'     => 'fields five',
				'screen'    => 'blogStack',
				'heading'   => 'Blog Stack'
			),
			'aFields'  => array(
				array(
					'component' => 'wiloke-input-read-only',
					'name'      => 'key',
					'value'     => 'posts',
					'label'     => 'Page Name',
					'action'    => 'wilcity_get_page_id'
				),
				array(
					'component' => 'wiloke-input',
					'name'      => 'name',
					'value'     => 'Blog Stack'
				),
				array(
					'component' => 'wiloke-icon',
					'name'      => 'iconName',
					'label'     => 'Icon',
					'value'     => 'map-pin'
				),
				array(
					'component' => 'wiloke-select',
					'name'      => 'status',
					'value'     => 'enable',
					'label'     => 'Toggle Menu',
					'aOptions'   => array(
						array(
							'name'  => 'Disable',
							'value' => 'disable'
						),
						array(
							'name' => 'Enable',
							'value'=> 'enable'
						)
					)
				)
			)
		),
	)
];