<?php
/*
 |--------------------------------------------------------------------------
 | Theme Options
 |--------------------------------------------------------------------------
 | Configurations and handles
 |
 */
$this->_loader->add_action('init', $this->instThemeOptions, 'render');
$this->_loader->add_action('init', $this->instThemeOptions, 'update_theme_options');