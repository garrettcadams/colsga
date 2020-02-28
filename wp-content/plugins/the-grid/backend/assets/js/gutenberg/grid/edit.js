/**
 * Internal Dependencies
 */
import Inspector from "./inspector"
import Controls from "./controls"
import Select from './select';
import logo from '../icons/logo';

/**
 * WordPress Dependencies
 */
const { Fragment } = wp.element;
const { Placeholder } = wp.components;

/**
 * Block edit component
 *
 * @param {object} props
 */
export default props => {

	return (
		<Fragment>
			<Controls { ...props } />
			<Inspector { ...props } />
			<Placeholder icon={ logo }>
				<Select { ...props } />
			</Placeholder>
		</Fragment>
	);

}
