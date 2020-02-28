<?php

/**
 * Register all actions, filters for this plugin.
 * Maintain a list of hooks and filters, then register them and run with WP API
 *
 * @link        http://demo.wiloke.com
 * @since       1.0
 * @package     WilokeFrameWork
 * @subpackage  WilokeFrameWork/admin/inc
 * @author      Wiloke Team
 */


/**
 * Ensure that Wiloke Shortcode is exist or no
 */
if ( class_exists('WilokeLoader') )
{
    return;
}

class WilokeLoader
{
    /**
     * An array of actions
     * @since 1.0
     * @access protected
     * @var array $_actions The actions will be registered with WordPress to fire when the plugin is loaded
     */
    protected $_aActions;

    /*
     * An array of filters
     * @since 1.0
     * @access protected
     * @var array $_filters The filters will also be registered with WordPress
     */
    protected $_aFilters;

    /**
     * Initialize the collections used to maintain the actions, and filters
     * @since 1.0
     */
    public function __construct()
    {
        $this->_aActions = array();
        $this->_aFilters = array();
    }


    /**
     * Add a single action into the action collection
     * @since 1.0
     */
    public function add_action($hook, $component, $callback, $priority=10, $acceptedArgs=1)
    {
        $this->_aActions = $this->add($this->_aActions, $hook, $component, $callback, $priority, $priority, $acceptedArgs);
    }

    /**
     * Add a single filter into the filter collection
     * @since 1.0
     */
    public function add_filter($hook, $component, $callback, $priority=10, $acceptedArgs=1)
    {
        $this->_aFilters = $this->add($this->_aFilters, $hook, $component, $callback, $priority, $priority, $acceptedArgs);
    }


    /**
     * Create params for the action / filter
     * @access private
     * @since 1.0
     *
     * @param array     $hooks      The collection of hooks that is being registerd
     * @param string    $hook       The name of hook will be hooked
     * @param object    $component  A reference to the instance of object on which filters/actions was defined
     * @param string    $callback   The name of property will be callback
     * @param int       $priority   Set order for this hook (Optional)
     * @param int       $acceptedArgs Optional
     * @return array
     */
    private function add($hooks, $hook, $component, $callback, $priority, $acceptedArgs)
    {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $acceptedArgs,
        );

        return $hooks;
    }

    /**
     * Register the filter, action with WordPress
     * @since 1.0
     */
    public function run()
    {
        if ( !empty($this->_aActions) )
        {
            foreach ( $this->_aActions as $aHook )
            {
                add_action( $aHook['hook'], array($aHook['component'], $aHook['callback']), $aHook['priority'], $aHook['accepted_args'] );
            }
        }

        if ( !empty($this->_aFilters) )
        {
            foreach ( $this->_aFilters as $aFilter )
            {
                add_action( $aFilter['hook'], array($aFilter['component'], $aFilter['callback']), $aFilter['priority'], $aFilter['accepted_args'] );
            }
        }
    }

}