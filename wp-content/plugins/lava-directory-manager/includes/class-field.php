<?php
/**
 *
 *
 *
 *
 */
class Lava_Directory_Manager_Field{

	public $fieldGroup = 'lava_additem_meta';

	public $is_admin = false;

	public $fieldClassPrefix = 'field';

	public $fieldName = '';

	public $fieldValue = '';

	public $element = '';

	public $element_type = '';

	public $label = '';

	public $value = '';

	public $values = Array();

	public $classes = '';

	public $placeholder = '';

	public $dialog_title = '';

	public $button_upload_label = '';

	public $button_remove_label = '';

	public function __construct( $fieldName, $args=Array(), $reload=false ) {

		$args = apply_filters( 'lava/directory/field/args', $args, $fieldName );

		$this->fieldName = $fieldName;
		$this->setVariables( $args );
		$this->register_hooks();

		if( in_array( 'enqueue_script', get_class_methods( $this ) ) ) {
			add_action( 'admin_footer', array( get_class( $this ), 'enqueue_script' ) );
		}

		if( ! $reload ) {
			do_action( $this->hook_prefix . 'init', $fieldName, $args );
		}
	}

	public function setVariables( $args=Array() ){
		$this->post_type = lava_directory()->core->getSlug();
		$this->is_admin = is_admin() && get_current_screen()->post_type == $this->post_type;
		$this->hook_prefix = sprintf( 'lava_%s_field_', $this->post_type );
		foreach(
			Array(
				'element' => 'element',
				'element_type' => 'type',
				'label' => 'label',
				'classes' => 'class',
				'values' => 'values',
				'multiple' => 'multiple',
				'required' => 'required',
				'placeholder' => 'placeholder',
				'dialog_title' => 'dialog_title',
				'button_upload_label' => 'button_upload_label',
				'button_remove_label' => 'button_remove_label',
				'attribute' => 'attribute',
				'description' => 'description',
			) as $strMemeberVar => $strArgsVar
		) $this->$strMemeberVar = !empty( $args[ $strArgsVar ] ) ? $args[ $strArgsVar ] : false;
	}

	public function register_hooks() {
		add_action( 'admin_footer', array( __CLASS__, 'admin_equeue' ) );
		add_filter( $this->hook_prefix . 'input', array( __CLASS__, 'inputField' ), 10, 3 );
		add_filter( $this->hook_prefix . 'textarea', array( __CLASS__, 'textareaField' ), 10, 3 );
		add_filter( $this->hook_prefix . 'select', array( __CLASS__, 'dropdownField' ), 10, 3 );
		add_filter( $this->hook_prefix . 'wp_library', array( __CLASS__, 'mediaLibraryField' ), 10, 3 );
	}

	public function getClasses() {
		$arrClasses = (Array) explode( ' ', $this->classes );
		return join( ' ', $arrClasses );
	}

	public function getValue( $type='', $separator='' ) {
		return $this->value;

		/*
		switch( $type ) {
			case 'number' : return intVal( $this->value ); break;
			case 'esc_html' : return esc_html( $this->value ); break;
			case 'join' :
				$thisValue = is_array( $this->value ) ? implode( $separator, $this->value ) : $this->value );
			return $thisValue; break;
			default : return $this->value;
		}*/
	}

	public function getAttribute() {
		$output = ' ';
		if( ! is_array( $this->attribute ) ) {
			return $output;
		}
		foreach( $this->attribute as $key => $value ) {
			$output .= sprintf( '%1$s="%2$s"' . ' ', $key, $value );
		}
		return $output;
	}

	public function getDropDownItems( $default='' ) {
		$output = null;

		if( ! $default ) {
			$default = $this->getValue( 'join' );
		}

		if( ! is_array( $default ) ) {
			$default = Array( $default );
		}
		$default = Array_filter( $default );
		if( !is_array( $this->values ) ) {
			$temp = Array();
			$this->values = @explode( ',', $this->values );
			foreach( $this->values as $strValue ) {
				$temp[ $strValue ] = $strValue;
			}
			$this->values = $temp;
		}

		if( !empty( $this->values ) && is_array( $this->values ) ) {
			foreach( $this->values as $strValue => $strLabel ) {
				$output .= sprintf(
					'<option value="%1$s"%3$s>%2$s</option>',
					$strValue, $strLabel,
					selected( in_array( $strValue, $default ), true, false )
				);
			}
		}

		return $output;
	}

	public function before() { return sprintf( '<div class="lava-field-item form-inner %1$s%2$s">', $this->fieldClassPrefix, $this->fieldName ); }

	public function after() { return '</div>'; }

	public function admin_before() { return sprintf( '<tr class="lava-field-item %1$s%2$s">', $this->fieldClassPrefix, $this->fieldName ); }

	public function admin_after() { return '</tr>'; }

	public function label() {
		$format = $this->required ? '<label class="field-title">%2$s%1$s</label>' : '<label class="field-title">%1$s</label>';
		$require_tag = '<span class="field-required-star">*</span>';
		return sprintf( $format, $this->label, $require_tag );
	}

	public function description() {
		if( ! $this->description ) {
			return;
		}
		return sprintf( '<div class="field-description">%1$s</div>', $this->description );
	}

	public function getFieldName( $strFieldName ) {
		if( $this->fieldGroup ) {
			$strFieldName = sprintf( '%1$s[%2$s]', $this->fieldGroup, $strFieldName );
		}
		return $strFieldName;
	}

