<?php
return [
	'configuration' => array(
		'fields' => array(
			array(
				'type' => 'open_segment'
			),
			array(
				'text'    => esc_html__('General Settings', 'wiloke'),
				'type'    => 'header',
				'class'   => 'dividing toggle-anchor',
				'id'      => 'general-settings-header',
				'tag'     => 'h3'
			),
			array(
				'type'    => 'select',
				'heading' => esc_html__('Toggle Paid Claim', 'wiloke'),
				'name'    => 'wilcity_pc[toggle]',
				'id'      => 'wilcity_pc_toggle',
				'options' => array(
					'disable' => esc_html__('Disable', 'wiloke'),
					'enable'  => esc_html__('Enable', 'wiloke'),
				),
				'default'     => 'disable'
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
	)
];