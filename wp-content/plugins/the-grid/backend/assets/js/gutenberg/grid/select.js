/**
 * WordPress dependencies.
 */
const { __ } = wp.i18n;
const { withSelect } = wp.data;
const { Spinner, SelectControl } = wp.components;

const select = props => {

	const {
		options,
		attributes,
		setAttributes
	} = props;

	const { name } = attributes;

	return [
		options.loading && (
			<Spinner/>
		),
		! options.loading && (
			<SelectControl
				label={ __( 'Please, select a grid', 'tg-text-domain' ) }
				value={ name || '' }
				onChange={ ( value ) => setAttributes( { name: value } ) }
				options={ options }
			/>
		),
	];

}

export default withSelect( select => {

	const { getGrids } = select( 'the_grid' );

	return {
		options: getGrids(),
	};

} )( select )
