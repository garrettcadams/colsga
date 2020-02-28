<?php
namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

class CustomCMB2Fields {
	use CustomFieldTools;

	public $aParseName;

	public function __construct() {
		add_filter('cmb2_render_wiloke_map', array( $this, 'renderPWMap' ), 10, 5);
		add_filter('cmb2_render_wiloke_field', array( $this, 'renderWilokeField' ), 10, 5);
		add_filter('cmb2_render_select2_user', array($this, 'renderSelect2User'), 10, 5);
		add_filter('cmb2_render_select2_posts', array($this, 'renderSelect2Posts'), 10, 5);

		add_action('save_post', array( $this, 'saveMetaBox' ), 10, 1);
		add_action('wp_ajax_wiloke_select_user', array($this, 'fetchUsers'));
		add_action('wp_ajax_wiloke_fetch_posts', array($this, 'fetchPosts'));

		add_filter('cmb2_render_wiloke_select2_ajax', array( $this, 'renderWilokeSelect' ), 10, 5 );
		add_filter('cmb2_render_wiloke_multiselect2_ajax', array( $this, 'renderWilokeMultipleSelect' ), 10, 5 );
		add_filter('cmb2_sanitize_wiloke_multiselect2_ajax', array( $this, 'renderWilokeMultiselectSanitize' ), 10, 4 );
		add_filter('cmb2_types_esc_wiloke_multiselect2_ajax', array( $this, 'renderWilokeMultiselectEscapedValue' ), 10, 3 );

		add_action('admin_enqueue_scripts', array($this, 'enqueueSelect2Scripts'));
	}

	public function pwSelect2Scripts() {
		wp_register_script( 'select2', WILOKE_LISTING_TOOL_URL . 'admin/assets/select2/select2.min.js', array( 'jquery' ), '4.0.3' );
		wp_enqueue_script( 'pw-select2-init', WILOKE_LISTING_TOOL_URL . 'admin/source/js/pw-select2.js', array( 'select2' ), WILOKE_LISTING_TOOL_VERSION);
		wp_enqueue_style( 'select2', WILOKE_LISTING_TOOL_URL . 'admin/assets/select2/select2.min.css', array(), '4.0.3' );
	}

	public function renderWilokeSelect($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$this->pwSelect2Scripts();
		$field_type_object->type = new \CMB2_Type_Select( $field_type_object );
		echo $field_type_object->select( array(
			'class'            => 'pw_select2 pw_select',
			'desc'             => $field_type_object->_desc( true ),
			'options'          => '<option></option>' . $field_type_object->concat_items(),
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
			'data-action'      => $field->args('action'),
			'data-args'        => $field->args('args')
		) );
	}

	public function get_pw_multiselect_options($field_escaped_value = array(), $field_type_object){
		if ( empty($field_escaped_value) ){
			return $field_escaped_value;
		}

		$selected_items = '';
		foreach ($field_escaped_value as $optVal){
			$option = array(
				'value' => $optVal,
				'label' => $optVal,
				'checked' => true
			);
			$selected_items .= $field_type_object->select_option( $option );
		}
		return $selected_items;
	}

	public function renderWilokeMultipleSelect($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$this->pwSelect2Scripts();
		$field_type_object->type = new \CMB2_Type_Select( $field_type_object );

		$a = $field_type_object->parse_args( 'pw_multiselect', array(
			'multiple'         => 'multiple',
			'style'            => 'width: 99%',
			'class'            => 'pw_select2 pw_multiselect',
			'name'             => $field_type_object->_name() . '[]',
			'id'               => $field_type_object->_id(),
			'desc'             => $field_type_object->_desc( true ),
			'options'          => $this->get_pw_multiselect_options( $field_escaped_value, $field_type_object ),
			'data-action'      => $field->args('action'),
			'data-args'        => $field->args('args')
		) );

		$attrs = $field_type_object->concat_attrs( $a, array( 'desc', 'options' ) );
		echo sprintf( '<select%s>%s</select>%s', $attrs, $a['options'], $a['desc'] );
	}

	public function renderWilokeMultiselectSanitize($check, $meta_value, $object_id, $field_args){
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[$key] = array_map( 'sanitize_text_field', $val );
		}

