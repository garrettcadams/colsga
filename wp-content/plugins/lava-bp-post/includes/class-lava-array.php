<?php

if( ! class_exists( 'lava_array' ) ) {

	class lava_array {

		public $options = Array();

		public function __construct( $values=Array() ) {
			if( is_array( $values ) ) {
				$this->setVariables( $values );
			}
		}

		public function setVariables( $values=Array() ) {
			$this->options = $values;
		}

		public function __get( $key='' ) {
			$default = false;

			if( empty( $key ) || ! is_array( $this->options ) ) {
				return $default;
			}

			if( array_key_exists( $key, $this->options ) ) {
				if( is_numeric( $this->options[ $key ] ) ) {
					return $this->options[ $key ];
				}else{
					if( !empty( $this->options[ $key ] ) ) {
						$default = $this->options[ $key ];
					}
				}
			}
			return $default;
		}

		public function get( $name='', $default=false ){
			$value = $this->__get( $name );
			return $value !== false ? $value : $default;
		}
	}
}