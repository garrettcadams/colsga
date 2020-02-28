/**
 * Internal Dependencies
 */
import Select from './select';

/**
 * WordPress dependencies.
 */
const { __ } = wp.i18n;
const { InspectorControls } = wp.editor;
const { PanelBody } = wp.components;

export default props => {

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Grid Settings' ) }>
				<Select { ...props } />
			</PanelBody>
		</InspectorControls>
	);

};