		return $meta_value;
	}

	public function renderWilokeMultiselectEscapedValue($check, $meta_value, $field_args){
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[$key] = array_map( 'esc_attr', $val );
		}

		return $meta_value;
	}

	public function fetchPosts(){
		$aEmpty = array(
			'text' => '-----------',
			'id'   => ''
		);

		if ( empty($_GET['q']) || !isset($_GET['post_types']) || empty($_GET['post_types']) ){
			wp_send_json_success(array($aEmpty));
		}
		global $wpdb;

		$s = $wpdb->_real_escape($_GET['q']);

		$postTypes = str_replace(array('directoryTypes', 'postTypes'), array('', ''), $_GET['post_types']);
		if ( strpos(':', $postTypes) !== false ){
			$rawPostTypes = explode(':', $postTypes);
		}else{
			$rawPostTypes = explode(',', $postTypes);
		}

		$aPostTypes = array();
		foreach ($rawPostTypes as $key => $postType){
			$aPostTypes[] = $wpdb->_real_escape(trim($postType));
		}

		$tblPosts = $wpdb->posts;
		$aResults = $wpdb->get_results($wpdb->prepare(
			"SELECT ID, post_title FROM $tblPosts WHERE post_title LIKE %s AND post_type IN('".implode("','", $aPostTypes)."')",
			'%'.$s.'%'
		));

		if ( empty($aResults) ){
			wp_send_json_success(array($aEmpty));
		}

		$aOptions = array();
		foreach ($aResults as $key => $oResult){
			$aOptions[$key]['text'] = $oResult->post_title;
			$aOptions[$key]['id']   = $oResult->ID;
		}

		$aOptions = array_merge(array($aEmpty), $aOptions);

		wp_send_json_success(array(
			'results' => $aOptions
		));
	}

	public function fetchUsers(){
		if ( empty($_GET['q']) ){
			wp_send_json_error();
		}
		global $wpdb;
		$s = $wpdb->_real_escape($_GET['q']);

		$aResults = GetSettings::getTransient('wilcity_fetch_post_keywords_'.$s);
		if ( empty($aResults) ){
			$tblUser = $wpdb->users;
			$aResults = $wpdb->get_results("SELECT ID, display_name FROM $tblUser WHERE user_login LIKE '%".$s."%' OR display_name LIKE '%".$s."%'");

			if ( empty($aResults) ){
				wp_send_json_error();
			}else{
				SetSettings::setTransient('wilcity_fetch_post_keywords_'.$s, $aResults, 600);
			}
		}
		$aOptions = array();
		foreach ($aResults as $key => $oResult){
			$aOptions[$key]['text'] = $oResult->display_name;
			$aOptions[$key]['id']   = $oResult->ID;
		}

		wp_send_json_success(array(
			'results' => $aOptions
		));
	}

	public function enqueueMapScripts() {
		global $wiloke;
		$key = '';


		if ( isset($wiloke->aThemeOptions['map_type']) && $wiloke->aThemeOptions['map_type'] == 'mapbox' ){
			if ( !isset($wiloke->aThemeOptions['mapbox_api']) || empty($wiloke->aThemeOptions['mapbox_api']) ){
				return false;
			}

			wp_register_script( 'mapbox-gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js');
			wp_register_script( 'mapbox-gl-geocoder', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.6/mapbox-gl-geocoder.min.js');

			wp_enqueue_style( 'maps-places-api', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css', null, null );
			wp_enqueue_style( 'mapbox-gl-geocoder', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.6/mapbox-gl-geocoder.css', null, null );
			wp_enqueue_script( 'pw-custom-map', plugin_dir_url(__FILE__) . 'assets/js/custom-mapbox.js', array( 'mapbox-gl', 'mapbox-gl-geocoder' ), WILOKE_LISTING_TOOL_VERSION, true );
			wp_enqueue_style( 'pw-google-maps', plugin_dir_url(__FILE__)  . 'assets/css/style.css', array(), WILOKE_LISTING_TOOL_VERSION );
			wp_localize_script('mapbox-gl', 'WILOKE_MAPBOX', array('api'=>$wiloke->aThemeOptions['mapbox_api']));

		}else{
			if ( !isset($wiloke->aThemeOptions['general_google_api']) ){
				$aThemeOptions = class_exists('\Wiloke') ? \Wiloke::getThemeOptions(true) : array();
				if ( isset($aThemeOptions['general_google_api']) ){
					$key = $aThemeOptions['general_google_api'];
				}
			}else{
				$key = $wiloke->aThemeOptions['general_google_api'];
			}

			wp_register_script( 'maps-places-api', 'https://maps.googleapis.com/maps/api/js?key='.$key.'&libraries=places', null, null );

			wp_enqueue_script( 'pw-custom-map', plugin_dir_url(__FILE__) . 'assets/js/custom-map.js', array( 'maps-places-api' ), WILOKE_LISTING_TOOL_VERSION );
			wp_enqueue_style( 'pw-google-maps', plugin_dir_url(__FILE__)  . 'assets/css/style.css', array(), WILOKE_LISTING_TOOL_VERSION );
		}
	}

	public function enqueueSelect2Scripts() {
		wp_enqueue_style( 'select2', WILOKE_LISTING_TOOL_URL . 'admin/assets/select2/select2.min.css');
		wp_enqueue_script( 'select2', WILOKE_LISTING_TOOL_URL  . 'admin/assets/select2/select2.min.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true );
		wp_enqueue_script( 'select2-field', plugin_dir_url(__FILE__)  . 'assets/js/select2-field.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true );
	}

	public function renderSelect2User($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$aField = array(
			'type'       => 'select',
			'id'         => $field->args('_name'),
			'name'       => $field->args('_name'),
			'value'      => $field_escaped_value
		);
		if ( $field->args('desc') ){
			$aField['desc'] = '<p style="margin-top: 10px;"><i>'.$field->args('desc').'</i></p>';
		}

		$aField['class'] =  'cmb2-select2 wiloke-select2';
		$userID = GetSettings::getPostMeta($field_object_id, $field->args('_name'));

		if ( !empty($userID) ){
			$oUser = get_user_by( 'id', $userID);
		}

		if ( isset($oUser) && !empty($oUser) ){
			$aField['options']  = '<option selected value="'.esc_attr($oUser->ID).'">'.esc_html($oUser->user_login).'</option>';
		}else{
			$aField['options']  = '<option value="">---------------</option>';
		}

		echo $field_type_object->select($aField);
	}

	public function renderSelect2Posts($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$aField = array(
			'type'       => 'select',
			'id'         => $field->args('_name'),
			'name'       => $field->args('multiple') ? $field->args('_name') . '[]' : $field->args('_name'),
			'value'      => $field_escaped_value,
			'desc'       => ''
		);
		if ( $field->args('multiple') ){
			$aField['multiple'] = 'multiple';
		}

		if ( $field->args('show_link') ){
			$aField['desc'] = '<p><a href="'.add_query_arg(array('action'=>'edit', 'post'=>$field_escaped_value), admin_url('post.php')).'" target="_blank">Click me to go to the post</a></p>';
		}

		$aField['class'] =  'cmb2-select2 wiloke-select2';

		$filterKey = 'wiloke-listing-tools/'.General::detectPostType().'/render-select2-posts-value/'.$field->args('_name');
		if ( has_filter($filterKey) ){
			$val = apply_filters($filterKey, $field_object_id);
		}else{
			$val = GetSettings::getPostMeta($field_object_id, $field->args('_name'));
		}

		if ( empty($val) ){
			$val = $aField['value'];
		}
		$aPosts = array();

		if ( !$field->args('multiple') ){
			if ( !empty($val) ){
				$aPosts[] = get_post($val);
			}
		}else{
			if ( !empty($val) ){
				$aParseVal = is_array($val) ? $val : explode(',', $val);
				foreach ($aParseVal as $postID){
					$aPosts[] = get_post($postID);
				}

			}
		}

		$aField['options'] = '';

		if ( !empty($aPosts) && !is_wp_error($aPosts) ){
			foreach ($aPosts as $oPost){
				$aField['options']  .= '<option selected value="'.esc_attr($oPost->ID).'">'.esc_html($oPost->post_title).'</option>';
			}

		}else{
			$aField['options']  = '<option value="">---------------</option>';
		}

		$aField['value'] = is_array($val) ? implode(',', $val) : $val;
		echo $field_type_object->select($aField);
		$field_type_object->_desc( true, true );
	}

	public function renderPWMap($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$this->enqueueMapScripts();
		$aResult = apply_filters('wiloke-listing-tools/map-field-values', $field_object_id);

		echo '<div class="wil-address-setting">';
			echo '<label style="display: inline;">Listing Address</label>';
			echo $field_type_object->input( array(
				'type'       => 'text',
				'name'       => $field->args('_name') . '[address]',
				'value'      => stripslashes($aResult['address']),
				'class'      => 'large-text pw-map-search',
				'desc'       => '',
			));

			if ( \WilokeThemeOptions::getOptionDetail('map_type') == 'mapbox' ){
				echo '<div id="pw-geocoder" class="pw-geocoder geocoder" data-lat="'.esc_attr($aResult['lat']).'" data-lng="'.esc_attr($aResult['lng']).'" data-address="'.stripslashes($aResult['address']).'"></div>';
			}

		echo '</div>';

		echo '<div id="pw-map" class="pw-map"></div>';

		$field_type_object->_desc( true, true );

		echo '<div class="wil-lat-lng-settings" style="margin-top: 30px;">';
			echo '<div class="wil-lat-setting" style="display: inline">';
				echo '<label>Latitude: </label>';
				echo $field_type_object->input( array(
					'type'       => 'text',
					'name'       => $field->args('_name') . '[lat]',
					'value'      => $aResult['lat'],
					'class'      => 'pw-map-latitude',
					'desc'       => '',
				));
			echo '</div>';

			echo '<div class="wil-lat-setting" style="display: inline; margin-left: 20px">';
				echo '<label>Longitude: </label>';
				echo $field_type_object->input( array(
					'type'       => 'text',
					'name'       => $field->args('_name') . '[lng]',
					'value'      => $aResult['lng'],
					'class'      => 'pw-map-longitude',
					'desc'       => '',
				));
			echo '</div>';
		echo '</div>';
	}

	public function sanitizeBeforeSaving($data){
		if ( !is_array($data) ){
			return sanitize_text_field($data);
		}else{
			$data = array_map(array($this, 'sanitizeBeforeSaving'), $data);
			return $data;
		}
	}

	public function saveMetaBox($postID){
		if ( !current_user_can('edit_theme_options') ){
			return false;
		}

		if ( !isset($_POST['wiloke_custom_field']) ){
			return false;
		}

		$aData = $_POST['wiloke_custom_field'];

		$aData = apply_filters('wiloke-listing-tools/save-meta-boxes', $aData);
		if ( empty($aData) ){
			return false;
		}

		foreach ($aData as $metaKey => $aValues){
			$aData = array();
			foreach ($aValues as $key => $val){
				$aData[$key] = $this->sanitizeBeforeSaving($val);
			}
			SetSettings::setPostMeta($postID, $metaKey, $aData);
		}
	}

	public function parseFieldName($field){
		$this->aParseName = explode(':', $field->args('_name'));
	}

	public function renderWilokeField($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
		$this->parseFieldName($field);
		$aResult = GetSettings::getPostMeta($field_object_id, $this->aParseName[0]);

		if ( $field->args('value') !== '' && $field->args('value') !== false ){
			$val = $field->args('value');
		}else{
			$val = !empty($aResult) && isset($aResult[$this->aParseName[1]]) ? $aResult[$this->aParseName[1]] : '';
		}

		$parseID = isset($this->aParseName[1]) ? 'wiloke_custom_field['.$this->aParseName[0] . ']['.$this->aParseName[1].']' : 'wiloke_custom_field['.$this->aParseName[0] . ']';

		$aField = array(
			'id'         => $parseID,
			'name'       => $parseID,
			'value'      => $val,
			'desc'       => ''
		);

		$func = $field->args('fieldType');

		if ( $field->args('fieldType') == 'select' ){
			$aField['options'] = $this->generateOptions($field, $val);
		}else if ( $field->args('fieldType') == 'multicheck' || $field->args('fieldType') == 'multicheck_inline' ){
			$aField['class'] = false === $field->args( 'select_all_button' ) ? 'cmb2-checkbox-list no-select-all cmb2-list' : 'cmb2-checkbox-list cmb2-list';
		    $aField['name']     = $aField['name'] . '[]';
		    $aField['options']  = $this->generateCheckboxField($field, $field_type_object, $aField, $val);
			$func = 'radio';
		}elseif ( $field->args('fieldType') == 'text' ){
			$func = 'input';
        }

		echo $field_type_object->{$func}($aField);
		$field_type_object->_desc( true, true );
	}
}