/**
 * Internal Dependencies
 */
import edit from './edit';
import icon from '../icons/picto';

/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

export default registerBlockType( 'the-grid/grid', {
	title       :'The Grid',
	description : __( 'Display a grid.', 'tg-text-domain' ),
	icon,
	category    : 'the_grid',
	keywords    : [
		__( 'masonry', 'tg-text-domain' ),
		__( 'layout', 'tg-text-domain' ),
		__( 'grid', 'tg-text-domain' )
	],
	supports    : {
		html : false,
	},
	attributes  : {
		className : {
			type    : 'string',
			default : '',
		},
		align : {
			type    : 'string',
			default : 'none',
		},
		name : {
			type    : 'string',
			default : '',
		}
	},
	getEditWrapperProps( { align } ) {

		const isAligned = [ 'wide', 'full' ].includes( align );
		return isAligned && { 'data-align': align };

	},
	edit,
	save : () => {},
} );
