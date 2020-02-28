<?php
/**
 * WilokeNavMenu Class
 *
 * @category Menu
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
	exit;
}

class WilokeNavMenu
{
	public function __construct()
	{
		add_action('init', array($this, 'register_menu'));
	}

	public function register_menu()
	{
		global $wiloke;

		if ( isset($wiloke->aConfigs['frontend']['register_nav_menu']['menu']) )
		{
			foreach ( $wiloke->aConfigs['frontend']['register_nav_menu']['menu'] as $aMenu )
			{
				register_nav_menu($aMenu['key'], $aMenu['name']);
			}

		}

		if ( isset($wiloke->aConfigs['frontend']['register_nav_menus']) )
		{
			register_nav_menu($wiloke->aConfigs['frontend']['register_nav_menus'], '');
		}

	}
}

