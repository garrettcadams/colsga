<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit;
}

if ( !class_exists('ReduxFramework_Extension_Wiloke_Description') )
{
    class ReduxFramework_Extension_Wiloke_Description extends ReduxFramework
    {
        protected $parent;

        public function __construct( $parent ) {

        }
    }
}