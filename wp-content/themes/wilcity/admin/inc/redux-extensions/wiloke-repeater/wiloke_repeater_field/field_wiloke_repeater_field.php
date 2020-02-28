<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_wiloke_repeater_field' ) )
{

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wiloke_repeater_field extends ReduxFramework
    {
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         */
        public function __construct( $field = array(), $value ='', $parent )
        {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit(str_replace('\\', '/', get_template_directory() . '/admin/inc/redux-extensions/wiloke-repeater/wiloke_repeater_field'));
                $this->extension_url = site_url(str_replace(trailingslashit(str_replace('\\', '/', ABSPATH)), '', $this->extension_dir));
            }
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */

        public function render()
        {
            global $wiloke;

            $classNamePrefix = 'ReduxFramework_';

            $classes = isset($this->field['sortable']) && $this->field['sortable'] ? 'wiloke-redux-repeater-wrapper wiloke-sortable' : 'wiloke-redux-repeater-wrapper wiloke-no-sortable';

            $aDefault = $this->field;
            unset($aDefault['id']);
            unset($aDefault['fields']);

            echo '<div class="'.esc_attr($classes).'">';
                if ( !empty($this->value) ) {
                    $i = 0;
                    foreach ( $this->value as $value ) {
                        $this->render_item($value, $aDefault, $i, $classNamePrefix);
                        $i++;
                    }
                }else{
                    $this->render_item('', $aDefault, 0, $classNamePrefix);
                }
            echo '</div>';
            echo '<button class="button button-primary wiloke-redux-add-new-group">'.esc_html__('Add New', 'wilcity').'</button>';
            echo '</div>';
        }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue()
        {
            wp_enqueue_script('jquery-sortable');
            wp_enqueue_script(
                'wiloke-redux-repeater-js',
                WILOKE_AD_REDUX_URI . 'wiloke-repeater/wiloke_repeater_field/source/script.js',
                array('jquery'),
                WILOKE_THEMEVERSION,
                true
            );
        }

        public function render_item($value='', $aDefault, $i, $classNamePrefix){
            echo '<div class="wiloke-redux-group-item">';
                echo '<div class="wiloke-redux-item-fields">';
                    foreach ( $this->field['fields'] as $order => $aField )
                    {
                        $fieldVal = '';
                        if ( isset($value[$aField['id']]) ) {
                            $fieldVal = $value[$aField['id']];
                        }elseif ( isset($aField['default']) ) {
                            $fieldVal = $aField['default'];
                        }

                        $aField['name']        = $this->generate_name($aField['id'], $i);
                        $aField['name_suffix'] = '';
                        $aField['id']          = uniqid($aField['id']);
                        $aField = wp_parse_args($aField, $aDefault);

                        $className = $classNamePrefix . $aField['type'];

                        if (class_exists($className))
                        {
                            echo '<fieldset class="wiloke-field redux-field redux-field-container redux-field-init redux-container-'.esc_attr($aField['type']).'" style="padding: 5px;" data-type='.esc_attr($aField['type']).'>';

                                if (isset($aField['title'])) {
                                    echo ' <strong style="width: 150px; display: inline-block;">' . esc_html($aField['title']) . '</strong>';
                                }
                                $instance = new $className($aField, $fieldVal, $this->parent);
                                $instance->render();

                            echo '</fieldset>';
                        }
                    }
                    echo '<a href="#" class="wiloke-redux-delete"><span class="dashicons dashicons-no"></span></a>';
                echo '</div>';
            echo '</div>';
        }

        public function generate_name($namePrefix, $order)
        {
            return $this->field['name'] . '['.$order.'][' . $namePrefix . ']';
        }

        public function return_value($aValue, $aInfo, $key)
        {
            if ( isset($aInfo['name']) )
            {
                $value  = $aValue . '[' . $aInfo['name'] . ']';
            }else{
                $value  = $aValue[$key];
            }
            return $value;
        }

        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function output()
        {
            if ( $this->field['enqueue_frontend'] ) {

            }
        }
    }
}
