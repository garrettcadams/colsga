<?php
namespace jvbpdelement;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

final class Manager {
	private $modules = [];

	public function __construct() {
		$modules = [
			'carousel',
			'block',
			'meta',
			'review',
			'button',
			'slider',
			'testimonial',
			'userforms',
		];

		foreach ( $modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\Module';

			/** @var Module_Base $class_name */
			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}
	}

	public function get_modules( $module_name ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}
		return $this->modules;
	}
}