	public static function inputField( $output=null, $fieldName=false, $obj=false ) {
		$strElementType = strtolower( $obj->element_type );
		$output = null;
		switch( $strElementType ) {
			case 'checkbox' : case 'radio' :
				$output .= $obj->getExplodeOptions( ',', $strElementType );
				break;

			case 'text' : case 'email' : case 'number' :
				$output .=  sprintf(
					'<input type="%1$s" name="%2$s" class="%3$s" value="%4$s" placeholder="%5$s"%6$s %7$s>',
					$obj->element_type,
					$obj->getFieldName( $fieldName ),
					$obj->getClasses(),
					$obj->getValue( 'join', false ),
					$obj->placeholder,
					( $obj->required ? ' required="required"' : '' ),
					$obj->getAttribute()
				);
				break;

			default:
		}
		return $output;
	}

	public function getExplodeOptions( $separator=',', $element_type='' ) {
		$arrOptions = @explode( $separator, $this->values );
		$output = null;
		$thisValue = is_array( $this->value ) ? $this->value : array();
		if( !empty( $arrOptions ) ) {
			foreach( $arrOptions as $strOption ) {
				$output .= sprintf(
					'<label><input type="%1$s" name="%2$s" class="%3$s" value="%4$s"%5$s> %4$s</label>',
					$element_type, $this->getFieldName( $this->fieldName ) . ( $element_type == 'checkbox' ? '[]' : '[option]' ),
					$this->getClasses(), $strOption, checked( in_array( $strOption, $thisValue ), true, false )
				);
			}
		}
		return $output;
	}

	public static function textareaField( $output=null, $fieldName=false, $obj=false ) {
		ob_start();

		printf(
			'<textarea name="%1$s" class="%2$s" rows="8" placeholder="%3$s" style="resize:none;"%5$s>%4$s</textarea>',
			$obj->getFieldName( $fieldName ),
			$obj->getClasses(),
			$obj->placeholder,
			$obj->getValue(),
			$obj->getAttribute()
		);

		return ob_get_clean();
	}

	public static function dropdownField( $output=null, $fieldName=false, $obj=false ) {
		return sprintf(
			'<select name="%1$s" class="%2$s"%4$s>%3$s</select>',
			$obj->getFieldName( $fieldName ),
			$obj->getClasses(),
			$obj->getDropDownItems(),
			$obj->getAttribute()
		);
	}

	public static function mediaLibraryField( $output=null, $fieldName=false, $obj=false ) {
		if( ! is_user_logged_in() ) {
			$output = sprintf( '<div class="lava-upload-wrap" data-field="%1$s" data-multiple="false" data-value=\'%2$s\'%3$s><div class="upload-item-group"></div></div>',
				$obj->getFieldName( $fieldName ),
				json_encode( $obj->value ),
				$obj->getAttribute()
			);
		}else{
			$output = sprintf(
				'<div class="lava-listing-wp-media%9$s" data-label="%10$s" data-field="%4$s" data-multiple="%12$s" data-value=\'%6$s\' data-modal-title="%1$s" data-button-select="%2$s" data-button-remove="%8$s"%11$s><input type="hidden" name="%4$s" value="%6$s" data-media-input><div class="upload-item-group"></div><div class="upload-preview" style="background-image:url(%7$s);"></div><div class="upload-action"><button type="button" class="action-add-item button"><i class="fa fa-plus"></i>%2$s</button></div></div>',
				$obj->dialog_title,
				$obj->button_upload_label,
				$fieldName . '-preview',
				$obj->getFieldName( $fieldName ),
				$obj->getClasses( false ),
				$obj->value,
				wp_get_attachment_url( $obj->value ),
				$obj->button_remove_label,
				( $obj->required ? ' required': '' ),
				$obj->label,
				$obj->getAttribute(),
				($obj->multiple ? $obj->multiple : 'false')
			);
		}
		return $output;
	}

	public function frontend_render() {
		$output = $this->before();
			$output .= $this->label();
			$output .= apply_filters( $this->hook_prefix . $this->element, '', $this->fieldName, $this );
			$output .= $this->description();
		$output .= $this->after();
		return $output;
	}

	public function backend_render() {
		$output = $this->admin_before();
			$output .= '<th>' . $this->label() . '</th>';
			$output .= '<td>' . apply_filters( $this->hook_prefix . $this->element, '', $this->fieldName, $this ) . '</td>';
		$output .= $this->admin_after();
		return $output;
	}

	public static function admin_equeue() {
		wp_localize_script(
			lava_directory()->enqueue->getHandleName( 'lava-submit-script.js' ),
			'lava_directory_manager_submit_args',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxhook' => lava_directory()->submit->ajaxhook,
				'post_id' => isset( $GLOBALS[ 'post' ] ) ? $GLOBALS[ 'post' ]->ID : 0,
				'images' => Array(
					'loading' => lava_directory()->image_url . 'loading.gif',
				),
				'strings' => Array(
					'success' => esc_html__( "has been saved successfully.", 'Lavacode' ),
					'download' => esc_html__( "Download", 'Lavacode' ),
					'btn_remove' => esc_html__( "Remove", 'Lavacode' ),
					'limitDetailImages' => sprintf( esc_html( "Limited amount of images : %s ( You have uploaded : %s )", 'Lavacode' ), '{limit}', '{count}' ),
				),
			)
		);
		wp_enqueue_script( lava_directory()->enqueue->getHandleName( 'lava-submit-script.js' ) );
	}

	public function output() {
		$output = null;
		if( $this->is_admin ) {
			$output = $this->backend_render();
		}else{
			$output = $this->frontend_render();
		}
		return apply_filters( $this->hook_prefix . 'output', $output );
	}

}