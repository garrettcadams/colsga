<?php

namespace WilokeListingTools\MetaBoxes;


trait CustomFieldTools {
	public function generateCheckboxField($field, $field_type_object, $aField, $aValues) {
		$aValues = empty($aValues) ? array() : $aValues;
		$options = '';

		if ( $field->args('object_types') ){
			$oPosts = get_posts(
				array(
					'post_type'     =>  $field->args('object_types'),
					'post_status'   =>  'publish',
					'posts_per_page'=>  -1
				)
			);
			$i = 1;
			foreach ($oPosts as $oPost){
				$args = array(
					'value' => $oPost->ID,
					'label' => $oPost->post_title,
					'type' => 'checkbox',
					'name' => $aField['name']
				);
				if ( in_array( $oPost->ID, $aValues ) ) {
					$args[ 'checked' ] = 'checked';
				}
				$options .= $field_type_object->list_input( $args, $i );

				$i++;
			}
		}else{
			$i = 1;
			foreach ($field->args('options') as $val => $name){
				$args = array(
					'value' => $val,
					'label' => $name,
					'type' => 'checkbox',
					'name' => $aField['name']
				);

				if ( in_array( $val, $aValues ) ) {
					$args[ 'checked' ] = 'checked';
				}
				$options .= $field_type_object->list_input( $args, $i );

				$i++;
			}
		}

		return $options;
	}

	public function generateOptions($field, $val){
		$val = !is_array($val) ? array($val) : $val;
		ob_start();
		if ( $field->args('object_types') ){
			$oPosts = get_posts(
				array(
					'post_type'         => $field->args('object_types'),
					'post_status'       => 'publish',
					'posts_per_page'    =>  -1
				)
			);

			foreach ($oPosts as $oPost){
				?>
				<option <?php if(in_array($oPost->ID, $val)) { echo 'selected'; } ?> value="<?php echo esc_attr($oPost->ID) ?>"><?php echo esc_html($oPost->post_title); ?></option>
				<?php
			}
		}else{
			foreach ($field->args('options') as $value => $name){
				?>
				<option <?php if(in_array($value, $val)) { echo 'selected'; } ?> value="<?php echo esc_attr($value); ?>"><?php echo esc_html($name) ?></option>
				<?php
			}
		}
		$options = ob_get_contents();
		ob_end_clean();
		return $options;
	}
}